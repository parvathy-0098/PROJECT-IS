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
$pass = '';

$conn = new mysqli($host, $user, $pass, $db, 3306);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission for saving review
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_name = $conn->real_escape_string($_POST['product_name']);
    $review = $conn->real_escape_string($_POST['review']);

    $sql = "INSERT INTO reviews (product_name, user_email, review) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $product_name, $email, $review);

    if ($stmt->execute()) {
        echo "<script>alert('Review submitted successfully!'); window.location.href = 'review.php';</script>";
    } else {
        echo "<script>alert('Error submitting review. Please try again later.');</script>";
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
    <title>Product Review</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f7fa;
            font-family: Arial, sans-serif;
        }

        .review-container {
            margin: 50px auto;
            width: 60%;
            background-color: white;
            padding: 30px;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        h2 {
            margin-bottom: 20px;
            text-align: center;
            color: #3498db;
        }

        .btn-submit {
            background-color: #3498db;
            color: white;
            width: 100%;
            border: none;
        }

        .btn-submit:hover {
            background-color: #2874a6;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="review-container">
            <h2>Write a Review</h2>
            <form method="POST" id="reviewForm">
                <div class="mb-3">
                    <label for="product_name" class="form-label">Product Purchase Name</label>
                    <input type="text" class="form-control" id="product_name" name="product_name" placeholder="Enter product name" required>
                </div>
                <div class="mb-3">
                    <label for="review" class="form-label">Write Review</label>
                    <textarea class="form-control" id="review" name="review" rows="5" placeholder="Write your review here..." required></textarea>
                </div>
                <button type="submit" class="btn btn-submit">Submit</button>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // JavaScript Validation
        document.getElementById('reviewForm').addEventListener('submit', function(e) {
            const productName = document.getElementById('product_name').value.trim();
            const review = document.getElementById('review').value.trim();

            if (productName === "" || review === "") {
                e.preventDefault();
                alert("All fields are required.");
            }
        });
    </script>
</body>
</html>
