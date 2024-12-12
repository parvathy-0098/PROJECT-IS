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
$pass = '';

$conn = new mysqli($host, $user, $pass, $db, 3306);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch products added by the logged-in user
$sql = "SELECT id, product_name, product_description, product_category, product_price, product_image FROM products WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

// Handle product deletion
if (isset($_GET['delete'])) {
    $productId = $_GET['delete'];
    $deleteSql = "DELETE FROM products WHERE id = ?";
    $deleteStmt = $conn->prepare($deleteSql);
    $deleteStmt->bind_param("i", $productId);
    if ($deleteStmt->execute()) {
        echo "<script>alert('Product deleted successfully!'); window.location.href = 'see-your-products.php';</script>";
    } else {
        echo "<script>alert('Error deleting product.');</script>";
    }
}

// Handle product update
if (isset($_GET['edit'])) {
    $productId = $_GET['edit'];
    header("Location: update-product.php?id=$productId");
    exit();
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Products - ElectroExchange</title>
    
    <style>
        /* Basic Styling */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7fa;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
        }

        header {
            background-color: #333;
            color: white;
            padding: 15px 0;
            text-align: center;
        }

        header img {
            width: 50px;
            vertical-align: middle;
        }

        nav {
            margin-top: 15px;
        }

        nav a {
            color: white;
            margin: 0 15px;
            text-decoration: none;
            font-size: 18px;
            text-transform: uppercase;
        }

        nav a:hover {
            text-decoration: underline;
        }

        .product-list {
            margin-top: 20px;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }

        .product-list h2 {
            text-align: center;
            color: #333;
        }

        .products {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .product-card {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .product-card img {
            width: 100%;
            max-width: 250px;
            height: auto;
            margin-bottom: 15px;
            border-radius: 8px;
        }

        .product-card h3 {
            font-size: 22px;
            margin-bottom: 10px;
            color: #333;
        }

        .product-card p {
            font-size: 16px;
            margin: 5px 0;
            color: #555;
        }

        .product-actions {
            margin-top: 20px;
        }

        .product-actions a {
            display: inline-block;
            padding: 8px 15px;
            margin: 5px;
            text-decoration: none;
            font-size: 16px;
            border-radius: 5px;
        }

        .delete-btn {
            background-color: #e74c3c;
            color: white;
        }

        .update-btn {
            background-color: #3498db;
            color: white;
        }

        .product-actions a:hover {
            opacity: 0.8;
        }

        footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 10px 0;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Navigation Bar -->
        <header>
            <img src="logo.jpg" alt="ElectroExchange Logo" class="logo">
            <nav>
                <a href="welcome.php">Home</a>
                <a href="sell-product.php">Sell Product</a>
                <a href="about-us.php">About Us</a>
                <a href="logout.php" class="logout-btn">Logout</a>
            </nav>
        </header>

        <!-- Your Products Section -->
        <div class="product-list">
            <h2>Your Products</h2>

            <?php if ($result->num_rows > 0): ?>
                <div class="products">
                    <?php while ($product = $result->fetch_assoc()): ?>
                        <div class="product-card">
                            <img src="data:image/jpeg;base64,<?php echo base64_encode($product['product_image']); ?>" alt="Product Image" class="product-image">
                            <h3><?php echo htmlspecialchars($product['product_name']); ?></h3>
                            <p><strong>Description:</strong> <?php echo htmlspecialchars($product['product_description']); ?></p>
                            <p><strong>Category:</strong> <?php echo htmlspecialchars($product['product_category']); ?></p>
                            <p><strong>Price:</strong> $<?php echo number_format($product['product_price'], 2); ?></p>
                            <div class="product-actions">
                                <a href="see-your-products.php?delete=<?php echo $product['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                                
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p>No products added yet. <a href="sell-product.php">Add a product</a></p>
            <?php endif; ?>
        </div>

        <!-- Footer -->
        <footer>
            <div class="footer-content">
                <p>&copy; 2024 ElectroExchange</p>
                <p>Contact us: info@electroexchange.com</p>
            </div>
        </footer>
    </div>
</body>
</html>
