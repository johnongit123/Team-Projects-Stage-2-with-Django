<?php
$status = session_status();
if($status == PHP_SESSION_NONE){
    //There is no active session
    session_start();
}
include "../../server.php";

include_once "../components/sidebar-component.php";

$conn = connect();
if(!authenticate("Manager")){
    header("Location: ../../index.php");
}
if ($_SERVER["REQUEST_METHOD"] == "POST"){
    $title = $_POST['task-title'];
    $desc = $_POST['task-desc'];
    $date = date("Y-m-d");
    $sql = "INSERT INTO threads (Title, Content, Date) VALUES ('$title', '$desc', '$date')";
    $result = $conn->query($sql);
    if  ($result !== TRUE) {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Topics</title>

    <!-- css -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../../css/main.css">
    <link rel="stylesheet" href="../../css/dash.css">
    <link rel="stylesheet" href="../../css/forum-post-style.css">
    <link rel="stylesheet" href="../../css/modal-form-style.css">

    <!-- javascript -->
    <!-- jQuery library --><script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="../../js/forum.js" defer></script>
    <script src="../../js/sidebar.js" defer></script>
    <script src="../../js/modal.js" defer></script>
</head>
<body>
<div class="page-container">
    <?=getSidebarComponent("forum", $_SESSION["username"], "../../images/office2.jpeg");?>

    <main class="main-content">
        <section class="forum-container central">
            <header>
                <h1>Forum</h1>
            </header>

            <section class="background column-container" style="gap: 2.5rem">
                <!-- Search Bar & New Thead button -->
                <div class="row-container">
                    <!-- Search Bar -->
                    <label class="search-bar">
                        <i class="bx bx-search" id="searchIcon"></i> <!-- &#xebf7 -->
                        <input type="text" oninput="search()" id="searchText" placeholder="Search Topics">
                    </label>

                    <!-- Add Topic Btn -->
                    <button class="button open-modal" data-target-id="new-topic-form">+ New Topic</button>
                </div>


                <!-- Search Results -->
                <div class="column-container" style="gap: 1rem">
                    <header>
                        <h2><i class="bx bx-category-alt"></i> Topics:</h2>
                    </header>

                    <ol id="topics" class="results-list">
                        <?php

                        // get list of threads - including post count

                        $sql = "Select t.*, COUNT(p.PostID) as PostCount
                                FROM threads t 
                                LEFT JOIN posts p on t.ThreadID = p.ThreadID
                                GROUP BY t.ThreadID";
                        $result = $conn->query($sql);
                        if ($result->num_rows > 0) {
                            while ($thread = $result->fetch_assoc()) {
                                echo '
                                <li class="result-item">
                                    <a href="posts.php?' . $thread['ThreadID'] . '">
                                        <h3 class="title">' . $thread['Title'] . '</h3>
                                        <div class="bottom">
                                            <p class="timestamp"><i class="bx bx-calendar"></i> ' . $thread['Date'] . '</p>
                                            <p class="comment-count"><i class="bx bx-detail"></i> '.$thread["PostCount"].' Posts</p>
                                        </div>
                                    </a>
                                </li>';
                            }
                        } else {
                            echo "0 results";
                        }
                        ?>
                    </ol>
                </div>
            </section>



            <!-- New Topic Modal -->
            <div id="form-background" class="modal-background" data-hidden="true">
                <form id="new-topic-form" class="modal modal-form" method="POST">
                    <header>
                        <h3>Create Topic</h3>
                        <button type="button" class="close-modal" id="close-new-topic-form"><i class="bx bx-x"></i></button>
                    </header>

                    <!-- title -->
                    <label for="topic-title-input">Topic Title<br>
                        <input type="text" name="task-title" id="topic-title-input" required>
                    </label>

                    <!-- description -->
                    <label for="topic-desc-input">Topic Description<br>
                        <textarea name="task-desc" id="topic-desc-input" cols="40" rows="10"></textarea>
                    </label>

                    <button type="submit" name="submit">Add Task</button>
                </form>
            </div>
        </section>
    </main>
</div>
</body>
</html>
<?php
close($conn);
?>