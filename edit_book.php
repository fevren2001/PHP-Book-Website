<?php
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user']) || !$_SESSION['user']['admin']) {
    header("Location: index.php");
    exit();
}

// Retrieve the book ID from the query parameters
$bookId = isset($_GET['id']) ? $_GET['id'] : null;

// Load books data
$booksJson = file_get_contents('books.json');
$booksData = json_decode($booksJson, true);

if ($bookId === null || !array_key_exists($bookId, $booksData['books'])) {
    echo "Book not found.";
    exit();
}

$book = $booksData['books'][$bookId];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $description = trim($_POST['description']);
    $cover = trim($_POST['cover']);
    $year = trim($_POST['year']);
    $source = trim($_POST['source']);
    $genre = trim($_POST['genre']);

    // Validate inputs
    if (empty($title) || empty($author) || empty($description) || empty($cover) || empty($year) || empty($source) || empty($genre)) {
        $errors[] = "All fields are required.";
    }

    if (!filter_var($year, FILTER_VALIDATE_INT)) {
        $errors[] = "Year must be a valid integer.";
    }

    // If no errors, update the book data
    if (empty($errors)) {
        $booksData['books'][$bookId] = [
            'title' => $title,
            'author' => $author,
            'description' => $description,
            'cover' => $cover,
            'year' => (int)$year,
            'source' => $source,
            'rating' => $book['rating'], // Keep the existing rating and ratings count
            'ratings' => $book['ratings'],
            'reviews' => $book['reviews'],
            'genre' => $book['genre']
        ];

        file_put_contents('books.json', json_encode($booksData, JSON_PRETTY_PRINT));
        header("Location: details.php?id=$bookId");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Book</title>
    <link rel="stylesheet" href="styles/main.css">
</head>
<body>
    <header>
        <h1><a href="index.php">IK-Library</a> > Edit Book</h1>
    </header>
    <div id="content">
        <h2>Edit Book: <?= htmlspecialchars($book['title']) ?></h2>
        <?php if (!empty($errors)): ?>
            <div class="errors">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <form method="post" action="edit_book.php?id=<?= htmlspecialchars($bookId) ?>">
            <label for="title">Title:</label>
            <input type="text" name="title" id="title" value="<?= htmlspecialchars($book['title']) ?>" required><br><br>
            <label for="author">Author:</label>
            <input type="text" name="author" id="author" value="<?= htmlspecialchars($book['author']) ?>" required><br><br>
            <label for="description">Description:</label>
            <textarea name="description" id="description" required><?= htmlspecialchars($book['description']) ?></textarea><br><br>
            <label for="cover">Cover URL:</label>
            <input type="text" name="cover" id="cover" value="<?= htmlspecialchars($book['cover']) ?>" required><br><br>
            <label for="year">Year:</label>
            <input type="text" name="year" id="year" value="<?= htmlspecialchars($book['year']) ?>" required><br><br>
            <label for="source">Source:</label>
            <input type="text" name="source" id="source" value="<?= htmlspecialchars($book['source']) ?>" required><br><br>

            <label for="genre">Genre:</label>
            <input type="text" name="genre" id="genre" value="<?= htmlspecialchars($book['genre']) ?>" required><br><br>

            <input type="submit" value="Update Book">
        </form>
    </div>
    <footer>
        <p>IK-Library | ELTE IK Webprogramming</p>
    </footer>
</body>
</html>
