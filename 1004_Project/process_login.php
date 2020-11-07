<?php
session_start();

?>

<?php

$email = $pwd_hashed = $errorMsg = "";
$success = true;
    if(isset($_POST['login-submit'])){
    if (empty($_POST["email"])) {
        $errorMsg .= "Email is required.<br>";
        $success = false;
    } else {
        $email = sanitize_input($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errorMsg .= "Invalid email format.";
            $success = false;
        }
    }

    if (empty($_POST["pwd"])) {
        $errorMsg .= "Password are required.<br>";
        $success = false;
    } else {
        $pwd_hashed = password_hash($_POST["pwd"], PASSWORD_DEFAULT);
    }
}

authenticateUser();

function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/*
 * Helper function to authenticate the login.
 */

function authenticateUser() {
    global $fname, $lname, $email, $pwd_hashed, $errorMsg, $success;
// Create database connection.   
   // $config = parse_ini_file('../../private/db-config.ini');
  //  $conn = new mysqli($config['servername'], $config['username'],
          //  $config['password'], $config['dbname']);
    $servername = "localhost";
    $username = "root";
    $password = "kahwei";
    $dbname = "1004_Project";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection    
    if ($conn->connect_error) {
        $errorMsg = "Connection failed: " . $conn->connect_error;
        $success = false;
    } else {
        // Prepare the statement:        
        $stmt = $conn->prepare("SELECT * FROM member WHERE email=?");
        // Bind & execute the query statement:        
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            // Note that email field is unique, so should only have       
            // one row in the result set.            
            $row = $result->fetch_assoc();
            $fname = $row["fname"];
            $lname = $row["lname"];
            $pwd_hashed = $row["password"];
            
            $_SESSION['use']=$fname;
            echo $_SESSION['use'];
            
            if (!isset($_SESSION['use'])) {
                echo "You are not logged in";
            }
            else{
                echo "You are logged in";
                header("location: index.php");
                exit();
            }
            // Check if the password matches:            
            if (!password_verify($_POST["pwd"], $pwd_hashed)) {
                // Don't be too specific with the error message - hackers don't                
                // need to know which one they got right or wrong. :)               
                $errorMsg = "Email not found or password doesn't match...";
                $success = false;
            }
        } else {
            $errorMsg = "Email not found or password doesn't match...";
            $success = false;
        }
        $stmt->close();
    }
    $conn->close();
}
?>

<html>
    <head>
<?php
include "head.inc.php";
?>
    </head>
    <body>
<?php
include "nav.inc.php";
?>
        <main class="container">
        <?php
        if ($success) {
            echo "<h1>Login successful!</h1>";
            echo "<h3>Welcome back " . $fname . " " . $lname . "</h3>";
            echo "<a class=\"btn btn-success\" href=\"index.php\">Return to Home</a>";
        }
            else 
    {
            echo "<h1>Oops!</h1>";
            echo "<h3>The following input errors were detected:</h3>";
            echo "<p>" . $errorMsg . "</p>";
            echo "<a class=\"btn btn-warning\" href=\"login.php\">Return to Login</a>";
        }   
        
        ?>                  
            </main>
               
    </body>
</html>