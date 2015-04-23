<?php

$regionID = $_POST['regionID'];

if ($regionID == null)
{
	echo "The region id was not provided in the request.";
	return;
}

$dsn = "mysql:host=lovett.usask.ca;dbname=cmpt350_ral362";
$username = "cmpt350_ral362";
$password = "zm6uafeyio";

try {
    $db = new PDO($dsn, $username, $password, array(PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)); 
    $query = $db->prepare("delete from t_regions where reg_identifier = :regionID;");
    $query->bindParam(':regionID', $regionID, PDO::PARAM_INT);
	$query->execute();
	
	$query = $db->prepare("delete from t_region_coordinates where reg_identifier = :regionID;");
    $query->bindParam(':regionID', $regionID, PDO::PARAM_INT);
	$query->execute();    
            
    echo "The region was deleted successfully.";
} catch (PDOException $e) {
    $errorResponse = $e->getMessage();
    echo json_encode($errorResponse);
}



?>