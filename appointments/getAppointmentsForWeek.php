<?php
function getAppointmentsForWeek(int $year, int $week, string $mechanicId, $conn) {
    header('Content-Type: application/json');

    try {
        // ISO-8601 week: Monday → Sunday
        $date = new DateTime();
        $date->setISODate($year, $week, 1);
        $monday = $date->format('Y-m-d');

        $date->modify('+6 days');
        $sunday = $date->format('Y-m-d');

        $stmt = $conn->prepare(
            "SELECT 
                aid,
                userid,
                appointmentdate,
                appointmenttime,
                repairs,
                status
             FROM appointments
             WHERE mechanicid = ?
               AND appointmentdate BETWEEN ? AND ?
               AND appointmentdate != '0000-00-00'
             ORDER BY appointmentdate ASC, appointmenttime ASC"
        );

        $stmt->bind_param("sss", $mechanicId, $monday, $sunday);
        $stmt->execute();

        $appointments = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

        echo json_encode([
            "isSuccess" => true,
            "message" => "Afspraken succesvol opgehaald",
            "data" => $appointments,
            "debug_range" => [
                "start" => $monday,
                "end" => $sunday
            ]
        ]);
    } catch (Exception $e) {
        echo json_encode([
            "isSuccess" => false,
            "message" => "Fout: " . $e->getMessage(),
            "data" => []
        ]);
    }
}
