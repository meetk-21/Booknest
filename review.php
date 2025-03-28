<?php
require 'database.php';

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
}


if (isset($_POST['book_id'])) {
    $book_id = $_POST['book_id'];
    $stmt = $pdo->prepare("SELECT * FROM books WHERE id = ?");
    $stmt->execute([$book_id]);
    $book = $stmt->fetch();
    if (!$book) {
        echo "Book not found!";
        exit;
    }
} else {
    echo "Book ID is missing!";
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review</title>
</head>

<body>
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
        </select><br>
        Review: <textarea name="review" required></textarea>
        <input type="submit" value="Submit Review">
    </form>

</body>

</html>