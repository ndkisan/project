<?php
session_start(); // Start the session

// Check if the form is submitted
if (isset($_POST['add_to_cart'])) {
    // Check if the user is logged in
    if (!isset($_SESSION['name'])) {
        // Show pop-up message and redirect to login page
        echo '<script>
                alert("You need to be logged in to add products to the cart!");
                window.location.href = "login.php";
              </script>';
        exit;
    }

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
    $user_name = $_SESSION['name'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $image = $_POST['image']; // Add image data
    $description = $_POST['description']; // Add description data

    // Check if the product already exists in the cart
    $check_stmt = $conn->prepare("SELECT quantity FROM user_cart WHERE user_name = ? AND product_name = ?");
    $check_stmt->bind_param("ss", $user_name, $name);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        // Product already exists, update quantity
        $existing_row = $check_result->fetch_assoc();
        $new_quantity = $existing_row['quantity'] + $quantity;

        $update_stmt = $conn->prepare("UPDATE user_cart SET quantity = ? WHERE user_name = ? AND product_name = ?");
        $update_stmt->bind_param("iss", $new_quantity, $user_name, $name);
        if ($update_stmt->execute()) {
            // Show success message using JavaScript
            echo '<script>
                    var confirmMessage = confirm("Product quantity updated in cart! Do you want to go to your cart?");
                    if (confirmMessage) {
                        window.location.href = "mycart.php";
                    } else {
                        window.location.href = "bedroom.php";
                    }
                  </script>';
        } else {
            // Show error message
            echo "Error updating quantity: " . $update_stmt->error;
        }
    } else {
        // Product does not exist, insert new row
        $insert_stmt = $conn->prepare("INSERT INTO user_cart (user_name, product_name, product_price, quantity, image, description) VALUES (?, ?, ?, ?, ?, ?)");
        $insert_stmt->bind_param("sssiss", $user_name, $name, $price, $quantity, $image, $description);
        
        if ($insert_stmt->execute()) {
            // Show success message using JavaScript
            echo '<script>
                    var confirmMessage = confirm("Product added to cart successfully! Do you want to go to your cart?");
                    if (confirmMessage) {
                        window.location.href = "mycart.php";
                    } else {
                        window.location.href = "bedroom.php";
                    }
                  </script>';
        } else {
            // Show error message
            echo "Error inserting product: " . $insert_stmt->error;
        }
    }

    // Close statements
    $check_stmt->close();
    $insert_stmt->close();
    $conn->close();
}

// SQL query to retrieve product details including the image path
$mysqli = new mysqli("localhost", "root", "", "furniture");
$sql = "SELECT name, price, description, image FROM products1";
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
            <h2 class="logo">Your <br>Furniture <br>Store</h2>
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
                            <form action="bedroom.php" method="post">
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

            $mysqli->close();
            ?>
        </div>
    </div>
</div>

<script src="https://unpkg.com/ionicons@5.4.0/dist/ionicons.js"></script>
</body>
</html>
