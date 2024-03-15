<?php
session_start(); // Start the session

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "furniture";

// Establish database connection
$mysqli = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_to_cart'])) {
    // Check if the user is logged in
    if (!isset($_SESSION['name'])) {
        // Redirect to login page if not logged in
        header("Location: login.php");
        exit;
    }

    // Retrieve product details from the form
    $user_name = $_SESSION['name'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $image = $_POST['image']; // Add image data
    $description = $_POST['description']; // Add description data

    // Check if the product is already in the cart
    $stmt_check = $mysqli->prepare("SELECT * FROM user_cart WHERE user_name = ? AND product_name = ?");
    $stmt_check->bind_param("ss", $user_name, $name);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        // Product already exists in the cart, update the quantity
        $row = $result_check->fetch_assoc();
        $new_quantity = $row['quantity'] + $quantity;

        // Prepare and bind the SQL statement to update the quantity
        $stmt_update = $mysqli->prepare("UPDATE user_cart SET quantity = ? WHERE user_name = ? AND product_name = ?");
        $stmt_update->bind_param("iss", $new_quantity, $user_name, $name);
        $stmt_update->execute();
    } else {
        // Product doesn't exist in the cart, insert a new row
        // Prepare and bind the SQL statement to insert cart items into the database
        $stmt_insert = $mysqli->prepare("INSERT INTO user_cart (user_name, product_name, product_price, quantity, image, description) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt_insert->bind_param("sssiss", $user_name, $name, $price, $quantity, $image, $description);
        $stmt_insert->execute();
    }

    // Close prepared statements
    $stmt_check->close();
    if (isset($stmt_update)) {
        $stmt_update->close();
    }
    if (isset($stmt_insert)) {
        $stmt_insert->close();
    }

    // Show popup message after adding to cart
    echo '<script>
            var goToCart = confirm("Product added to cart successfully! Do you want to go to your cart?");
            if (goToCart) {
                window.location.href = "mycart.php"; // Redirect to cart page
            } else {
                // Stay on the same page
            }
          </script>';
}

// SQL query to retrieve product details including the image path
$sql = "SELECT name, price, description, image FROM products";
$result = $mysqli->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Bedroom</title>
    <link rel="stylesheet" href="bedroom.css">
    <style>
        /* Add margin between buttons */
        .button-container button {
            margin-right: 10px; /* Adjust the value as needed */
        }
        .quantity-input {
            width: 70px;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-right: 10px; /* Add margin between quantity input and add to cart button */
        }
    </style>
</head>
<body>

<div class="main">
    <div class="navbar">
        <div class="icon">
            <h2 class="logo">Your <br>Furniture<br> Store</h2>
        </div>
        <?php include_once('header.php') ?>
    </div>

    <div class="content">
        <div class="product-list">
            <?php
            if ($result && $result->num_rows > 0) {
                // Output data of each row
                while ($row = $result->fetch_assoc()) {
                    ?>
                    <div class="product">
                        <div class="product-image">
                            <br>
                            <?php
                            // Display the image using the path from the database
                            if (!empty($row['image']) && file_exists($row['image'])) {
                                echo '<img src="' . $row['image'] . '" alt="' . $row["name"] . '">';
                            } else {
                                // If the file does not exist, display a placeholder image
                                echo '<img src="placeholder.jpg" alt="' . $row["name"] . '">';
                            }
                            ?>
                        </div>
                        <div class="product-description">
                            <br>
                            <h2><?php echo $row["name"]; ?></h2>
                            <h2>Price: ₹<?php echo $row["price"]; ?></h2>
                            <p><?php echo $row["description"]; ?></p>
                        </div>
                        <div class="button-container">
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                <input type="hidden" name="name" value="<?php echo htmlspecialchars($row["name"]); ?>">
                                <input type="hidden" name="price" value="<?php echo htmlspecialchars($row["price"]); ?>">
                                <input type="hidden" name="description" value="<?php echo htmlspecialchars($row["description"]); ?>">
                                <input type="hidden" name="image" value="<?php echo htmlspecialchars($row["image"]); ?>">
                                <input type="number" class="quantity-input" name="quantity" value="1" min="1">
                                <button type="submit" class="add-to-cart-button" name="add_to_cart">Add to Cart</button>
                            </form>
                            <button class="add-to-cart-button">Buy Now</button>
                        </div>
                        <br>
                    </div>
                    <?php
                }
            } else {
                echo "0 results";
            }

            // Close the result set
            $result->close();
            ?>
        </div>
    </div>
</div>

<script src="https://unpkg.com/ionicons@5.4.0/dist/ionicons.js"></script>
</body>
</html>

<?php
// Close the connection
$mysqli->close();
?>
