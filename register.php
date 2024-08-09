<?php
session_start();

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    // Load existing users data
    $usersData = json_decode(file_get_contents('users.json'), true);

    // Validate inputs
    if (empty($username) || empty($email) || empty($password) || empty($confirmPassword)) {
        $errors[] = "All fields are required.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email address.";
    }

    foreach ($usersData['users'] as $user) {
        if ($user['username'] === $username) {
            $errors[] = "Username is already taken.";
            break;
        }
    }

    if ($password !== $confirmPassword) {
        $errors[] = "Passwords do not match.";
    }

    // If no errors, proceed with registration
    if (empty($errors)) {
        $newUser = [
            'username' => $username,
            'email' => $email,
            'password' => $password, // Store password as plain text
            'last_login' => date('Y-m-d H:i:s'),
            'admin' => false
        ];

        $usersData['users'][] = $newUser;
        file_put_contents('users.json', json_encode($usersData, JSON_PRETTY_PRINT));

        $_SESSION['user'] = $newUser;
        header("Location: index.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="styles/main.css">
</head>
<body>
    <header>
        <h1><a href="index.php">IK-Library</a> > Register</h1>
        <div id="user-links">
            <a href="login.php">Login</a>
        </div>
    </header>
    <div id="content">
        <h2>Register</h2>
        <?php if (!empty($errors)): ?>
            <div class="errors">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <form method="post" action="register.php">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" value="<?= htmlspecialchars($username ?? '') ?>" required><br><br>
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" value="<?= htmlspecialchars($email ?? '') ?>" required><br><br>
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required><br><br>
            <label for="confirm_password">Confirm Password:</label>
            <input type="password" name="confirm_password" id="confirm_password" required><br><br>
            <input type="submit" value="Register">
        </form>
    </div>
    <footer>
        <p>IK-Library | ELTE IK Webprogramming</p>
    </footer>
</body>
</html>
