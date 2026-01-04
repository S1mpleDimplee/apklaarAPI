<?php

function createAppointment($data, $conn)
{
    $userid = $data['userid'];
    $mechanicid = $data['mechanicid'];
    $appointmentDate = $data['appointmentDate'];
    $appointmentTime = $data['appointmentTime'];
    $repairs = json_encode($data['repairs']);
    $totalNetPrice = $data['totals']['netPrice'];
    $totalGrossPrice = $data['totals']['grossPrice'];
    $totalLaborTime = $data['totals']['totalLaborTime'];

    $sql = "INSERT INTO appointments ( userid, mechanicid, appointmentDate, appointmentTime, repairs, totalNetPrice, totalGrossPrice, totalLaborTime) 
            VALUES ('$userid', '$mechanicid', '$appointmentDate', '$appointmentTime', '$repairs', '$totalNetPrice', '$totalGrossPrice', '$totalLaborTime')";


    $emptyFields = [];
    foreach (['userid', 'mechanicid', 'appointmentDate', 'appointmentTime', 'repairs', 'totals'] as $field) {
        if (empty($data[$field])) {
            $emptyFields[] = $field;
        }
    }
    if (empty($emptyFields)) {
        echo json_encode([
            "success" => false,
            "message" => "De volgende velden zijn verplicht en mogen niet leeg zijn: " . implode(", ", $emptyFields)
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