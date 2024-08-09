<?php
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['user']) || !$_SESSION['user']['admin']) {
    header("Location: login.php");
    exit();
}

// Define an array to store error messages
$errors = [];

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate form inputs
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $description = trim($_POST['description']);
    $cover = trim($_POST['cover']);
    $year = trim($_POST['year']);
    $source = trim($_POST['source']);
    $genre = trim($_POST['genre']);

    if (empty($title)) {
        $errors[] = "Title is required.";
    }
    if (empty($author)) {
        $errors[] = "Author is required.";
    }
    if (empty($description)) {
        $errors[] = "Description is required.";
    }
    if (empty($cover)) {
        $errors[] = "Cover URL is required.";
    }
    if (empty($year) || !is_numeric($year)) {
        $errors[] = "Year must be a valid number.";
    }
    if (empty($source)) {
        $errors[] = "Source is required.";
    }
    if (empty($genre)) {
        $errors[] = "Genre is required.";
    }

    // If there are no errors, add the new book to books.json
    if (empty($errors)) {
        $booksJson = file_get_contents('books.json');
        $books = json_decode($booksJson, true);
        
        $newBook = [
            'title' => $title,
            'author' => $author,
            'description' => $description,
            'cover' => $cover,
            'year' => (int)$year,
            'source' => $source,
            'rating' => 0,
            'ratings' => 0,
            'genre' => $genre
        ];

        $books['books'][] = $newBook;
        file_put_contents('books.json', json_encode($books, JSON_PRETTY_PRINT));

        header("Location: index.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Book</title>
    <link rel="stylesheet" href="styles/main.css">
</head>

<body>
    <header>
        <h1>Add New Book</h1>
        <div id="user-links">
            <a href="logout.php">Logout</a>
            <a href="index.php">Back to Home</a>
        </div>
    </header>
    <div id="content">
        <?php if (!empty($errors)): ?>
            <div class="errors">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <label for="title">Title:</label>
            <input type="text" name="title" id="title" required><br><br>
            <label for="author">Author:</label>
            <input type="text" name="author" id="author" required><br><br>
            <label for="description">Description:</label>
            <textarea name="description" id="description" required></textarea><br><br>
            <label for="cover">Cover URL:</label>
            <input type="text" name="cover" id="cover" required><br><br>
            <label for="year">Year of Publication:</label>
            <input type="text" name="year" id="year" required><br><br>
            <label for="source">Source:</label>
            <input type="text" name="source" id="source" required><br><br>

            <label for="source">Genre:</label>
            <input type="text" name="genre" id="genre" required><br><br>
            
            <input type="submit" value="Add Book">
        </form>
    </div>
    <footer>
        <p>IK-Library | ELTE IK Webprogramming</p>
    </footer>
</body>
</html>
