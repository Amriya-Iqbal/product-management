
<?php
session_start();

$error = $_SESSION['login_error'] ?? '';
$success = $_SESSION['login_success'] ?? '';
$oldEmail = $_SESSION['login_email'] ?? '';

unset(
    $_SESSION['login_error'],
    $_SESSION['login_success'],
    $_SESSION['login_email']
);
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Admin Login - Product Manager</title>

    <!-- Bootstrap -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- Bootstrap Icons -->

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >

    <!-- Google Font -->

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap"
        rel="stylesheet"
    >

    <!-- Login CSS -->

    <link
        rel="stylesheet"
        href="assets/css/login.css"
    >

</head>

<body>

<div class="auth-page">

    <!-- =========================
         LEFT SIDE
    ========================== -->

    <div class="auth-left">

        <!-- BRAND -->

        <div class="auth-brand">

            <div class="auth-brand-icon">
                <i class="bi bi-box-seam"></i>
            </div>

            <div>

                <h4>
                    Product Manager
                </h4>

                <span>
                    Inventory Management System
                </span>

            </div>

        </div>

        <!-- INTRO -->

        <div class="auth-intro">

            <span class="auth-label">
                ADMINISTRATION
            </span>

            <h1>
                Welcome back to your
                inventory dashboard.
            </h1>

            <p>
                Sign in to manage products, monitor stock,
                organize categories and keep your inventory
                under control.
            </p>

            <div class="auth-features">

                <!-- FEATURE 1 -->

                <div class="auth-feature">

                    <div class="auth-feature-icon">
                        <i class="bi bi-box-seam"></i>
                    </div>

                    <div>

                        <strong>
                            Product Management
                        </strong>

                        <span>
                            Manage your entire product catalog.
                        </span>

                    </div>

                </div>

                <!-- FEATURE 2 -->

                <div class="auth-feature">

                    <div class="auth-feature-icon">
                        <i class="bi bi-bar-chart"></i>
                    </div>

                    <div>

                        <strong>
                            Inventory Tracking
                        </strong>

                        <span>
                            Monitor stock levels and low-stock items.
                        </span>

                    </div>

                </div>

                <!-- FEATURE 3 -->

                <div class="auth-feature">

                    <div class="auth-feature-icon">
                        <i class="bi bi-shield-check"></i>
                    </div>

                    <div>

                        <strong>
                            Secure Administration
                        </strong>

                        <span>
                            Keep your administration account protected.
                        </span>

                    </div>

                </div>

            </div>

        </div>

        <!-- COPYRIGHT -->

        <div class="auth-copyright">

            © <?= date('Y') ?> Product Manager.
            All rights reserved.

        </div>

    </div>

    <!-- =========================
         RIGHT SIDE
    ========================== -->

    <div class="auth-right">

        <div class="auth-form-container">

            <!-- MOBILE BRAND -->

            <div class="auth-mobile-brand">

                <div class="auth-brand-icon">
                    <i class="bi bi-box-seam"></i>
                </div>

                <div>

                    <h4>
                        Product Manager
                    </h4>

                    <span>
                        Admin Panel
                    </span>

                </div>

            </div>

            <!-- FORM HEADER -->

            <div class="auth-form-header">

                <span class="auth-form-label">
                    ADMIN LOGIN
                </span>

                <h2>
                    Welcome back
                </h2>

                <p>
                    Sign in to access your product management dashboard.
                </p>

            </div>

            <!-- SUCCESS MESSAGE -->

            <?php if ($success): ?>

                <div
                    class="alert alert-success auth-alert"
                    role="alert"
                >

                    <i class="bi bi-check-circle-fill"></i>

                    <span>
                        <?= htmlspecialchars($success) ?>
                    </span>

                </div>

            <?php endif; ?>

            <!-- ERROR MESSAGE -->

            <?php if ($error): ?>

                <div
                    class="alert alert-danger auth-alert"
                    role="alert"
                >

                    <i class="bi bi-exclamation-circle-fill"></i>

                    <span>
                        <?= htmlspecialchars($error) ?>
                    </span>

                </div>

            <?php endif; ?>

            <!-- =========================
                 LOGIN FORM
            ========================== -->

            <form
                action="login_process.php"
                method="POST"
                id="loginForm"
                novalidate
            >

                <!-- EMAIL -->

                <div class="auth-form-group">

                    <label
                        for="email"
                        class="form-label"
                    >
                        Email Address
                    </label>

                    <div class="auth-input">

                        <i class="bi bi-envelope"></i>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-control"
                            placeholder="Enter your email address"
                            value="<?= htmlspecialchars($oldEmail) ?>"
                            maxlength="150"
                            autocomplete="email"
                            required
                        >

                    </div>

                    <div class="invalid-feedback">
                        Please enter your email address.
                    </div>

                </div>

                <!-- PASSWORD -->

                <div class="auth-form-group">

                    <div class="d-flex justify-content-between align-items-center">

                        <label
                            for="password"
                            class="form-label mb-0"
                        >
                            Password
                        </label>

                        <a
                            href="#"
                            class="forgot-password"
                        >
                            Forgot password?
                        </a>

                    </div>

                    <div class="auth-input mt-2">

                        <i class="bi bi-lock"></i>

                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control"
                            placeholder="Enter your password"
                            autocomplete="current-password"
                            required
                        >

                        <button
                            type="button"
                            class="password-toggle"
                            id="passwordToggle"
                            aria-label="Show password"
                        >

                            <i class="bi bi-eye"></i>

                        </button>

                    </div>

                    <div class="invalid-feedback">
                        Please enter your password.
                    </div>

                </div>

                <!-- REMEMBER ME -->

                <div class="auth-login-options">

                    <div class="form-check">

                        <input
                            class="form-check-input"
                            type="checkbox"
                            id="remember"
                            name="remember"
                        >

                        <label
                            class="form-check-label"
                            for="remember"
                        >
                            Remember me
                        </label>

                    </div>

                </div>

                <!-- =========================
                     LOGIN BUTTON
                ========================== -->

                <button
                    type="submit"
                    class="btn btn-primary auth-submit"
                >

                    <span>
                        Sign In
                    </span>

                    <i class="bi bi-arrow-right"></i>

                </button>

            </form>

            <!-- =========================
                 REGISTER LINK
            ========================== -->

            <div class="auth-footer">

                <span>
                    Don't have an admin account?
                </span>

                <a href="register.php">
                    Create Account
                </a>

            </div>

        </div>

    </div>

</div>

<!-- =========================
     JAVASCRIPT
========================= -->

<script>

document.addEventListener('DOMContentLoaded', function () {

    const loginForm =
        document.getElementById('loginForm');

    const password =
        document.getElementById('password');

    const passwordToggle =
        document.getElementById('passwordToggle');


    /* =========================
       PASSWORD TOGGLE
    ========================== */

    passwordToggle.addEventListener(
        'click',
        function () {

            const icon =
                passwordToggle.querySelector('i');

            if (password.type === 'password') {

                password.type = 'text';

                icon.classList.remove('bi-eye');

                icon.classList.add('bi-eye-slash');

                passwordToggle.setAttribute(
                    'aria-label',
                    'Hide password'
                );

            } else {

                password.type = 'password';

                icon.classList.remove('bi-eye-slash');

                icon.classList.add('bi-eye');

                passwordToggle.setAttribute(
                    'aria-label',
                    'Show password'
                );

            }

        }
    );


    /* =========================
       FORM VALIDATION
    ========================== */

    loginForm.addEventListener(
        'submit',
        function (event) {

            if (!loginForm.checkValidity()) {

                event.preventDefault();

                event.stopPropagation();

            }

            loginForm.classList.add(
                'was-validated'
            );

        }
    );

});

</script>

</body>
</html>

