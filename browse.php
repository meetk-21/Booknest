<?php
require 'database.php';

$search = $_GET['search'] ?? '';
$stmt = $pdo->prepare("SELECT * FROM books WHERE title LIKE ? OR author LIKE ?");
$stmt->execute(["%$search%", "%$search%"]);
$books = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browsing</title>
    <link rel="stylesheet" href="./css/browse-style.css">
</head>

<body>
    <?php include 'includes/header.php'; ?>

    <?php if (isset($_GET['success']) && $_GET['success'] === 'review'): ?>
        <div class="success-message">Your review has been submitted successfully!</div>
    <?php endif; ?>

    <form method="GET">
        Search: <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>">
        <input type="submit" value="Search">
    </form>

    <h2>Books</h2>
    <ul>
        <?php foreach ($books as $book): ?>
            <li>
                <strong><?php echo htmlspecialchars($book['title']); ?></strong> by <?php echo htmlspecialchars($book['author']); ?>
                Price: $<?php echo htmlspecialchars($book['price']); ?>
                <a href="review.php?book_id=<?php echo $book['id']; ?>">Leave a Review</a>
            </li>
        <?php endforeach; ?>
    </ul>
    <?php include 'includes/footer.php'; ?>

</body>

</html>
