<?php

function fetchManagerDashboardStats($conn)
{
    $today = date('Y-m-d');

    /* Appointments today */
    $appointmentsSql = "
        SELECT 
            COUNT(*) AS total,
            SUM(status = 'completed') AS completed,
            SUM(status = 'scheduled') AS scheduled
        FROM appointments
        WHERE status = 'scheduled'
    ";

    $appointmentsResult = mysqli_query($conn, $appointmentsSql);
    $appointments = mysqli_fetch_assoc($appointmentsResult);

    /* Mechanics count */
    $mechanicsSql = "
        SELECT COUNT(*) AS total
        FROM user
        WHERE role = 2
    ";

    $mechanicsResult = mysqli_query($conn, $mechanicsSql);
    $mechanics = mysqli_fetch_assoc($mechanicsResult);

    /* Customers count */
    $customersSql = "
        SELECT COUNT(*) AS total
        FROM user
        WHERE role = 1
    ";

    $customersResult = mysqli_query($conn, $customersSql);
    $customers = mysqli_fetch_assoc($customersResult);

    $revenueSql = "
    SELECT COALESCE(SUM(cost), 0) AS total
    FROM invoice
    WHERE status = 'payed'
";

$revenueResult = mysqli_query($conn, $revenueSql);
$revenue = mysqli_fetch_assoc($revenueResult);


    echo json_encode([
        "success" => true,
        "data" => [
            "appointmentsToday" => [
                "total" => (int)$appointments['total'],
                "completed" => (int)$appointments['completed'],
                "scheduled" => (int)$appointments['scheduled']
            ],
            "mechanics" => (int)$mechanics['total'],
            "customers" => (int)$customers['total'],
             "revenueToday" => (float)$revenue['total']
        ]
    ]);
}
