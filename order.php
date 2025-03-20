<?php
require 'database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $book_id = $_POST['book_id'];
    $user_id = 1;  // Assume the user is logged in with user_id 1
    $quantity = $_POST['quantity'];

    $stmt = $pdo->prepare("INSERT INTO orders (user_id, book_id, quantity) VALUES (?, ?, ?)");
    if ($stmt->execute([$user_id, $book_id, $quantity])) {
        echo "Order placed successfully!";
    } else {
        echo "Failed to place order.";
    }
}

if (isset($_GET['book_id'])) {
    $book_id = $_GET['book_id'];
    $stmt = $pdo->prepare("SELECT * FROM books WHERE id = ?");
    $stmt->execute([$book_id]);
    $book = $stmt->fetch();

    if ($book) {
        echo "<h2>Order " . htmlspecialchars($book['title'] ?? 'Unknown Book') . "</h2>";
    } else {
        echo "Book not found.";
        exit;
    }
} else {
    echo "No book selected.";
    exit;
}
?>

<form method="POST">
    <input type="hidden" name="book_id" value="<?php echo htmlspecialchars($book_id); ?>">
    Quantity: <input type="number" name="quantity" min="1" required><br>
    <input type="submit" value="Place Order">
</form>