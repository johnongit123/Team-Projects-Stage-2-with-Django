<?php
$status = session_status();
if ($status == PHP_SESSION_NONE) {
    //There is no active session
    session_start();
}

// check if user is logged in
if (!isset($_SESSION["user_id"])) {
    echo "User not logged in!";
    header("Location: ../../index.php");
}


// // check if project id is set
// if (!isset($_GET["id"])) {
//     // redirect to project overview
//     header("Location: project-overview.php"); // change this  !!!
// }
// // get project id
// $project_id = $_GET["id"];


// include_once - prevents redeclaration of functions
include_once "../../server.php";
include_once "../other/project-queries.php";
include_once "../other/status-icon.php";
include_once "../components/sidebar-component.php";


// connect to db
$conn = connect();
if(!authenticate("Manager")){
    header("Location: ../../index.php");
}

if(isset($_POST["projectSel"])){
    $projectID = $_POST["projectSel"];
    $userID = $_POST["userSel"];
    $teamLeader = $_POST["teamLeaderSel"];
    $sql = "INSERT INTO userprojects (UserID, ProjectID, isTeamLeader) VALUES ('$userID', '$projectID', '$teamLeader')";
    try {
        $conn->query($sql);
    } catch (Exception $e) {
        echo '<script>alert("User already in that Project")</script>';

    }


}
if(isset($_POST["projectDSel"])){
    $projectID = $_POST["projectDSel"];
    $userID = $_POST["userDSel"];
    $sql = "DELETE FROM userprojects WHERE UserID = '$userID' AND ProjectID = '$projectID'";
    $conn->query($sql);

}
if(isset($_POST["modSel"])){
    $userID = $_POST["modSel"];
    $sql = "UPDATE users SET isMod = '1' WHERE UserID = '$userID'";
    $conn->query($sql);
}
?>


<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Overview</title>

    <!-- css -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../../css/main.css">
    <link rel="stylesheet" href="../../css/dash.css">
    <link rel="stylesheet" href="../../css/overview.css">
    <link rel="stylesheet" href="../../css/employee-view.css">
    <link rel="stylesheet" href="../../css/modal-form-style.css">


    <!-- javascript -->
    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="../../js/to-do-list.js" defer></script>
    <script src="../../js/sidebar.js" defer></script>
</head>
<body>

<div class="page-container">
    <?= getSidebarComponent("employees", $_SESSION["username"], "../../images/office2.jpeg"); ?>

    <main class="main-content">
        <section class="overview central">
            <header class="header">
                <h1>Employees</h1>
                <h2>Overview</h2>
            </header>

            <!-- Employees -->
            <section class="background column-container">
                <h3>Employee List:</h3>

                <ul class="column-container employee-list" style="list-style: none;">
                    <?php
                    $sql = "SELECT * FROM users";
                    $users = $conn->query($sql);
                    while ($row = mysqli_fetch_assoc($users)) {
                        $sql = "SELECT `UserID`, t2.ProjectName,`isTeamLeader`  FROM `userprojects` t1 JOIN `projects` t2 ON t1.ProjectID = t2.ProjectID WHERE UserID = ".$row["UserID"];
                        $projects = $conn->query($sql);
                        $projects = $projects->fetch_all(MYSQLI_ASSOC);
                        $project_list = "";

                        if (empty($projects)) {
                            $project_list = "<li>No Projects</li>";
                        }
                        else {
                            foreach ($projects as $project) {
                                if($project["isTeamLeader"] == 1)
                                    $project_list .=  "<li class='background'>".$project["ProjectName"]." <span>(Team Leader)</span></li>";
                                else
                                    $project_list = "<li class='background'>".$project["ProjectName"]."</li>";
                            }
                        }
                        echo '
                        <li class="background row-container employee">
                                <div>
                                    <h4>'.$row["Username"].'</h4>
                                    <h5><i class="bx bx-user-circle"></i> '.$row["UserType"].'</h5>
                                </div>
                                <div class="row-container">
                                    <h4>Projects:</h4>
                                    <ul style="list-style: none;">
                                        '.$project_list.'    
                                    </ul>
                                </div>
   
                            </form>
                        </li>
                          ';

                    }
                    ?>
                </ul>

                <div class="row-container forms">
                    <!-- Assign Users to Projects -->
                    <div class="background column-container" style="--bg-colour: var(--bg-1);">
                        <h3>Assign Users:</h3>

                        <form method="post" name="AssignUser" class="modal-form column-container">
                            <!-- Select Project -->
                            <label>
                                Project:
                                <select name="projectSel" id="project">
                                    <?php
                                    $sql = "SELECT * from projects";
                                    $project = $conn->query($sql);

                                    while ($row = mysqli_fetch_assoc($project)) {
                                        echo '<option value="'.$row["ProjectID"].'">'.$row["ProjectName"].'</option>';
                                    }
                                    ?>
                                </select>
                            </label>

                            <!-- Select User -->
                            <label>
                                User:
                                <select name="userSel" id="user">
                                    <?php
                                    $sql = "SELECT * from users";
                                    $users = $conn->query($sql);

                                    while ($row = mysqli_fetch_assoc($users)) {
                                        echo '<option value="'.$row["UserID"].'">'.$row["Username"].'</option>';
                                    }
                                    ?>
                                </select>
                            </label>

                            <!-- Select Role -->
                            <label>
                                Role:
                                <select name="teamLeaderSel" id="assign">team leader
                                    <option value="1">Team Leader</option>
                                    <option value="0">Member</option>
                                </select>
                            </label>

                            <button type="submit" name="submit" class="button">Assign</button>
                        </form>
                    </div>

                    <!-- Remove Project Members -->
                    <div class="background column-container" style="--bg-colour: var(--bg-1);">
                        <h3>Remove Project Members:</h3>

                        <form name="RemoveUser" method="post" class="modal-form column-container" >
                            <!-- Select Project -->
                            <label>
                                Project:
                                <select name="projectDSel" id="project">
                                    <?php
                                    $sql = "SELECT * from projects";
                                    $project = $conn->query($sql);

                                    while ($row = mysqli_fetch_assoc($project)) {
                                        echo '<option value="'.$row["ProjectID"].'">'.$row["ProjectName"].'</option>';
                                    }
                                    ?>
                                </select>
                            </label>

                            <!-- Select user -->
                            <label>
                                User:
                                <select name="userDSel" id="user">
                                    <?php
                                    $sql = "SELECT * from users";
                                    $users = $conn->query($sql);

                                    while ($row = mysqli_fetch_assoc($users)) {
                                        echo '<option value="'.$row["UserID"].'">'.$row["Username"].'</option>';
                                    }
                                    ?>
                                </select>
                            </label>
                            <button type="submit" name="submitD" class="button" >Remove</button>
                        </form>
                    </div>
                </div>
            </section>

            <!-- Forum Moderators -->
            <section class="list-container">
                <header>
                    <h3>Forum Mods</h3>
                </header>
                <ul class="column-container employee-list" style="list-style: none;">
                    <?php
                    $sql = "SELECT * FROM users WHERE isMod = '1'";
                    $mods = $conn->query($sql);
                    while ($row = mysqli_fetch_assoc($mods)) {
                        echo '
                        <li class="background row-container employee">
                                <div>
                                    <h4>'.$row["Username"].'</h4>
                                </div>
                        </li>';
                    }
                    ?>
                </ul>
            </section>
            <section class="list-container">
                <header>
                    <h3>Assign Forum Mod</h3>
                </header>
                <form name="AssignMod" method="post" class="modal-form row-container">
                    <label>
                        User:
                        <select name="modSel" id="user">
                            <?php
                            $sql = "SELECT * from users Where isMod = '0'";
                            $users = $conn->query($sql);

                            while ($row = mysqli_fetch_assoc($users)) {
                                echo '
                        <option value="'.$row["UserID"].'">'.$row["Username"].'</option>
                       ';
                            }
                            ?>
                        </select>
                    </label>
                    <button type="submit" name="submit" class="button">Assign</button>
            </section>







            <?php /*
            $sql = "SELECT * FROM users WHERE UserType = 'Manager'";
            $managers = $conn->query($sql);
            if ($_SESSION["user_type"] == "Admin") {
                echo ' <section class="list-container">

                    <header>
                        <h3>Managers:</h3>
                    </header>

                
                    <ul class="employee-list">';


                while ($row = mysqli_fetch_assoc($managers)) {

                    */?>
            <!--
            <a href="http://localhost/php-pages/other/employee-view.php?id=<?php /*= $row["UserID"] */?>">
                <li class="employee-card" data-project-id="1">
                    <div class="top-section" style="background-color: brown">
                        <h3><?php /*= $row["Username"] */?></h3>
                    </div>
                    <div class="bottom-section">
                        <img src="../../images/blank-user.png" alt="Employee Profile Image">
                        <div class="employee-progress">

                        </div>
                    </div>
                </li>
            </a>
                    <?php
/*                }

            }
            */?>
            </ul>
        </section>-->
</section>
</main>
</div>

</body>
</html>