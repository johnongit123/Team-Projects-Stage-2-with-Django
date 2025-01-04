<?php
include ("../server.php");

// Create connection
$conn = connect();

// Check connection
if ($conn->connect_error) {
    echo("connection error");
  die("Connection failed: " . $conn->connect_error);
}


function isPasswordStrong($password) {
    // Regular expression pattern
    $pattern = "/^(?=.*[A-Z])(?=.*[!@#$%^&*()\-_=+\[\]{};':\"\\|,.<>\/?])(?=.*\d).{8,}$/";

    // Check if the password matches the pattern
    return preg_match($pattern, $password);
}


// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {


    // Get username and password from the form
    $uname = $_POST["user-name"];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $re_password = $_POST['re-password'];


    //login checks
    if($password != $re_password){
        $error_message = "Passwords Don't match";
    }
    else if(!str_ends_with($email, "@example.com")){
        $error_message = "Invalid email domain, email must end with @example.com";
    }
    else if(!isPasswordStrong($password)){
        $error_message = "Password doesn't meet requirements of one capital, one symbol and 8 characters";
    }else{
        // Query to check if the user exists
        $sql = "SELECT * FROM users WHERE Email = '$email' OR Username = '$uname'";
        $result = $conn->query($sql);

        // Check if the query returned a row (valid user)
        if ($result->num_rows > 0) {
            $error_message = "Sorry there is already a user with that email/username. Please Log In";
            // Redirect to a different page or perform other actions after successful login
        } else {
            $password = hash("sha3-256", $password);
            $sql = "INSERT INTO users (Username, Email, Password, UserType) VALUES ('$uname', '$email', '$password', 'User');";
            $result = $conn->query($sql);
            if($result){
                $sucsess_message = "Sign Up sucsess, once you have verified your email you will be able to log in.";
            }else{
                $error_message = "Error inserting values to database";
            }
        }
    }
    
}

// Close the database connection
$conn->close();

?>

<!doctype html>

<html lang="en" data-theme="light">

    <head>

        <meta charset="UTF-8">

        <title>Sign Up</title>
        <link rel="stylesheet" type="text/css" href="../css/main.css?version=1">
        <link rel="stylesheet" type="text/css" href="../index.css?version=1">
        <!-- jQuery library --><script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    </head>

    <body>

        <section>

            <div class="signin">

                <div class="content">

                    <h2>MAKE-IT-ALL</h2>

                    <form class="form" method="post" action="./signup.php">


                        <div class="error-text" id="email-error-text">
                            Email Exists
                        </div>
                        <div class="inputBox" id="email-input">

                            <input type="text" id="email" name="email" required> <i>Email</i>

                        </div>

                        <div class="error-text" id="user-error-text">
                            Email Exists
                        </div>
                        <div class="inputBox" id="user-input">

                            <input type="text" id="user-name" name="user-name" required> <i>User Name</i>

                        </div>

                        <div class="error-text" id="password-error-text">
                            Email Exists
                        </div>
                        <div class="inputBox" id="password-input">

                            <input type="password" id="password" name="password" required> <i>Password</i>

                        </div>

                        <div class="error-text" id="re-password-error-text">
                            Email Exists
                        </div>
                        <div class="inputBox" id="re-password-input">

                            <input type="password" id="re-password" name="re-password" required> <i>Re-Type Password</i>

                        </div>

                        <div class="links"> 

                            <a href="forgot_password.php">Forgot Password</a>

                            <a href="../index.php">Log In</a>

                        </div>

                        <div class="inputBox">

                            <input type="submit" value="Sign Up">
                                 
                        </div>

                        <div class="errorBox">
                            <?php echo $error_message ?? ''; ?>
                        </div>

                        <div class="sucsessBox">
                            <?php echo $sucsess_message ?? ''; ?>
                        </div>

                    </form>

                </div>

            </div>

        </section>
        <script>
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
        <script src="/js/signup.js"></script>
    </body>
</html>