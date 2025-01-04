<?php
$status = session_status();
if($status == PHP_SESSION_NONE) {
    //There is no active session
    session_start();
}

// check if user is logged in
if (!isset($_SESSION["user_id"])) {
    echo "User not logged in!";
    header("Location: ../index.html");
}

// check if project id is set
if (!isset($_GET["id"])) {
    // redirect to project overview
    header("Location: project-overview.php");
}
// get project id
$project_id = $_GET["id"];


// include_once - prevents redeclaration of functions
include_once "../../server.php";
include_once "../other/project-queries.php";
include_once "../other/status-icon.php";
include_once "../components/sidebar-component.php";

// connect to db
$conn = connect();

// get project data
$project_data = getProjectData($conn, $project_id);
if ($project_data == null) {
    header("Location: project-overview.php");
}
$is_team_leader = isTeamLeader($conn, $_SESSION["user_id"], $project_id);

// determine project user role
$isProjectManager = ($is_team_leader == "1") || ($_SESSION["user_type"] == "Manager");
$project_user_role = ($isProjectManager) ? "Team Leader" : "Member";

// handle post method if necessary
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // for manager actions only
    if ($isProjectManager) {

        /* Removing Project */
        if (isset($_POST["delete-project-id"])) {
            $sql = "DELETE FROM projects WHERE ProjectID = ".$project_id;
            $result = $conn->query($sql);
            if ($result !== true) {
                echo "Error removing project!";
            }
            header("Location: project-overview.php");
        }

        /* Updating Project Details */
        if (isset($_POST["project-name"])) {
            // update project details
            $result = updateProject($conn, $project_id, $_POST["project-name"],
                $_POST["project-deadline"], $_POST["project-status"], $_POST["project-colour"], $_POST["project-description"]);

            if ($result !== true) {
                echo "Error updating project details!";
            }
        }

        /* Moving Task to Different Member */
        if (isset($_POST["move-task-id"])) {
            $task_id = $_POST["move-task-id"];
            $member_id = $_POST["project-member-id"];

            // update task member id
            $sql = "UPDATE usertasks SET UserID = ".$member_id." WHERE TaskID = ".$task_id;
            $result = $conn->query($sql);
            if ($result !== true) {
                echo "Error moving task!";
            }
        }
    }

    // redirect to same page - prevents form resubmission
    header("Location: project-details.php?id=".$project_id);
}

// check if user is a member of the project - prevents user changing url to access other projects
$user_projects = getUserProjects($conn, $_SESSION["user_id"]);
if (!in_array($project_id, array_column($user_projects, "ProjectID"))) {
    echo "<h1>User not a member of this project!</h1>";
    // maybe show error page?
    exit();
}

// get task and member data
$project_task_count_data = getProjectTaskCountData($conn, $project_id);
$project_members = getMemberData($conn, $project_id, true);



// close db connection
close($conn);
?>

<!DOCTYPE html>
<html lang="en" data-theme="light" data-project-id="<?=$project_id?>" data-user-id="<?=$_SESSION["user_id"]?>" data-is-manager="<?=($isProjectManager) ? 1 : 0?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?=$project_data["ProjectName"]?> - Details</title>

    <!-- CSS -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../../css/main.css">
    <link rel="stylesheet" href="../../css/dash.css">
    <link rel="stylesheet" href="../../css/modal-form-style.css">
    <link rel="stylesheet" href="../../css/overview.css">
    <link rel="stylesheet" href="../../css/project-details.css">
    <link rel="stylesheet" href="../../css/status-icons.css">
    <link rel="stylesheet" href="../../css/to-do-list-style.css">


    <!-- javascript -->
    <!-- jQuery library --><script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <!-- Chart.js --><script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>
    <script src="../../js/sidebar.js" defer></script>
    <script src="../../js/to-do-list.js" defer></script>
    <script src="../../js/project-details.js" defer></script>
    <script src="../../js/modal.js" defer></script>
</head>
<body>
<div class="page-container">
    <?=getSidebarComponent("overview", $_SESSION["username"], "../../images/office2.jpeg");?>

    <main class="main-content">
        <div class="central">
            <header style="margin-bottom: 2rem">
                <h1 class="breadcrumb"><a href="./project-overview.php">Projects Overview</a> > <b>Project Details</b></h1>


                <?php
                // only project managers can edit project details
                if ($isProjectManager) {
                    echo '<button class="button open-modal" data-target-id="edit-project-modal">Edit Project Details</button>';

                    if ($project_data["Status"] == "Complete" || $project_data["Status"] == "Not Started") {
                        echo '<button class="button open-modal" data-target-id="confirm-delete-project">Delete Project</button>';
                    }
                }
                ?>

            </header>

            <section class="project-details burger-layout" style="--project-colour: <?=$project_data["Colour"]?>">
                <!-- Project Header -->
                <header class="row">
                    <!-- Project Name -->
                    <h2><?=$project_data["ProjectName"]?></h2>

                    <!-- Project Deadline -->
                    <div>
                        <p>Deadline: <b><?= DateTime::createFromFormat("Y-m-d", $project_data["DueDate"])->format("d-m-Y")?></b></p>
                    </div>

                    <!-- Project Status -->
                    <div class="status-section">
                        <p>Status:</p>
                        <i class="bx <?=getStatusIconClass($project_data["Status"])?>"></i>
                    </div>
                </header>

                <!-- Project Description -->
                <section>
                    <h3>Description:</h3>
                    <p style="white-space: pre-wrap;"><?=$project_data["Description"]?></p>
                </section>

                <!-- Project Overall Progress -->
                <section>
                    <h3>Overall Progress:</h3>
                    <div class="project-progress">
                        <progress value="<?=$project_task_count_data["ProgressPercent"]?>" max="100" class="project-progress-bar"></progress>
                        <p><?=$project_task_count_data["ProgressPercent"]?>% (<?=$project_task_count_data["ProgressFraction"]?>) Complete</p>
                    </div>

                    <!-- Project Task Overview Charts -->
                    <div class="chart-container">
                        <!-- Bar & Pie Charts -->
                        <div id="columnChartContainer" style="height: 450px; width: 45%; min-width: 500px;"></div>
                        <div id="pieChartContainer" style="height: 450px; width: 45%; min-width: 500px;"></div>
                    </div>
                </section>

                <!-- Project Members -->
                <section class="list-container">
                    <header>
                        <h3>
                            <?php if ($isProjectManager) {
                                echo "Manage Project Members:";
                            }
                            else {
                                echo "Project Members:";
                            }?>
                        </h3>
                    </header>

                    <ul class="employee-list" id="project-members">
                        <?php
                        foreach ($project_members as $id => $member_data) {
                            echo '
                                <li class="burger-layout employee-card" style="--employee-colour: '."royalblue".'">
                                    <h4>'.(($id == $_SESSION["user_id"]) ? "You" : $member_data["Username"]).'</h4>
        
                                    <div class="employee-details">
                                        <div class="profile">
                                            <img src="../../images/blank-user.png" alt="Employee Profile Image">
                                            <div class="role">
                                                <i class="bx bx-user-circle"></i>
                                                <b>'.$project_user_role.'</b>
                                            </div>
                                        </div>
        
                                        <div class="progress">
                                            <div>
                                                <progress value="'.$member_data["TaskCountData"]["ProgressPercent"].'" max="100" class="project-progress-bar"></progress>
                                                <p><b>'.$member_data["TaskCountData"]["ProgressFraction"].'</b> Tasks Complete</p>
                                            </div>
                                            <div><i class="bx bx-time-five"></i> <p><b>'.$member_data["TaskCountData"]["TotalIncompleteDuration"].' hrs</b> of Incomplete Tasks</p></div>
                                            
                                            '.
                                            (($id == $_SESSION["user_id"] || $isProjectManager) ?
                                            '
                                            <button class="button view-tasks open-modal" data-target-id="project-member-tasks-modal" data-member-id="'.$id.'" title="Click to view Member\'s Tasks!">
                                                '.(($id == $_SESSION["user_id"]) ? "View Your Tasks" : "View Tasks").'
                                            </button>'
                                            : '') .'
                                        </div>
                                    </div>
                                </li>';
                        }
                        ?>
                    </ul>
                </section>
            </section>

            <!-- Viewing Chosen Member Tasks -->
            <div class="modal-background" data-hidden="true">
                <div class="modal" id="project-member-tasks-modal">
                    <header class="row-container">
                        <h3>Member Tasks View</h3>
                        <button class="button close-modal"><i class="bx bx-x"></i></button>
                    </header>

                    <ul class="employee-list" id="employee-list-modal">
                        <!-- chosen project member -->
                    </ul>

                    <!-- Employee Tasks -->
                    <!-- other members' tasks loaded via ajax request - results cached until page reload -->
                    <section class="column-container" style="gap: 3rem">
                        <header class="row-container">
                            <h3>Project Tasks:</h3>

                            <!-- Buttons -->
                            <div class="row-container" id="task-header-buttons">
                                <div class="toggle-edit">
                                    <input type="checkbox" id="edit-project-tasks" class="row-container toggle-class" data-target-id="to-do-list" data-toggle-class="editing">
                                    <label for="edit-project-tasks" class="button"><i class="bx bx-edit"></i> Project Tasks</label>
                                </div>

                                <?php
                                if ($isProjectManager) {
                                    echo "<button type='button' class='right button open-modal' 
                                    data-target-id='new-project-task-form'>+ New Project Task</button>";
                                }
                                ?>
                            </div>
                        </header>

                        <!-- Starts Empty - AJAX post request when 'view tasks' clicked -->
                        <ul id="to-do-list" class="task-list">

                        </ul>
                    </section>
                </div>
            </div>


            <!-- PROJECT RELATED MODALS -->
            <!-- Edit Project Details -->
            <div class="modal-background" data-hidden="true">
                <form action="project-details.php?id=<?=$project_id?>" method="post" class="modal modal-form" id="edit-project-modal">
                    <header>
                        <h3>Edit Project Details:</h3>
                        <button type="button" class="close-modal"><i class="bx bx-x"></i></button>
                    </header>

                    <!-- Project Name -->
                    <label>
                        <span>Project Name:</span>
                        <input type="text" name="project-name" value="<?=$project_data["ProjectName"]?>">
                    </label>

                    <!-- Project Colour -->
                    <label>Task Title Colour:<br>
                        <select name="project-colour">
                            <option value="royalblue"
                                <?php if (strtolower($project_data["Colour"]) == "royalblue") {
                                    echo "selected";
                                } ?>>Blue
                            </option>
                            <option value="#2fa90e"
                                <?php if (strtolower($project_data["Colour"]) == "#2fa90e") {
                                    echo "selected";
                                } ?>>Green
                            </option>
                            <option value="#c03a00"
                                <?php if (strtolower($project_data["Colour"]) == "#c03a00") {
                                    echo "selected";
                                } ?>>Red
                            </option>
                            <option value="#9963d9"
                                <?php if (strtolower($project_data["Colour"]) == "#9963d9") {
                                    echo "selected";
                                }
                                ?>>Purple
                            </option>
                        </select>
                    </label>

                    <!-- Project Deadline -->
                    <label>
                        <span>Project Deadline:</span>
                        <!-- Date needs to be reversed to set the value -->
                        <input type="date" name="project-deadline" value="<?=$project_data["DueDate"]?>">
                    </label>

                    <!-- Project Status -->
                    <label>
                        <span>Project Status:</span>
                        <select name="project-status">
                            <option value="Ongoing" <?php if (strtolower($project_data["Status"]) == "ongoing") {echo "selected";}?>>Ongoing</option>
                            <option value="Paused" <?php if (strtolower($project_data["Status"]) == "paused") {echo "selected";}?>>Paused</option>
                            <option value="Not Started" <?php if (strtolower($project_data["Status"]) == "not started") {echo "selected";}?>>Not Started</option>
                            <option value="Overdue" <?php if (strtolower($project_data["Status"]) == "overdue") {echo "selected";}?>>Overdue</option>
                            <option value="Complete" <?php if (strtolower($project_data["Status"]) == "complete") {echo "selected";}?>>Complete</option>
                        </select>
                    </label>

                    <!-- Project Description -->
                    <label>
                        <span>Project Description:</span>
                        <textarea name="project-description" cols="40" rows="10"><?=$project_data["Description"]?></textarea>
                    </label>

                    <button type="submit" class="button" style="--bg-colour: var(--bg-1);">Save Changes</button>
                </form>
            </div>


            <!-- Deletion of Project -->
            <div class="modal-background" data-hidden="true">
                <div id="confirm-delete-project" class="modal">
                    <header>
                        <h3>Delete this Project?</h3>
                    </header>

                    <div class="row-container">
                        <!-- Remove Project -->
                        <form action="project-details.php?id=<?=$project_id?>" method="post" class="modal-form">
                            <input type="hidden" name="delete-project-id" id="delete-project-id" value="<?=$project_id?>">
                            <button type="submit" class="button">Delete</button>
                        </form>
                        <button type="button" class="button close-modal">Cancel</button>
                    </div>
                </div>
            </div>


            <!-- TASK RELATED MODALS -->
            <!-- new project task form - starts off hidden -->
            <div id="form-background" class="modal-background" data-hidden="true">
                <form id="new-project-task-form" class="modal modal-form" action="to-do-list.php" method="post">
                    <!-- Way to get back to current page (as otherwise redirected to to-do-list) -->
                    <input type="hidden" name="redirect" value="project-details.php?id=<?=$project_id?>">

                    <input type="hidden" name="project-task-user-id" id="project-task-user-id" value="">

                    <header>
                        <h3>Add New Task</h3>
                        <button type="button" class="close-modal"><i class="bx bx-x"></i></button>
                    </header>

                    <!-- title -->
                    <label for="project-task-title-input">Task Title<br>
                        <input type="text" name="project-task-title" id="project-task-title-input" required>
                    </label>

                    <!-- color -->
                    <label for="project-task-color-input">Task Title Color<br>
                        <select name="project-task-color" id="project-task-color-input">
                            <option value="royalblue">Blue</option>
                            <option value="#2fa90e">Green</option>
                            <option value="#c03a00">Red</option>
                            <option value="#9963d9">Purple</option>
                        </select>
                    </label>

                    <!-- relevant project -->
                    <input type="hidden" name="project-task-project" value="<?=$project_id?>">

                    <!-- due date -->
                    <label>Due Date<br>
                        <input type="date" name="project-task-duedate" id="project-task-duedate-input">
                    </label>

                    <!-- time to complete -->
                    <label for="project-time-to-complete">Time to Complete (hrs)<br>
                        <input type="number" name="project-task-time" id="project-time-to-complete">
                    </label>

                    <!-- description -->
                    <label for="project-task-desc-input">Task Description<br>
                        <textarea name="project-task-desc" id="project-task-desc-input" cols="40" rows="10"></textarea>
                    </label>

                    <button type="submit">Add Task</button>
                </form>
            </div>

            <!-- Edit task form -->
            <div class="modal-background" data-hidden="true">
                <form action="to-do-list.php" method="post" class="modal modal-form" id="edit-task-form">
                    <!-- Way to get back to current page (as otherwise redirected to to-do-list) -->
                    <input type="hidden" name="redirect" value="project-details.php?id=<?=$project_id?>">

                    <header>
                        <h3>Edit Task</h3>
                        <button type="button" class="close-modal"><i class="bx bx-x"></i></button>
                    </header>

                    <label>
                        <input type="hidden" name="task-id" id="edited-task-id" value="">
                    </label>

                    <!-- Task title/Name -->
                    <label>
                        <span>Task Name:</span>
                        <input type="text" name="edited-task-name" id="edited-task-name" value="" required>
                    </label>

                    <!-- Task Colour -->
                    <label>
                        <span>Task Colour:</span>
                        <select name="edited-task-colour" id="edited-task-colour">
                            <option value="royalblue">Blue</option>
                            <option value="#2fa90e">Green</option>
                            <option value="#c03a00">Red</option>
                            <option value="#9963d9">Purple</option>
                        </select>
                    </label>

                    <!-- Task Deadline -->
                    <label>
                        <span>Task Deadline:</span>
                        <!-- Date needs to be reversed to set the value -->
                        <input type="date" name="edited-task-deadline" id="edited-task-deadline" value="" required>
                    </label>

                    <!-- Task Duration -->
                    <label>
                        <span>Est. Task Duration (hrs):</span>
                        <input type="number" name="edited-task-duration" id="edited-task-duration" value="">
                    </label>

                    <!-- Task Status -->
                    <label>
                        <span>Task Status:</span>
                        <select name="edited-task-status" id="edited-task-status">
                            <option value="Ongoing">Ongoing</option>
                            <option value="Paused">Paused</option>
                            <option value="Not Started">Not Started</option>
                            <option value="Overdue">Overdue</option>
                            <option value="Complete">Complete</option>
                        </select>
                    </label>

                    <!-- Task Description -->
                    <label>
                        <span>Task Description:</span>
                        <textarea name="edited-task-description" cols="40" rows="10" id="edited-task-description"></textarea>
                    </label>

                    <button type="submit" class="button">Save Changes</button>
                </form>
            </div>

            <!-- Confirm Removal of Project Task -->
            <!-- JS copies task id into value when modal shown-->
            <div class="modal-background" data-hidden="true">
                <div id="confirm-delete-task" class="modal">
                    <header>
                        <h3>Delete Task?</h3>
                    </header>

                    <div class="row-container">
                        <!-- Remove Task -->
                        <form action="to-do-list.php" method="post" class="modal-form">
                            <input type="hidden" name="redirect" value="project-details.php?id=<?=$project_id?>">
                            <input type="hidden" name="delete-task-id" id="delete-task-id" value="">
                            <button type="submit" class="button">Delete</button>
                        </form>
                        <button type="button" class="button close-modal">Cancel</button>
                    </div>
                </div>
            </div>

            <?php
            // only project managers can move tasks
            if ($isProjectManager) {
                echo '            
                <!-- Move task to different project member -->
                <div class="modal-background" data-hidden="true">
                    <form action="project-details.php?id='.$project_id.'" method="post" class="modal modal-form" id="move-task-form">
                        <header>
                            <h3>Move Employee\'s Task</h3>
                            <button type="button" class="close-modal"><i class="bx bx-x"></i></button>
                        </header>
    
                        <input type="hidden" name="move-task-id" id="move-task-id" value="">
    
                        <!-- List of project members\' usernames -->
                        <label>
                            <span>Move to Project Member: </span>
                            <select name="project-member-id" id="project-member-id">';
                foreach ($project_members as $id => $member_data) {
                    echo '      <option value="'.$id.'">'.$member_data["Username"].'</option>';
                }
                echo '
                            </select>
                        </label>
                        
                        <button type="submit" class="button" style="--bg-colour: var(--bg-1);">Move Task</button>
                    </form>
                </div>';
            }
            ?>

        </div>
    </main>
</div>
</body>
</html>


