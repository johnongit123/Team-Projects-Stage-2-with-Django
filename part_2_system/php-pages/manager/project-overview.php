<?php
$status = session_status();
if($status == PHP_SESSION_NONE) {
    //There is no active session
    session_start();
}

// include_once - prevents redeclaration of functions
include_once "../../server.php";
include_once "../other/project-queries.php";
include_once "../other/status-icon.php";
include_once "../components/sidebar-component.php";

// get local database data
$conn = connect();
if(!authenticate("Manager")){
    header("Location: ../../index.php");
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    /* New Project */
    if (isset($_POST["project-name"])) {
        $sql = "
            INSERT INTO projects (ProjectName, Description, DueDate, Status, Colour)
            VALUES ('".$_POST["project-name"]."','".$_POST["project-description"]."','".$_POST["project-deadline"]."', 'Not Started', '".$_POST["project-colour"]."');";
        $result = $conn->query($sql);
        if ($result !== true) {
            echo "Error creating new project!";
        }
    }
}


// get projects associated with user
$managing_projects = [];
$member_projects = [];
$all_projects = [];
foreach (getUserProjects($conn, $_SESSION["user_id"], true) as $id => $project) {
    // add project task count data
    $project["TaskCountData"] = getProjectTaskCountData($conn, $project["ProjectID"]);

    // add project to list based on user role
    if ($project["IsTeamLeader"] == "1") {
        $managing_projects[$id] = $project;
    }
    else {
        $member_projects[$id] = $project;
    }

}
foreach (getAllProjects($conn) as $id => $project) {
    // add project task count data
    $project["TaskCountData"] = getProjectTaskCountData($conn, $project["ProjectID"]);
    $all_projects[$id] = $project;
}


// close the connection
close($conn);

// generate each project's progress card HTML from list of projects associated with user
$managing_projects_html = generateProjectListHTMl($managing_projects);
$member_projects_html = generateProjectListHTMl($member_projects);
$all_projects_html = generateProjectListHTMl($all_projects);
function generateProjectListHTMl($projects) {
    $project_list_html = "";
    foreach ($projects as $id => $project) {
        // data stored with project
        $name = $project["ProjectName"];
        $deadline = $project["DueDate"];
        $status = $project["Status"];
        $colour = $project["Colour"];

        // could be stored & updated along with project or just calculated with database queries
        $progress = $project["TaskCountData"]["ProgressFraction"];
        $progress_percent = $project["TaskCountData"]["ProgressPercent"];

        // create link to project-details page
        $details_link = "./project-details.php?id=" . $id;

        // add project HTML to list
        $project_list_html .=
            '<li class="project-progress-card">
                <a href="'.$details_link.'"  title="Click to view this Project\'s Details!"></a>
                <div class="status">
                    <i class="bx '.getStatusIconClass($status).'"></i>
                </div>
                <div class="project-info">
                    <h3>'. $name .'</h3>
                    <p>Deadline: '.  DateTime::createFromFormat("Y-m-d", $deadline)->format("d-m-Y") .'</p>
                </div>
                <div class="project-progress">
                    <progress value="'. $progress_percent .'" max="100" 
                    class="project-progress-bar" style="--_inner-element-color:'.$colour.'"></progress>
                    <p>'. $progress_percent .'% ('. $progress .') Complete</p>
                </div>
            </li>';
    }
    return $project_list_html;
}
?>

<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Overview</title>

    <!-- css -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../../css/main.css">
    <link rel="stylesheet" href="../../css/dash.css">
    <link rel="stylesheet" href="../../css/overview.css">
    <link rel="stylesheet" href="../../css/modal-form-style.css">
    <link rel="stylesheet" href="../../css/status-icons.css">

    <!-- javascript -->
    <!-- jQuery library --><script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="../../js/sidebar.js" defer></script>
    <script src="../../js/modal.js" defer></script>
</head>
<body>

<div class="page-container">
    <!-- Sidebar -->
    <?=getSidebarComponent("overview", $_SESSION["username"], "../../images/office2.jpeg");?>

    <!-- Page Content -->
    <main class="main-content">
        <div class="overview central">
            <!-- Header - title + help button -->
            <header>
                <h1>Projects Overview</h1>

                <button class="button open-modal" data-target-id="new-project-modal">+ New Project</button>

                <button class="help-icon status open-modal" data-target-id="modal-icon-meaning" title="Click for Icon Meaning!">
                    <i class="bx bx-help-circle"></i>
                </button>
            </header>

            <!-- Managing Projects -->
            <?php
            if (!empty($all_projects)) {
                echo '
                <section class="list-container">
                    <header>
                        <h3><i class="bx bxs-crown"></i> Managing Project(s)</h3>
                    </header>
    
                    <ul class="project-list">
                        '.$all_projects_html.'
                    </ul>
                </section>';
            }
            ?>



            <?php

            // Check if there are no projects
            if (empty($all_projects)) {
                echo '
                    <section class="list-container">
                        <p>No  projects found.</p>
                    </section>';
            }


            ?>


        </div>


        <!-- New Project -->
        <div class="modal-background" data-hidden="true">
            <form method="post" class="modal modal-form" id="new-project-modal">
                <header>
                    <h3>Edit Project Details:</h3>
                    <button type="button" class="close-modal"><i class="bx bx-x"></i></button>
                </header>

                <!-- Project Name -->
                <label>
                    <span>Project Name:</span>
                    <input type="text" name="project-name" value="" required>
                </label>

                <!-- Project Colour -->
                <label>Task Title Colour:<br>
                    <select name="project-colour" required>
                        <option value="royalblue">Blue</option>
                        <option value="#2fa90e">Green</option>
                        <option value="#c03a00">Red</option>
                        <option value="#9963d9">Purple</option>
                    </select>
                </label>

                <!-- Project Deadline -->
                <label>
                    <span>Project Deadline:</span>
                    <input type="date" name="project-deadline" value="" required>
                </label>

                <!-- Project Description -->
                <label>
                    <span>Project Description:</span>
                    <textarea name="project-description" cols="40" rows="10" required></textarea>
                </label>

                <button type="submit" class="button" style="--bg-colour: var(--bg-1);">Save Changes</button>
            </form>
        </div>

        <!-- Modal - Icon Meanings -->
        <?php include "../components/icon-help-modal.html";?>
    </main>
</div>
</body>
</html>