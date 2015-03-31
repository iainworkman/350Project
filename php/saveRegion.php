<?php
    
$errorMessage = "";
if($_POST["userID"] == null) 
{
    $errorMessage = errorMessage + "The userID was not provided in the request.";
}

if ($_POST["type"] == null)
{
    $errorMessage = errorMEssage + " The type of the region was not provided in the request.";
}

if ($_POST["name"] == null)
{
    $errorMessage = errorMessage + " The name of the region was not provided in the request.";
}

if ($_POST["description"] == null)
{
    $errorMessage = errorMessage + " The description of the region was not provided in the request.";
}

if ($_POST["regionID"] == null)
{
    $errorMessage = errorMessage + " The regionID of the region was not provided in the request.";
}


$dsn = "mysql:host=lovett.usask.ca;dbname=cmpt350_ral362";
$username = "cmpt350_ral362";
$password = "zm6uafeyio";

try {
    $db = new PDO($dsn, $username, $password, array(PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)); 
	
	
	$statement = $db->prepare(
    "SELECT 
    reg_identifier 
    FROM 
    t_regions
	ORDER BY
	reg_identifier DESC
	LIMIT 1
    ;");
    
    $statement->execute();
    $result = $statement->fetchAll();
	
	//there should be only one
	 foreach ($result as $row) {

	 //update the regionID to be the latest one plus one.
		$regionID = $row['reg_identifier'] + 1;
	 }
		
		
    $statement = $db->prepare(
    "INSERT INTO t_regions(reg_identifier, reg_user_email, reg_name, reg_description, reg_type)
    VALUES(
    :regionID,
    :userID,
    :name,
    :description,
    :type);"
    );
    $statement->bindParam(':regionID', $regionID, PDO::PARAM_INT);
    $statement->bindParam(':userID', $_POST['userID'], PDO::PARAM_STR);
    $statement->bindParam(':name', $_POST['name'], PDO::PARAM_STR);
    $statement->bindParam(':description', $_POST['description'], PDO::PARAM_STR);
    $statement->bindPAram(':type', $_POST['type'], PDO::PARAM_STR);
    
    $statement->execute();
    
	echo $regionID;
    }

 catch (PDOException $e) {
    $errorResponse = array("error" => $e->getMessage());
    echo json_encode($errorResponse);
}
?>