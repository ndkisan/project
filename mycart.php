<?php
session_start(); // Start the session

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

// Check if the user is logged in
if (!isset($_SESSION['name'])) {
    // Redirect to login page if user is not logged in
    header("Location: login.php");
    exit();
}

$user_name = $_SESSION['name']; // Retrieve user name from session

// Function to generate a unique identifier for the user's cart
function generateCartKey($user_name) {
    return 'cart_' . md5($user_name);
}

// Check if the cart session is set
$cart_key = generateCartKey($user_name);
$cart = !empty($_SESSION[$cart_key]) ? $_SESSION[$cart_key] : array();

// Function to retrieve cart items from the database
function getCartItems($conn, $user_name) {
    $cart_items = array();
    $stmt = $conn->prepare("SELECT product_name, product_price, quantity, image, description FROM user_cart WHERE user_name = ?");
    $stmt->bind_param("s", $user_name);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $cart_items[] = array(
            'name' => $row['product_name'],
            'price' => $row['product_price'],
            'quantity' => $row['quantity'],
            'image' => $row['image'],
            'description' => $row['description']
        );
    }
    $stmt->close();
    return $cart_items;
}

// Retrieve cart items from the database
$cart = getCartItems($conn, $user_name);

// Check if clear button is clicked
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['clear_index'])) {
    $index = $_POST['clear_index'];
    if (isset($cart[$index])) {
        $product_name = $cart[$index]['name'];
        $product_quantity = $cart[$index]['quantity'];
        if ($product_quantity > 1) {
            $new_quantity = $product_quantity - 1;
            $stmt = $conn->prepare("UPDATE user_cart SET quantity = ? WHERE user_name = ? AND product_name = ?");
            $stmt->bind_param("iss", $new_quantity, $user_name, $product_name);
            $stmt->execute();
            $stmt->close();
            $cart[$index]['quantity'] = $new_quantity;
        } else {
            $stmt = $conn->prepare("DELETE FROM user_cart WHERE user_name = ? AND product_name = ?");
            $stmt->bind_param("ss", $user_name, $product_name);
            $stmt->execute();
            $stmt->close();
            unset($cart[$index]);
            $cart = array_values($cart);
        }
    }
    $_SESSION[$cart_key] = $cart;
    header("Location: mycart.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>My Cart</title>
    <link rel="stylesheet" href="mycart.css">
    <style>
        /* Add margin between buttons */
        .button-container button {
            margin-right: 10px; /* Adjust the value as needed */
        }
    </style>
</head>
<body>

<div class="main">
    <div class="navbar">
        <div class="icon">
            <h2 class="logo">Your Furniture Store</h2>
        </div>
        <?php include_once('header.php') ?>
    </div>

    <div class="content">
        <div class="product-list">
            <?php if (!empty($cart)): ?>
                <?php
                $total_price = 0; // Initialize total price
                foreach ($cart as $index => $product):
                    // Remove non-numeric characters from price
                    $clean_price = preg_replace("/[^0-9.]/", "", $product["price"]);
                    // Calculate total price for this product
                    $product_total_price = is_numeric($clean_price) ? $clean_price * $product["quantity"] : 0;
                    // Accumulate total price
                    $total_price += $product_total_price;
                ?>
                    <div class="product">
                        <div class="product-image">
                            <br>
                            <?php if (!empty($product['image']) && file_exists($product['image'])): ?>
                                <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product["name"]); ?>">
                            <?php else: ?>
                                <img src="placeholder.jpg" alt="<?php echo htmlspecialchars($product["name"]); ?>">
                            <?php endif; ?>
                        </div>
                        <div class="product-description">
                            <br>
                            <h2><?php echo htmlspecialchars($product["name"]); ?></h2>
                            <h2>Price: ₹<?php echo htmlspecialchars($product["price"]); ?></h2>
                            <p><?php echo htmlspecialchars($product["description"]); ?></p>
                            <p>Quantity: <?php echo htmlspecialchars($product["quantity"]); ?></p>
                        </div>
                        <div class="button-container">
                            <!-- Clear Button -->
                            <form action="mycart.php" method="post">
                                <input type="hidden" name="clear_index" value="<?php echo $index; ?>">
                                <button type="submit" class="add-to-cart-button" name="clear">Clear</button>
                            </form>
                        </div>
                        <br>
                    </div>
                <?php endforeach; ?>
                <!-- Single Buy Now Button -->
                <div class="button-container">
                    <form action="buynow.php" method="post">
                        <?php foreach ($cart as $index => $product): ?>
                            <input type="hidden" name="product_name[]" value="<?php echo htmlspecialchars($product['name']); ?>">
                            <input type="hidden" name="product_price[]" value="<?php echo htmlspecialchars($product['price']); ?>">
                            <input type="hidden" name="product_quantity[]" value="<?php echo htmlspecialchars($product['quantity']); ?>">
                            <input type="hidden" name="product_image[]" value="<?php echo htmlspecialchars($product['image']); ?>">
                            <input type="hidden" name="product_description[]" value="<?php echo htmlspecialchars($product['description']); ?>">
                        <?php endforeach; ?>
                        <button class="add-to-cart-button" type="submit">Buy Now</button>
                    </form>
                </div>
                <br>
                <p style="font-size: 24px;">Total Price: ₹<?php echo $total_price; ?></p>
            <?php else: ?>
                <p>Your cart is empty.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://unpkg.com/ionicons@5.4.0/dist/ionicons.js"></script>
</body>
</html>
