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

// Handle form submission for cart
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_cart'])) {
    $productName = $_POST['product_name'];
    $productPrice = $_POST['product_price'];
    $userName = $_POST['user_name'];
    $contactDetails = $_POST['contact_details'];
    $message = $_POST['message'];

    // Insert data into cart table
    $insertCartSql = "INSERT INTO cart_details (username, product_name, product_price, user_name, contact_details, message) 
                      VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insertCartSql);
    $stmt->bind_param("ssssss", $username, $productName, $productPrice, $userName, $contactDetails, $message);

    if ($stmt->execute()) {
        echo "<script>alert('Product added to cart successfully!'); window.location.href = 'shop.php';</script>";
    } else {
        echo "<script>alert('Error adding product to cart.');</script>";
    }

    $stmt->close();
}

// Search by category (if a category is selected)
$categoryFilter = '';
if (isset($_GET['category']) && !empty($_GET['category'])) {
    $categoryFilter = $_GET['category'];
}

// Fetch all products with or without category filter
$sql = "SELECT id, product_name, product_description, product_category, product_price, product_image FROM products";
if ($categoryFilter) {
    $sql .= " WHERE product_category = ?";
}

$stmt = $conn->prepare($sql);

if ($categoryFilter) {
    $stmt->bind_param("s", $categoryFilter);
}

$stmt->execute();
$result = $stmt->get_result();

// Close connection
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop - ElectroExchange</title>
    <style>
        /* Basic Styling */
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

        .product-list {
            width: 80%;
            margin: 20px auto;
        }

        .search-filter {
            margin-bottom: 20px;
            text-align: center;
        }

        .products {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .product-card {
            background-color: white;
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
        }

        .product-card h3 {
            margin-bottom: 10px;
        }

        .product-card .price {
            font-size: 18px;
            color: #e74c3c;
            font-weight: bold;
        }

        .add-to-cart-btn {
            padding: 8px 15px;
            margin-top: 10px;
            text-decoration: none;
            font-size: 16px;
            border-radius: 5px;
            background-color: #3498db;
            color: white;
            border: none;
            cursor: pointer;
        }

        .add-to-cart-btn:hover {
            opacity: 0.9;
        }

        /* Modal Styling */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: white;
            margin: 10% auto;
            padding: 20px;
            border-radius: 8px;
            width: 50%;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .modal-content h2 {
            text-align: center;
        }

        .close-btn {
            float: right;
            font-size: 28px;
            font-weight: bold;
            color: #333;
            cursor: pointer;
        }

        .close-btn:hover {
            color: red;
        }

        .modal-content form {
            display: flex;
            flex-direction: column;
        }

        .modal-content form input,
        .modal-content form textarea,
        .modal-content form button {
            margin: 10px 0;
            padding: 10px;
            font-size: 16px;
        }

        .submit-btn {
            background-color: #28a745;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }

        .submit-btn:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <header>
        <h1>ElectroExchange</h1>
        <nav>
            <a href="welcome.php">Home</a>
            <a href="sell-product.php">Sell Product</a>
            <a href="cart.php">Cart</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <div class="product-list">
        <h2>All Products</h2>
        
        <!-- Search Filter -->
        <div class="search-filter">
            <form method="GET" action="shop.php">
                <label for="category">Search by Category:</label>
                <select name="category" id="category">
                    <option value="">All</option>
                    <option value="mobile" <?php if ($categoryFilter == 'mobile') echo 'selected'; ?>>Mobile</option>
                    <option value="laptop" <?php if ($categoryFilter == 'laptop') echo 'selected'; ?>>Laptop</option>
                    <option value="electric appliance" <?php if ($categoryFilter == 'electric appliance') echo 'selected'; ?>>Electric Appliance</option>
                    <option value="headphones" <?php if ($categoryFilter == 'headphones') echo 'selected'; ?>>Headphones</option>
                </select>
                <button type="submit">Search</button>
            </form>
        </div>

        <div class="products">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($product = $result->fetch_assoc()): ?>
                    <div class="product-card">
                        <img src="data:image/jpeg;base64,<?php echo base64_encode($product['product_image']); ?>" alt="Product Image">
                        <h3><?php echo htmlspecialchars($product['product_name']); ?></h3>
                        <p><?php echo htmlspecialchars($product['product_description']); ?></p>
                        <p class="price">$<?php echo number_format($product['product_price'], 2); ?></p>
                        <button class="add-to-cart-btn" 
                                data-name="<?php echo htmlspecialchars($product['product_name']); ?>" 
                                data-price="<?php echo htmlspecialchars($product['product_price']); ?>">
                            Add to Cart
                        </button>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No products available.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal" id="cartModal">
        <div class="modal-content">
            <span class="close-btn" id="closeModal">&times;</span>
            <h2>Complete Your Cart</h2>
            <form method="POST" action="shop.php">
                <input type="text" id="product_name" name="product_name" readonly>
                <input type="text" id="product_price" name="product_price" readonly>
                <input type="text" name="user_name" placeholder="Your Name" required>
                <input type="text" name="contact_details" placeholder="Contact Details" required>
                <textarea name="message" placeholder="Message" required></textarea>
                <button type="submit" name="submit_cart" class="submit-btn">Add to Cart</button>
            </form>
        </div>
    </div>

    <script> // Modal functionality
 const modal = document.getElementById("cartModal");
 const closeModal = document.getElementById("closeModal");

 const addToCartBtns = document.querySelectorAll(".add-to-cart-btn");

 addToCartBtns.forEach(btn => {
     btn.addEventListener("click", function() {
         const productName = this.getAttribute("data-name");
         const productPrice = this.getAttribute("data-price");

         document.getElementById("product_name").value = productName;
         document.getElementById("product_price").value = productPrice;

         modal.style.display = "block";
     });
 });

 closeModal.addEventListener("click", function() {
     modal.style.display = "none";
 });

 window.addEventListener("click", function(event) {
     if (event.target == modal) {
         modal.style.display = "none";
     }
 });</script>
</body>
</html>
