<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['name'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit;
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database connection details
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "furniture";

    // Establish database connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Retrieve product details from the form
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $image = $_POST['image'];

    // Prepare and bind the SQL statement to insert product into the database
$stmt = $conn->prepare("INSERT INTO products (name, price, description, image) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $name, $price, $description, $image);

    // Execute the statement
    if ($stmt->execute()) {
        // Product added successfully
        $success_message = "Product added successfully!";
    } else {
        // Error in adding product
        $error_message = "Error: " . $stmt->error;
    }

    // Close the connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link rel="stylesheet" href="add_product.css"> <!-- Assuming you have a CSS file -->
</head>
<body>

<div class="container">
    <?php include_once('header.php');?>
    <h2>Add Product</h2>
    <?php if(isset($success_message)) { ?>
        <div class="success"><?php echo $success_message; ?></div>
    <?php } ?>
    <?php if(isset($error_message)) { ?>
        <div class="error"><?php echo $error_message; ?></div>
    <?php } ?>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label for="name">Product Name:</label><br>
        <input type="text" id="name" name="name" required><br>
        <label for="price">Price:</label><br>
        <input type="text" id="price" name="price" required><br>
        <label for="description">Description:</label><br>
        <textarea id="description" name="description" required></textarea><br>
        <label for="image">Image Path:</label><br>
        <input type="text" id="image" name="image" required><br>
        <button type="submit">Add Product</button>
    </form>
</div>

</body>
</html>
