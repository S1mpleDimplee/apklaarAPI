<?php

function getAppointmentsDataPatient($userid, $conn)
{
   
    // Query to get appointment details
    $sql = "SELECT a.appointmentid, a.date, a.time, a.status, CONCAT(u.firstname, ' ', u.lastname) AS dentistname
            FROM appointments a
            JOIN users u ON a.dentistid = u.userid
            WHERE a.patientid = '$userid'
            ORDER BY a.date, a.time";
    $result = $conn->query($sql);

    $appointments = [];
    $totalAppointments = 0;
    $upcomingAppointments = 0;
    $today = date('Y-m-d');

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $appointments[] = $row;
            $totalAppointments++;
            if ($row['date'] >= $today && $row['status'] == 'upcoming') {
                $upcomingAppointments++;
            }
        }
    }

    return [
        'appointments' => $appointments,
        'totalAppointments' => $totalAppointments,
        'upcomingAppointments' => $upcomingAppointments
    ];
}