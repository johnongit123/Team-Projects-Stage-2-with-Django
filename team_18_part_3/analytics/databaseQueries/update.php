<?php
require_once 'session-config.php';

if (isset($_GET['graphID'])) {
    $graphID = $_GET['graphID'];
    $projectID = $_GET['projectID'];
    $graph_title = $_GET['graph_title'];
    $graph_content = $_GET['graph_content'];
    $graph_type = $_GET['graph_type'];
    $graph_filter = $_GET['graph_filter'];

    try {
        require_once 'dbh.php';
        global $con;

        $errors = [];

        // Validate input if necessary
        if (empty($graphID)) {
            $errors["missing_graphID"] = "Graph ID is required!";
        }

        if (!empty($errors)) {
            // Handle validation errors
            die("Validation errors occurred: " . json_encode($errors));
        } else {
            // Perform the SQL UPDATE operation
            $query = "UPDATE project_graph_table 
                      SET ProjectID=?, graph_title=?, graph_content=?, graph_type=?, graph_filter=? 
                      WHERE GraphID=?";
            $stmt = $con->prepare($query);
            $stmt->bind_param('issssi', $projectID, $graph_title, $graph_content, $graph_type, $graph_filter, $graphID);
            
            if ($stmt->execute()) {
                // Update successful
                die("Graph updated successfully!");
            } else {
                // Handle SQL error
                die("Error updating graph: " . $stmt->error);
            }
        }
    } catch (Exception $e) {
        // Handle any other exceptions
        die("Query failed: " . $e->getMessage());
    }
}
?>
