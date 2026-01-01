<?php
function editCar($data, $conn)
{

    // Verplicht
    $brand = $data['brand'] ?? null;
    $model = $data['model'] ?? null;
    $buildyear = $data['buildyear'] ?? null;
    $licensePlateCountry = $data['countryCode'] ?? null;
    $licensePlate = $data['licensePlate'] ?? null;


    // Niet verplicht
    $color = $data['color'] ?? null;
    $fuelType = $data['fuelType'] ?? null;
    $carNickname = $data['carNickname'] ?? null;
    $lastInspection = $data['lastInspection'] ?? null;
    $carimage = $data['carimage'] ?? null;

    $userid = $data['userid'] ?? null;

    if ($carimage) {
        $carimage = mysqli_real_escape_string($conn, $carimage);
    }




    $editCarSql = "UPDATE car SET 
                carnickname = '$carNickname',
                licenseplatecountry = '$licensePlateCountry',
                licenseplate = '$licensePlate',
                brand = '$brand',
                fueltype = '$fuelType',
                lastinspection = '$lastInspection',
                buildyear = '$buildyear',
                model = '$model',
                color = '$color',
                carimage = '$carimage'
                WHERE userid = '$userid'";

    if (mysqli_query($conn, $editCarSql)) {

        AddNotification([
            "userid" => $userid,
            "preset" => "caredited",
            "carname" => $carNickname
        ], $conn);

        echo json_encode([
            "success" => true,
            "message" => "Auto succesvol aangepast"
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Fout bij het aanpassen van de auto: " . mysqli_error($conn)
        ]);
    }
}

?>