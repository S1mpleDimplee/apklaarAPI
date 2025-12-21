<?php

require "../emailtriggers/newnotification.php";

$notifcationpresets = [
    "welcome" => [
        "title" => "Welkom bij onze dienst!",
        "message" => "Bedankt voor het registreren. We zijn blij je aan boord te hebben.",
        "type" => "info"
    ],
    "password_change" => [
        "title" => "Wachtwoord gewijzigd",
        "message" => "Je wachtwoord is succesvol gewijzigd. Als jij dit niet was, neem dan onmiddellijk contact op met de ondersteuning.",
        "type" => "warning"
    ],
    "verfication_success" => [
        "title" => "Account geverifieerd",
        "message" => "Je account is succesvol geverifieerd. Je kunt nu inloggen en onze diensten gebruiken.",
        "type" => "success"
    ],
    "subscription_ending" => [
        "title" => "Abonnement bijna verlopen",
        "message" => "Je abonnement verloopt over 3 dagen. Vergeet niet om te verlengen om ononderbroken toegang te behouden.",
        "type" => "alert"
    ],
    "caradded" => [
        "title" => "Nieuwe auto toegevoegd",
        "message" => "U heeft een nieuwe auto genaamd \" {carname} \" aan uw account toegevoegd. U ontvangt nu meldingen voor deze auto.",
        "type" => "info"
    ],
    "cardeleted" => [
        "title" => "Auto verwijderd",
        "message" => "De auto {carname} is van uw account verwijderd. Als dit een vergissing is, neem dan contact op met de klantenservice.",
        "type" => "warning"
    ]
];

function AddNotification($data, $conn)
{
    global $notifcationpresets;

    $userid = $data['userid'] ?? null;
    $preset = $data['preset'] ?? null;
    $carname = $data['carname'] ?? "";

    $title = $notifcationpresets[$preset]['title'] ?? "";
    $message = $notifcationpresets[$preset]['message'] ?? "";


    if ($preset === "caradded") {
        $message = str_replace("{carname}", $carname, $message);
    }

    sendNewNotification([
        "userid" => $userid,
        "title" => $title,
        "message" => $message
    ], $conn);

    $createNotifcationSQL = "INSERT INTO notifications (userid, title, description, date) VALUES ('$userid', '$title', '$message', NOW())";
    mysqli_query($conn, $createNotifcationSQL);
}

function FetchNotifcations($data, $conn)
{
    $userid = $data['userid'] ?? null;

    $fetchNotifcationsSQL = "SELECT * FROM notifications WHERE userid = $userid ORDER BY date DESC";
    $result = mysqli_query($conn, $fetchNotifcationsSQL);

    $notifications = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $notifications[] = $row;
    }

    echo json_encode([
        "success" => true,
        "message" => "Notifications fetched successfully",
        "data" => $notifications
    ]);
}

?>