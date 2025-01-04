<?php
$status = session_status();
if($status == PHP_SESSION_NONE) {
    //There is no active session
    session_start();
}


function connect(){
    $dbhost = "localhost";
    $dbuser = "root";
    $dbpass = "";
    $dbname = "project_db";
    $conn = new mysqli($dbhost, $dbuser, $dbpass,$dbname) or die("Connect failed: %s\n". $conn -> error);
    return $conn;
}
function query($conn,$sql){
    $result = $conn->query($sql);
    return $result;
}
function close($conn){
    $conn -> close();
}
function insert_into_db($conn,$table,$columns,$values){
    $sql = "INSERT INTO ".$table." (".$columns.") VALUES (".$values.")";
    $result = $conn->query($sql);
    return $result;
}


function authenticate($level){
    if(!isset($_SESSION["user_id"])){
        return false;
    }else if($_SESSION["user_type"] == $level || $_SESSION["user_type"] == "Manager"){
        return true;
    }
}

?>