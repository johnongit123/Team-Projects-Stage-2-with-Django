<?php
$status = session_status();
if ($status == PHP_SESSION_NONE) {
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
if(!authenticate("User")){
    header("Location: ../../index.php");
}


// get projects associated with user
$managing_projects = [];
$member_projects = [];
foreach (getUserProjects($conn, $_SESSION["user_id"], true) as $id => $project) {
    // add project task count data
    $project["TaskCountData"] = getProjectTaskCountData($conn, $project["ProjectID"]);

    // add project to list based on user role
    if ($project["IsTeamLeader"] == "1") {
        $managing_projects[$id] = $project;
    } else {
        $member_projects[$id] = $project;
    }
}

// close the connection
close($conn);

// generate each project's progress card HTML from list of projects associated with user
$managing_projects_html = generateProjectListHTMl($managing_projects);
$member_projects_html = generateProjectListHTMl($member_projects);
function generateProjectListHTMl($projects)
{
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
                <a href="' . $details_link . '"  title="Click to view this Project\'s Details!"></a>
                <div class="status">
                    <i class="bx ' . getStatusIconClass($status) . '"></i>
                </div>
                <div class="project-info">
                    <h3>' . $name . '</h3>
                    <p>Deadline: ' . DateTime::createFromFormat("Y-m-d", $deadline)->format("d-m-Y") . '</p>
                </div>
                <div class="project-progress">
                    <progress value="' . $progress_percent . '" max="100" 
                    class="project-progress-bar" style="--_inner-element-color:' . $colour . '"></progress>
                    <p>' . $progress_percent . '% (' . $progress . ') Complete</p>
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
    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="../../js/sidebar.js" defer></script>
    <script src="../../js/modal.js" defer></script>
</head>
<body>

<div class="page-container">
    <!-- Sidebar -->
    <?= getSidebarComponent("overview", $_SESSION["username"], "../../images/office2.jpeg"); ?>

    <!-- Page Content -->
    <main class="main-content">
        <div class="overview central">
            <!-- Header - title + help button -->
            <header>
                <h1>Projects Overview</h1>
                <button class="help-icon status open-modal" data-target-id="modal-icon-meaning"
                        title="Click for Icon Meaning!">
                    <i class="bx bx-help-circle"></i>
                </button>
            </header>

            <!-- Managing Projects -->
            <?php
            if (!empty($managing_projects)) {
                echo '
                <section class="list-container">
                    <header>
                        <h3><i class="bx bxs-crown"></i> Managing Project(s)</h3>
                    </header>
    
                    <ul class="project-list">
                        ' . $managing_projects_html . '
                    </ul>
                </section>';
            }
            ?>


            <!-- Membering Projects -->
            <?php
            if (!empty($member_projects)) {
                echo '
                <section class="list-container">
                    <header>
                        <h3><i class="bx bxs-group"></i> Membering Project(s)</h3>
                    </header>
    
                    <ul class="project-list">
                        ' . $member_projects_html . '
                    </ul>
                </section>';
            }
            ?>
            <?php

            // Check if there are no managing projects
            if (empty($managing_projects)) {
                echo '
                    <section class="list-container">
                        <p>No managing projects found.</p>
                    </section>';
            }

            // Check if there are no member projects
            if (empty($member_projects)) {
                echo '
                    <section class="list-container">
                        <p>No member projects found.</p>
                    </section>';
            }
            ?>


        </div>

        <!-- Modal - Icon Meanings -->
        <?php include "../components/icon-help-modal.html"; ?>
    </main>
</div>
</body>
</html>