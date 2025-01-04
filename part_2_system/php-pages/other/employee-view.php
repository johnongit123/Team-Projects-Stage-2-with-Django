<?php
$status = session_status();
if($status == PHP_SESSION_NONE) {
    //There is no active session
    session_start();
}

// check if user is logged in
if (!isset($_SESSION["user_id"])) {
    echo "User not logged in!";
    header("Location: ../../index.php");
}

// check if project id is set
if (!isset($_GET["id"])) {
    // redirect to project overview
    header("Location: project-overview.php"); // change this  !!!
}
// // get project id
// $project_id = $_GET["id"];


// include_once - prevents redeclaration of functions
include_once "../../server.php";
include_once "../other/project-queries.php";
include_once "../other/status-icon.php";
include_once "../components/sidebar-component.php";

// connect to db
$conn = connect();

$emp_id = $_GET["id"];


function get_employee_data($conn, $emp_id){
    $sql = "SELECT Username, Email, UserType FROM users WHERE UserID = '$emp_id'";

    $result = $conn->query($sql);

    $row = $result->fetch_assoc();
    return array($row["Username"], $row["Email"], $row["UserType"]);
}

list($uname, $email, $utype) = get_employee_data($conn, $emp_id);





?>

<!DOCTYPE html>
<html lang="en" data-theme="light" data-project-id="<?=$project_id?>" data-user-id="<?=$_SESSION["user_id"]?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?=$uname?> - Details</title>

    <!-- CSS -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../../css/main.css">
    <link rel="stylesheet" href="../../css/dash.css">
    <link rel="stylesheet" href="../../css/modal-form-style.css">
    <link rel="stylesheet" href="../../css/overview.css">
    <link rel="stylesheet" href="../../css/project-details.css">
    <link rel="stylesheet" href="../../css/status-icons.css">

    <link rel="stylesheet" href="../../css/to-do-list-section-style.css">
    <link rel="stylesheet" href="../../css/to-do-list-style.css">
    <link rel="stylesheet" href="../../css/employee-view.css">


    <!-- javascript -->
    <!-- jQuery library --><script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="../../js/to-do-list.js" defer></script>
    <script src="../../js/sidebar.js" defer></script>

    <!-- Chart.js -->
    <script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>
    <!-- <script src="../../js/project-details.js" defer></script> -->
    <script src="../../js/modal.js" defer></script>
</head>
<body>
<div class="page-container">
    <?=getSidebarComponent("overview", $_SESSION["username"], "../../images/office2.jpeg");?>

    <main class="main-content">
        <div class="central">
            <header style="margin-bottom: 2rem">
                <h1><a href="./employee-overview.php">Employee Overview</a> > <b>Employee Details</b></h1>


            </header>

            <section class="project-details burger-layout" style="--project-colour: <?=$project_data["Colour"]?>">
                <!-- Employee Header -->
                <header class="row" style="grid-template-columns: auto 1fr auto auto;">
                    <div style="padding: 10px;">
                        <img src="../../images/blank-user.png" alt="" width="100", height="100">
                    </div>

                    <!-- Employee User Name -->
                    <div style="background-color: green;">
                        <h2><?=$uname?></h2>
                    </div>
                    

                    <!-- Employee Email -->
                    <div>
                        <p>Email: <b><?= $email?></b></p>
                    </div>

                    <!-- Employee statue -->
                    <div class="status-section">
                        <p>Status: <b><?= $utype ?></b></p>
                    </div>

                    
                </header>

                <!-- project list -->
                <section id="to-do-list-section">
                    <header>
                        <h2>Projects:</h2>
                    </header>
                    <br>

                    
                    <ul id="project-list" style="list-style-type: none;">
                        <?php

                        $sql = "SELECT * FROM userprojects WHERE UserID = '$emp_id'";
                        $result = $conn->query($sql);
                        while($row = mysqli_fetch_assoc($result)){
                        $id = $row["ProjectID"];
                            $sql2 = "SELECT * FROM projects WHERE ProjectID = '$id'";
                            $project = $conn->query($sql2)->fetch_assoc();
                            ?>
                            <li>
                                <div class="top-section">
                                    <h3><?= $project["ProjectName"] ?></h3>
                                    <progress value="60" max="100" class="project-progress-bar"></progress>
                                    <button type="button" class="edit-option">🞭</button>
                                </div>
                                <div class="bottom-section">
                                    <p>Status: <?= $project["Status"] ?></p>
                                    <p>Start Date: <?= $project["DueDate"] ?></p>
                                    <p>Progress: </p>
                                    <div style="text-align: center;">
                                        <a href="http://localhost/php-pages/manager/project-details.php?id=<?= $row["ProjectID"]?>">
                                            <button >View Project</button>
                                        </a>
                                        
                                    </div>
                                </div>
                                
                            </li>
                        <?php
                        }   

                        
                        ?>
                    </ul>
                </section>

                <!-- Project Description -->
                <section id="to-do-list-section">
                    <header>
                        <h2>Tasks:</h2>
                    </header>
                    <br>

                    <!-- Starts Empty - AJAX post request when 'view tasks' clicked -->
                    <ul id="project-list" style="list-style-type: none;">
                        <?php

                        // used for stats later but saves re querying the database
                        $completed = 0;
                        $ongoing = 0;
                        $not_started = 0;

                        $overdue = 0;
                        $this_week = 0;
                        $next_week = 0;
                        $later = 0;

                        $sql = "SELECT * FROM usertasks WHERE UserID = '$emp_id'";
                        $tasks = $conn->query($sql);
                        while($row = mysqli_fetch_assoc($tasks)){
                            $id = $row["TaskID"];
                            $sql2 = "SELECT * FROM tasks WHERE TaskID = '$id' AND IsPrivate = 0";
                            $task = $conn->query($sql2)->fetch_assoc();

                            if($task["TaskStatus"] == "Ongoing"){
                                $ongoing += 1;
                            }else if($task["TaskStatus"] == "Completed"){
                                $completed += 1;
                            }else{
                                $not_started += 1;
                            }

                            $today = date('w');
                            $weekStart = date('Y-m-d', strtotime("-{$today} days"));
                            $weekEnd = date('Y-m-d', strtotime("+6 days", strtotime($weekStart)));


                            function isDateInCurrentWeek($targetDate) {
                                global $weekStart, $weekEnd;
                            
                                return ($targetDate >= $weekStart) && ($targetDate <= $weekEnd);
                            }

                            function isDateInNextWeek($targetDate) {
                                $nextWeekStart = date('Y-m-d', strtotime('next Monday')); 

                                $nextWeekEnd = date('Y-m-d', strtotime("+6 days", strtotime($nextWeekStart)));
                            
                                return ($targetDate >= $nextWeekStart) && ($targetDate <= $nextWeekEnd);
                            }



                            if($task["DueDate"] < date("Y-m-d")){
                                $overdue += 1;
                            }else if(isDateInCurrentWeek(date("Y-m-d"))){
                                $this_week += 1;
                            }else if(isDateInNextWeek(date("Y-m-d"))){
                                $next_week += 1;
                            }else{
                                $later += 1;
                            }
                            ?>
                            <li>
                                <div class="top-section">
                                    <h3><?= $task["TaskName"] ?></h3>
                                    <button type="button" class="edit-option">🞭</button>
                                </div>
                                <div class="bottom-section">
                                    <p>Status: <?= $task["TaskStatus"] ?></p>
                                    <p>Duration Est: <?= $task["TaskDuration"] ?>hrs</p>
                                    <p>Due Date: <?= $task["DueDate"] ?></p>
                                    <p>Description:<?= $task["TaskDescription"] ?> </p>
                                    <div style="text-align: center;">      
                                    </div>
                                </div>
                                
                            </li>
                        <?php
                        }   

                        
                        ?>
                    </ul>
                </section>

                <!-- Tasks Overall Progress -->
                <section>
                    <h3>Overall Progress:</h3>
                    <div class="project-progress">
                        <?php
                        $completion_percentage = ($completed + 0.5*$ongoing)/($completed + $ongoing + $not_started) * 100;
                        ?>
                        <progress value="<?= $completion_percentage ?>" max="100" class="project-progress-bar"></progress>
                        <p><?=$completion_percentage?>% (<?=$completed?>/ <?= $completed + $ongoing + $not_started ?>) Complete</p>
                    </div>

                    <!-- Project Task Overview Charts -->
                    <div class="chart-container">
                        <!-- Bar & Pie Charts -->
                        <div id="columnChartContainer" style="height: 450px; width: 45%; min-width: 500px;"></div>
                        <div id="pieChartContainer" style="height: 450px; width: 45%; min-width: 500px;"></div>
                    </div>
                </section>

                <section>
                    <header>
                        <h2>Actions:</h2>
                    </header>

                    <ul id="action-list">

                        <?php
                        if(($_SESSION["user_type"] == "Manager" || $_SESSION["user_type"] == "Admin") && $utype == "User"){
                            ?>
                        <button>
                            Promote to Manager
                        </button>
                        <?php
                        }
                        ?>
                        
                    </ul>

                </section>

                <!-- Employee Tasks -->
                <!-- only your tasks loaded first -->
                <!-- other members' tasks loaded via ajax request - cache results until page reload -->
                
            </section>


            <!-- Modals -->
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

            <div class="modal-background" data-hidden="true">
                <form action="" method="post" class="modal modal-form">
                    <header>
                        <h3>Move Employee's Task</h3>
                        <button type="button" class="close-modal"><i class="bx bx-x"></i></button>
                    </header>

                    <button type="submit" class="button" style="--bg-colour: var(--bg-1);">Move Task</button>
                </form>
            </div>
        </div>
    </main>
</div>

<script>

function create_charts(){
    const theme = localStorage.getItem("theme")

    var chart = new CanvasJS.Chart("columnChartContainer", {
	animationEnabled: true,
    backgroundColor: (theme === "light") ? "#F2F2F2" : "#373E43",
	theme: "light1", // "light1", "light2", "dark1", "dark2"
	title: {
            text: "Task Due Dates",
            horizontalAlign: "center",
            fontSize: 24,
            fontFamily: "monospace",
            fontColor: (theme === "light") ? "#000000" : "#FFFFFF",
            padding: {top: 10, left: 0, right: 0, bottom: 20},
        },
    axisX: {
            labelFontFamily: "monospace",
            labelFontSize: 24,
            labelFontColor: (theme === "light") ? "#000000" : "#FFFFFF",
         },
    axisY: {
            title: "# of Tasks",
            titleFontFamily: "monospace",
            titleFontColor: (theme === "light") ? "#000000" : "#FFFFFF",
            interval: 1,
            labelFontFamily: "monospace",
            labelFontSize: 24,
            labelFontColor: (theme === "light") ? "#000000" : "#FFFFFF",
        },
	data: [{
		type: "column", //change type to bar, line, area, pie, etc
		//indexLabel: "{y}", //Shows y value on all Data Points
		indexLabelFontColor: (theme === "light") ? "#000000" : "#FFFFFF",
      	indexLabelFontSize: 16,
		indexLabelPlacement: "outside",
		dataPoints: [
			{ label: "Overdue", y: <?= $overdue ?> },
			{ label: "This Week", y: <?= $this_week ?>  },
			{ label: "Next Week", y: <?= $next_week ?> },
			{ label: "Later", y: <?= $later ?> },
		]
	}]
    });
    chart.render();

    var chart2 = new CanvasJS.Chart("pieChartContainer", {
        animationEnabled: true,
        backgroundColor: (theme === "light") ? "#F2F2F2" : "#373E43",
        title: {
            text: "Weekly Task Status",
            horizontalAlign: "center",
            fontSize: 24,
            fontFamily: "monospace",
            fontColor: (theme === "light") ? "#000000" : "#FFFFFF",
            padding: {top: 10, left: 0, right: 0, bottom: 20},
        },
        data: [{
            type: "pie",
            startAngle: 240,
            yValueFormatString: "##0.00\"%\"",
            indexLabel: "{label} {y}",
            indexLabelFontColor: (theme === "light") ? "#000000" : "#FFFFFF",
            dataPoints: [
                {y: <?= $ongoing ?>, label: "Ongoing"},
                {y: <?= $completed ?>, label: "Complete"},
                {y: <?= $not_started ?>, label: "Not Started"},
            ]
        }]
    });
    chart2.render();

    console.log("complete");

}





create_charts();

const light_toggle = document.getElementsByClassName("switch-to-light")[0];
const dark_toggle = document.getElementsByClassName("switch-to-dark")[0];

light_toggle.addEventListener("click", () => {
    setTimeout(() =>create_charts(), 200);
})

dark_toggle.addEventListener("click", () => {
    setTimeout(() =>create_charts(), 200);
})


</script>
</body>
</html>

<?php 
// close db connection
close($conn);
?>