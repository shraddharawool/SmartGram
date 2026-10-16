<?php

session_start();

if (isset($_SESSION["admin_logged_in"])) {
    header("Location: dashboard.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($username === "admin" && $password === "admin123") {

        $_SESSION["admin_logged_in"] = true;
        $_SESSION["admin_username"] = "admin";

        header("Location: dashboard.php");
        exit;

    } else {

        $error =
            "Invalid Smart Gram administrator username or password.";
    }
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Smart Gram Admin Login</title>

    <link
        rel="stylesheet"
        href="/smartgram/admin/style.css?v=20261008"
    >

</head>

<body>

<div class="login-page">

    <div class="login-card">

        <div class="login-logo">
            🏡
        </div>

        <h1>
            Smart Gram
        </h1>

        <p class="login-subtitle">
            Administrator Portal
        </p>

        <?php if ($error): ?>

            <div class="alert alert-error">
                <?php echo htmlspecialchars($error); ?>
            </div>

        <?php endif; ?>

        <form method="POST">

            <div class="form-group">

                <label for="username">
                    Username
                </label>

                <input
                    type="text"
                    id="username"
                    name="username"
                    placeholder="Enter admin username"
                    required
                    autocomplete="username"
                >

            </div>

            <div class="form-group">

                <label for="password">
                    Password
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Enter admin password"
                    required
                    autocomplete="current-password"
                >

            </div>

            <button
                type="submit"
                class="login-button"
            >
                🔐 Sign In
            </button>

        </form>

        <a
            href="../index.php"
            class="back-link"
        >
            ← Back to Smart Gram
        </a>

    </div>

</div>

</body>

</html>