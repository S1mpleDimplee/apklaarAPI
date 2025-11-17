<?php

function getAppointmentData($data, $conn)
{
  header('Content-Type: application/json');

  // Validate input
  if (!isset($data['appointmentId']) || empty($data['appointmentId'])) {
    echo json_encode([
      "success" => false,
      "message" => "Ongeldige invoer (appointmentId ontbreekt)",
      "data" => []
    ]);
    return;
  }

  // Sanitize input
  $appointmentId = mysqli_real_escape_string($conn, $data['appointmentId']);

  // Query the database
  $sql = "SELECT * FROM appointments WHERE id = '$appointmentId'";
  $result = mysqli_query($conn, $sql);

  if (!$result) {
    echo json_encode([
      "success" => false,
      "message" => "Databasefout: " . mysqli_error($conn),
      "data" => []
    ]);
    return;
  }

  $appointments = mysqli_fetch_all($result, MYSQLI_ASSOC);

  // Check if appointment was found
  if (empty($appointments)) {
    echo json_encode([
      "success" => false,
      "message" => "Er is geen afspraak gevonden met ID: $appointmentId",
      "data" => []
    ]);
    return;
  }

  // Return the found appointment
  echo json_encode([
    "success" => true,
    "message" => "Afspraakgegevens succesvol opgehaald",
    "data" => $appointments
  ]);
}
