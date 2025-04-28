<?php
session_start();
require 'database.php';

// Set page title for header
$page_title = "Register";

// Initialize variables
$username = '';
$email = '';
$error_messages = [];
$success_message = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error_messages[] = "Security validation failed. Please try again.";
    } else {
        // Sanitize and validate inputs
        $username = trim(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
        $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
        $password = trim($_POST['password'] ?? '');
        $confirm_password = trim($_POST['confirm_password'] ?? '');

        // Validate username
        if (empty($username)) {
            $error_messages[] = "Username is required.";
        } elseif (strlen($username) < 3 || strlen($username) > 30) {
            $error_messages[] = "Username must be between 3 and 30 characters.";
        } else {
            // Check if username already exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->fetch()) {
                $error_messages[] = "Username already exists. Please choose another one.";
            }
        }

        // Validate email
        if (empty($email)) {
            $error_messages[] = "Email is required.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error_messages[] = "Please enter a valid email address.";
        } else {
            // Check if email already exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $error_messages[] = "Email already registered. Please use another email or <a href='login.php'>login</a>.";
            }
        }

        // Validate password
        if (empty($password)) {
            $error_messages[] = "Password is required.";
        } elseif (strlen($password) < 8) {
            $error_messages[] = "Password must be at least 8 characters long.";
        } elseif (!preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/[0-9]/', $password)) {
            $error_messages[] = "Password must include at least one uppercase letter, one lowercase letter, and one number.";
        }

        // Validate password confirmation
        if ($password !== $confirm_password) {
            $error_messages[] = "Passwords do not match.";
        }

        // If no errors, proceed with registration
        if (empty($error_messages)) {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert user into database
            $stmt = $pdo->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");

            if ($stmt->execute([$username, $hashed_password, $email])) {
                // Registration successful
                $success_message = "Registration successful! You can now <a href='login.php'>login</a> to your account.";

                // Clear form data
                $username = '';
                $email = '';
            } else {
                $error_messages[] = "Registration failed. Please try again later.";
            }
        }
    }
}

// Generate CSRF token
$csrf_token = bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $csrf_token;
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Register for a Booknest account to access book reviews and more">
    <title><?php echo $page_title; ?> - Booknest</title>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/register-style.css">
</head>

<body>
    <?php include 'includes/header.php'; ?>

    <main id="main-content">
        <article class="register-container">
            <header>
                <h1>Create Your Account</h1>
                <p class="subtitle">Join Booknest to discover, review, and share your favorite books</p>
            </header>

            <?php if (!empty($success_message)): ?>
                <section class="success-message" role="alert">
                    <?php echo $success_message; ?>
                </section>
            <?php else: ?>
                <?php if (!empty($error_messages)): ?>
                    <section class="error-container" role="alert">
                        <ul class="error-list">
                            <?php foreach ($error_messages as $error): ?>
                                <li><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </section>
                <?php endif; ?>

                <section class="register-form-section">
                    <form method="POST" class="register-form" novalidate>
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                        <fieldset>
                            <legend>Account Information</legend>

                            <section class="form-group">
                                <label for="username">Username <span class="required" aria-hidden="true">*</span></label>
                                <input
                                    type="text"
                                    name="username"
                                    id="username"
                                    value="<?php echo htmlspecialchars($username); ?>"
                                    required
                                    aria-required="true"
                                    autocomplete="username"
                                    minlength="3"
                                    maxlength="30">
                                <p class="form-hint">Choose a username between 3-30 characters</p>
                            </section>

                            <section class="form-group">
                                <label for="email">Email Address <span class="required" aria-hidden="true">*</span></label>
                                <input
                                    type="email"
                                    name="email"
                                    id="email"
                                    value="<?php echo htmlspecialchars($email); ?>"
                                    required
                                    aria-required="true"
                                    autocomplete="email">
                                <p class="form-hint">We'll never share your email with anyone else</p>
                            </section>
                        </fieldset>

                        <fieldset>
                            <legend>Security</legend>

                            <section class="form-group password-group">
                                <label for="password">Password <span class="required" aria-hidden="true">*</span></label>
                                <div class="password-input-wrapper">
                                    <input
                                        type="password"
                                        name="password"
                                        id="password"
                                        required
                                        aria-required="true"
                                        autocomplete="new-password"
                                        minlength="8">
                                    <button type="button" class="password-toggle" aria-label="Show password" data-show="false">
                                        <span class="password-toggle-icon" aria-hidden="true">👁️</span>
                                    </button>
                                </div>
                                <p class="form-hint">Password must be at least 8 characters and include uppercase, lowercase, and numbers</p>
                            </section>

                            <section class="form-group password-group">
                                <label for="confirm_password">Confirm Password <span class="required" aria-hidden="true">*</span></label>
                                <div class="password-input-wrapper">
                                    <input
                                        type="password"
                                        name="confirm_password"
                                        id="confirm_password"
                                        required
                                        aria-required="true"
                                        autocomplete="new-password">
                                    <button type="button" class="password-toggle" aria-label="Show password" data-show="false">
                                        <span class="password-toggle-icon" aria-hidden="true">👁️</span>
                                    </button>
                                </div>
                                <p class="form-hint">Re-enter your password to confirm</p>
                            </section>
                        </fieldset>

                        <section class="form-actions">
                            <button type="submit" class="submit-btn">Create Account</button>
                        </section>
                    </form>

                    <section class="login-link">
                        <p>Already have an account? <a href="login.php">Log in here</a></p>
                    </section>
                </section>
            <?php endif; ?>
        </article>
    </main>

    <?php include 'includes/footer.php'; ?>

</html>