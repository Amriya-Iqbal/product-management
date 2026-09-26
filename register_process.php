
<?php
session_start();

require_once 'config/database.php';

/* =========================
   ONLY POST REQUESTS
========================= */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: register.php');
    exit;
}

/* =========================
   GET FORM VALUES
========================= */

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';
$terms = isset($_POST['terms']);

/* =========================
   KEEP OLD VALUES
========================= */

$_SESSION['register_name'] = $name;
$_SESSION['register_email'] = $email;

/* =========================
   VALIDATION
========================= */

if ($name === '') {
    $_SESSION['register_error'] = 'Please enter your full name.';
    header('Location: register.php');
    exit;
}

if (strlen($name) < 2) {
    $_SESSION['register_error'] = 'Name must contain at least 2 characters.';
    header('Location: register.php');
    exit;
}

if (strlen($name) > 100) {
    $_SESSION['register_error'] = 'Name cannot exceed 100 characters.';
    header('Location: register.php');
    exit;
}

if ($email === '') {
    $_SESSION['register_error'] = 'Please enter your email address.';
    header('Location: register.php');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['register_error'] = 'Please enter a valid email address.';
    header('Location: register.php');
    exit;
}

if (strlen($email) > 150) {
    $_SESSION['register_error'] = 'Email address is too long.';
    header('Location: register.php');
    exit;
}

if ($password === '') {
    $_SESSION['register_error'] = 'Please create a password.';
    header('Location: register.php');
    exit;
}

if (strlen($password) < 6) {
    $_SESSION['register_error'] = 'Password must contain at least 6 characters.';
    header('Location: register.php');
    exit;
}

if ($password !== $confirmPassword) {
    $_SESSION['register_error'] = 'Passwords do not match.';
    header('Location: register.php');
    exit;
}

if (!$terms) {
    $_SESSION['register_error'] = 'Please agree to the terms and conditions.';
    header('Location: register.php');
    exit;
}

/* =========================
   CHECK DUPLICATE EMAIL
========================= */

try {

    $checkStmt = $pdo->prepare("
        SELECT id
        FROM admins
        WHERE email = ?
        LIMIT 1
    ");

    $checkStmt->execute([$email]);

    if ($checkStmt->fetch()) {

        $_SESSION['register_error'] =
            'An account with this email address already exists.';

        header('Location: register.php');
        exit;
    }

    /* =========================
       HASH PASSWORD
    ========================== */

    $hashedPassword = password_hash(
        $password,
        PASSWORD_DEFAULT
    );

    if ($hashedPassword === false) {

        $_SESSION['register_error'] =
            'Unable to secure your password. Please try again.';

        header('Location: register.php');
        exit;
    }

    /* =========================
       INSERT ADMIN
    ========================== */

    $insertStmt = $pdo->prepare("
        INSERT INTO admins (
            name,
            email,
            password
        )
        VALUES (?, ?, ?)
    ");

    $insertStmt->execute([
        $name,
        $email,
        $hashedPassword
    ]);

    /* =========================
       CLEAR FORM SESSION DATA
    ========================== */

    unset(
        $_SESSION['register_name'],
        $_SESSION['register_email']
    );

    /* =========================
       SUCCESS MESSAGE
    ========================== */

    $_SESSION['login_success'] =
        'Your admin account has been created successfully. Please sign in.';

    header('Location: login.php');
    exit;

} catch (PDOException $e) {

    /*
     * Do not display database errors
     * to the user in production.
     */

    $_SESSION['register_error'] =
        'Something went wrong while creating your account. Please try again.';

    header('Location: register.php');
    exit;
}


