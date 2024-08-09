<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['admin']) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $bookId = $_POST['book_id'];
    $rating = (int)$_POST['rating'];
    $reviewText = $_POST['review'];
    $read = isset($_POST['read']) ? true : false;

    $booksJson = file_get_contents('books.json');
    $books = json_decode($booksJson, true);

    if (!isset($books['books'][$bookId])) {
        echo "Book not found.";
        exit();
    }

    $review = [
        'user' => $_SESSION['user']['username'],
        'rating' => $rating,
        'review' => $reviewText,
        'read' => $read
    ];
    $books['books'][$bookId]['reviews'][] = $review;

    file_put_contents('books.json', json_encode($books, JSON_PRETTY_PRINT));

    header("Location: details.php?id=$bookId");
    exit();
}
?>
