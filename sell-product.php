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

$conn = new mysqli($host, $user, $pass, $db,3306);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productName = $_POST['product_name'];
    $productDescription = $_POST['product_description'];
    $productCategory = $_POST['product_category'];
    $productPrice = $_POST['product_price'];

    // Handle file upload
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
        $productImage = file_get_contents($_FILES['product_image']['tmp_name']);

        // Insert data into the database
        $stmt = $conn->prepare("INSERT INTO products (username, product_name, product_description, product_category, product_price, product_image) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssds", $username, $productName, $productDescription, $productCategory, $productPrice, $productImage);

        if ($stmt->execute()) {
            echo "<script>alert('Product added successfully!');</script>";
        } else {
            echo "<script>alert('Error adding product.');</script>";
        }

        $stmt->close();
    } else {
        echo "<script>alert('Error uploading image.');</script>";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sell Product - ElectroExchange</title>
   
</head>
<style>/* Example CSS for styling the page */
body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 0;
}

.container {
    width: 80%;
    margin: 0 auto;
}

header {
    background-color: black;
    padding: 20px;
    color: white;
    text-align: center;
}

header img {
    width: 50px;
    vertical-align: middle;
}

nav a {
    color: white;
    margin: 0 15px;
    text-decoration: none;
    font-size: 18px;
}

nav a:hover {
    text-decoration: underline;
}

.form-container {
    background-color: white;
    padding: 30px;
    margin-top: 20px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.form-container h2 {
    text-align: center;
}

label {
    display: block;
    margin-bottom: 5px;
}

input, textarea, select {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

.submit-btn {
    background-color: blue;
    color: white;
    padding: 12px 20px;
    border: none;
    cursor: pointer;
    width: 100%;
}

.submit-btn:hover {
    background-color: #45a049;
}

footer {
    background-color: black;
    color: white;
    text-align: center;
    padding: 10px 0;
    position: fixed;
    width: 100%;
    bottom: 0;
}
</style>
<body>
    <div class="container">
        <!-- Navigation Bar -->
        <header>
            <img src="logo.jpg" alt="ElectroExchange Logo" class="logo">
            <nav>
                <a href="welcome.php">Home</a>
                <a href="see-your-products.php">See Your Added Products</a>
                <a href="about-us.php">About Us</a>
                <a href="logout.php" class="logout-btn">Logout</a>
            </nav>
        </header>

        <!-- Sell Product Form -->
        <div class="form-container">
            <h2>Sell Your Product</h2>
            <form method="POST" enctype="multipart/form-data">
                <label for="product_name">Product Name</label>
                <input type="text" id="product_name" name="product_name" required>

                <label for="product_description">Product Description</label>
                <textarea id="product_description" name="product_description" rows="4" required></textarea>

                <label for="product_category">Product Category</label>
                <select id="product_category" name="product_category" required>
                    <option value="laptop">Laptop</option>
                    <option value="pc">PC</option>
                    <option value="mobile">Mobile</option>
                    <option value="headphones">Headphones</option>
                    <option value="electric_appliances">Electric Appliances</option>
                </select>

                <label for="product_price">Product Price</label>
                <input type="number" id="product_price" name="product_price" step="0.01" required>

                <label for="product_image">Product Image</label>
                <input type="file" id="product_image" name="product_image" accept="image/*" required>

                <button type="submit" class="submit-btn">Submit Product</button>
            </form>
        </div>
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
