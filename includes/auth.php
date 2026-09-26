
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (
    !isset($_SESSION['logged_in']) ||
    $_SESSION['logged_in'] !== true ||
    !isset($_SESSION['admin_id']) ||
    !isset($_SESSION['admin_name']) ||
    !isset($_SESSION['admin_email'])
) {
    header('Location: ../login.php');
    exit;
}

