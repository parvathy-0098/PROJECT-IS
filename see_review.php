<?php
// Start session
session_start();

// Redirect to login page if user is not logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}

// Database connection
$host = 'localhost';
$db = 'ElectroExchange';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db, 3306);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch reviews
$sql = "SELECT product_name, user_email, review, created_at FROM reviews ORDER BY created_at DESC";
$result = $conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>See Reviews</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f7fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .container {
            margin: 50px auto;
            max-width: 900px;
            padding: 20px;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        h2 {
            text-align: center;
            color: #3498db;
            margin-bottom: 30px;
        }

        .review-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 20px;
            padding: 15px;
            transition: all 0.3s ease;
        }

        .review-card:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .product-name {
            font-weight: bold;
            font-size: 1.2rem;
            color: #2c3e50;
        }

        .user-email {
            color: #7f8c8d;
            font-size: 0.9rem;
        }

        .review-text {
            margin: 10px 0;
            font-size: 1rem;
            color: #34495e;
        }

        .review-date {
            font-size: 0.8rem;
            color: #95a5a6;
            text-align: right;
        }

        .navbar {
            margin-bottom: 30px;
            background-color: #3498db;
            color: white;
        }

        .navbar a {
            color: white;
            text-decoration: none;
        }

        .navbar a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">ElectroExchange</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="review.php">Write Review</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <h2>Customer Reviews</h2>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="review-card">
                    <div class="product-name"><?php echo htmlspecialchars($row['product_name']); ?></div>
                    <div class="user-email">Reviewed by: <?php echo htmlspecialchars($row['user_email']); ?></div>
                    <div class="review-text"><?php echo htmlspecialchars($row['review']); ?></div>
                    <div class="review-date">Reviewed on: <?php echo htmlspecialchars($row['created_at']); ?></div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-center">No reviews available.</p>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
