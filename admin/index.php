<?php
session_start();
require '../database.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: ../login.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Booknest</title>
    <link rel="stylesheet" href="../css/admin-style.css">
</head>
<body>
    <header>
        <h1>Booknest Admin Dashboard</h1>
        <nav>
            <a href="index.php">Dashboard</a>
            <a href="books.php">Manage Books</a>
            <a href="users.php">Manage Users</a>
            <a href="reviews.php">Manage Reviews</a>
            <a href="../index.php">Back to Site</a>
            <a href="../logout.php">Logout</a>
        </nav>
    </header>

    <main>
        <section class="dashboard-summary">
            <h2>Dashboard Summary</h2>
            
            <?php
            // Get counts from database
            $bookCount = $pdo->query("SELECT COUNT(*) FROM books")->fetchColumn();
            $userCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
            $reviewCount = $pdo->query("SELECT COUNT(*) FROM reviews")->fetchColumn();
            ?>
            
            <div class="stats-container">
                <div class="stat-box">
                    <h3>Total Books</h3>
                    <p class="stat-number"><?php echo $bookCount; ?></p>
                    <a href="books.php" class="action-link">Manage Books</a>
                </div>
                
                <div class="stat-box">
                    <h3>Registered Users</h3>
                    <p class="stat-number"><?php echo $userCount; ?></p>
                    <a href="users.php" class="action-link">Manage Users</a>
                </div>
                
                <div class="stat-box">
                    <h3>Book Reviews</h3>
                    <p class="stat-number"><?php echo $reviewCount; ?></p>
                    <a href="reviews.php" class="action-link">Manage Reviews</a>
                </div>
            </div>
        </section>
        
        <section class="recent-activity">
            <h2>Recent Activity</h2>
            <div class="activity-list">
                <?php
                // Get recent reviews
                $stmt = $pdo->query("SELECT r.*, b.title as book_title, u.username 
                                    FROM reviews r 
                                    JOIN books b ON r.book_id = b.id 
                                    JOIN users u ON r.user_id = u.id 
                                    ORDER BY r.created_at DESC LIMIT 5");
                $recentReviews = $stmt->fetchAll();
                
                if (count($recentReviews) > 0) {
                    echo "<h3>Recent Reviews</h3>";
                    echo "<ul>";
                    foreach ($recentReviews as $review) {
                        echo "<li>";
                        echo "<strong>" . htmlspecialchars($review['username']) . "</strong> ";
                        echo "rated <strong>" . htmlspecialchars($review['book_title']) . "</strong> ";
                        echo "with " . htmlspecialchars($review['rating']) . " stars";
                        echo "</li>";
                    }
                    echo "</ul>";
                } else {
                    echo "<p>No recent reviews.</p>";
                }
                
                // Get recent users
                $stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC LIMIT 5");
                $recentUsers = $stmt->fetchAll();
                
                if (count($recentUsers) > 0) {
                    echo "<h3>Recent Users</h3>";
                    echo "<ul>";
                    foreach ($recentUsers as $user) {
                        echo "<li>";
                        echo "<strong>" . htmlspecialchars($user['username']) . "</strong> ";
                        echo "joined on " . date('M d, Y', strtotime($user['created_at']));
                        echo "</li>";
                    }
                    echo "</ul>";
                } else {
                    echo "<p>No recent users.</p>";
                }
                ?>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Booknest Admin Panel. All rights reserved.</p>
    </footer>
</body>
</html>
