<?php

function Cancelappointment($data, $conn)
{
    $appointmentID = $data['appointmentId'] ?? null;

    $cancelAppointmentSQL = "DELETE FROM appointments WHERE aid='$appointmentID'";
    if (mysqli_query($conn, $cancelAppointmentSQL)) {
        echo json_encode([
            "success" => true,
            "message" => "Afspraak succesvol geannuleerd."
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Fout bij het annuleren van de afspraak"
        ]);
        return;
    }
}