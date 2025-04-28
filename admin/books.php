<?php
session_start();
require '../database.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: ../login.php");
    exit();
}

// Handle book deletion
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $book_id = $_GET['delete'];
    
    // First delete any reviews for this book
    $stmt = $pdo->prepare("DELETE FROM reviews WHERE book_id = ?");
    $stmt->execute([$book_id]);
    
    // Then delete the book
    $stmt = $pdo->prepare("DELETE FROM books WHERE id = ?");
    if ($stmt->execute([$book_id])) {
        $success_message = "Book deleted successfully!";
    } else {
        $error_message = "Failed to delete book.";
    }
}

// Handle book addition/editing
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $price = floatval($_POST['price']);
    $description = trim($_POST['description']);
    $genre = trim($_POST['genre']);
    
    if (empty($title) || empty($author) || $price <= 0) {
        $error_message = "Please fill in all required fields.";
    } else {
        if (isset($_POST['book_id']) && is_numeric($_POST['book_id'])) {
            // Update existing book
            $stmt = $pdo->prepare("UPDATE books SET title = ?, author = ?, price = ?, description = ?, genre = ? WHERE id = ?");
            if ($stmt->execute([$title, $author, $price, $description, $genre, $_POST['book_id']])) {
                $success_message = "Book updated successfully!";
            } else {
                $error_message = "Failed to update book.";
            }
        } else {
            // Add new book
            $stmt = $pdo->prepare("INSERT INTO books (title, author, price, description, genre) VALUES (?, ?, ?, ?, ?)");
            if ($stmt->execute([$title, $author, $price, $description, $genre])) {
                $success_message = "Book added successfully!";
            } else {
                $error_message = "Failed to add book.";
            }
        }
    }
}

// Get book for editing if ID is provided
$edit_book = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM books WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_book = $stmt->fetch();
}

// Get all books
$stmt = $pdo->query("SELECT * FROM books ORDER BY title");
$books = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Books - Booknest Admin</title>
    <link rel="stylesheet" href="../css/admin-style.css">
</head>
<body>
    <header>
        <h1>Booknest Admin Dashboard</h1>
        <nav>
            <a href="index.php">Dashboard</a>
            <a href="books.php" class="active">Manage Books</a>
            <a href="users.php">Manage Users</a>
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
        
        <section class="book-form">
            <h2><?php echo $edit_book ? 'Edit Book' : 'Add New Book'; ?></h2>
            <form method="POST" action="books.php">
                <?php if ($edit_book): ?>
                    <input type="hidden" name="book_id" value="<?php echo $edit_book['id']; ?>">
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="title">Title:</label>
                    <input type="text" id="title" name="title" value="<?php echo $edit_book ? htmlspecialchars($edit_book['title']) : ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="author">Author:</label>
                    <input type="text" id="author" name="author" value="<?php echo $edit_book ? htmlspecialchars($edit_book['author']) : ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="price">Price ($):</label>
                    <input type="number" id="price" name="price" step="0.01" min="0" value="<?php echo $edit_book ? htmlspecialchars($edit_book['price']) : ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="genre">Genre:</label>
                    <input type="text" id="genre" name="genre" value="<?php echo $edit_book ? htmlspecialchars($edit_book['genre']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" rows="4"><?php echo $edit_book ? htmlspecialchars($edit_book['description']) : ''; ?></textarea>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn-primary"><?php echo $edit_book ? 'Update Book' : 'Add Book'; ?></button>
                    <?php if ($edit_book): ?>
                        <a href="books.php" class="btn-secondary">Cancel</a>
                    <?php endif; ?>
                </div>
            </form>
        </section>
        
        <section class="book-list">
            <h2>Book List</h2>
            <?php if (count($books) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Price</th>
                            <th>Genre</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($books as $book): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($book['id']); ?></td>
                                <td><?php echo htmlspecialchars($book['title']); ?></td>
                                <td><?php echo htmlspecialchars($book['author']); ?></td>
                                <td>$<?php echo htmlspecialchars(number_format($book['price'], 2)); ?></td>
                                <td><?php echo htmlspecialchars($book['genre'] ?? 'N/A'); ?></td>
                                <td>
                                    <a href="books.php?edit=<?php echo $book['id']; ?>" class="btn-edit">Edit</a>
                                    <a href="books.php?delete=<?php echo $book['id']; ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this book? This will also delete all reviews for this book.')">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No books found in the database.</p>
            <?php endif; ?>
        </section>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Booknest Admin Panel. All rights reserved.</p>
    </footer>
</body>
</html>
