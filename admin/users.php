<?php
session_start();
require '../database.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: ../login.php");
    exit();
}

// Handle user deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $user_id = $_GET['delete'];
    
    // Don't allow deleting yourself
    if ($user_id == $_SESSION['user_id']) {
        $error_message = "You cannot delete your own account.";
    } else {
        // First delete any reviews by this user
        $stmt = $pdo->prepare("DELETE FROM reviews WHERE user_id = ?");
        $stmt->execute([$user_id]);
        
        // Then delete the user
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        if ($stmt->execute([$user_id])) {
            $success_message = "User deleted successfully!";
        } else {
            $error_message = "Failed to delete user.";
        }
    }
}

// Handle user status toggle (active/inactive)
if (isset($_GET['toggle_status']) && is_numeric($_GET['toggle_status'])) {
    $user_id = $_GET['toggle_status'];
    
    // Don't allow deactivating yourself
    if ($user_id == $_SESSION['user_id']) {
        $error_message = "You cannot change your own status.";
    } else {
        // Get current status
        $stmt = $pdo->prepare("SELECT active FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();
        
        if ($user) {
            $new_status = $user['active'] ? 0 : 1;
            $stmt = $pdo->prepare("UPDATE users SET active = ? WHERE id = ?");
            if ($stmt->execute([$new_status, $user_id])) {
                $success_message = "User status updated successfully!";
            } else {
                $error_message = "Failed to update user status.";
            }
        } else {
            $error_message = "User not found.";
        }
    }
}

// Handle admin status toggle
if (isset($_GET['toggle_admin']) && is_numeric($_GET['toggle_admin'])) {
    $user_id = $_GET['toggle_admin'];
    
    // Don't allow removing your own admin status
    if ($user_id == $_SESSION['user_id']) {
        $error_message = "You cannot change your own admin status.";
    } else {
        // Get current status
        $stmt = $pdo->prepare("SELECT is_admin FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();
        
        if ($user) {
            $new_status = $user['is_admin'] ? 0 : 1;
            $stmt = $pdo->prepare("UPDATE users SET is_admin = ? WHERE id = ?");
            if ($stmt->execute([$new_status, $user_id])) {
                $success_message = "Admin status updated successfully!";
            } else {
                $error_message = "Failed to update admin status.";
            }
        } else {
            $error_message = "User not found.";
        }
    }
}

// Get all users
$stmt = $pdo->query("SELECT * FROM users ORDER BY username");
$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Booknest Admin</title>
    <link rel="stylesheet" href="../css/admin-style.css">
</head>
<body>
    <header>
        <h1>Booknest Admin Dashboard</h1>
        <nav>
            <a href="index.php">Dashboard</a>
            <a href="books.php">Manage Books</a>
            <a href="users.php" class="active">Manage Users</a>
            <a href="reviews.php">Manage Reviews</a>
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
        
        <section class="user-list">
            <h2>User Management</h2>
            <?php if (count($users) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Admin</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['id']); ?></td>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td>
                                    <?php if ($user['active']): ?>
                                        <span class="status-active">Active</span>
                                    <?php else: ?>
                                        <span class="status-inactive">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($user['is_admin']): ?>
                                        <span class="status-admin">Yes</span>
                                    <?php else: ?>
                                        <span>No</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo isset($user['created_at']) ? date('M d, Y', strtotime($user['created_at'])) : 'N/A'; ?></td>
                                <td>
                                    <a href="users.php?toggle_status=<?php echo $user['id']; ?>" class="btn-toggle">
                                        <?php echo $user['active'] ? 'Deactivate' : 'Activate'; ?>
                                    </a>
                                    <a href="users.php?toggle_admin=<?php echo $user['id']; ?>" class="btn-toggle">
                                        <?php echo $user['is_admin'] ? 'Remove Admin' : 'Make Admin'; ?>
                                    </a>
                                    <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                        <a href="users.php?delete=<?php echo $user['id']; ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this user? This will also delete all reviews by this user.')">Delete</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No users found in the database.</p>
            <?php endif; ?>
        </section>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Booknest Admin Panel. All rights reserved.</p>
    </footer>
</body>
</html>
