<?php
session_start();
require 'database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $email = $_POST['email'];


    $stmt = $pdo->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
    if ($stmt->execute([$username, $password, $email])) {
        echo "Registration successful!";
    } else {
        echo "Registration failed!";
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="./css/register-style.css">

</head>

<body>
    <?php include 'includes/header.php'; ?>

    <h2>Register Here</h2>
    <form method="POST">
        <section>
            <label for="username"> Username: </label>
            <input type="text" name="username" id="username">
        </section>
        <section>
            <label for="password"> Password: </label>
            <input type="password" name="password" id="password">
        </section>
        <section>
            <label for="Email"> Email: </label>
            <input type="email" name="email"
                </section>
            <section>
                <input type="submit" value="Register">
            </section>
    </form>

    <?php include 'includes/footer.php'; ?>

</body>

</html>