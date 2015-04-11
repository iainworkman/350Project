<?php
    
if($_GET["userId"] === null) {
    $errorResponse = array("error" => "No user id included in request");
    echo json_encode($errorResponse);
    return; 
}

if($_GET["latitude"] === null) {
    $errorResponse = array("error" => "No latitude included in request");
    echo json_encode($errorResponse);
    return;
}

if($_GET["longitude"] === null) {
    $errorResponse = array("error" => "No longitude included in request");
    echo json_encode($errorResponse);
    return;
}

$dsn = "mysql:host=lovett.usask.ca;dbname=cmpt350_ral362";
$username = "cmpt350_ral362";
$password = "zm6uafeyio";

try {
    $db = new PDO($dsn, $username, $password, array(PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)); 
    $query = $db->prepare(
    "SELECT 
    reg_identifier, reg_name, reg_description, reg_type, reg_user_email 
    FROM 
    t_regions
    WHERE
    (
        (reg_user_email = ?)
        OR
        (reg_type = 'universal')
    )
    AND
    reg_identifier in (
        SELECT reg_identifier 
        FROM t_region_coordinates 
        WHERE 
            ABS(reg_latitude - ?) < 100.0
            AND
            ABS(reg_longitude - ?) < 100.0
    )
    "
    );
    $query->bindParam(1, $_GET["userId"], PDO::PARAM_STR);
    $query->bindParam(2, $_GET["latitude"]);
    $query->bindParam(3, $_GET["longitude"]);
    
    $query->execute();
    $fetch = $query->fetchAll();
    
    $resultArray = array();

    foreach ($fetch as $row) {

        $currentRegion = array(
            "id" => $row['reg_identifier'],
            "name" => $row['reg_name'],
            "description" => $row['reg_description'],
            "type" => $row['reg_type'],
            "owner" => $row['reg_user_email']
        );

        $resultArray[] = $currentRegion;
    }
    $resultsObject = array("regions" => $resultArray);
    echo json_encode($resultsObject);
    
} catch (PDOException $e) {
    $errorResponse = array("error" => $e->getMessage());
    echo json_encode($errorResponse);
}
?>