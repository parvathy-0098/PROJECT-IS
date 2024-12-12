<?php
session_start();

// Redirect to login page if user is not logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}

$username = $_SESSION['username'];

// Database connection
$host = 'localhost';
$db = 'ElectroExchange';
$user = 'root';
$pass = 'sunshine';

$conn = new mysqli($host, $user, $pass, $db, 3306);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch cart details for the logged-in user
$sql = "SELECT id, product_name, product_price, user_name, contact_details, message, created_at 
        FROM cart_details WHERE username = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

// Handle form submission for removing cart items
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['remove_cart_item'])) {
    $cartItemId = $_POST['cart_item_id'];

    $removeSql = "DELETE FROM cart_details WHERE id = ?";
    $removeStmt = $conn->prepare($removeSql);
    $removeStmt->bind_param("i", $cartItemId);

    if ($removeStmt->execute()) {
        echo "<script>alert('Item removed from cart successfully!'); window.location.href = 'cart.php';</script>";
    } else {
        echo "<script>alert('Error removing item from cart.');</script>";
    }

    $removeStmt->close();
}

// Handle form submission for updating cart items
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_cart_item'])) {
    $cartItemId = $_POST['cart_item_id'];
    $updatedMessage = $_POST['message'];
    $updatedContactDetails = $_POST['contact_details'];

    $updateSql = "UPDATE cart_details SET message = ?, contact_details = ? WHERE id = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("ssi", $updatedMessage, $updatedContactDetails, $cartItemId);

    if ($updateStmt->execute()) {
        echo "<script>alert('Cart item updated successfully!'); window.location.href = 'cart.php';</script>";
    } else {
        echo "<script>alert('Error updating cart item.');</script>";
    }

    $updateStmt->close();
}

// Handle checkout submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['checkout'])) {
    // Insert cart items into the checkout table
    $insertSql = "INSERT INTO checkout (product_name, product_price, user_name, contact_details, message, created_at)
                  SELECT product_name, product_price, user_name, contact_details, message, created_at
                  FROM cart_details WHERE username = ?";

    $insertStmt = $conn->prepare($insertSql);
    $insertStmt->bind_param("s", $username);

    if ($insertStmt->execute()) {
        // Remove the cart items after checkout
        $deleteSql = "DELETE FROM cart_details WHERE username = ?";
        $deleteStmt = $conn->prepare($deleteSql);
        $deleteStmt->bind_param("s", $username);
        $deleteStmt->execute();

        echo "<script>alert('Checkout successful!'); window.location.href = 'checkout.php';</script>";
    } else {
        echo "<script>alert('Error during checkout.');</script>";
    }

    $insertStmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart - ElectroExchange</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
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

        .cart-container {
            width: 80%;
            margin: 20px auto;
        }

        .cart-items {
            display: table;
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .cart-items th, .cart-items td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }

        .cart-items th {
            background-color: #333;
            color: white;
        }

        .cart-items td {
            background-color: #f9f9f9;
        }

        .remove-btn {
            background-color: #e74c3c;
            color: white;
            padding: 6px 12px;
            border: none;
            cursor: pointer;
            border-radius: 4px;
        }

        .remove-btn:hover {
            opacity: 0.9;
        }

        .update-btn {
            background-color: #f39c12;
            color: white;
            padding: 6px 12px;
            border: none;
            cursor: pointer;
            border-radius: 4px;
        }

        .update-btn:hover {
            opacity: 0.9;
        }

        .checkout-btn {
            background-color: #3498db;
            color: white;
            padding: 12px 24px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            font-size: 18px;
            margin-top: 20px;
        }

        .checkout-btn:hover {
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
        <h1>ElectroExchange - Your Cart</h1>
        <nav>
            <a href="welcome.php">Home</a>
            <a href="shop.php">Shop</a>
            <a href="sell-product.php">Sell Product</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <div class="cart-container">
        <h2>Your Cart</h2>

        <!-- Display Cart Items -->
        <?php if ($result->num_rows > 0): ?>
            <table class="cart-items">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Price</th>
                        <th>User Name</th>
                        <th>Contact Details</th>
                        <th>Message</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                            <td>$<?php echo number_format($row['product_price'], 2); ?></td>
                            <td><?php echo htmlspecialchars($row['user_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['contact_details']); ?></td>
                            <td><?php echo htmlspecialchars($row['message']); ?></td>
                            <td>
                                <!-- Remove Button -->
                                <form method="POST" action="cart.php" style="display:inline-block;">
                                    <input type="hidden" name="cart_item_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" name="remove_cart_item" class="remove-btn">Remove</button>
                                </form>
                                <!-- Update Form -->
                                <form method="POST" action="cart.php" style="display:inline-block;">
                                    <input type="hidden" name="cart_item_id" value="<?php echo $row['id']; ?>">
                                    <input type="text" name="contact_details" value="<?php echo htmlspecialchars($row['contact_details']); ?>" placeholder="New Contact Details" required>
                                    <input type="text" name="message" value="<?php echo htmlspecialchars($row['message']); ?>" placeholder="New Message" required>
                                    <button type="submit" name="update_cart_item" class="update-btn">Update</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <!-- Checkout Button -->
            <form method="POST" action="cart.php">
                <button type="submit" name="checkout" class="checkout-btn">Proceed to Checkout</button>
            </form>
        <?php else: ?>
            <p>Your cart is empty.</p>
        <?php endif; ?>
    </div>

    <footer>
        <p>&copy; 2024 ElectroExchange. All rights reserved.</p>
    </footer>
</body>
</html>
