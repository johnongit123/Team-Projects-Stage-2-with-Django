<?php
require_once 'session-config.php';

if (isset($_GET['projectID']) && isset($_GET['graph_title']) && isset($_GET['graph_content']) && isset($_GET['graph_type']) && isset($_GET['graph_filter'])) {
    $projectID = $_GET['projectID'];
    $graphID = rand(100, 999);
    $graph_title= $_GET['graph_title'];
    $graph_content= $_GET['graph_content'];
    $graph_type= $_GET['graph_type'];
    $graph_filter= $_GET['graph_filter'];   
    
    try {
        require_once 'dbh.php';
        $errors = [];

        if(empty($projectID) || empty($graph_title) || empty($graph_content)|| empty($graph_type) || empty($graph_filter)){
            $errors["empty_input"] = "Please fill in all fields!";   
        }


        //assigns error message to the session
        if (!empty($errors)){
            //$_SESSION["errors_graphs"] = $errors;
            //header("Location: project.php");
            die();
        } else {

            create_graph($projectID, $graphID, $graph_title, $graph_content, $graph_type, $graph_filter );
            //$_SESSION["success"] = "Graph made has been created successfully!";
            //header("Location: project.php");
            die();
        }
    } catch(Exception $e) {
        die("Query failed: " . $e->getMessage());
    }
}


function create_graph($projectID, $graphID, $graph_title, $graph_content, $graph_type, $graph_filter){
    global $con;

    $query = "INSERT INTO project_graph_table (ProjectID, GraphID, graph_title, graph_content, graph_type, graph_filter) 
    VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $con->prepare($query);
    $stmt->bind_param("iissss", $projectID, $graphID, $graph_title, $graph_content, $graph_type, $graph_filter);
    $stmt->execute();
    $graphID = $stmt->insert_id;
    $stmt->close();
}
?>