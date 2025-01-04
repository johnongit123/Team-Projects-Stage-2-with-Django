<?php
include "server.php";

// start session to allow cookies to be used
$status = session_status();
if($status == PHP_SESSION_NONE){
    //There is no active session
    session_start();
}else
    if($status == PHP_SESSION_DISABLED){
        //Sessions are not available
    }else
        if($status == PHP_SESSION_ACTIVE){
            //Destroy current and start new one
            session_destroy();
            session_start();
        }

// Create connection
$conn = connect();

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get username and password from the form
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Query to check if the user exists
    //$password = hash("sha3-256", $password);
    $sql = "SELECT * FROM users WHERE Email = '$email' AND Password = '$password'";
    $result = $conn->query($sql);

    // Check if the query returned a row (valid user)
    if ($result->num_rows > 0) {
        $user =  $result->fetch_assoc();
        $_SESSION["user_id"] = $user['UserID'];
        $_SESSION["username"] = $user['Username'];
        $_SESSION["user_type"] = $user['UserType'];
        $_SESSION["isMod"] = $user['isMod'];
        if($user['UserType'] == "Manager" || $user['UserType'] == "Admin"){
            header("Location: php-pages/manager/project-overview.php");
        }else if($user['UserType'] == "User"){
            header("Location: php-pages/employee/to-do-list.php"); //needs changing !!!
        }
        
        exit();
        // Redirect to a different page or perform other actions after successful login
    } else {
        $error_message = "Invalid username or password";
    }
}

// Close the database connection
$conn->close();

?>

<!doctype html>

<html lang="en" data-theme="light">

<head>

    <meta charset="UTF-8">

    <title>Log In</title>

    <link rel="stylesheet" type="text/css" href="css/main.css?version=1">
    <link rel="stylesheet" type="text/css" href="index.css?version=1">
    <!-- jQuery library --><script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
</head>
<body>
<section>
    <div class="signin">
        <div class="content">
            <h2>MAKE-IT-ALL</h2>
            <form class="form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="inputBox">
                    <input type="text" id="username" name="email" required> <i>Username</i>
                </div>
                <div class="inputBox">
                    <input type="password" id="password" name="password" required> <i>Password</i>
                </div>
                <div class="links">
                    <a href="php-pages/forgot_password.php">Forgot Password</a>
                    <a href="php-pages/signup.php">Signup</a>
                </div>
                <div class="inputBox">
                    <input type="submit" value="Login" id = "loginBtn">
                </div>

                <div class="errorBox">
                    <?php echo $error_message ?? ''; ?>
                </div>
            </div>
        </div>
    </div>
</section>

</body>
<script>
    // btn = document.getElementById("loginBtn")

    // btn.onclick= function (){
    //     username = document.getElementById("username").value
    //     password = document.getElementById("password").value
    //     if(username == "admin" && password == "admin"){
    //         window.location.href ='pages/manager/overview.html'
    //     }else if(username == "user" && password == "user"){
    //         window.location.href ='pages/employee/to-do-list.html'
    //     }else{

    //     }

    // }

    // check if system preference is dark colour scheme
    if (window.matchMedia("(prefers-color-scheme: dark)").matches) {
        // set to dark theme
        $("html").attr("data-theme", "dark")
    }

    // check if system preference changes
    window.matchMedia("(prefers-color-scheme: dark)").addEventListener('change', event => {
        const theme = event.matches ? "dark" : "light";
        $("html").attr("data-theme", theme)
    });
</script>
</html>