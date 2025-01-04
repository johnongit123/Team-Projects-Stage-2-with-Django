<?php
include "../server.php";

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


    // Query to check if the user exists
    $sql = "SELECT * FROM users WHERE Email = '$email'";
    $result = $conn->query($sql);

    // Check if the query returned a row (valid user)
    if ($result->num_rows > 0) {
        $sucsess_message = "A link has been sent to you inbox, please click it and follow the instructions.";
        // Redirect to a different page or perform other actions after successful login
    } else {
        $error_message = "No user with that email found!";
    }
}

// Close the database connection
$conn->close();

?>

<!doctype html>

<html lang="en" data-theme="light">

    <head>

        <meta charset="UTF-8">

        <title>Forgot Password</title>

        <link rel="stylesheet" href="../css/main.css">
        <link rel="stylesheet" href="../index.css">
        <!-- jQuery library --><script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    </head>

    <body>

        <section>

            <div class="signin">

                <div class="content">

                    <h2>MAKE-IT-ALL</h2>

                    <form class="form" method="post">

                        <div class="inputBox">

                            <input type="text" name="email" required> <i>Username</i>

                        </div>

                        <div class="links"> 

                            <a href="../index.php">Login</a>

                            <a href="signup.php">Signup</a>

                        </div>

                        <div class="inputBox">

                            <a href="manager/to-do-list.html">

                                <input type="submit" value="submit">

                            </a>
                            
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
    </body>
</html>