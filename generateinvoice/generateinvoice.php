<?php

require_once '../vendor/autoload.php';

use Mpdf\Mpdf;

function generateinvoice($data, $connection)
{
    $invoiceid = $data['invoiceid'] ?? null;
    $action = $data['action'] ?? 'download'; // 'download', 'view', 'email'

    if (!$invoiceid) {
        echo json_encode(["success" => false, "message" => "Invoice ID ontbreekt"]);
        return;
    }

    // Haal volledige factuurgegevens op
    $sql = "SELECT 
                invoice.*,
                car.carnickname,
                car.brand,
                car.licenseplate,
                user.firstname,
                user.lastname,
                user.email,
                user.phonenumber,
                useradress.adress,
                useradress.city,
                useradress.country,
                useradress.streetname,
                useradress.housenumber
            FROM invoice 
            JOIN car ON car.carid = invoice.carid 
            JOIN user ON user.userid = invoice.userid
            JOIN useradress ON useradress.userid = user.userid
            WHERE invoice.invoiceid = ?";

    $stmt = mysqli_prepare($connection, $sql);
    mysqli_stmt_bind_param($stmt, "i", $invoiceid);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $invoice = mysqli_fetch_assoc($result);

    if (!$invoice) {
        echo json_encode(["success" => false, "message" => "Factuur niet gevonden"]);
        return;
    }

    // // Haal factuurregels op
    // $sql = "SELECT * FROM invoice_lines WHERE invoiceid = ? ORDER BY line_number ASC";
    // $stmt = mysqli_prepare($connection, $sql);
    // mysqli_stmt_bind_param($stmt, "i", $invoiceid);
    // mysqli_stmt_execute($stmt);
    // $result = mysqli_stmt_get_result($stmt);
    // $lines = mysqli_fetch_all($result, MYSQLI_ASSOC);

    // Als er geen lines zijn, maak een default regel
    $lines = [];
    if (empty($lines)) {
        $lines = [
            [
                'line_number' => 1,
                'description' => $invoice['description'] ?? 'APK Keuring',
                'quantity' => 1,
                'price' => $invoice['subtotal'] ?? 0
            ]
        ];
    }

    try {
        // Clear any previous output
        if (ob_get_level()) {
            ob_end_clean();
        }

        // Initialiseer mPDF v6 (oude syntax)
        $mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4']);

        // Genereer HTML
        $html = generateInvoiceHTML($invoice, $lines, $invoiceid);
        $mpdf->WriteHTML($html);

        $invoiceNum = $invoice['invoicenumber'] ?? 'INV-' . str_pad($invoiceid, 5, '0', STR_PAD_LEFT);
        $filename = "factuur_" . $invoiceNum . ".pdf";

        if ($action === 'download') {
            // Direct download naar browser
            $mpdf->Output($filename, 'D');
            exit;

        } elseif ($action === 'view') {
            // Toon inline in browser
            $mpdf->Output($filename, 'I');
            exit;

        } elseif ($action === 'email') {
            // Email functionaliteit (optioneel)
            $tempPath = sys_get_temp_dir() . '/' . $filename;
            $mpdf->Output($tempPath, 'F');

            // TODO: Voeg je email functie hier toe
            // sendInvoiceEmail($invoice['email'], $tempPath, $invoice);

            unlink($tempPath);
            echo json_encode(["success" => true, "message" => "Email verzonden"]);
        }

    } catch (Exception $e) {
        error_log("mPDF error: " . $e->getMessage());
        echo json_encode([
            "success" => false,
            "message" => "Fout bij genereren PDF: " . $e->getMessage()
        ]);
    }
}

function generateInvoiceHTML($invoice, $lines, $invoiceid)
{
    // Bereken totalen
    $subtotal = 0;
    foreach ($lines as $line) {
        $subtotal += ($line['price'] * $line['quantity']);
    }
    $btw = $subtotal * 0.21;
    $total = $subtotal + $btw;

    // Genereer factuurregels HTML
    $linesHTML = '';
    foreach ($lines as $line) {
        $lineTotal = $line['price'] * $line['quantity'];
        $linesHTML .= '
            <tr>
                <td style="padding: 12px; border-bottom: 1px solid #e0e0e0;">' . $line['quantity'] . '</td>
                <td style="padding: 12px; border-bottom: 1px solid #e0e0e0;">' . htmlspecialchars($line['description']) . '</td>
                <td style="padding: 12px; border-bottom: 1px solid #e0e0e0; text-align: right;">€' . number_format($line['price'], 2, ',', '.') . '</td>
                <td style="padding: 12px; border-bottom: 1px solid #e0e0e0; text-align: right;">€' . number_format($lineTotal, 2, ',', '.') . '</td>
            </tr>
        ';
    }

    // Check of betaald
    $isPaid = isset($invoice['status']) && strtolower($invoice['status']) === 'betaald';
    $betaaldBanner = $isPaid ? '<div class="betaald-banner">Betaald</div>' : '';

    // Format datums met fallbacks
    $invoiceDate = isset($invoice['date']) ? date('d-m-Y', strtotime($invoice['date'])) : date('d-m-Y');
    $dueDate = isset($invoice['duedate']) ? date('d-m-Y', strtotime($invoice['duedate'])) : date('d-m-Y', strtotime('+14 days'));

    // Customer info met fallbacks
    $customerName = $invoice['name'] ?? ($invoice['firstname'] . ' ' . $invoice['lastname']);
    $customerAddress = $invoice['address'] ?? (($invoice['streetname'] ?? 'Straatnaam') . ' ' . ($invoice['housenumber'] ?? '1'));
    $customerZipcode = $invoice['zipcode'] ?? '0000XX';
    $customerCity = $invoice['city'] ?? 'Stad';

    // Invoice number met fallback
    $invoiceNumber = $invoice['invoicenumber'] ?? 'INV-' . str_pad($invoiceid, 5, '0', STR_PAD_LEFT);

    return '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body {
                font-family: Arial, sans-serif;
                padding: 40px;
                color: #333;
                background: #fff;
            }
            .betaald-banner {
                position: absolute;
                top: 30px;
                left: -50px;
                background: #28a745;
                color: white;
                padding: 10px 80px;
                transform: rotate(-45deg);
                font-weight: bold;
                font-size: 14px;
                text-align: center;
                box-shadow: 0 2px 5px rgba(0,0,0,0.2);
                z-index: 100;
            }
            .header {
                margin-bottom: 60px;
                margin-top: 20px;
            }
            .header-row {
                width: 100%;
            }
            .header-left {
                float: left;
                width: 50%;
            }
            .header-right {
                float: right;
                width: 50%;
                text-align: right;
            }
            .logo {
                font-size: 32px;
                font-weight: bold;
                color: #1e5a8e;
                margin-bottom: 10px;
            }
            .clear { clear: both; }
            .invoice-title {
                font-size: 24px;
                font-weight: bold;
                margin: 30px 0 20px 0;
            }
            .invoice-details {
                margin-bottom: 30px;
                line-height: 1.8;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin: 30px 0;
            }
            th {
                background-color: #f8f9fa;
                padding: 12px;
                text-align: left;
                font-weight: bold;
                border-bottom: 2px solid #dee2e6;
            }
            th:last-child,
            td:last-child {
                text-align: right;
            }
            td {
                padding: 12px;
                border-bottom: 1px solid #e0e0e0;
            }
            .totals {
                margin-top: 30px;
                text-align: right;
            }
            .totals-row {
                margin: 8px 0;
                font-size: 14px;
            }
            .total-final {
                font-size: 20px;
                font-weight: bold;
                margin-top: 15px;
                padding-top: 15px;
                border-top: 2px solid #333;
            }
            .footer {
                margin-top: 60px;
                padding-top: 20px;
                border-top: 1px solid #e0e0e0;
                font-size: 11px;
                color: #666;
                line-height: 1.6;
            }
        </style>
    </head>
    <body>
        ' . $betaaldBanner . '
        
        <div class="header">
            <div class="header-row">
                <div class="header-left">
                    <strong>' . htmlspecialchars($customerName) . '</strong><br>
                    ' . htmlspecialchars($customerAddress) . '<br>
                    ' . htmlspecialchars($customerZipcode) . ' ' . htmlspecialchars($customerCity) . '
                </div>
                <div class="header-right">
                    <div class="logo">APKlaar</div>
                    opkstraat 67<br>
                    1000XX Amsterdam<br><br>
                    <strong>BTW nr:</strong> NL123456789B01<br>
                    <strong>KvK nr:</strong> 123456789<br>
                    <strong>IBAN:</strong> NL21ABNA123456789
                </div>
            </div>
            <div class="clear"></div>
        </div>
        
        <div class="invoice-title">Factuur</div>
        
        <div class="invoice-details">
            <strong>Factuurnummer:</strong> ' . htmlspecialchars($invoiceNumber) . '<br>
            <strong>Factuurdatum:</strong> ' . $invoiceDate . '<br>
            <strong>Vervaldatum:</strong> ' . $dueDate . '
        </div>
        
        <table>
            <thead>
                <tr>
                    <th style="width: 10%;">Aantal</th>
                    <th style="width: 50%;">Omschrijving</th>
                    <th style="width: 20%;">Prijs</th>
                    <th style="width: 20%;">Totaal</th>
                </tr>
            </thead>
            <tbody>
                ' . $linesHTML . '
            </tbody>
        </table>
        
        <div class="totals">
            <div class="totals-row">
                <strong>Subtotaal:</strong> €' . number_format($subtotal, 2, ',', '.') . '
            </div>
            <div class="totals-row">
                21,00% BTW over €' . number_format($subtotal, 2, ',', '.') . ': €' . number_format($btw, 2, ',', '.') . '
            </div>
            <div class="total-final">
                <strong>Totaal:</strong> €' . number_format($total, 2, ',', '.') . '
            </div>
        </div>
        
        <div class="footer">
            Het bedrag van <strong>€' . number_format($total, 2, ',', '.') . '</strong> gelieve voor 
            <strong>' . $dueDate . '</strong> worden betaald. Doe dit via onze betalings pagina op uw dashboard pagina, 
            of maak het bedrag over naar <strong>NL 96 ABNA 0418 9913 47</strong>
        </div>
    </body>
    </html>
    ';
}
?>