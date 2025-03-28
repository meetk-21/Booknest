<?php
require 'database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $book_id = $_POST['book_id'];
    $user_id = 1; 
    $quantity = $_POST['quantity'];

    $stmt = $pdo->prepare("INSERT INTO orders (user_id, book_id, quantity) VALUES (?, ?, ?)");
    if ($stmt->execute([$user_id, $book_id, $quantity])) {
        echo "Order placed successfully!";
    } else {
        echo "Failed to place order.";
    }
}