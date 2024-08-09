<?php
session_start();

// Load the books data from JSON file
$booksJson = file_get_contents('books.json');
$booksData = json_decode($booksJson, true);

// Get the book ID from the URL
$bookId = $_GET['id'] ?? null;

// Retrieve the book details from the array
$book = $booksData['books'][$bookId] ?? null;

if (!$book) {
    echo "Book not found.";
    exit;
}

// Handle review submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user']) && !$_SESSION['user']['admin']) {
    $newReview = [
        'user' => $_SESSION['user']['username'],
        'rating' => (int)$_POST['rating'],
        'review' => $_POST['review'],
        'read' => isset($_POST['read']) ? true : false
    ];

    // Append the new review to the book's reviews
    $book['reviews'][] = $newReview;

    // Calculate the new average rating and update the number of ratings
    $totalRating = 0;
    $reviewCount = 0;
    foreach ($book['reviews'] as $review) {
        $totalRating += $review['rating'];
        $reviewCount++;
    }
    $book['rating'] = $totalRating / $reviewCount;
    $book['ratings'] = $reviewCount;

    // Update the book data in the array
    $booksData['books'][$bookId] = $book;

    // Save the updated books data back to the JSON file
    file_put_contents('books.json', json_encode($booksData, JSON_PRETTY_PRINT));

    // Redirect to avoid form resubmission
    header("Location: details.php?id=" . htmlspecialchars($bookId));
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IK-Library | <?= htmlspecialchars($book['title']) ?></title>
    <link rel="stylesheet" href="styles/main.css">
    <link rel="stylesheet" href="styles/details.css">
</head>
<body>
    <header>
        <h1><a href="index.php">IK-Library</a> > <?= htmlspecialchars($book['title']) ?></h1>
        <div id="user-links">
            <?php if (isset($_SESSION['user'])): ?>
                <a href="profile.php"><?php echo htmlspecialchars($_SESSION['user']['username']); ?></a>
                <a href="logout.php">Logout</a>
                <?php if ($_SESSION['user']['admin']): ?>
                    <a href="add_book.php">Add New Book</a>
                <?php endif; ?>
            <?php else: ?>
                <a href="login.php">Login</a>
            <?php endif; ?>
        </div>
    </header>
    <div id="content">
        <div class="book-detail">
            <img src="<?= htmlspecialchars($book['cover']) ?>" alt="<?= htmlspecialchars($book['title']) ?>">
            <h2><?= htmlspecialchars($book['title']) ?></h2>
            <p><strong>Author:</strong> <?= htmlspecialchars($book['author']) ?></p>
            <p><strong>Description:</strong> <?= htmlspecialchars($book['description']) ?></p>
            <p><strong>Year of Publication:</strong> <?= htmlspecialchars($book['year']) ?></p>
            <p><strong>Source Planet:</strong> <?= htmlspecialchars($book['source']) ?></p>
            <p><strong>Average Rating:</strong> <?= htmlspecialchars($book['rating']) ?> (<?= htmlspecialchars($book['ratings']) ?> ratings)</p>
        </div>

        <?php if (isset($_SESSION['user']) && !$_SESSION['user']['admin']): ?>
            <div class="review-form">
                <h3>Rate this book</h3>
                <form method="post">
                    <label for="rating">Rating:</label>
                    <input type="number" name="rating" id="rating" min="1" max="5" required><br>
                    <label for="review">Review:</label>
                    <textarea name="review" id="review" required></textarea><br>
                    <label for="read">Mark as read:</label>
                    <input type="checkbox" name="read" id="read"><br>
                    <input type="submit" value="Submit Review">
                </form>
            </div>
        <?php endif; ?>

        <div class="reviews">
            <h3>Reviews</h3>
            <?php if (!empty($book['reviews'])): ?>
                <ul>
                    <?php foreach ($book['reviews'] as $review): ?>
                        <li>
                            <p><strong><?= htmlspecialchars($review['user']) ?>:</strong> <?= htmlspecialchars($review['review']) ?> (Rating: <?= htmlspecialchars($review['rating']) ?>)</p>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No reviews yet.</p>
            <?php endif; ?>
        </div>
    </div>
    <footer>
        <p>IK-Library | ELTE IK Webprogramming</p>
    </footer>
</body>
</html>
