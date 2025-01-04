<?php
// handle post request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST["action"])) {
        return;
    }

    // get database connection
    include_once "../../server.php";
    $conn = connect();

    // update project
    if ($_POST["action"] == "getMemberTaskCountData") {
        // return result
        echo json_encode(getMemberTaskCountData($conn, $_POST["projectID"]));
    }
    else if ($_POST["action"] == "getMemberTaskData") {
        include_once "../other/status-icon.php";

        $member_task_data = getMemberTaskData($conn, $_POST["projectID"], $_POST["memberID"]);

        // format data before sending response back to js
        foreach ($member_task_data as $task_id => $task_data) {
            $member_task_data[$task_id]["DueDateFormatted"] = (new DateTime($task_data["DueDate"]))->format('d-m-Y');
            $member_task_data[$task_id]["TaskStatusClass"] = getStatusIconClass($task_data["TaskStatus"]);
        }

        // return result
        echo json_encode($member_task_data);
    }

    // close the connection
    close($conn);
}




/*
--- Project Data ---
    - ProjectID
    - ProjectName
    - Description
    - DueDate
    - Colour

--- Project Task Count Data ---
    - TotalComplete
    - TotalIncomplete
    - Total
    - ProgressFraction
    - ProgressPercent

--- Member Data[] : ---
    Associative Array (For each User in Project):
        UserID => Username, Email, Password, UserType, <TaskCountData>
        <TaskCountData>
            - TotalComplete
            - TotalIncomplete
            - Total
            - TotalIncompleteDuration
            - TotalDuration
            - ProgressFraction
            - ProgressPercent

--- Member Task Data ---
    Associative Array (For each Task in Project):
        TaskID => TaskName, TaskDescription, TaskStatus, TaskDuration, TaskDeadline
*/




function getProjectData($conn, $projectID) {
    $sql = "SELECT * 
            FROM projects p
            LEFT JOIN userprojects up ON p.ProjectID = up.ProjectID
            WHERE p.ProjectID = " . $projectID;
    $result = $conn->query($sql);

    if ($result->num_rows == 0) {
        return null;
    }
    
    $project = $result->fetch_assoc();
    // check if project is overdue - only for 'not started' / 'ongoing' projects
    if ($project["DueDate"] < date('Y-m-d')
        && ($project["Status"] == "Ongoing" || $project["Status"] == "Not Started"))
    {
        setProjectStatusOverdue($conn, $project["ProjectID"]);
        $project["Status"] = "Overdue";
    }

    // return project
    return $project;
}
function getAllProjects($conn) {
    $sql = "SELECT * 
            FROM projects";
    $result = query($conn, $sql);

    // return all results as associative array
    $all_projects = [];
    foreach ($result->fetch_all(MYSQLI_ASSOC) as $project) {
        $all_projects[$project["ProjectID"]] = $project;
    }
    return $all_projects;
}
function getProjectTaskCountData($conn, $projectID) {
    $sql = "SELECT 
                p.ProjectID, 
                p.ProjectName, 
                SUM(CASE WHEN t.TaskStatus = 'Complete' THEN 1 ELSE 0 END) AS TotalComplete,
                SUM(CASE WHEN t.TaskStatus != 'Complete' THEN 1 ELSE 0 END) AS TotalIncomplete
            FROM 
                projects p
            LEFT JOIN 
                taskprojects tp ON p.ProjectID = tp.ProjectID
            LEFT JOIN 
                tasks t ON tp.TaskID = t.TaskID
            WHERE 
                p.ProjectID = ". $projectID ."
            GROUP BY     
                p.ProjectID, p.ProjectName;";
    $result = query($conn, $sql);

    // return task count data
    // returns ProjectID, ProjectName, TotalCompleteTasks, TotalIncompleteTasks, TotalTasks, TaskProgress, TaskProgressPercent
    $project_task_count = $result->fetch_assoc();

    $project_task_count["Total"] = $project_task_count["TotalComplete"] + $project_task_count["TotalIncomplete"];
    $total = $project_task_count["Total"];
    $project_task_count["ProgressFraction"] = $project_task_count["TotalComplete"] . "/" . $total;
    $project_task_count["ProgressPercent"] = ($total > 0) ? strval(intval($project_task_count["TotalComplete"] / $total * 100)) : 0;
    // ^ prevent division by zero

    return $project_task_count;
}
function getMemberData($conn, $project_id, $include_task_count_data = false) {
    $sql = "SELECT
                u.*
            FROM
                users u
            LEFT JOIN
                userprojects up ON u.UserID = up.UserID
            WHERE
                up.ProjectID = ". $project_id ."
            GROUP BY
                u.UserID, u.Username;";
    $result = query($conn, $sql);

    // return in the form of UserID => [UserData]
    $project_members = [];
    foreach ($result->fetch_all(MYSQLI_ASSOC) as $member) {
        $project_members[$member["UserID"]] = $member;
    }

    // add task count data to each member
    if ($include_task_count_data) {
        $task_count_per_member = getMemberTaskCountData($conn, $project_id);
        foreach ($project_members as $user_id => $member_data) {
            // add task count data to member
            $task_count_data = $task_count_per_member[$user_id];
            $project_members[$user_id]["TaskCountData"] = $task_count_data;
        }
    }

    return $project_members;
}
function getMemberTaskCountData($conn, $project_id) {
    $sql = "SELECT 
                u.UserID,
                u.Username,
                SUM(CASE WHEN tp.ProjectID = ".$project_id." AND t.TaskStatus = 'Complete' THEN 1 ELSE 0 END) AS TotalComplete,
                SUM(CASE WHEN tp.ProjectID = ".$project_id." AND t.TaskStatus != 'Complete' THEN 1 ELSE 0 END) AS TotalIncomplete,
                SUM(CASE WHEN tp.ProjectID = ".$project_id." AND t.TaskStatus != 'Complete' THEN t.TaskDuration ELSE 0 END) AS TotalIncompleteDuration,
                SUM(CASE WHEN tp.ProjectID = ".$project_id." THEN t.TaskDuration ELSE 0 END) AS TotalDuration
            FROM 
                users u
            LEFT JOIN 
                userprojects up ON up.UserID = u.UserID 
            LEFT JOIN
                usertasks ut ON ut.UserID = u.UserID
            LEFT JOIN
                tasks t ON t.TaskID = ut.TaskID
            LEFT JOIN
                taskprojects tp ON tp.TaskID = ut.TaskID
            WHERE
                up.ProjectID = ".$project_id."
            GROUP BY 
                u.UserID";
    $result = query($conn, $sql);

    // return all results as associative array
    $task_count_per_user = [];
    foreach ($result->fetch_all(MYSQLI_ASSOC) as $row) {
        $total = $row["TotalComplete"] + $row["TotalIncomplete"];
        $task_count_per_user[$row["UserID"]] = [
            "Username" => $row["Username"],
            "TotalComplete" => $row["TotalComplete"],
            "TotalIncomplete" => $row["TotalIncomplete"],
            "Total" => $total,
            "TotalIncompleteDuration" => $row["TotalIncompleteDuration"],
            "TotalDuration" => $row["TotalDuration"],
            "ProgressFraction" => $row["TotalComplete"] . "/" . $total,
            "ProgressPercent" => ($total > 0) ? strval(intval($row["TotalComplete"] / $total * 100)) : 0
            // ^ prevent division by zero
        ];
    }
    return $task_count_per_user;
}
function getMemberTaskData($conn, $project_id, $user_id) {
    $sql = "SELECT 
                tp.ProjectID,
                ut.userID, 
                t.*
            FROM 
                tasks t
            INNER JOIN 
                usertasks ut ON ut.TaskID = t.TaskID AND ut.UserID = ".$user_id."
            INNER JOIN 
                taskprojects tp ON tp.TaskID = t.TaskID AND tp.ProjectID = ".$project_id."
            ORDER BY 
                tp.ProjectID";
    $result = query($conn, $sql);

    // return all results as associative array
    // in the form of TaskID => [TaskData]
    $task_data_per_user = [];
    foreach ($result->fetch_all(MYSQLI_ASSOC) as $row) {
        $task_data_per_user[$row["TaskID"]] = $row;
    }
    return $task_data_per_user;
}
function getUserProjects($conn, $user_id, $include_project_data = false) {
    $sql = "SELECT * 
            FROM userprojects 
            WHERE userprojects.ProjectID
            AND UserID = " . $user_id;
    $result = query($conn, $sql);

    if ($include_project_data) {
        $user_projects = array();
        foreach ($result->fetch_all(MYSQLI_ASSOC) as $project) {
            $user_projects[$project["ProjectID"]] = getProjectData($conn, $project["ProjectID"]);
            $user_projects[$project["ProjectID"]]["IsTeamLeader"] = isTeamLeader($conn, $user_id, $project["ProjectID"]);
        }
        return $user_projects;
    }
    else {
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
function updateProject($conn, $projectID, $projectName, $projectDeadline, $projectStatus, $projectColour, $projectDescription) {
    $sql = "UPDATE projects
            SET ProjectName = '".$projectName."', Description = '".$projectDescription."', 
            DueDate = '".$projectDeadline."', Colour = '".$projectColour."', Status = '".$projectStatus."'
            WHERE ProjectID = ".$projectID;
    $result = query($conn, $sql);
    if ($result) {
        return true;
    }
    else {
        return $result;
    }
}
function setProjectStatusOverdue($conn, $project_id) {
    $sql = "UPDATE projects
            SET Status = 'Overdue'
            WHERE ProjectID = " . $project_id;
    $result = query($conn, $sql);

    // return true if successful
    return ($result) ? true : $result;
}

function isTeamLeader($conn, $user_id, $project_id) {
    $sql = "SELECT * 
            FROM userprojects 
            WHERE UserID = " . $user_id . " AND ProjectID = " . $project_id;
    $result = query($conn, $sql);
    if ($result->num_rows == 0) {
        return "0";
    }
    else {
        $user_project_data = $result->fetch_assoc();
        return ($user_project_data["IsTeamLeader"]);
    }
}