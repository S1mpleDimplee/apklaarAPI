<?php

function getAppointmentData($data, $conn)
{
  header('Content-Type: application/json');

  // Validate input
  if (!isset($data['aid']) || empty($data['aid'])) {
    echo json_encode([
      "success" => false,
      "message" => "Ongeldige invoer (aid ontbreekt)",
      "data" => []
    ]);
    return;
  }

  // Sanitize input
  $aid = mysqli_real_escape_string($conn, $data['aid']);

  // Query the database
  $sql = "SELECT * FROM appointment WHERE id = '$aid'";
  $result = mysqli_query($conn, $sql);

  if (!$result) {
    echo json_encode([
      "success" => false,
      "message" => "Databasefout: " . mysqli_error($conn),
      "data" => []
    ]);
    return;
  }

  $appointment = mysqli_fetch_all($result, MYSQLI_ASSOC);

  // Check if appointment was found
  if (empty($appointment)) {
    echo json_encode([
      "success" => false,
      "message" => "Er is geen afspraak gevonden met ID: $aid",
      "data" => []
    ]);
    return;
  }

  // Return the found appointment
  echo json_encode([
    "success" => true,
    "message" => "Afspraakgegevens succesvol opgehaald",
    "data" => $appointment
  ]);
}
