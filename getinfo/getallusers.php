<?php


function getAllUsers($conn)
{
    $sql = "SELECT users.*, useradresses.* 
            FROM users 
            JOIN useradresses ON users.userid = useradresses.userid";

    $result = mysqli_query($conn, $sql);
    $users = mysqli_fetch_all($result, MYSQLI_ASSOC);


    if (!$users) {
        echo json_encode([
            "success" => false,
            "message" => "Er zijn nog geen gebruikers geregistreerd",
            "data" => []
        ]);
        return;
    }

    echo json_encode([
        "success" => true,
        "message" => "Alle gebruikers zijn succesvol opgehaald",
        "data" => $users
    ]);
    return;
}
