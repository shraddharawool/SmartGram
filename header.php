<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
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

    <title>Smart Gram</title>

    <link rel="stylesheet" href="/smartgram/style.css?v=20261008">

</head>

<body>


<header>

    <div>

        <h1>
            🏡 Smart Gram
        </h1>

        <?php if (isset($_SESSION["user_id"])): ?>

            <p>
                Welcome,
                <?php echo htmlspecialchars($_SESSION["user_name"]); ?>
            </p>

        <?php else: ?>

            <p>
                Connecting Citizens. Empowering Villages.
            </p>

        <?php endif; ?>

    </div>

</header>


<nav>

    <a href="/smartgram/index.php">
        🏠 Home
    </a>


    <?php if (isset($_SESSION["user_id"])): ?>


        <a href="/smartgram/dashboard.php">
            📊 Dashboard
        </a>


        <a href="/smartgram/services.php">
            🏛️ Services
        </a>


        <a href="/smartgram/apply.php">
            📝 Apply
        </a>


        <a href="/smartgram/track.php">
            🔎 Track Application
        </a>


        <a href="/smartgram/notices.php">
            📢 Notices
        </a>


        <a href="/smartgram/schemes.php">
            💰 Schemes
        </a>


        <a href="/smartgram/grievance.php">
            📣 Grievance
        </a>


        <a href="/smartgram/profile.php">
            👤 Profile
        </a>


        <a
            href="/smartgram/logout.php"
            onclick="return confirm('Are you sure you want to logout?');"
        >
            🚪 Logout
        </a>


    <?php else: ?>


        <a href="/smartgram/login.php">
            🔐 Citizen Login
        </a>


        <a href="/smartgram/register.php">
            📝 Register
        </a>


        <a href="/smartgram/admin/login.php">
            ⚙️ Admin
        </a>


    <?php endif; ?>


</nav>