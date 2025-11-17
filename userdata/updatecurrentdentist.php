<?php

function updatecurrentdentist($userid, $currentdentistid, $conn)
{
    $sql = "update users set currentdentistid = '$currentdentistid' where userid = '$userid'";
    mysqli_query($conn, $sql);


    if (mysqli_affected_rows($conn) > 0) {
        echo json_encode([
            "success" => true,
            "message" => "Uw tandarts is succesvol bijgewerkt"
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Update mislukt of geen wijzigingen aangebracht"
        ]);
    }
}
?>