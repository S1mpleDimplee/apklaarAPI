<?php

function getAllAppointments($conn)
{
    header('Content-Type: application/json');

    $sql = "
        SELECT 
            a.aid,
            a.appointmentDate AS date,
            a.appointmentTime AS time,
            a.totalLaborTime AS duration,
            a.status,
            a.repairs AS note,
            u.firstname AS customer_firstname,
            u.lastname AS customer_lastname,
            m.firstname AS mechanic_firstname,
            m.lastname AS mechanic_lastname
        FROM appointments a
        LEFT JOIN user u ON a.userid = u.userid
        LEFT JOIN user m ON a.mechanicid = m.userid AND m.role = 2
        ORDER BY a.appointmentDate, a.appointmentTime
    ";

    $result = mysqli_query($conn, $sql);

    if (!$result) {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "message" => mysqli_error($conn),
            "data" => []
        ]);
        exit;
    }

    $appointments = mysqli_fetch_all($result, MYSQLI_ASSOC);

    echo json_encode([
        "success" => true,
        "data" => $appointments
    ]);
    exit;
}
