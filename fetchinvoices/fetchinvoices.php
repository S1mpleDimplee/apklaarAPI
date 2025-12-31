<?php

function fetchinvoices($data, $connection)
{
    $userid = $data['userid'] ?? null;

    $userid = mysqli_real_escape_string($connection, $userid);

    $sql = "SELECT invoice.*, 
            IF(car.carnickname = '' OR car.carnickname IS NULL, 
                 CONCAT(car.brand, ' ', car.model), 
                 car.carnickname) AS carnickname, 
            car.brand FROM invoice 
        JOIN car ON car.carid = invoice.carid 
        WHERE car.userid = '$userid' 
        ORDER BY invoice.date DESC";

    $result = mysqli_query($connection, $sql);
    if (!$result) {
        error_log("Fout bij het ophalen van facturen: " . mysqli_error($connection));
        echo json_encode([
            "success" => false,
            "message" => "Fout bij het ophalen van facturen"
        ]);
        return;
    }

    // Fetch the actual data from the result set
    $invoices = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $invoices[] = $row;
    }

    echo json_encode([
        "success" => true,
        "message" => "Factures succesvol opgehaald",
        "data" => $invoices
    ]);
}