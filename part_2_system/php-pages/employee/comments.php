
<?php
//todo: edit button toggles delete buttons
//todo: css for delete button and comment input box

$status = session_status();

if($status == PHP_SESSION_NONE){
    //There is no active session
    session_start();
}

include "../../server.php";

include_once "../components/sidebar-component.php";

$conn = connect();
if(!authenticate("User")){
    header("Location: ../../index.php");
}

// new comment
if(isset($_POST['commentInfo'])){
    $postID = $_SERVER['QUERY_STRING'];
    $desc = $_POST['commentInfo'];
    $date = date("Y-m-d");
    $sql = "INSERT INTO comments ( Content, Date ,postID , UserID) VALUES ('$desc', '$date' ,'$postID','" . $_SESSION["user_id"] . "' )";
    $result = $conn->query($sql);
    if  ($result !== TRUE) {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// delete comment
if(isset($_POST['commentDID'])){
    $commentID = $_POST['commentDID'];
    $sql = "DELETE FROM comments WHERE CommentID = " . $commentID;
    $result = $conn->query($sql);
    if  ($result !== TRUE) {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// edit comment
if(isset($_POST['commentEInfo'])){
    $commentID = $_POST['commentEID'];
    $desc = $_POST['commentEInfo'];
    $sql = "UPDATE comments SET Content = '$desc' WHERE CommentID = " . $commentID;
    $result = $conn->query($sql);
    if  ($result !== TRUE) {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// edit post
if(isset($_POST['edited-post-title'])){
    // Get the edited title and content from the form
    $editedTitle = $_POST['edited-post-title'];
    $editedContent = $_POST['edited-post-desc'];

    // Construct the SQL query to update the post
    $sql = "UPDATE posts SET Title = '$editedTitle', Content = '$editedContent' WHERE PostID =" . $_SERVER['QUERY_STRING'];
    // Execute the query
    $result = $conn->query($sql);

}

// remove post

if (isset($_POST["delete-post-id"])) {
    // Construct the SQL query to update the post
    $sql = "DELETE FROM posts WHERE PostID =" . $_POST["delete-post-id"];
    // Execute the query
    $result = $conn->query($sql);

    // go back to topic page
    header("Location: ../employee/posts.php?" . $_POST["topic-id"]);
}

/* Moved sql queries up here for more readable html */
// get post details
$post_id = $_SERVER['QUERY_STRING'];
$sql = "Select * FROM posts WHERE PostID = ".$post_id;
$result1 = $conn->query($sql);
if ($result1->num_rows > 0) {
    $post = $result1->fetch_assoc(); // only 1st result as post id unique
}
else {
    // Post not found
    echo '
        <h1>Post not found</h1><br>
        <a href="./threads.php">Back</a>';
    exit();
}

// get comments within post
$sql = "SELECT c.*, c.UserID as 'Author'
        FROM comments c
        WHERE c.PostID = ".$post_id;
$result = $conn->query($sql);
$comments = [];
if ($result->num_rows > 0) {
    $comments = $result->fetch_all(MYSQLI_ASSOC); // gets all rows as 2d array
}
$comment_count = count($comments);
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Details & Comments</title>

    <!-- css -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../../css/dash.css">
    <link rel="stylesheet" href="../../css/main.css">
    <link rel="stylesheet" href="../../css/forum-post-style.css">
    <link rel="stylesheet" href="../../css/modal-form-style.css">

    <!-- javascript -->
    <!-- jQuery library --><script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="../../js/to-do-list.js" defer></script>
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
                <h1 class="breadcrumb"> <a href="../employee/threads.php">Forum</a> > <a href="../employee/posts.php?<?=$post["ThreadID"]?>">Topic Details</a> > <b>Post Details</b> </h1>

                <!-- Only Moderator Stuff -->
                <?php
                $sql = "SELECT Username AS `Author`, t2.UserID FROM `posts` t1 JOIN `users` t2 ON t1.UserID = t2.UserID WHERE postID =". $_SERVER['QUERY_STRING'];
                $result = $conn->query($sql);
                $row = $result->fetch_assoc();
                if ($_SESSION["isMod"] OR $result->num_rows > 0) {
                    if ($_SESSION["isMod"] OR ($row["UserID"] == $_SESSION["user_id"])) {
                        echo '
                        <div class="right">
                            <button class="button open-modal right" data-target-id="edit-post-form">Edit Post</button>
                            <button class="button open-modal right" data-target-id="confirm-delete-post">Remove Post</button>
                        </div>';
                    }
                }
                ?>
            </header>

            <!-- Post Details -->
            <section class="burger-layout">
                <!-- Adding Post Details -->
                <?php
                $sql = "Select * FROM posts WHERE PostID =" . $post_id;
                $result1 = $conn->query($sql);
                if ($result1->num_rows > 0) {
                    $post = $result1->fetch_assoc(); // get first result - should only be one
                    echo '
                        <header class="row-container">
                            <h2><i class="bx bx-detail"></i> '.$post["Title"].'</h2>
                            
                            <div class="row-container right details">
                                <div class="row-container date">
                                    <i class="bx bx-calendar"></i>
                                    <p>'.$post["Date"].'</p>
                                </div>
                            </div>
                        </header>
                        
                        <div class="column-container" style="--gap: 1rem;">
                            <h3>Description</h3>
                            <p class="formatted-text">'.$post["Content"].'</p>
                        </div>
                        ';
                }
                ?>
            </section>

            <!-- Post Comments -->
            <div class="background column-container" style="gap: 2.5rem">
                <!-- New Comment -->
                <div class="row-container">
                    <!-- Comment Input -->
                    <form method="post" name="addComment"
                          id="new-comment" class="burger-layout" style="--padding: 0">
                        <div class="row" style="--gap: .25rem">
                            <label>
                                <input type="text" name="commentInfo" id="comment-text" placeholder="Comment" required>
                            </label>
                            <button id="add-comment" type="submit"><i class="bx bx-send"></i></button>
                        </div>
                    </form>

                    <!-- Comment Count -->
                    <div class="row-container post-comment-count right">
                        <i class="bx bx-message-square-detail"></i>
                        <p><b><?=$comment_count?></b></p>
                    </div>
                </div>


                <!-- Comments List -->
                <ul class="column-container" id="comments">
                    <?php
                    if (count($comments) == 0) {
                        echo "0 Comments";
                    }
                    else {
                        foreach ($comments as $comment) {
                            echo '
                            <li class="background column-container comment">
                                <div class="row-container comment-info">
                                    <p><i class="bx bx-user-circle"></i> '.$comment["Author"].'</p>
                                    <p><i class="bx bx-calendar"></i> '.$comment["Date"].'</p>
                                ';
                            if($_SESSION["isMod"]  OR ($comment["UserID"] == $_SESSION["user_id"])){
                                echo '
                                    <!-- Remove Comment Button/Form -->
                                    <form method="post" id="delete-comment-form" name="CommentDel">
                                        <input type="hidden" name="commentDID" value="'.$comment["CommentID"].'">
                                    </form>
                                    <button type="submit" form="delete-comment-form" class="red-circle-button"><i class="bx bx-x"></i></button> <!-- 🞮 -->

                                    <!-- Toggle Edit Button -->
                                    <button class="button edit-button"><i class="bx bx-edit"></i></button>
                                    
                                    <form method="post" name="saveComment" class="save-comment-form" style="display: none">
                                        <input type="hidden" name="commentEID" value="'.$comment["CommentID"].'">
                                        <input type="hidden" name="commentEInfo" class="comment-content" required>
                                        <button type="submit" class="button save-changes"><i class="bx bxs-edit"></i> Save Changes</button>
                                    </form>';
                            }
                            echo '
                                </div>
                                <p class="comment-content">'.$comment["Content"].'</p>
                            </li>';
                        }
                    }
                    ?>
                </ul>
            </div>
        </section>


        <!-- Edit Post Modal Form -->
        <div id="form-background" class="modal-background" data-hidden="true">
            <!-- Edit Post -->
            <form id="edit-post-form" class="modal modal-form" method="post">
                <header>
                    <h3>Edit Post:</h3>
                    <button type="button" class="close-modal"><i class="bx bx-x"></i></button>
                </header>

                <!-- title -->
                <label>Post Title<br>
                    <input type="text" name="edited-post-title" value="<?php echo $post["title"] ?>" required>
                </label>

                <!-- description -->
                <label>Post Description<br>
                    <textarea name="edited-post-desc" cols="40" rows="10" ><?php echo $post["content"] ?></textarea>
                </label>

                <button type="submit" class="button">Save Changes</button>
            </form>
        </div>

        <!-- Confirm Removal of Post -->
        <div class="modal-background" data-hidden="true">
            <div id="confirm-delete-post" class="modal">
                <header>
                    <h3>Delete Post?</h3>
                </header>

                <div class="row-container">
                    <!-- Remove Post -->
                    <form method="post" class="modal-form">
                        <input type="hidden" name="delete-post-id" value="<?=$post_id?>">
                        <input type="hidden" name="topic-id" value="<?=$post["ThreadID"]?>">
                        <button type="submit" class="button">Delete</button>
                    </form>
                    <button type="button" class="button close-modal">Cancel</button>
                </div>
            </div>
        </div>
    </main>
</div>

</body>
</html>
<?php
close($conn);
?>