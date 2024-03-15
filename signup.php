
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

// Initialize $check_stmt
$check_stmt = null;

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $name = $_POST["name"];
    $username = $_POST["username"];
    $password = $_POST["password"];
    $confirmPassword = $_POST["confirmPassword"];

    // Validate that the username is a valid email address
    if (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Error: Invalid email address. Please enter a valid email.');window.location.replace('signup.html');</script>";
    } else {
        // Password validation: At least one special character, one number, and one uppercase letter
        if (!preg_match('/[!@#$%^&*(),.?":{}|<>0-9]/', $password) || !preg_match('/[0-9]/', $password) || !preg_match('/[A-Z]/', $password)) {
            echo "<script>alert('Error: Password must contain at least one special character, one number, and one uppercase letter.');window.location.replace('signup.html');</script>";
        } else {
            // Check if passwords match
            if ($password != $confirmPassword) {
                echo "<script>alert('Error: Password and confirm password do not match.');window.location.replace('signup.html');</script>";
            } else {
                // Check if the username (email) already exists
                $check_username_query = "SELECT * FROM users WHERE user_username = ?";
                $check_stmt = $conn->prepare($check_username_query);
                $check_stmt->bind_param("s", $username);
                $check_stmt->execute();
                $result = $check_stmt->get_result();

                if ($result->num_rows > 0) {
                    // Username already exists, display a pop-up message
                    echo "<script>alert('Error: Username already exists. Choose a different username.');window.location.replace('signup.html');</script>";
                } else {
                    // Prepare and bind the SQL statement for registration
                    $stmt = $conn->prepare("INSERT INTO users (user_name, user_username, user_password) VALUES (?, ?, ?)");
                    $stmt->bind_param("sss", $name, $username, $password);

                    // Execute the registration statement
                    if ($stmt->execute()) {
                        // Registration successful, display a pop-up message and redirect to login.html
                        echo "<script>alert('Registration successful!'); window.location.replace('login.html');</script>";
                        exit(); // Make sure to exit after the header redirection
                    } else {
                        // Display an error message as a pop-up
                        echo "<script>alert('Error: " . $stmt->error . "');window.location.replace('signup.html');</script>";
                    }
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Sign Up - Your Furniture Store</title>
    <link rel="stylesheet" href="signup.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script>
        function togglePassword() {
            var passwordInput = document.getElementById("signup-password");

            if (passwordInput.type === "password") {
                passwordInput.type = "text";
            } else {
                passwordInput.type = "password";
            }
        }
        
        function toggleConfirmPassword() {
            var passwordInput = document.getElementById("signup-confirm-password");

            if (passwordInput.type === "password") {
                passwordInput.type = "text";
            } else {
                passwordInput.type = "password";
            }
        }
     </script>
</head>
<body>

    <div class="main">
        <div class="navbar">
            <div class="icon">
                <h2 class="logo">Your Furniture Store</h2>
            </div>

            <div class="menu">
                <ul>
                    <li><a href="index.php">HOME</a></li>
                    <li><a href="menu.html">LIVING</a></li>
                    <li><a href="bedroom.php">BEDROOM</a></li>
                    <li><a href="dining.php">DINING</a></li>
                    <li><a href="#">ABOUT US</a></li>
                    <li><a href="cart.php">MY CART</a></li>
                    <li><a href="login.php">LOGIN</a></li>
                    
                </ul>
            </div>
        </div>

        <div class="content">
            <div class="form">
                <h2>Sign Up</h2>
                <form method="post" action="signup.php">
                    <div class="textbox">
                        <input type="text" id="signup-name" name="name" placeholder="Enter Your Name:" required>
                    </div>
                    <div class="textbox">
                        <input type="text" id="signup-username" name="username" placeholder="Enter Your Email:" required>
                    </div>
                    <div class="textbox">
                        <input type="password" id="signup-password" name="password" placeholder="Enter Password:" required>
                        <span class="eye-icon fas fa-eye" onclick="togglePassword()"></span>
                    </div>
                    <div class="textbox">
                        <input type="password" id="signup-confirm-password" name="confirmPassword" placeholder="Confirm Password:" required>    
                        <span class="eye-icon fas fa-eye" onclick="toggleConfirmPassword()"></span>
                    
                    </div>
                    <button type="submit" class="btnn">Sign Up</button>

                    <p class="link">Already have an account?<br>
                        <a href="login.html">Login</a> here</p>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
    <script src="https://unpkg.com/ionicons@5.4.0/dist/ionicons.js"></script>
</body>
</html>
