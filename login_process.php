
<?php
session_start();

require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$remember = isset($_POST['remember']);

$_SESSION['login_email'] = $email;

if ($email === '') {
    $_SESSION['login_error'] = 'Please enter your email address.';
    header('Location: login.php');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['login_error'] = 'Please enter a valid email address.';
    header('Location: login.php');
    exit;
}

if ($password === '') {
    $_SESSION['login_error'] = 'Please enter your password.';
    header('Location: login.php');
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT
            id,
            name,
            email,
            password
        FROM admins
        WHERE email = ?
        LIMIT 1
    ");

    $stmt->execute([$email]);

    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$admin || !password_verify($password, $admin['password'])) {
        $_SESSION['login_error'] = 'Invalid email or password.';
        header('Location: login.php');
        exit;
    }

    session_regenerate_id(true);

    $_SESSION['admin_id'] = $admin['id'];
    $_SESSION['admin_name'] = $admin['name'];
    $_SESSION['admin_email'] = $admin['email'];
    $_SESSION['logged_in'] = true;

    if ($remember) {
        $_SESSION['remember_me'] = true;
    } else {
        unset($_SESSION['remember_me']);
    }

    unset(
        $_SESSION['login_error'],
        $_SESSION['login_email'],
        $_SESSION['login_success']
    );

    header('Location: dashboard.php');
    exit;

} catch (PDOException $e) {
    $_SESSION['login_error'] =
        'Something went wrong while signing in. Please try again.';

    header('Location: login.php');
    exit;
}

