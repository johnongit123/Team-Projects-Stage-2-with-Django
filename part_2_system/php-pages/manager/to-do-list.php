<?php
$status = session_status();
if($status == PHP_SESSION_NONE){
    //There is no active session
    session_start();
}
include "../../server.php";



include_once "../components/sidebar-component.php";
include_once "../other/status-icon.php";

$conn = connect();
if(!authenticate("Manager")){
    header("Location: ../../index.php");
}


$userID = (int)$_SESSION['user_id'];

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['private-task-title'])){
    $taskTitle = $_POST['private-task-title'];
    $taskDescription = $_POST['private-task-desc'];
    $taskDueDate = $_POST['private-task-duedate'];
    $timeToComplete = $_POST['private-task-time'];
    $taskColour = $_POST['private-task-color'];
    addPrivateTask($conn, $userID, $taskTitle, $taskDescription, $taskDueDate, $timeToComplete, $taskColour);
    
}
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['project-task-title'])){
    $taskTitle = $_POST['project-task-title'];
    $taskDescription = $_POST['project-task-desc'];
    $taskDueDate = $_POST['project-task-duedate'];
    $timeToComplete = $_POST['project-task-time'];
    $taskColour = $_POST['project-task-color'];
    $ProjectID = $_POST['project-task-project'];

    if (isset($_POST["project-task-user-id"])) {
        addProjectTask($conn, $_POST["project-task-user-id"], $taskTitle, $taskDescription, $taskDueDate, $timeToComplete, $taskColour, $ProjectID);
    }
    else {
        addProjectTask($conn, $userID, $taskTitle, $taskDescription, $taskDueDate, $timeToComplete, $taskColour, $ProjectID);
    }

    // redirect to specified page
    if (isset($_POST["redirect"])) {
        header("Location: ".$_POST["redirect"]);
    }
}
if(isset($_POST['delete-task-id'])){
    $taskID = $_POST['delete-task-id'];
    
    removeTask($conn, $taskID);
    //fix delete function

    // redirect to specified page
    if (isset($_POST["redirect"])) {
        header("Location: ".$_POST["redirect"]);
    }
}

if (isset($_POST['edited-task-name'])) {
    $task_name = $_POST['edited-task-name'];
    $task_colour = $_POST['edited-task-colour'];
    $task_deadline = $_POST['edited-task-deadline'];
    $task_duration = $_POST['edited-task-duration'];
    $task_status = $_POST['edited-task-status'];
    $task_description = $_POST['edited-task-description'];

    $sql = "UPDATE `tasks` 
            SET 
                TaskName = '".$task_name."', 
                Colour = '".$task_colour."', 
                DueDate = '".$task_deadline."', 
                TaskDuration = '".$task_duration."',
                TaskStatus = '".$task_status."', 
                TaskDescription = '".$task_description."'
            WHERE 
                TaskID = ".$_POST['task-id'];
    $result = $conn->query($sql);
    if ($result !== true) {
        echo "Error updating task!";
    }

    // redirect to specified page
    if (isset($_POST["redirect"])) {
        header("Location: ".$_POST["redirect"]);
    }
}

function formatDate($date)  {
    $date = new DateTime($date);
    return $date->format('d-m-Y');
}

function getPrivateTasks($conn, $userID){
    $sql = "SELECT * 
            FROM `tasks`, `usertasks`
            WHERE tasks.TaskID = usertasks.TaskID AND UserID = $userID AND IsPrivate = 1";
    
    $result = $conn->query($sql);

    return $result->fetch_all(MYSQLI_ASSOC);
}


function addPrivateTask($conn, $userID, $taskTitle, $taskDescription, $taskDueDate, $timeToComplete, $taskColour){
    $status = 'Not Started';
    $sql = "INSERT INTO `tasks` VALUES (NULL, ?, ?, ?, ?, 1, ?, ?)";
    $stmt = $conn->prepare($sql);

    $stmt->bind_param("ssssis", $taskTitle, $taskDescription, $status, $taskDueDate, $timeToComplete, $taskColour);
    $stmt->execute();

    $lastInsertedID = $conn->insert_id;
    $stmt->close();
    

    $sql2 = "INSERT INTO `usertasks`
             VALUES ($userID, $lastInsertedID)";
    $conn->query($sql2);

}

function removeTask($conn, $TaskID){
    $sql = "DELETE FROM usertasks
            WHERE TaskID = ".$TaskID;
    $conn->query($sql);
    
    $sql1 = "DELETE FROM tasks 
             WHERE TaskID = ".$TaskID;
    $conn->query($sql1);

}


function getProjectTasks($conn, $userID){
    $sql = "SELECT *
            FROM `tasks`, `usertasks`, `taskprojects`
            WHERE tasks.TaskID = usertasks.TaskID AND tasks.TaskID = taskprojects.TaskID AND usertasks.UserID = $userID AND tasks.IsPrivate = 0";
             
    $result = $conn->query($sql);

    return $result->fetch_all(MYSQLI_ASSOC); // MYSQLI_ASSOC -> $row[column name] instead of $row[index]
}

function addProjectTask($conn, $userID, $TaskTitle, $TaskDescription, $taskDueDate, $TimeToComplete, $TaskColour, $ProjectID){
    $status = "Not Started";
    $sql = "INSERT INTO `tasks` VALUES (NULL, ?, ?, ?, ?, 0, ?, ?)";
    $stmt = $conn->prepare($sql);

    $stmt->bind_param("ssssis", $TaskTitle, $TaskDescription, $status, $taskDueDate, $TimeToComplete, $TaskColour);
    $stmt->execute();

    $lastInsertedID = $conn->insert_id;
    $stmt->close();

    $sql2 = "INSERT INTO `taskprojects`
             VALUES ($lastInsertedID, $ProjectID)";
    $conn->query($sql2);

    $sql3 = "INSERT INTO `usertasks`
             VALUES ($userID, $lastInsertedID)";
    $conn->query($sql3);

}

function getProjects($conn, $userID){
    $sql = "SELECT *
            FROM `projects` p
            INNER JOIN userprojects up ON p.ProjectID = up.ProjectID
            WHERE up.UserID = ".$userID;
    
    $result = $conn->query($sql);

    return $result->fetch_all();
}
?>

<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-do List</title>

    <!-- css -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../../css/main.css">
    <link rel="stylesheet" href="../../css/dash.css">
    <link rel="stylesheet" href="../../css/status-icons.css">
    <link rel="stylesheet" href="../../css/to-do-list-style.css">
    <link rel="stylesheet" href="../../css/modal-form-style.css">

    <!-- javascript -->
    <!-- jQuery library --><script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="../../js/to-do-list.js" defer></script>
    <script src="../../js/sidebar.js" defer></script>
    <script src="../../js/modal.js" defer></script>
</head>
<body>

<div class="page-container">
    <?=getSidebarComponent("to-do-list", $_SESSION["username"], "../../images/office2.jpeg");?>

    <main class="main-content">
        <section id="to-do-list-section" class="column-container central">
            <header>
                <h1>To-Do List</h1>
            </header>


            <div class="task-list-container">

                <!-- Project Tasks Section -->
                <section class="background column-container">
                    <header class="row-container">
                        <h2>Project Tasks</h2>

                        <!-- Buttons -->
                        <div class="row-container">
                            <div class="project-filter">
                                <label for="switch-project" >Switch Project</label>
                                <select id="switch-project" class="button">
                                    <option value="0">All Projects</option>
                                    <?php
                                    foreach(getProjects($conn, $userID) as $rowindex => $row){
                                        echo "<option value='".$row[0]."'>".$row[1]."</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="toggle-edit">
                                <input type="checkbox" id="edit-project-tasks" class="row-container toggle-class" data-target-id="to-do-list" data-toggle-class="editing">
                                <label for="edit-project-tasks" class="button"><i class="bx bx-edit"></i> Project Tasks</label>
                            </div>

                            <?php
                            $sql = "SELECT *
                                FROM `userprojects`
                                WHERE UserID = ".$userID;
                            $result = $conn->query($sql);
                            $user_projects = $result->fetch_all(MYSQLI_ASSOC);

                            if ($result->num_rows > 0) {
                                foreach ($user_projects as $user) {
                                    if ($user["IsTeamLeader"] === '1') {
                                        echo "<button type='button' class='right button open-modal new-project-task-button' 
                                            data-target-id='new-project-task-form' data-project-id='".$user["ProjectID"]."'>+ New Project Task</button>";
                                    }
                                }
                            }
                            ?>
                        </div>
                    </header>

                    <ul id="to-do-list" class="task-list">
                        <?php
                            foreach(getProjectTasks($conn, $userID) as $task){
                                $complete = "";
                                if($task["TaskStatus"] === "Complete"){
                                    $complete = "complete";
                                }
                                echo '
                                <li class="burger-layout task '.$complete.'" data-project-id="'.$task["ProjectID"].'">
                                    <div class="row-container" style="background-color: '.$task["Colour"].'">                                    
                                        <h3>'.$task["TaskName"].'</h3>
                                        
                                        <!-- Edit Button -->
                                        <!-- JS Copies data accross to edit form -->
                                        <button type="button" class="right button task-option-button edit-task open-modal"
                                        data-target-id="edit-task-form"
                                        data-task-id="'.$task["TaskID"].'"
                                        data-task-colour="'.$task["Colour"].'"
                                        data-task-name="'.$task["TaskName"].'"
                                        data-task-description="'.$task["TaskDescription"].'"
                                        data-task-deadline="'.$task["DueDate"].'"
                                        data-task-duration="'.$task["TaskDuration"].'"
                                        data-task-status="'.$task["TaskStatus"].'"
                                        ><i class="bx bx-edit"></i></button>
                                        
                                        <!-- X button -->
                                        <button type="button" class="red-circle-button open-modal" 
                                        data-target-id="confirm-delete-task"
                                        data-task-id="'.$task["TaskID"].'"
                                        ><i class="bx bx-x"></i></button> <!-- 🞮 -->
                                    </div>
                                    <p class="description">'.$task["TaskDescription"].'</p>
                    
                                    <div class="row">
                                        <div> <p>Deadline</p> <b>'.formatDate($task["DueDate"]).'</b> </div>
                                        <div> <p>Duration</p> <b>'.$task["TaskDuration"].' hrs</b> </div>
                                        <div> <p>Status</p> <i class="bx '.getStatusIconClass($task["TaskStatus"]).'"></i> </div>
                                    </div>
                                </li>
                                ';
                            }
                        ?>
                    </ul>
                </section>


                <!-- Private Tasks Section -->
                <section class="background">
                    <header class="row-container">
                        <h2>Private Tasks</h2>

                        <!-- Buttons -->
                        <div class="row-container">
                            <div class="toggle-edit">
                                <input type="checkbox" id="edit-tasks" class="row-container toggle-class" data-target-id="to-do-list1" data-toggle-class="editing">
                                <label for="edit-tasks" class="button"><i class="bx bx-edit"></i> Private Tasks</label>
                            </div>

                            <button type='button' class='right button open-modal' data-target-id='new-private-task-form'>
                                + New Private Task
                            </button>
                        </div>
                    </header>
                    <br>
                    <ul id="to-do-list1"  class="task-list">
                        <?php
                        foreach(getPrivateTasks($conn, $userID) as $task){
                            $complete = "";
                            if($task["TaskStatus"] === "Complete"){
                                $complete = "complete";
                            }
                            echo '
                                <li class="burger-layout task '.$complete.'">
                                    <div class="row-container" style="background-color: '.$task["Colour"].'">                                    
                                        <h3>'.$task["TaskName"].'</h3>
                                        
                                        <!-- Edit Button -->
                                        <!-- JS Copies data accross to edit form -->
                                        <button type="button" class="right button task-option-button edit-task open-modal"
                                        data-target-id="edit-task-form"
                                        data-task-id="'.$task["TaskID"].'"
                                        data-task-colour="'.$task["Colour"].'"
                                        data-task-name="'.$task["TaskName"].'"
                                        data-task-description="'.$task["TaskDescription"].'"
                                        data-task-deadline="'.$task["DueDate"].'"
                                        data-task-duration="'.$task["TaskDuration"].'"
                                        data-task-status="'.$task["TaskStatus"].'"
                                        ><i class="bx bx-edit"></i></button>
                                        
                                        <!-- X button -->
                                        <button type="button" class="red-circle-button open-modal" 
                                        data-target-id="confirm-delete-task"
                                        data-task-id="'.$task["TaskID"].'"
                                        ><i class="bx bx-x"></i></button> <!-- 🞮 -->
                                    </div>
                                    <p class="description">'.$task["TaskDescription"].'</p>
                    
                                    <div class="row">
                                        <div> <p>Deadline</p> <b>'.formatDate($task["DueDate"]).'</b> </div>
                                        <div> <p>Duration</p> <b>'.$task["TaskDuration"].' hrs</b> </div>
                                        <div> <p>Status</p> <i class="bx '.getStatusIconClass($task["TaskStatus"]).'"></i> </div>
                                    </div>
                                </li>
                                ';
                            }
                        ?>
                    </ul>
                </section>
            </div>

            <!-- new project task form - starts off hidden -->
            <div id="form-background" class="modal-background" data-hidden="true">
                <form id="new-project-task-form" class="modal modal-form" action="to-do-list.php" method="post">
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
                    <label for="private-task-project-input">Associated Project<br>
                        <select name="project-task-project" id="private-task-project-input" required>
                            <option value="1">Project 1</option>
                            <option value="2">Project 2</option>
                        </select>
                    </label>

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

            <!-- new private task form - starts off hidden -->
            <div id="form-background1" class="modal-background" data-hidden="true">
                <form id="new-private-task-form" class="modal modal-form" action="to-do-list.php" method="post">
                    <header>
                        <h3>Add New Task</h3>
                        <button type="button" class="close-modal"><i class="bx bx-x"></i></button>
                    </header>

                    <!-- title -->
                    <label for="private-task-title-input">Task Title<br>
                        <input type="text" name="private-task-title" id="private-task-title-input" required>
                    </label>

                    <!-- color -->
                    <label for="private-task-color-input">Task Title Color<br>
                        <select name="private-task-color" id="private-task-color-input">
                            <option value="royalblue">Blue</option>
                            <option value="#2fa90e">Green</option>
                            <option value="#c03a00">Red</option>
                            <option value="#9963d9">Purple</option>
                        </select>
                    </label>

                    <!-- due date -->
                    <label>Due Date<br>
                        <input type="date" name="private-task-duedate" id="private-task-duedate-input" min="datetime" required>
                    </label>

                    <!-- time to complete -->
                    <label for="private-time-to-complete">Time to Complete (hrs)<br>
                        <input type="number" name="private-task-time" id="private-time-to-complete">
                    </label>

                    <!-- description -->
                    <label for="private-task-desc-input">Task Description<br>
                        <textarea name="private-task-desc" id="private-task-desc-input" cols="40" rows="10"></textarea>
                    </label>

                    <button type="submit">Add Task</button>
                </form>
            </div>

            <!-- Edit task form -->
            <div class="modal-background" data-hidden="true">
                <form action="to-do-list.php" method="post" class="modal modal-form" id="edit-task-form">
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
                            <input type="hidden" name="delete-task-id" id="delete-task-id" value="">
                            <button type="submit" class="button">Delete</button>
                        </form>
                        <button type="button" class="button close-modal">Cancel</button>
                    </div>
                </div>
            </div>

        </section>
    </main>

</div>

</body>
</html>
