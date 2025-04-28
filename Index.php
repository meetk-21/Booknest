<?php
session_start();
include 'includes/config.php';

// Set page title for header
$page_title = "Welcome";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Booknest - Your online destination for discovering and reviewing books">
    <title><?php echo $page_title; ?> - Booknest</title>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/index-style.css">
</head>

<body>
    <?php include 'includes/header.php'; ?>

    <div id="main-content">
        <section class="content-section">
            <h1 class="section-title">Welcome To Booknest Book Store</h1>
            <p>Your online destination for discovering and reviewing books.</p>

            <div class="card-footer">
                <div>
                    <a href="browse.php" class="btn btn-primary">Browse Books</a>
                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <a href="register.php" class="btn btn-secondary">Sign Up</a>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </div>

    <?php include 'includes/footer.php'; ?>

</body>

</html>
