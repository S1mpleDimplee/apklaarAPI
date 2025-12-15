<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
include '../notifications/addnotifcation.php';

function SendVerificationEmail($data)
{
    $code = rand(100000, 999999);
    $_SESSION['verification_code'] = $code;

    $to = $data['email'] ?? '';
    $name = "Jaylano van der Veen";

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'noreply.apklaar@gmail.com';
        $mail->Password = 'fcivxqefmvmczgvz'; // App wahctwoord
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('noreply.apklaar@gmail.com', 'apklaar');
        $mail->addAddress($to, $name);

        $mail->isHTML(true);
        $mail->Subject = 'Jou verificatiecode';

        // Styled HTML body
        $mail->Body = "
        <div>
            <h2>apklaar</h2>
            <p>Hallo <strong>$name</strong>,</p>
            <p>Hier is uw verificatiecode:</p>
            <div>$code</div>
            <p>Voer deze code in op de verificatiepagina om uw identiteit te bevestigen.</p>
            <hr>
            <p>Deze code vervalt over 5 minuten.</p>
    </div>
    ";

        $mail->send();
        echo json_encode(["success" => true, "message" => "Email verstuurd! Controleer uw inbox voor de verificatiecode.<br><a href='verify_code.php'>Ga naar de verificatiepagina</a>"]);
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => "Email versturen is fout gegaan.", "error" => $mail->ErrorInfo]);
    }
}


function CheckIfCodeIsValid($data, $conn)
{
    $inputCode = $data['code'] ?? '';
    $userid = $data['userid'] ?? null;

    if (!$userid) {
        echo json_encode(["success" => false, "message" => "Ongeldige gebruiker."]);
        return;
    }

    if (isset($_SESSION['verification_code']) && $_SESSION['verification_code'] == $inputCode) {
        unset($_SESSION['verification_code']);
        echo json_encode(["success" => true, "message" => "Verificatie succesvol!"]);

        $updateUserVerifieStatusSQL = "UPDATE user SET isverified = 1 WHERE id = '$userid'";
        mysqli_query($conn, $updateUserVerifieStatusSQL);

        AddNotification([
            "userid" => $userid,
            "preset" => "verfication_success"
        ], $conn);

    } else {
        echo json_encode(["success" => false, "message" => "Ongeldige verificatiecode, Probeer het opnieuw."]);
    }
}




