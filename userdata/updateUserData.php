<?php



function UpdateUserData($userData, $conn)
{
    $streetname = $userData['streetname'] ?? null;
    $city = $userData['city'] ?? null;
    $postalCode = $userData['postalcode'] ?? null;
    $country = $userData['country'] ?? null;
    $housenumber = $userData['housenumber'] ?? null;
    $addition = $userData['addition'] ?? null;
    $firstname = $userData['firstname'] ?? null;
    $lastname = $userData['lastname'] ?? null;
    $email = $userData['email'] ?? null;

    $userid = $userData['userid'] ?? null;

    if (!$userid) {
        echo json_encode([
            "success" => false,
            "message" => "Userid niet gevonden"
        ]);
        return;
    }

    if (isEmailRegistered($email, $conn)) {
        echo json_encode([
            "success" => false,
            "message" => "Dit email adress is al geregistreerd, probeer een andere email adress"
        ]);
        return;
    }

    $updateAdresses = "UPDATE useradresses SET 
        streetname='$streetname', 
        city='$city', 
        postalcode='$postalCode', 
        housenumber='$housenumber',
        addition='$addition',
        country='$country'
    WHERE userid='$userid'";

    mysqli_query($conn, $updateAdresses);


    $updateUser = "UPDATE users SET 
        firstname='$firstname', 
        lastname='$lastname', 
        email='$email'
    WHERE userid='$userid'";

    mysqli_query($conn, $updateUser);

    echo json_encode([
        "success" => mysqli_affected_rows($conn) > 0,
        "message" => mysqli_affected_rows($conn) > 0 ? "Gebruiker informatie succesvol bijgewerkt" : "Er zijn geen aanpassingen gedaan"
    ]);
}
