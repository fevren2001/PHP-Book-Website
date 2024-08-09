<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$booksJson = file_get_contents('books.json');
$books = json_decode($booksJson, true)['books'];

$userReviews = [];
foreach ($books as $bookId => $book) {
    if (isset($book['reviews'])) {
        foreach ($book['reviews'] as $review) {
            if ($review['user'] === $_SESSION['user']['username']) {
                $userReviews[] = [
                    'book' => $book['title'],
                    'rating' => $review['rating'],
                    'review' => $review['review'],
                    'read' => $review['read']
                ];
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($_SESSION['user']['username']); ?>'s Profile</title>
    <link rel="stylesheet" href="styles/main.css">
</head>

<body>
    <header>
        <h1><?php echo htmlspecialchars($_SESSION['user']['username']); ?>'s Profile</h1>
        <div id="user-links">
            <a href="logout.php">Logout</a>
            <a href="index.php">Back to Home</a>
        </div>
    </header>
    <div id="content">
        <h2>User Information</h2>
        <p><strong>Username:</strong> <?php echo htmlspecialchars($_SESSION['user']['username']); ?></p>
        <p><strong>Last Login:</strong> <?php echo htmlspecialchars($_SESSION['user']['last_login']); ?></p>
        <p><strong>Admin:</strong> <?php echo $_SESSION['user']['admin'] ? 'Yes' : 'No'; ?></p>
        
        <h2>Your Reviews</h2>
        <?php if (empty($userReviews)): ?>
            <p>You have not reviewed any books yet.</p>
        <?php else: ?>
            <ul>
                <?php foreach ($userReviews as $review): ?>
                    <li>
                        <strong>Book:</strong> <?php echo htmlspecialchars($review['book']); ?><br>
                        <strong>Rating:</strong> <?php echo htmlspecialchars($review['rating']); ?><br>
                        <strong>Review:</strong> <?php echo htmlspecialchars($review['review']); ?><br>
                        <strong>Read:</strong> <?php echo $review['read'] ? 'Yes' : 'No'; ?><br>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
    <footer>
        <p>IK-Library | ELTE IK Webprogramming</p>
    </footer>
</body>

</html>
