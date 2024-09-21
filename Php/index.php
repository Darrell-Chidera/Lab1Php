<?php
include 'Book.php';

session_start();

$error = ''; 
$successMessage = ''; 
$showMessage = false; 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_book'])) {
    $showMessage = true; 
    
    
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $year = trim($_POST['year']);

    
    if (empty($title) || empty($author) || empty($year)) {
        $error = 'All fields must be filled in.';
    } elseif (!is_numeric($year) || $year <= 0) {
        $error = 'Please enter a valid positive number for the year.';
    } else {
        
        try {
            $book = new Book($title, $author, $year);
        
            if (!isset($_SESSION['books'])) {
                $_SESSION['books'] = [];
            }
        
            $_SESSION['books'][] = $book;

        
            $_SESSION['success_message'] = "Book added successfully!";

        
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } catch (Exception $e) {
        
            $error = $e->getMessage();
        }
    }
}


if (isset($_SESSION['success_message'])) {
    $showMessage = true;
    $successMessage = $_SESSION['success_message'];
    unset($_SESSION['success_message']); 
}

function displayBooks($books) {
    if (empty($books)) {
        echo "<p>No books have been added yet.</p>";
    } else {
        echo "<h2>List of Books</h2>";
        echo "<table border='1'>";
        echo "<tr><th>Title</th><th>Author</th><th>Year</th></tr>";
        foreach ($books as $book) {
            if ($book instanceof Book) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($book->getTitle()) . "</td>";
                echo "<td>" . htmlspecialchars($book->getAuthor()) . "</td>";
                echo "<td>" . htmlspecialchars($book->getYear()) . "</td>";
                echo "</tr>";
            }
        }
        echo "</table>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Book Management System</title>
</head>
<body>
    <h1>Add a book</h1>
    <form method="POST">
        <input type="text" placeholder="Book title" id="title" name="title" required><br><br>
        <input type="text" placeholder="Author name" id="author" name="author" required><br><br>
        <input type="number" placeholder="Publishing year" id="year" name="year" required><br><br>
        <button type="submit" name="add_book">Add Book</button>
    </form>

    <?php if ($showMessage): ?>
        <?php if (!empty($error)): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php elseif (!empty($successMessage)): ?>
            <p class="success"><?= htmlspecialchars($successMessage) ?></p>
        <?php endif; ?>
    <?php endif; ?>

    <?php displayBooks($_SESSION['books'] ?? []); ?>
</body>
</html>