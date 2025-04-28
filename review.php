<?php
session_start();
require 'database.php';

// Set page title for header
$page_title = "Leave a Review";

// Initialize variables
$book_id = isset($_GET['book_id']) ? filter_input(INPUT_GET, 'book_id', FILTER_SANITIZE_NUMBER_INT) : "";
$success_message = "";
$error_message = "";
$book_title = "";

// Check if book_id is provided and valid
if (!empty($book_id)) {
    // Get book details
    $stmt = $pdo->prepare("SELECT title FROM books WHERE id = ?");
    $stmt->execute([$book_id]);
    $book = $stmt->fetch();

    if ($book) {
        $book_title = $book['title'];
    } else {
        $error_message = "Book not found.";
        $book_id = "";
    }
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        $error_message = "You must be logged in to submit a review. <a href='login.php'>Log in</a> or <a href='register.php'>register</a> to continue.";
    } else {
        // Sanitize and validate inputs
        $book_id = filter_input(INPUT_POST, 'book_id', FILTER_SANITIZE_NUMBER_INT);
        $user_id = $_SESSION['user_id'];
        $rating = filter_input(INPUT_POST, 'rating', FILTER_SANITIZE_NUMBER_INT);
        $review_text = filter_input(INPUT_POST, 'review', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        // Check if user has already reviewed this book
        $stmt = $pdo->prepare("SELECT id FROM reviews WHERE book_id = ? AND user_id = ?");
        $stmt->execute([$book_id, $user_id]);
        if ($stmt->fetch()) {
            $error_message = "You have already reviewed this book.";
        }
        // Validate rating
        else if ($rating < 1 || $rating > 5) {
            $error_message = "Invalid rating. Please select a rating between 1 and 5.";
        }
        // Validate review text
        else if (empty($review_text)) {
            $error_message = "Please enter your review.";
        } else {
            // Insert review into database
            $stmt = $pdo->prepare("INSERT INTO reviews (book_id, user_id, rating, review) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$book_id, $user_id, $rating, $review_text])) {
                $success_message = "Your review has been submitted successfully!";
                header("Location: browse.php?id=" . $book_id);
                exit();
            } else {
                $error_message = "Failed to submit review. Please try again later.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review - <?php echo htmlspecialchars($book_title); ?></title>
    <link rel="stylesheet" href="./css/review-style.css">

</head>

<body>
    <?php include 'includes/header.php'; ?>

    <main>
        <?php if (!empty($error_message)): ?>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <?php if (!empty($book_title)): ?>
            <h2>Leave a Review for: <?php echo htmlspecialchars($book_title); ?></h2>
            <form method="POST">
                <input type="hidden" name="book_id" value="<?php echo $book_id; ?>">

                <fieldset>
                    <label for="rating">Rating:</label>
                    <select name="rating" id="rating" required>
                        <option value="">Select Rating</option>
                        <option value="1">1 - Poor</option>
                        <option value="2">2 - Fair</option>
                        <option value="3">3 - Good</option>
                        <option value="4">4 - Very Good</option>
                        <option value="5">5 - Excellent</option>
                    </select>
                </fieldset>

                <fieldset>
                    <label for="review">Your Review:</label>
                    <textarea name="review" id="review" required></textarea>
                </fieldset>

                <input type="submit" value="Submit Review">
            </form>
        <?php else: ?>
            <p class="error-message">Please select a book to review from the <a href="browse.php">book list</a>.</p>
        <?php endif; ?>
    </main>

    <?php include 'includes/footer.php'; ?>

</body>

</html>