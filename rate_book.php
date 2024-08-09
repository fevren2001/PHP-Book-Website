<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['admin']) {
    header("Location: login.php");
    exit();
}

$booksJson = file_get_contents('books.json');
$books = json_decode($booksJson, true)['books'];

$bookId = $_GET['id'] ?? null;

$book = $books[$bookId] ?? null;

if (!$book) {
    echo "Book not found.";
    exit;
}

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $rating = trim($_POST['rating']);
    $review = trim($_POST['review']);
    $read = isset($_POST['read']) ? true : false;

    if (empty($rating) || !is_numeric($rating)) {
        $errors[] = "Rating must be a valid number.";
    }

    if (empty($errors)) {
        $reviewData = [
            'user' => $_SESSION['user']['username'],
            'rating' => (float)$rating,
            'review' => $review,
            'read' => $read
        ];

        $books[$bookId]['reviews'][] = $reviewData;
        file_put_contents('books.json', json_encode(['books' => $books], JSON_PRETTY_PRINT));

        header("Location: profile.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rate Book</title>
    <link rel="stylesheet" href="styles/main.css">
</head>

<body>
    <header>
        <h1>Rate <?php echo htmlspecialchars($book['title']); ?></h1>
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
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?id=" . htmlspecialchars($bookId); ?>">
            <label for="rating">Rating (1-5):</label>
            <input type="text" name="rating" id="rating" required><br><br>
            <label for="review">Review:</label>
            <textarea name="review" id="review" required></textarea><br><br>
            <label for="read">Mark as Read:</label>
            <input type="checkbox" name="read" id="read"><br><br>
            <input type="submit" value="Submit Review">
        </form>
    </div>
    <footer>
        <p>IK-Library | ELTE IK Webprogramming</p>
    </footer>
</body>

</html>
