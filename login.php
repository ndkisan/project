<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "furniture";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Start the session
session_start();

// Include PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load PHPMailer autoloader
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

// Function to send a welcome email
function sendWelcomeEmail($username, $userEmail) {
    // Create a new PHPMailer instance
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'ndkisan51@gmail.com';
        $mail->Password   = 'emdsgyqnuhgnwarh'; // Update with your SMTP password
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('ndkisan51@gmail.com', 'Your Furniture Store');
        $mail->addAddress($userEmail, $username); // Add the user's email and name

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Welcome to Your Furniture Store';
        $mail->Body    = 'Dear ' . $username . ',<br>Welcome to Your Furniture Store!';

        // Send email
        if ($mail->send()) {
            echo "Message has been sent successfully!";
        } else {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } catch (Exception $e) {
        // Handle exception
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Check if the user is the admin
    if ($username === 'admin@gmail.com' && $password === 'admin123') {
        $_SESSION['name'] = "admin";
        // Redirect to add_product.php for admin
        header("Location: add_product.php");
        exit;
    }

    // Prepare and bind the SQL statement
    $stmt = $conn->prepare("SELECT * FROM users WHERE user_username = ?");
    $stmt->bind_param("s", $username);

    // Execute the statement
    $stmt->execute();

    // Get result
    $result = $stmt->get_result();

    // Check if user exists
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Check if the password is correct (you should use password_hash and password_verify for secure password handling)
        if ($password === $row['user_password']) {
            // Set session variable for successful login
            $_SESSION['name'] = $row['user_name'];
            // Send welcome email
            sendWelcomeEmail($row['name'], $row['user_username']); // Assuming email is a column in your users table
            // Redirect to index.php after successful login
            $_SESSION['user_id'] = $userId; // Set user ID based on your authentication logic
            header('Location: index.php');
            exit();
        } else {
            // Display an error message using JavaScript alert
            echo "<script>alert('Incorrect password!');</script>";
        }
    } else {
        // Display an error message using JavaScript alert
        echo "<script>alert('User not found!');</script>";
    }

    // Close statement
    $stmt->close();
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login - Your Furniture Store</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="login.css">
    <!-- Add Font Awesome CSS link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script>
        function togglePassword() {
            var passwordInput = document.getElementById("password");

            if (passwordInput.type === "password") {
                passwordInput.type = "text";
            } else {
                passwordInput.type = "password";
            }
        }

        function validateForm() {
            var username = document.getElementById("username").value;
            var password = document.getElementById("password").value;

            if (username === "" || password === "") {
                alert("Please fill out all fields");
                return false;
            }

            // You can add more complex validation logic here if needed

            return true;
        }
    </script>
</head>
<body>

    <div class="main">
        <div class="navbar">
            <div class="icon">
                <h2 class="logo">Your Furniture Store</h2>
            </div>
            <?php include_once('header.php');?>
        </div>

        <div class="content">
            <div class="form">
                <h2>Login</h2>
                <form method="post" action="login.php">
                    <div class="textbox">
                        <input type="text" id="username" name="username" placeholder="Username" required>
                    </div>
                    <div class="textbox">
                        <input type="password" id="password" name="password" placeholder="Password" required>
                        <!-- Use Font Awesome icon for the eye -->
                        <span class="eye-icon fas fa-eye" onclick="togglePassword()"></span>
                    </div>
                    <button type="submit" class="btnn">Login</button>

                    <p class="link">Don't have an account?<br>
                        <a href="signup.php">Sign up</a> here</p>
                </form>
            </div>
        </div>
    </div>

    <!-- Include Font Awesome JS script -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
</body>
</html>
