<?php
require_once 'session-config.php';

if (isset($_GET['projectID'])) {
    require_once 'dbh.php';
    global $con;

    $projectID = $_GET['projectID'];

    $query = "DELETE FROM project_graph_table WHERE ProjectID = ?";
    $stmt = mysqli_prepare($con, $query);
    $stmt->bind_param('i', $projectID);

    if($stmt->execute()){
        // $_SESSION["success_delete"] = "The graph has been removed successfully!";
        // header("Location: project.php"); REDIRECT TO MAIN PAGE CARRYING ABOVE MESSAGE IN SESSION (CAN BE DISPLAYED ON THAT PAGE)
        die();
    } else {
        // Handle any errors that occur during the deletion process
        echo "Error deleting project: " . $stmt->error;
    }

    $stmt->close();    
}