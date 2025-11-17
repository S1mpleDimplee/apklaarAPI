<?php
function getAllUserData($data, $conn)
{
    $userid = $data ?? null;

    if (is_null($userid)) {
        echo json_encode([
            "success" => false,
            "message" => "Userid niet gevonden"
        ]);
        return;
    }

    // Fetch user data
    $userSql = "SELECT u.*, a.* 
                FROM users u
                LEFT JOIN useradresses a ON u.userid = a.userid
                WHERE u.userid = '$userid'";
    $userResult = mysqli_query($conn, $userSql);
    $user = mysqli_fetch_assoc($userResult);

    // Fetch treatments
    $treatmentSql = "SELECT t.* FROM usertreatments t WHERE t.userid = '$userid'";
    $treatmentResult = mysqli_query($conn, $treatmentSql);
    $treatments = [];
    while ($row = mysqli_fetch_assoc($treatmentResult)) {
        $treatments[] = [
            'userid' => $row['userid'],
            'treatment' => $row['treatment'],
            'note' => $row['note'],
            'id' => $row['id'],
            'treatmentdate' => $row['treatmentdate']
        ];
    }

    if ($user) {
        // Add treatments inside the user data
        $user['treatments'] = $treatments;

        echo json_encode([
            "success" => true,
            "data" => $user
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Geen gebruiker gevonden"
        ]);
    }
}
?>