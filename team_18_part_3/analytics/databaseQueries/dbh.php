<?php
$dbhost = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "team18_da_database";

try {
     // Attempt to establish a connection
    $con = mysqli_connect($dbhost,$dbuser,$dbpass,$dbname);
    
    // Check if the connection was successful
    if (!$con){
        throw new Exception("Connection failed: " . mysqli_connect_error());
    }
} catch(Exception $e) {
    // Connection failed, handle the exception
    die("Connection failed: " . $e->getMessage());
}

