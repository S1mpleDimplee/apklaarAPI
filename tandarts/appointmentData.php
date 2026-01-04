<?php
function checkAppointments($conn) {
    $today = date("Y-m-d");
    $tomorrow = date("Y-m-d", strtotime("+1 day"));

    // Select only today and tomorrow appointments
    $sql = "SELECT `date` FROM `appointment` 
            WHERE DATE(`date`) IN ('$today', '$tomorrow')";
    $result = $conn->query($sql);

    $appointmentsToday = 0;
    $appointmentsTomorrow = 0;

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $rowDate = date("Y-m-d", strtotime($row['date']));
            if ($rowDate === $today) $appointmentsToday++;
            if ($rowDate === $tomorrow) $appointmentsTomorrow++;
        }
    }
    return [
        "today" => $appointmentsToday,
        "tomorrow" => $appointmentsTomorrow
    ];
}
?>
