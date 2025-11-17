<?php

function getAppointmentsForWeek($userid, $week, $year, $conn)
{
    if (is_null($userid) || is_null($week) || is_null($year)) {
        echo json_encode([
            "success" => false,
            "message" => "Verplichte parameters ontbreken"
        ]);
        return;
    }

    // Calculate start and end dates of the week
    $dto = new DateTime();
    $dto->setISODate($year, $week);
    $startOfWeek = $dto->format('Y-m-d');
    $dto->modify('+6 days');
    $endOfWeek = $dto->format('Y-m-d');

    // Use prepared statements to prevent SQL injection
    $sql = "SELECT * FROM appointments WHERE dentistid = ? AND date BETWEEN ? AND ? ORDER BY date, time";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $userid, $startOfWeek, $endOfWeek);
    $stmt->execute();
    $result = $stmt->get_result();

    $appointments = [];
    while ($row = $result->fetch_assoc()) {
        $appointments[] = $row;
    }

    echo json_encode([
        "success" => true,
        "message" => "Afspraakgegevens opgehaald",
        "data" => $appointments
    ]);
}