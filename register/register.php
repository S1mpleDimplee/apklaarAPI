<?php

include_once('../functions/isEmailRegistered.php'); 


function isPasswordStrong($password, &$message)
{
    if (strlen($password) < 8) {
        $message = "Wachtwoord moet minimaal 8 tekens bevatten";
        return false;
    }
    if (!preg_match('/[A-Z]/', $password)) {
        $message = "Wachtwoord moet minimaal één hoofdletter bevatten";
        return false;
    }
    if (!preg_match('/[0-9]/', $password)) {
        $message = "Wachtwoord moet minimaal één cijfer bevatten";
        return false;
    }
    if (!preg_match('/[\W]/', $password)) {
        $message = "Wachtwoord moet minimaal één speciaal teken bevatten zoals !, @, #, $, -, etc.";
        return false;
    }
    return true;
}

function addUser($data, $conn)
{
    $firstName = $data['firstname'] ?? null;
    $lastName = $data['lastname'] ?? null;
    $email = $data['email'] ?? null;
    $password = $data['password'] ?? null;
    $phonenumber = $data['phonenumber'] ?? null;
    $adress = $data['address'] ?? null;
    $streetname = $data['streetname'] ?? null;
    $housenumber = $data['housenumber'] ?? null;
    $postalcode = $data['postalcode'] ?? null;
    $city = $data['city'] ?? null;



    // First check if email is already in use if so succes = false and return a error message
    if (isEmailRegistered($email, $conn)) {
        echo json_encode([
            "success" => false,
            "message" => "Dit email adress is al geregistreerd, probeer een andere email adress"
        ]);
        return;
    }

    // if (!isPasswordStrong($password, $message)) {
    //     echo json_encode([
    //         "success" => false,
    //         "message" => $message
    //     ]);
    //     return;
    // }

    // If any of the fields are empty return an error message
    if (empty($firstName) || empty($lastName) || empty($email) || empty($password)) {
        echo json_encode([
            "success" => false,
            "message" => "Alle velden zijn verplicht"
        ]);
        return;
    }

    // Hases the passowrd
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO user (firstname, lastname, email, phonenumber ) VALUES ('$firstName', '$lastName', '$email', '$phonenumber')";
    mysqli_query($conn, $sql);

    // Get the user id after adding
    $newId = mysqli_insert_id($conn);

    // Create a format for the user id with the new ID
    $userId = 'U-' . str_pad($newId, 5, '0', STR_PAD_LEFT);

    $updateSql = "UPDATE user SET userid='$userId' WHERE id=$newId";
    mysqli_query($conn, $updateSql);
    
    $passwordSql = "INSERT INTO userpassword (userid, password) VALUES ('$userId', '$hashedPassword')";
    mysqli_query($conn, $passwordSql);

    $addUserAdressSql = "INSERT INTO useradress (userid, adress, streetname, city, country) VALUES ('$userId')";
    mysqli_query($conn, $addUserAdressSql);

    echo json_encode([
        "success" => mysqli_affected_rows($conn) > 0,
        "message" => mysqli_affected_rows($conn) > 0 ? "Account is succesvol aangemaakt" : "Registratie mislukt",
        "userId" => $userId
    ]);
}

function checkLogin($data, $conn)
{
    $email = $data['email'] ?? null;
    $password = $data['password'] ?? null;

    $sql = "SELECT *, p.password FROM user u JOIN userpasswords p ON u.userid = p.userid WHERE u.email='$email'";
    $result = mysqli_query($conn, $sql);
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($password, $user['password'])) {
        echo json_encode([
            "success" => true,
            "message" => "Login successful",
            "data" => [
                "id" => $user['id'],
                "firstName" => $user['firstname'],
                "lastName" => $user['lastname'],
                "email" => $user['email'],
                "role" => $user['role'],
                "userid" => $user['userid']
            ],
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Email of wachtwoord is onjuist, probeer het opnieuw"
        ]);
    }
}
