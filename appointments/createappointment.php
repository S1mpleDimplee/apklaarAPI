<?php

function createAppointment($data, $conn)
{
    $aid = $data['aid'] ?? null;
    $userid = $data['userid'] ?? null;
    $repid = $data['repid'] ?? null;
    $moid = $data['moid'] ?? null;
    $apk = $data['apk'] ?? null;
    $note = $data['note'] ?? null;
    $time = $data['time'] ?? null;
    $date = $data['date'] ?? null;
    $duration = $data['duration'] ?? null;

    $sql = "INSERT INTO appointments (aid, userid, repid, moid, apk, note, time, date, duration) VALUES 
    ('$aid','$userid', '$repid','$moid', '$apk', '$note', '$time', '$date', '$duration')";

    if (mysqli_query($conn, $sql)) {
        echo json_encode([
            "success" => true,
            "message" => "Afspraak succesvol aangemaakt"
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Afspraak kon niet worden aangemaakt: " . mysqli_error($conn)
        ]);
    }
}