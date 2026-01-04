<?php
header("Access-Control-Allow-Origin: http://localhost:3000");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");
session_start();

// Database connection
$connection = mysqli_connect("localhost", "root", "", "apklaar");
if (!$connection) {
    die(json_encode([
        "success" => false,
        "message" => "Connectie met de database is mislukt contacteer ons via apklaar@gmail.com"
    ]));
}

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Include backend functions
include '../Treatments/addtreatment.php';
include '../Treatments/removetreatment.php';
include '../Treatments/edittreatment.php';
include '../Treatments/getalltreatments.php';
include '../appointments/createappointment.php';
include '../appointments/getappointmentdata.php';
include '../appointments/getAllAppointments.php';
include '../appointments/getMechanicAppointments.php';
include '../tandarts/getAppointmentsForWeek.php';
include '../getinfo/getalldentists.php';
include '../getinfo/getallpatients.php';
include '../getinfo/getallusers.php';
include '../getinfo/getcurrentdentist.php';
include '../register/register.php';
include '../userdata/getAllUserData.php';
include '../userdata/getUserData.php';
include '../userdata/updateUserData.php';
include '../userdata/updatecurrentdentist.php';
include '../userdata/updateUserRole.php';
include '../emailtriggers/verificationcode.php';
include '../addcar/addcar.php';
include '../getcars/getcars.php';
include '../notifications/getNotifications.php';
include '../removecar/removecar.php';
include '../editcar/editcar.php';
include '../dashboardinfo/customerdashboard.php';
include '../fetchinvoices/fetchinvoices.php';
include '../generateinvoice/generateinvoice.php';
include '../stripe_payment/stripe_payment.php';
include '../fetchreparations/fetchreparations.php';
include '../fetchmechanics/fetchmechanics.php';

// Read POST data
$request = json_decode(file_get_contents('php://input'), true);
if (!$request) {
    die(json_encode([
        "success" => false,
        "message" => "Er is iets fout gegaan contacteer ons via apklaar@gmail.com"
    ]));
}

// Get function name and data
$function = strtolower($request['function'] ?? '');
$data = $request['data'] ?? [];

// Router
switch ($function) {
    // User / auth
    case 'adduser':
        addUser($data, $connection);
        break;
    case 'loginuser':
        checkLogin($data, $connection);
        break;

    // Notifications
    case 'getnotifications':
        getNotifications($data, $connection);
        break;

    // Cars
    case 'getcars':
        getcars($data, $connection);
        break;
    case 'addcar':
        addCar($data, $connection);
        break;
    case 'removecar':
        removeCar($data, $connection);
        break;
    case 'editcar':
        editCar($data, $connection);
        break;

    // Appointments
    case 'createappointment':
        CreateAppointment($data, $connection);
        break;
    case 'getallappointments':
        getAllAppointments($connection);
        break;
    case 'getmechanicappointments':
        getMechanicAppointments($data, $connection);
        break;
    case 'getappointmentsforweek':
        getAppointmentsForWeek($data, $connection);
        break;
    case 'checkappointments':
        $stats = checkAppointments($connection);
        echo json_encode([
            "success" => true,
            "message" => "Appointments counted",
            "data" => $stats
        ]);
        break;

    // Other
    case 'fetchcustomerdashboard':
        fetchCustomerDashboard($data, $connection);
        break;
    case 'fetchinvoices':
        fetchinvoices($data, $connection);
        break;
    case 'generateinvoice':
        generateinvoice($data, $connection);
        break;
    case 'stripe_payment':
        handleStripePayment($data, $connection);
        break;
    case 'fetchreparations':
        fetchReparations($data, $connection);
        break;
    case 'fetchmechanics':
        fetchMechanics($connection);
        break;

    // Get info
    case 'getalldentists':
        getAllDentists($connection);
        break;
    case 'getallpatients':
        getAllPatients($connection);
        break;
    case 'getallusers':
        getAllUsers($connection);
        break;
    case 'getcurrentdentist':
        getCurrentDentist($connection);
        break;

    // Email verification
    case 'sendverificationcode':
        SendVerificationEmail($data);
        break;
    case 'checkverificationcode':
        CheckIfCodeIsValid($data, $connection);
        break;

    default:
        echo json_encode([
            "success" => false,
            "message" => "Functie niet gevonden"
        ]);
        break;
}
