<?php
session_start();
require 'database.php';

// Set page title for header
$page_title = "Login";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize inputs
    $username = trim(htmlspecialchars($_POST['username'] ?? '', ENT_QUOTES, 'UTF-8'));
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $error = "Please fill in all fields";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Check if user is active
            if (isset($user['active']) && $user['active'] == 0) {
                $error = "Your account has been deactivated. Please contact an administrator.";
            } else {
                // Regenerate session ID to prevent session fixation
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];

                // Store admin status in session
                $_SESSION['is_admin'] = isset($user['is_admin']) ? $user['is_admin'] : 0;

                // Set session timeout
                $_SESSION['last_activity'] = time();

                // Redirect admin users to admin panel, regular users to home page
                if ($_SESSION['is_admin']) {
                    header("Location: admin/index.php");
                } else {
                    header("Location: index.php");
                }
                exit();
            }
        } else {
            $error = "Invalid username or password";
        }
    }
}

// Add CSRF protection
$csrf_token = bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $csrf_token;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Booknest</title>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/login-style.css">
</head>

<body>
    <?php include 'includes/header.php'; ?>
    <h2>Login</h2>

    <?php if (isset($error)): ?>
        <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" autocomplete="off">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
        <section>
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" required>
        </section>
        <section>
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>
        </section>
        <section>
            <input type="submit" value="Login">
        </section>
    </form>
    <?php include 'includes/footer.php'; ?>
</body>
