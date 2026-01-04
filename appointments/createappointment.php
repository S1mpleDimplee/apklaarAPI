<?php

function createAppointment($data, $conn)
{
    $userid = $data['userid'];
    $mechanicid = $data['mechanicid'];
    $carid = $data['carid'];
    $appointmentDate = $data['appointmentDate'];
    $appointmentTime = $data['appointmentTime'];
    $repairs = json_encode($data['repairs']);
    $totalNetPrice = $data['totals']['netPrice'];
    $totalGrossPrice = $data['totals']['grossPrice'];
    $totalLaborTime = $data['totals']['totalLaborTime'];

    $sql = "INSERT INTO appointments ( userid, mechanicid, carid, appointmentDate, appointmentTime, repairs, totalNetPrice, totalGrossPrice, totalLaborTime) 
            VALUES ('$userid', '$mechanicid', '$carid', '$appointmentDate', '$appointmentTime', '$repairs', '$totalNetPrice', '$totalGrossPrice', '$totalLaborTime')";


    if ($userid && $mechanicid && $carid && $appointmentDate && $appointmentTime && $repairs && $totalNetPrice && $totalGrossPrice && $totalLaborTime) {
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Vul alle velden in"
        ]);
        return;
    }


    if (mysqli_query($conn, $sql)) {
        echo json_encode([
            "success" => true,
            "message" => "Afspraak succesvol aangemaakt",
        ]);

        // AddNotification([
        //     "userid" => $userid,
        //     "preset" => "appointmentcreated",
        //     "appointmentId" => $aid

        // ], $conn);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Afspraak kon niet worden aangemaakt: " . mysqli_error($conn)
        ]);
    }
}