
<?php
session_start();

$error = $_SESSION['register_error'] ?? '';
$oldName = $_SESSION['register_name'] ?? '';
$oldEmail = $_SESSION['register_email'] ?? '';

unset(
    $_SESSION['register_error'],
    $_SESSION['register_name'],
    $_SESSION['register_email']
);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Create Admin Account - Product Manager</title>

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

        <div class="auth-brand">

            <div class="auth-brand-icon">
                <i class="bi bi-box-seam"></i>
            </div>

            <div>
                <h4>Product Manager</h4>
                <span>Inventory Management System</span>
            </div>

        </div>

        <div class="auth-intro">

            <span class="auth-label">
                ADMINISTRATION
            </span>

            <h1>
                Manage your inventory
                with confidence.
            </h1>

            <p>
                Create your administrator account and get complete
                control over products, stock, categories and inventory.
            </p>

            <div class="auth-features">

                <div class="auth-feature">

                    <div class="auth-feature-icon">
                        <i class="bi bi-box-seam"></i>
                    </div>

                    <div>
                        <strong>Product Management</strong>
                        <span>
                            Add, edit and manage products easily.
                        </span>
                    </div>

                </div>

                <div class="auth-feature">

                    <div class="auth-feature-icon">
                        <i class="bi bi-bar-chart"></i>
                    </div>

                    <div>
                        <strong>Inventory Tracking</strong>
                        <span>
                            Monitor stock levels and low-stock items.
                        </span>
                    </div>

                </div>

                <div class="auth-feature">

                    <div class="auth-feature-icon">
                        <i class="bi bi-shield-check"></i>
                    </div>

                    <div>
                        <strong>Secure Administration</strong>
                        <span>
                            Your admin account is protected with secure authentication.
                        </span>
                    </div>

                </div>

            </div>

        </div>

        <div class="auth-copyright">
            © <?= date('Y') ?> Product Manager. All rights reserved.
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
                    <h4>Product Manager</h4>
                    <span>Admin Panel</span>
                </div>

            </div>

            <!-- FORM HEADER -->

            <div class="auth-form-header">

                <span class="auth-form-label">
                    ADMIN ACCOUNT
                </span>

                <h2>
                    Create an account
                </h2>

                <p>
                    Enter your details to create your administrator account.
                </p>

            </div>

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
                 REGISTER FORM
            ========================== -->

            <form
                action="register_process.php"
                method="POST"
                id="registerForm"
                novalidate
            >

                <!-- FULL NAME -->

                <div class="auth-form-group">

                    <label
                        for="name"
                        class="form-label"
                    >
                        Full Name
                    </label>

                    <div class="auth-input">

                        <i class="bi bi-person"></i>

                        <input
                            type="text"
                            id="name"
                            name="name"
                            class="form-control"
                            placeholder="Enter your full name"
                            value="<?= htmlspecialchars($oldName) ?>"
                            maxlength="100"
                            autocomplete="name"
                            required
                        >

                    </div>

                    <div class="invalid-feedback">
                        Please enter your full name.
                    </div>

                </div>

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
                        Please enter a valid email address.
                    </div>

                </div>

                <!-- PASSWORD -->

                <div class="auth-form-group">

                    <label
                        for="password"
                        class="form-label"
                    >
                        Password
                    </label>

                    <div class="auth-input">

                        <i class="bi bi-lock"></i>

                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control"
                            placeholder="Create a password"
                            minlength="6"
                            autocomplete="new-password"
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

                    <!-- PASSWORD STRENGTH -->

                    <div class="password-strength">

                        <div class="strength-bars">

                            <span></span>
                            <span></span>
                            <span></span>
                            <span></span>

                        </div>

                        <small id="passwordStrengthText">
                            Use at least 6 characters.
                        </small>

                    </div>

                </div>

                <!-- CONFIRM PASSWORD -->

                <div class="auth-form-group">

                    <label
                        for="confirm_password"
                        class="form-label"
                    >
                        Confirm Password
                    </label>

                    <div class="auth-input">

                        <i class="bi bi-lock-fill"></i>

                        <input
                            type="password"
                            id="confirm_password"
                            name="confirm_password"
                            class="form-control"
                            placeholder="Confirm your password"
                            minlength="6"
                            autocomplete="new-password"
                            required
                        >

                        <button
                            type="button"
                            class="password-toggle"
                            id="confirmPasswordToggle"
                            aria-label="Show password"
                        >
                            <i class="bi bi-eye"></i>
                        </button>

                    </div>

                    <div
                        class="invalid-feedback"
                        id="confirmPasswordFeedback"
                    >
                        Passwords do not match.
                    </div>

                </div>

                <!-- TERMS -->

                <div class="auth-terms">

                    <div class="form-check">

                        <input
                            class="form-check-input"
                            type="checkbox"
                            id="terms"
                            name="terms"
                            required
                        >

                        <label
                            class="form-check-label"
                            for="terms"
                        >
                            I agree to the
                            <a href="#">
                                terms and conditions
                            </a>
                        </label>

                    </div>

                    <div class="invalid-feedback">
                        You must agree before creating your account.
                    </div>

                </div>

                <!-- =========================
                     REGISTER BUTTON
                ========================== -->

                <button
                    type="submit"
                    class="btn btn-primary auth-submit"
                >

                    <span>
                        Create Admin Account
                    </span>

                    <i class="bi bi-arrow-right"></i>

                </button>

            </form>

            <!-- =========================
                 LOGIN LINK
            ========================== -->

            <div class="auth-footer">

                <span>
                    Already have an account?
                </span>

                <a href="login.php">
                    Sign In
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

    const registerForm =
        document.getElementById('registerForm');

    const password =
        document.getElementById('password');

    const confirmPassword =
        document.getElementById('confirm_password');

    const passwordToggle =
        document.getElementById('passwordToggle');

    const confirmPasswordToggle =
        document.getElementById('confirmPasswordToggle');

    const strengthText =
        document.getElementById('passwordStrengthText');

    const strengthBars =
        document.querySelectorAll('.strength-bars span');


    /* =========================
       PASSWORD TOGGLE
    ========================== */

    function togglePassword(input, button) {

        const icon = button.querySelector('i');

        if (input.type === 'password') {

            input.type = 'text';

            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');

            button.setAttribute(
                'aria-label',
                'Hide password'
            );

        } else {

            input.type = 'password';

            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');

            button.setAttribute(
                'aria-label',
                'Show password'
            );

        }
    }


    passwordToggle.addEventListener(
        'click',
        function () {
            togglePassword(
                password,
                passwordToggle
            );
        }
    );


    confirmPasswordToggle.addEventListener(
        'click',
        function () {
            togglePassword(
                confirmPassword,
                confirmPasswordToggle
            );
        }
    );


    /* =========================
       PASSWORD STRENGTH
    ========================== */

    password.addEventListener(
        'input',
        function () {

            const value = password.value;

            let strength = 0;

            if (value.length >= 6) {
                strength++;
            }

            if (value.length >= 8) {
                strength++;
            }

            if (/[A-Z]/.test(value)) {
                strength++;
            }

            if (/[0-9]/.test(value)) {
                strength++;
            }

            strengthBars.forEach(
                function (bar, index) {

                    bar.classList.toggle(
                        'active',
                        index < strength
                    );

                }
            );


            if (value.length === 0) {

                strengthText.textContent =
                    'Use at least 6 characters.';

            } else if (strength <= 1) {

                strengthText.textContent =
                    'Weak password';

            } else if (strength <= 2) {

                strengthText.textContent =
                    'Medium password';

            } else {

                strengthText.textContent =
                    'Strong password';

            }

        }
    );


    /* =========================
       PASSWORD MATCH
    ========================== */

    confirmPassword.addEventListener(
        'input',
        function () {

            if (
                confirmPassword.value !== '' &&
                confirmPassword.value !== password.value
            ) {

                confirmPassword.classList.add(
                    'is-invalid'
                );

            } else {

                confirmPassword.classList.remove(
                    'is-invalid'
                );

            }

        }
    );


    /* =========================
       FORM VALIDATION
    ========================== */

    registerForm.addEventListener(
        'submit',
        function (event) {

            let valid = true;

            if (!registerForm.checkValidity()) {
                valid = false;
            }

            if (
                password.value !==
                confirmPassword.value
            ) {

                confirmPassword.classList.add(
                    'is-invalid'
                );

                valid = false;

            } else {

                confirmPassword.classList.remove(
                    'is-invalid'
                );

            }

            if (!valid) {

                event.preventDefault();
                event.stopPropagation();

            }

            registerForm.classList.add(
                'was-validated'
            );

        }
    );

});

</script>

</body>
</html>

