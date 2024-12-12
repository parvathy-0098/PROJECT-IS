<?php
session_start();

// Redirect to login page if user is not logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}

$username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - ElectroExchange</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* General Styling */
        body {
            margin: 0;
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f9;
            color: #333;
        }

        .container {
            max-width: 1200px;
            margin: auto;
        }

        header {
            text-align: center;
            padding: 10px 0;
            background-color: #212121;
            color: #fff;
        }

        .logo {
            max-height: 80px;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #007BFF;
            padding: 10px 20px;
        }

        .navbar a {
            color: #fff;
            text-decoration: none;
            margin: 0 15px;
            font-weight: bold;
        }

        .navbar a:hover {
            text-decoration: underline;
        }

        .navbar .btn-sell {
            background-color: #FF5722;
            padding: 10px 15px;
            border-radius: 5px;
        }

        .navbar .btn-sell:hover {
            background-color: #E64A19;
        }

        .welcome-content {
            text-align: center;
            padding: 50px 20px;
        }

        .btn {
            display: inline-block;
            margin: 15px;
            padding: 10px 20px;
            background-color: #007BFF;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .footer {
            background-color: #212121;
            color: #fff;
            padding: 20px 0;
            text-align: center;
        }

        .footer p {
            margin: 5px 0;
        }

        .footer a {
            color: #FF5722;
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Navbar -->
        <nav class="navbar">
            <a href="welcome.php"><img src="logo.jpg" alt="ElectroExchange Logo" class="logo"></a>
            <div>
                <a href="see-your-products.php">See Your Added Products</a>
                <a href="about.php">About Us</a>
                <a href="sell-product.php" class="btn-sell">Sell Product</a>
                <a href="logout.php">Logout</a>
                <a href="review.php">Give reviews</a>
                <a href="see_review.php">See review</a>
            </div>
        </nav>

        <!-- Welcome Content -->
        <div class="welcome-content">
            <h2>Hello, <span id="userEmail"><?php echo htmlspecialchars($username); ?></span>!</h2>
            <p>Start exploring our platform to buy and sell electronics.</p>
            <a href="shop.php" class="btn">Shop Now</a>
        </div>

        <!-- Footer -->
        <footer class="footer">
            <p>Contact Us: <a href="mailto:support@electroexchange.com">support@electroexchange.com</a></p>
            <p>Phone: +1 800 123 4567</p>
            <p>&copy; 2024 ElectroExchange. All Rights Reserved.</p>
        </footer>
    </div>
</body>
</html>
