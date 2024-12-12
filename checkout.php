<?php
session_start();

// Redirect to login page if user is not logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}

$email = $_SESSION['username'];

// Database connection
$host = 'localhost';
$db = 'ElectroExchange';
$user = 'root';
$pass = 'sunshine';

$conn = new mysqli($host, $user, $pass, $db, 3306);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission for processing payment
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['pay_now'])) {
    // Get form values
    $username = $_POST['username'];
    $card_holder_name = $_POST['card_holder_name'];
    $card_number = $_POST['card_number'];
    $cvv_pin = $_POST['cvv_pin'];
    $expiry_date = $_POST['expiry_date'];

    // Fetch checkout details for the user
    $sql = "SELECT product_name, product_price, user_name, contact_details, message
            FROM checkout WHERE user_name = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Insert transaction details into transaction table
            $insertSql = "INSERT INTO transaction (username, product_name, product_price, user_name, contact_details, message, 
                          card_holder_name, card_number, cvv_pin, expiry_date)
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $insertStmt = $conn->prepare($insertSql);
            $insertStmt->bind_param("ssssssssss", $email, $row['product_name'], $row['product_price'], $row['user_name'],
                                    $row['contact_details'], $row['message'], $card_holder_name, $card_number, $cvv_pin, $expiry_date);

            if ($insertStmt->execute()) {
                echo "<script>alert('Payment successful!'); window.location.href = 'thankyou.php';</script>";
            } else {
                echo "<script>alert('Error processing payment.');</script>";
            }

            $insertStmt->close();
        }
    } else {
        echo "<script>alert('No checkout data found for this user.');</script>";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - ElectroExchange</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fa;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 15px 0;
        }

        header nav a {
            color: white;
            margin: 0 15px;
            text-decoration: none;
            font-size: 18px;
            text-transform: uppercase;
        }

        header nav a:hover {
            text-decoration: underline;
        }

        .checkout-container {
            width: 80%;
            margin: 20px auto;
        }

        .search-bar {
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        input[type="text"], input[type="password"], input[type="date"], input[type="number"] {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            padding: 10px 20px;
            background-color: #3498db;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 4px;
        }

        button:hover {
            opacity: 0.9;
        }

        footer {
            text-align: center;
            padding: 20px;
            background-color: #333;
            color: white;
        }
    </style>
</head>
<body>
    <header>
        <h1>ElectroExchange - Only card payment accepted</h1>
        <nav>
            <a href="welcome.php">Home</a>
            <a href="shop.php">Shop</a>
            <a href="sell-product.php">Sell Product</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <div class="checkout-container">
        <h2>Checkout</h2>

        <!-- Search User -->
        <form method="POST" action="checkout.php">
            <div class="search-bar">
                <label for="username">Enter Username:</label>
                <input type="text" id="username" name="username" placeholder="Enter User's Username" required>
                <button type="submit" name="search_user">Search</button>
            </div>
        </form>

        <!-- Display Checkout Details (if available) -->
        <?php
        if (isset($_POST['search_user'])) {
            $username = $_POST['username'];

            // Fetch checkout details for the user
            $conn = new mysqli($host, $user, $pass, $db, 3306);
            $sql = "SELECT product_name, product_price, user_name, contact_details, message 
                    FROM checkout WHERE user_name = ?";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "
                    <div class='form-group'>
                        <p><strong>Product Name:</strong> " . htmlspecialchars($row['product_name']) . "</p>
                        <p><strong>Price:</strong> $" . number_format($row['product_price'], 2) . "</p>
                        <p><strong>User Name:</strong> " . htmlspecialchars($row['user_name']) . "</p>
                        <p><strong>Contact Details:</strong> " . htmlspecialchars($row['contact_details']) . "</p>
                        <p><strong>Message:</strong> " . htmlspecialchars($row['message']) . "</p>
                    </div>
                    <form method='POST' action='checkout.php'>
                        <div class='form-group'>
                            <label for='card_holder_name'>Card Holder Name:</label>
                            <input type='text' id='card_holder_name' name='card_holder_name' required>
                        </div>
                        <div class='form-group'>
                            <label for='card_number'>Card Number:</label>
                            <input type='number' id='card_number' name='card_number' required>
                        </div>
                        <div class='form-group'>
                            <label for='cvv_pin'>CVV Pin:</label>
                            <input type='password' id='cvv_pin' name='cvv_pin' required>
                        </div>
                        <div class='form-group'>
                            <label for='expiry_date'>Expiry Date:</label>
                            <input type='date' id='expiry_date' name='expiry_date' required>
                        </div>
                        <input type='hidden' name='username' value='" . htmlspecialchars($username) . "'>
                        <button type='submit' name='pay_now'>Pay Now</button>
                    </form>";
                }
            } else {
                echo "<p>No checkout data found for this user.</p>";
            }
            $stmt->close();
            $conn->close();
        }
        ?>
    </div>

    <footer>
        <p>&copy; 2024 ElectroExchange | All rights reserved.</p>
    </footer>
</body>
</html>
