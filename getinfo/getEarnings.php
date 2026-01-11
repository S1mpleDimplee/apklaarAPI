<?php
function getEarnings($conn, $period = 'weekly') {
    header('Content-Type: application/json');

    $data = [];

    switch($period) {
        case 'weekly':
            // Earnings per day of current week
            $sql = "
                SELECT 
                    DAYNAME(appointmentDate) AS label,
                    COALESCE(SUM(totalPrice), 0) AS amount
                FROM appointments
                WHERE YEARWEEK(appointmentDate, 1) = YEARWEEK(CURDATE(), 1)
                GROUP BY DAYNAME(appointmentDate)
                ORDER BY appointmentDate
            ";
            break;

        case 'monthly':
            // Earnings per day of current month
            $sql = "
                SELECT 
                    DATE(appointmentDate) AS label,
                    COALESCE(SUM(totalPrice), 0) AS amount
                FROM appointments
                WHERE MONTH(appointmentDate) = MONTH(CURDATE())
                AND YEAR(appointmentDate) = YEAR(CURDATE())
                GROUP BY DATE(appointmentDate)
                ORDER BY appointmentDate
            ";
            break;

        case 'yearly':
            // Earnings per month of current year
            $sql = "
                SELECT 
                    MONTHNAME(appointmentDate) AS label,
                    COALESCE(SUM(totalPrice), 0) AS amount
                FROM appointments
                WHERE YEAR(appointmentDate) = YEAR(CURDATE())
                GROUP BY MONTH(appointmentDate)
                ORDER BY MONTH(appointmentDate)
            ";
            break;

        default:
            echo json_encode([
                "success" => false,
                "message" => "Invalid period",
                "data" => []
            ]);
            exit;
    }

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

    while($row = mysqli_fetch_assoc($result)) {
        $row['amount'] = (float)$row['amount'];
        $data[] = $row;
    }

    echo json_encode([
        "success" => true,
        "data" => $data
    ]);
    exit;
}
