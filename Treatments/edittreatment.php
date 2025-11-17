<?php

function editTreatment($data, $conn)
{
    $userid = $data['userid'] ?? null;
    $treatmentid = $data['id'] ?? null;
    $note = $data['note'] ?? null;
    $treatment = $data['treatment'] ?? null;

    if (is_null($userid) || is_null($treatmentid) || $treatment == "") {
        echo json_encode([
            "success" => false,
            "message" => "Niet alle vereiste velden zijn ingevuld"
        ]);
        return;
    }

    $sql = "UPDATE usertreatments SET treatment = '$treatment', note = '$note' WHERE userid='$userid' AND id='$treatmentid'";
    if (mysqli_query($conn, $sql)) {
        echo json_encode([
            "success" => true,
            "message" => "Behandeling succesvol bijgewerkt"
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Er is een fout opgetreden bij het bijwerken van de behandeling: " . mysqli_error($conn)
        ]);
    }
}