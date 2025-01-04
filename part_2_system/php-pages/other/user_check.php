<?php
include ("../../server.php");

$conn = connect();

$username = $_POST['username'];
$email = $_POST['email'];

// Prepare and execute a query to check if the user exists
$sql = "SELECT UserID FROM users WHERE Username = '$username'";
$result = $conn->query($sql);
$userExists = ($result->num_rows > 0);

$sql = "SELECT UserID FROM users WHERE Email = '$email'";
$result = $conn->query($sql);
$emailExists = ($result->num_rows > 0);

// Return JSON response
header('Content-Type: application/json');
echo json_encode(['userExists' => $userExists, 'emailExsits' => $emailExists]); 
?>