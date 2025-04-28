<?php
session_start();
require '../database.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: ../login.php");
    exit();
}

// Handle review deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $review_id = $_GET['delete'];
    
    $stmt = $pdo->prepare("DELETE FROM reviews WHERE id = ?");
    if ($stmt->execute([$review_id])) {
        $success_message = "Review deleted successfully!";
    } else {
        $error_message = "Failed to delete review.";
    }
}

// Handle review approval/rejection
if (isset($_GET['approve']) && is_numeric($_GET['approve'])) {
    $review_id = $_GET['approve'];
    
    $stmt = $pdo->prepare("UPDATE reviews SET approved = 1 WHERE id = ?");
    if ($stmt->execute([$review_id])) {
        $success_message = "Review approved successfully!";
    } else {
        $error_message = "Failed to approve review.";
    }
}

if (isset($_GET['reject']) && is_numeric($_GET['reject'])) {
    $review_id = $_GET['reject'];
    
    $stmt = $pdo->prepare("UPDATE reviews SET approved = 0 WHERE id = ?");
    if ($stmt->execute([$review_id])) {
        $success_message = "Review rejected successfully!";
    } else {
        $error_message = "Failed to reject review.";
    }
}

// Get all reviews with book and user information
$stmt = $pdo->query("SELECT r.*, b.title as book_title, u.username 
                     FROM reviews r 
                     JOIN books b ON r.book_id = b.id 
                     JOIN users u ON r.user_id = u.id 
                     ORDER BY r.created_at DESC");
$reviews = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Reviews - Booknest Admin</title>
    <link rel="stylesheet" href="../css/admin-style.css">
</head>
<body>
    <header>
        <h1>Booknest Admin Dashboard</h1>
        <nav>
            <a href="index.php">Dashboard</a>
            <a href="books.php">Manage Books</a>
            <a href="users.php">Manage Users</a>
            <a href="reviews.php" class="active">Manage Reviews</a>
            <a href="../index.php">Back to Site</a>
            <a href="../logout.php">Logout</a>
        </nav>
    </header>

    <main>
        <?php if (isset($success_message)): ?>
            <div class="success-message"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        
        <section class="review-list">
            <h2>Review Management</h2>
            <?php if (count($reviews) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Book</th>
                            <th>User</th>
                            <th>Rating</th>
                            <th>Review</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reviews as $review): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($review['id']); ?></td>
                                <td><?php echo htmlspecialchars($review['book_title']); ?></td>
                                <td><?php echo htmlspecialchars($review['username']); ?></td>
                                <td><?php echo htmlspecialchars($review['rating']); ?> / 5</td>
                                <td class="review-text"><?php echo htmlspecialchars($review['review']); ?></td>
                                <td>
                                    <?php if (isset($review['approved']) && $review['approved'] == 1): ?>
                                        <span class="status-approved">Approved</span>
                                    <?php elseif (isset($review['approved']) && $review['approved'] == 0): ?>
                                        <span class="status-rejected">Rejected</span>
                                    <?php else: ?>
                                        <span class="status-pending">Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo isset($review['created_at']) ? date('M d, Y', strtotime($review['created_at'])) : 'N/A'; ?></td>
                                <td>
                                    <?php if (!isset($review['approved']) || $review['approved'] != 1): ?>
                                        <a href="reviews.php?approve=<?php echo $review['id']; ?>" class="btn-approve">Approve</a>
                                    <?php endif; ?>
                                    
                                    <?php if (!isset($review['approved']) || $review['approved'] != 0): ?>
                                        <a href="reviews.php?reject=<?php echo $review['id']; ?>" class="btn-reject">Reject</a>
                                    <?php endif; ?>
                                    
                                    <a href="reviews.php?delete=<?php echo $review['id']; ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this review?')">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No reviews found in the database.</p>
            <?php endif; ?>
        </section>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Booknest Admin Panel. All rights reserved.</p>
    </footer>
</body>
</html>


