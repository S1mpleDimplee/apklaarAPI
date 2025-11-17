<?php

function removeTreatment($data, $conn)
{
    $userid = $data['userid'] ?? null;
    $treatmentid = $data['id'] ?? null;

    if (is_null($userid) || is_null($treatmentid)) {
        echo json_encode([
            "success" => false,
            "message" => "Ongeldige waarde ingevoerd"
        ]);
        return;
    }

    $sql = "DELETE FROM usertreatments WHERE userid='$userid' AND id='$treatmentid'";
    if (mysqli_query($conn, $sql)) {
        echo json_encode([
            "success" => true,
            "message" => "Behandeling succesvol verwijderd"
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Er is een fout opgetreden bij het verwijderen van de behandeling: " . mysqli_error($conn)
        ]);
    }
}