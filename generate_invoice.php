<?php
// Include the FPDF library
require('fpdf/fpdf.php');

// Function to generate invoice
function generateInvoice($order_id, $user_name, $address, $phone, $payment_method, $order_date)
{
    // Create a new instance of FPDF
    $pdf = new FPDF();
    $pdf->AddPage();

    // Set font for the title
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, 'Invoice', 0, 1, 'C');

    // Set font for the details
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, '', 0, 1); // Add space

    // Output order details
    $pdf->Cell(0, 10, 'Order ID: ' . $order_id, 0, 1);
    $pdf->Cell(0, 10, 'Customer Name: ' . $user_name, 0, 1);
    $pdf->Cell(0, 10, 'Address: ' . $address, 0, 1);
    $pdf->Cell(0, 10, 'Phone: ' . $phone, 0, 1);
    $pdf->Cell(0, 10, 'Payment Method: ' . $payment_method, 0, 1);
    $pdf->Cell(0, 10, 'Order Date: ' . $order_date, 0, 1);

    // Output footer
    $pdf->Cell(0, 10, '', 0, 1); // Add space
    $pdf->Cell(0, 10, 'Thank you for your purchase!', 0, 1, 'C');

    // Output the PDF
    $pdf->Output('D', 'invoice.pdf');
}

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

// Check if order_id is provided in the URL
if (isset($_GET['order_id'])) {
    // Fetch order details from the database
    $order_id = $_GET['order_id'];
    $sql = "SELECT order_id, user_name, address, phone, payment_method, order_date FROM order_checkout WHERE order_id = $order_id"; 
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Fetch the first row (assuming there is only one order with the specified ID)
        $row = $result->fetch_assoc();

        // Extract order details
        $order_id = $row["order_id"];
        $user_name = $row["user_name"];
        $address = $row["address"];
        $phone = $row["phone"];
        $payment_method = $row["payment_method"];
        $order_date = $row["order_date"];

        // Generate the invoice
        generateInvoice($order_id, $user_name, $address, $phone, $payment_method, $order_date);
    } else {
        echo "No order found with ID: $order_id";
    }
} else {
    echo "No order ID provided.";
}

// Close the database connection
$conn->close();
?>
