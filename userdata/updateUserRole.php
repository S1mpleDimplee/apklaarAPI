<?php

function updateUserRole($data, $conn)
{
    $userid = $data['userid'] ?? null;
    $newRole = $data['role'] ?? 0;
    $rolenames = ['patient', 'tandarts', 'assistente', 'manager'];

    if (is_null($userid) || is_null($newRole)) {
        echo json_encode([
            "success" => false,
            "message" => "Niet alle vereiste velden zijn ingevuld"
        ]);
        return;
    }

    $sql = "UPDATE users SET role='$newRole' WHERE userid='$userid'";
    if (mysqli_query($conn, $sql)) {

        echo json_encode([
            "success" => true,
            "message" => "gebruiker is nu een " . $rolenames[$newRole]
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Fout bij het bijwerken van de gebruikersrol: " . mysqli_error($conn)
        ]);
    }
}