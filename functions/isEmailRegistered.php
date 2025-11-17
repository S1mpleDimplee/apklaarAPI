<?php
function isEmailRegistered($email, $conn)
{
    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $sql);
    return mysqli_num_rows($result) > 0;
}
?>