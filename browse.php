<?php
require 'database.php';

$search = $_GET['search'] ?? '';
$stmt = $pdo->prepare("SELECT * FROM books WHERE title LIKE ? OR author LIKE ?");
$stmt->execute(["%$search%", "%$search%"]);
$books = $stmt->fetchAll();
?>

<form method="GET">
    Search: <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>">
    <input type="submit" value="Search">
</form>

<h2>Books</h2>
<ul>
    <?php foreach ($books as $book): ?>
        <li>
            <strong><?php echo htmlspecialchars($book['title']); ?></strong> by <?php echo htmlspecialchars($book['author']); ?><br>
            Price: $<?php echo htmlspecialchars($book['price']); ?><br>
            <a href="review.php?book_id=<?php echo $book['id']; ?>">Leave a Review</a>
        </li>
    <?php endforeach; ?>
</ul>