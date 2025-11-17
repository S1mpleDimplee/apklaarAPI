<?php
function getAllTreatments($conn)
{
    $sql = "SELECT * FROM treatments";
    $result = mysqli_query($conn, $sql);
    $treatments = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $treatments[] = $row;
    }

    echo json_encode([
        "success" => true,
        "message" => "All treatments fetched successfully",
        "data" => $treatments
    ]);
}
?>