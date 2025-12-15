<?php
$carname = null;

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
        "message" => "U heeft een nieuwe auto aan uw account toegevoegd: " . $carname . ". U ontvangt nu meldingen voor deze auto.",
        "type" => "info"
    ]
];


function AddNotification($data, $conn)
{
    global $notifcationpresets;

    $userid = $data['userid'] ?? null;
    $preset = $data['preset'] ?? null;
    $carname = $data['carname'] ?? null;

    $title = $notifcationpresets[$preset]['title'] ?? "";
    $message = $notifcationpresets[$preset]['message'] ?? "";


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