<?php
require 'database.php';
$book_id = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['book_id'])) {
        $book_id = $_POST['book_id'];
        $user_id = 1;
        $rating = $_POST['rating'];
        $review = $_POST['review'];

        $stmt = $pdo->prepare("INSERT INTO reviews (book_id, user_id, rating, review) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$book_id, $user_id, $rating, $review])) {
            echo "Review submitted successfully!";
        } else {
            echo "Failed to submit review.";
        }
    } else {
        echo "Book ID is missing!";
    }
} else {
    //read the books from the database and display them on the page
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review</title>
    <link rel="stylesheet" href="./css/review-style.css">

</head>

<body>
    <?php include 'includes/header.php'; ?>

    <h2>Leave a Review</h2>
    <form method="POST">
        <input type="hidden" name="book_id" value="<?php echo $book_id; ?>">
        Rating:
        <select name="rating" required>
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
        </select>
        Review: <textarea name="review" required></textarea>
        <input type="submit" value="Submit Review">
    </form>

    <?php include 'includes/footer.php'; ?>

</body>

</html>