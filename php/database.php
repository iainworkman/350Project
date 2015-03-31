<?php

/**
 * A PDO database object which is configured to access the site's database
 */
function getDBPDOObject() {
    $dsn = "mysql:host=lovett.usask.ca;cmpt350_ral362";
    $username = "cmpt350_ral362";
    $password = "zm6uafeyio";
    
    return new PDO($dsn, $username, $password, array(PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));    
}


?>