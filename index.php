<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IK-Library | Home</title>
    <link rel="stylesheet" href="styles/main.css">
    <link rel="stylesheet" href="styles/cards.css">
</head>

<body>
    <header>
        <h1><a href="index.php">IK-Library</a> > Home</h1>
        <div id="user-links">
            <?php if (isset($_SESSION['user'])): ?>
                <a href="profile.php"><?php echo htmlspecialchars($_SESSION['user']['username']); ?></a>
                <a href="logout.php">Logout</a>
                <?php if ($_SESSION['user']['admin']): ?>
                    <a href="add_book.php">Add New Book</a>
                <?php endif; ?>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
        </div>
    </header>
    <div id="content">
        <form method="GET" action="index.php">
            <label for="genre">Filter by genre:</label>
            <select name="genre" id="genre" onchange="this.form.submit()">
                <option value="">All</option>
                <?php
                    // Get all genres from the books
                    $booksJson = file_get_contents('books.json');
                    $books = json_decode($booksJson, true)['books'];
                    $genres = array_unique(array_column($books, 'genre'));
                    foreach ($genres as $genre) {
                        echo '<option value="' . htmlspecialchars($genre) . '" ' . (isset($_GET['genre']) && $_GET['genre'] == $genre ? 'selected' : '') . '>' . htmlspecialchars($genre) . '</option>';
                    }
                ?>
            </select>
        </form>
        <div id="card-list">
            <?php
                // Filter books by selected genre
                $selectedGenre = isset($_GET['genre']) ? $_GET['genre'] : '';
                foreach ($books as $id => $book) {
                    if ($selectedGenre && $book['genre'] != $selectedGenre) {
                        continue;
                    }
                    echo '<div class="book-card">';
                    echo '<div class="image"><img src="' . htmlspecialchars($book['cover']) . '" alt=""></div>';
                    echo '<div class="details"><h2><a href="details.php?id=' . htmlspecialchars($id) . '">' . htmlspecialchars($book['title']) . '</a></div>';
                    if (isset($_SESSION['user']) && $_SESSION['user']['admin']) {
                        echo '<div class="edit"><a href="edit_book.php?id=' . htmlspecialchars($id) . '">Edit</a></div>';
                    } else {
                        echo '<div class="edit"><span>Edit</span></div>';
                    }
                    echo '</div>';
                }
            ?>
        </div>
    </div>
    <footer>
        <p>IK-Library | ELTE IK Webprogramming</p>
    </footer>
</body>

</html>
