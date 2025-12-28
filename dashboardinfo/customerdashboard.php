<?php

function fetchCustomerDashboard($data, $connection)
{
    $userid = $data['userid'] ?? null;

    $userid = mysqli_real_escape_string($connection, $userid);

    $sql = "SELECT 
                (SELECT count(*) FROM invoice WHERE (status = 'pending' OR status = 'unpayed') AND userid = '$userid') AS openInvoices,

            ";
    $result = mysqli_query($connection, $sql);
    if (!$result) {
        error_log("Fout bij het ophalen van dashboardgegevens: " . mysqli_error($connection));
        echo json_encode([
            "success" => false,
            "message" => "Fout bij het ophalen van dashboardgegevens"
        ]);
        return;
    }

    echo json_encode([
        "success" => true,
        "message" => "Customer dashboard info fetched",
        "data" => $result
    ]);
}