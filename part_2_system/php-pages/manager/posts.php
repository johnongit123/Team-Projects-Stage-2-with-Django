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
// new post
if(isset($_POST['new-post-title'])){
    $title = $_POST['new-post-title'];
    $desc = $_POST['new-post-desc'];
    $threadID = $_SERVER['QUERY_STRING'];
    $date = date("Y-m-d");
    $sql = "INSERT INTO posts (Title, Content, Date ,ThreadID ,UserID) VALUES ('$title', '$desc', '$date' ,'$threadID','" . $_SESSION["user_id"] . "' )";
    $result = $conn->query($sql);
    if  ($result !== TRUE) {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// edit thread
if(isset($_POST['edited-thread-title'])){
    // Get the edited title and content from the form
    $editedTitle = $_POST['edited-thread-title'];
    $editedContent = $_POST['edited-thread-desc'];

    // Construct the SQL query to update the post
    $sql = "UPDATE threads SET Title = '$editedTitle', Content = '$editedContent' WHERE ThreadID =".$_SERVER['QUERY_STRING'];
    // Execute the query
    $result = $conn->query($sql);
}
// remove thread
if(isset($_POST['thread-id'])){
    $thread_id = $_POST['thread-id'];
    $sql = "DELETE FROM threads WHERE ThreadID = " . $thread_id;
    $result = $conn->query($sql);
    if  ($result !== TRUE) {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
    header("Location: threads.php"); // go back to threads page once thread is removed
}

/* Moved up here for more readable html */
// get thread details
$thread_id = $_SERVER['QUERY_STRING'];
$sql = "Select * FROM threads WHERE ThreadID =".$_SERVER['QUERY_STRING'];
$result = $conn->query($sql);
$thread = null;
if ($result->num_rows > 0) {
    // echo implode($thread); // debug
    $thread = $result->fetch_assoc(); // only need first row as id is unique

}
else {
    // Thread not found
    echo '
        <h1>Thread not found</h1><br>
        <a href="./threads.php">Back</a>';
    exit();
}
// get list of posts within thread - including comment count for each post
$sql = "Select p.*, COUNT(c.commentID) as CommentCount
        FROM posts p 
        LEFT JOIN comments c on p.PostID = c.PostID 
        WHERE p.ThreadID =".$thread_id."
        GROUP BY p.PostID";
$result = $conn->query($sql);
$posts = null;
if ($result->num_rows > 0) {
    $posts = $result->fetch_all(MYSQLI_ASSOC);
}
?>
    <!DOCTYPE html>
    <html lang="en" data-theme="light">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Topic Details & Posts</title>

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
            <section class="forum-container central column-container">

                <!-- Main Header -->
                <header class="row-container">
                    <h1 class="breadcrumb"> <a href="../manager/threads.php">Forum</a> > <b>Topic Details</b> </h1>

                    <div class="right">
                        <?php
                        if($_SESSION["isMod"]) {
                            echo '
                            <button class="button open-modal right" data-target-id="edit-topic-form">Edit Topic</button>
                            <button class="button open-modal right" data-target-id="confirm-delete-topic">Remove Topic</button>';
                        }
                        ?>
                    </div>
                </header>


                <!-- Topic Details -->
                <section class="burger-layout">

                    <!-- Topic title and date -->
                    <header class="row-container">
                        <h2><i class="bx bx-category-alt"></i> <?=$thread["Title"]?> </h2>

                        <div class="row-container right details">
                            <div class="row-container date">
                                <i class="bx bx-calendar"></i>
                                <p><?=$thread["Date"]?></p>
                            </div>
                        </div>
                    </header>

                    <!-- Topic Description -->
                    <div class="column-container" style="--gap: 1rem;">
                        <h3>Description</h3>
                        <p class="formatted-text"><?=$thread["Content"]?></p>
                    </div>

                </section>


                <!-- Topic Posts -->
                <div class="background column-container" style="gap: 2.5rem">
                    <div class="row-container">
                        <!-- Search Bar -->
                        <label class="search-bar">
                            <i class="bx bx-search" id="searchIcon"></i> <!-- &#xebf7 -->
                            <input type="text" oninput="search()" id="searchText" placeholder="Search Posts within Topic">
                        </label>

                        <!-- New Post Btn -->
                        <button class="button open-modal" data-target-id="new-post-form">+ New Post</button>

                        <!-- Topic's Number of Posts -->
                        <div class="row-container post-comment-count right">
                            <i class="bx bx-detail"></i> <p><?= ($posts == null) ? " 0" : " ".count($posts) ?></p>
                        </div>
                    </div>

                    <ol class="results-list">
                        <?php
                        if ($posts == null) {
                            echo "<h3>0 Posts</h3>";
                        }
                        else {
                            foreach ($posts as $post) {
                                echo '
                                <li class="result-item">
                                    <a href="comments.php?' . $post['PostID'] . '">
                                        <h3 class="title">' . $post['Title'] . '</h3>
                                        <div class="bottom">
                                            <p class="timestamp"><i class="bx bx-calendar"></i> ' . $post['Date'] . '</p>
                                            <p class="comment-count"><i class="bx bxs-message-square-detail"></i> '.$post["CommentCount"].' comments</p>
                                        </div>
                                    </a>
                                </li>';
                            }
                        }
                        ?>
                    </ol>
                </div>



                <!-- New Post Modal Form -->
                <div id="form-background" class="modal-background" data-hidden="true">
                    <form id="new-post-form" class="modal modal-form" method="post">
                        <header>
                            <h3>Create Post</h3>
                            <button type="button" class="close-modal"><i class="bx bx-x"></i></button>
                        </header>

                        <!-- title -->
                        <label for="topic-title-input">Post Title<br>
                            <input type="text" name="new-post-title" id="topic-title-input" required>
                        </label>

                        <!-- description -->
                        <label for="topic-desc-input">Post Description<br>
                            <textarea name="new-post-desc" id="topic-desc-input" cols="40" rows="10"></textarea>
                        </label>

                        <button type="submit">Add Post</button>
                    </form>
                </div>

                <!-- Edit Topic Modal Form -->
                <div class="modal-background" data-hidden="true">
                    <form id="edit-topic-form" class="modal modal-form" method="post">
                        <header>
                            <h3>Edit Topic</h3>
                            <button type="button" class="close-modal"><i class="bx bx-x"></i></button>
                        </header>

                        <!-- title -->
                        <label>Topic Title<br>
                            <input type="text" name="edited-thread-title" value="<?=$thread["Title"];?>" required>
                        </label>

                        <!-- description -->
                        <label>Topic Description<br>
                            <textarea name="edited-thread-desc" cols="40" rows="10" ><?=$thread["Content"];?></textarea>
                        </label>

                        <button type="submit" class="button">Save Changes</button>
                    </form>
                </div>

                <!-- Confirm Removal of Topic Modal Form-->
                <div class="modal-background" data-hidden="true">
                    <div id="confirm-delete-topic" class="modal">
                        <header>
                            <h3>Delete Topic?</h3>
                        </header>

                        <div class="row-container">
                            <!-- Remove Topic - ADD ACTION -->
                            <form method="post" class="modal-form">
                                <input type="hidden" name="thread-id" value="<?=$thread_id?>">
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
<?php
close($conn);
?>