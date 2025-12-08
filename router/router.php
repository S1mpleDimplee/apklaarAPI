<?php
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Dynamically get the current file name
$currentFileName = basename(__FILE__);

// Include other backend functions

include '../Treatments/addtreatment.php';
include '../Treatments/removetreatment.php';
include '../Treatments/edittreatment.php';
include '../Treatments/getalltreatments.php';
include '../appointments/createappointment.php';
include '../appointments/getappointmentdata.php';
include '../getinfo/getalldentists.php';
include '../getinfo/getallpatients.php';
include '../getinfo/getallusers.php';
include '../getinfo/getcurrentdentist.php';
include '../register/register.php';
include '../tandarts/appointmentData.php';
include '../tandarts/getAppointmentsForWeek.php';
include '../userdata/getAllUserData.php';
include '../userdata/getUserData.php';
include '../userdata/updateUserData.php';
include '../userdata/updatecurrentdentist.php';
include '../userdata/updateUserRole.php';


// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Database connection
$connection = mysqli_connect("localhost", "root", "", "apklaar");
if (!$connection) {
    error_log("Connectie met de database is mislukt contacteer ons via apklaar@gmail.com");
    die(json_encode(["success" => false, "message" => "Connectie met de database is mislukt contacteer ons via apklaar@gmail.com"]));
}

// Read POST data
$data = json_decode(file_get_contents('php://input'), true);
if (!$data) {
    error_log("Er is iets fout gegaan contacteer ons via apklaar@gmail.com");
    die(json_encode(["success" => false, "message" => "Er is iets fout gegaan contacteer ons via apklaar@gmail.com"]));
}

// Get function name
$function = strtolower($data['function'] ?? '');
$data = $data['data'] ?? [];

// Router switch
switch ($function) {
    // register and login functions
    case 'adduser':
        addUser($data, $connection);
        break;
    case 'loginuser':
        checkLogin($data, $connection);
        break;

    // user data functions
   

    // get functions
    case 'getalldentists':
        getAllDentists($connection);
        break;
  

    // appointment functions
    case 'checkappointments':
        $stats = checkAppointments($connection);
        echo json_encode([
            "success" => true,
            "message" => "Appointments counted",
            "data" => $stats
        ]);
        break;
  

    default:
        echo json_encode(["success" => false, "message" => "Functie niet gevonden"]);
        break;
}
