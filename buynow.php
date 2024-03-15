<?php
// Start the session
session_start();

require('fpdf/fpdf.php');

// Create PDF instance
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 12);

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

// Initialize total price variable
$total_price = 0;

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data if available
    $name = isset($_SESSION["name"]) ? $_SESSION["name"] : "";
    $address = isset($_POST["address"]) ? $_POST["address"] : "";
    $phone = isset($_POST["phone"]) ? $_POST["phone"] : "";
    $paymentMethod = isset($_POST["payment_method"]) ? $_POST["payment_method"] : "";

    // Check if all required fields are filled
    if (!empty($name) && !empty($address) && !empty($phone) && !empty($paymentMethod)) {
        // Retrieve cart data from the database for the specific user
        $user_name = $_SESSION['name'];

        $sql = "SELECT * FROM user_cart WHERE user_name = '$user_name'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // Output user information
            $pdf->SetFillColor(200, 220, 255);
            $pdf->SetFont('Arial', 'B', 14);
            $pdf->Cell(0, 10, 'User Information', 0, 1, 'C', true);
            $pdf->SetFont('Arial', '', 12);
            $pdf->Cell(0, 10, 'Name: ' . $name, 0, 1, '', true);
            $pdf->Cell(0, 10, 'Address: ' . $address, 0, 1, '', true);
            $pdf->Cell(0, 10, 'Phone: ' . $phone, 0, 1, '', true);
            $pdf->Cell(0, 10, 'Payment Method: ' . $paymentMethod, 0, 1, '', true);
            $pdf->Ln(10); // Add some space between user info and product details

            // Output product details in a table
            $pdf->SetFillColor(230, 230, 230);
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(60, 10, 'Product Name', 1, 0, 'C', true);
            $pdf->Cell(30, 10, 'Price', 1, 0, 'C', true);
            $pdf->Cell(30, 10, 'Quantity', 1, 0, 'C', true);
            $pdf->Cell(40, 10, 'Total Price', 1, 1, 'C', true);

            $pdf->SetFont('Arial', '', 12);
            while ($row = $result->fetch_assoc()) {
                $product_name = $row['product_name'];
                $product_price = intval(str_replace(',', '', $row['product_price'])); // Remove commas and convert to integer
                $product_quantity = $row['quantity'];
                $product_total_price = $product_price * $product_quantity;

                $pdf->Cell(60, 10, $product_name, 1, 0);
                $pdf->Cell(30, 10, '$' . number_format($product_price, 2), 1, 0, 'C');
                $pdf->Cell(30, 10, $product_quantity, 1, 0, 'C');
                $pdf->Cell(40, 10, '$' . number_format($product_total_price, 2), 1, 1, 'C');
                
                // Accumulate total price
                $total_price += $product_total_price;
            }

            // Output total price
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(120, 10, 'Total Price:', 1, 0, 'R', true);
            $pdf->Cell(40, 10, '$' . number_format($total_price, 2), 1, 1, 'C', true);

            // Output PDF
            if ($pdf->Output('D', 'order.pdf')) {
                echo "PDF generated successfully!";
            } else {
                echo "Error generating PDF!";
            }

            // Clear the cart after successful order
            $sql_clear_cart = "DELETE FROM user_cart WHERE user_name = '$user_name'";
            if ($conn->query($sql_clear_cart) !== TRUE) {
                // Handle error clearing the cart
                echo '<script>alert("Error placing order. Please try again later."); window.location.href = "buynow.php";</script>';
                exit();
            }
        } else {
            // Handle case when cart is empty
            echo '<script>alert("Your cart is empty."); window.location.href = "mycart.php";</script>';
        }
    } else {
        // Handle empty fields
        echo '<script>alert("Please fill all required fields."); window.location.href = "buynow.php";</script>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Buy Now - Your Furniture Store</title>
    <!-- Add your CSS link here -->
    <link rel="stylesheet" href="buynow.css">
</head>
<body>

<div class="main">
    <div class="navbar">
        <h2 class="logo">Your <br>Furniture<br> Store</h2>
        <?php include_once('header.php') ?>
    </div>


    <div class="content">
        <div class="form">
            <h2>Buy Now</h2>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <!-- Hidden input field to submit the name -->
                <input type="hidden" id="name" name="name" value="<?php echo isset($_SESSION["name"]) ? $_SESSION["name"] : ""; ?>">

                <!-- Read-only text input field to display the name -->
                <div class="textbox">
                    <input type="text" id="display_name" value="<?php echo isset($_SESSION["name"]) ? $_SESSION["name"] : ""; ?>" readonly required>
                </div>

                <div class="textbox">
                    <input type="text" id="address" name="address" placeholder="Enter Your Address:" required>
                </div>
                <div class="textbox">
                    <input type="text" id="phone" name="phone" placeholder="Enter Your Phone Number:" required>
                </div>
                <div class="textbox">
                    <select id="payment_method" name="payment_method" required>
                        <option value="" disabled selected>Select Payment Mode</option>
                        <option value="Cash On Delivery">Cash On Delivery</option>
                    </select>
                </div>
                <button type="submit" class="btnn">Check Out</button>
            </form>
        </div>
    </div>
</div>

<!-- Add your script includes here if needed -->
</body>
</html>
