<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["admin_logged_in"])) {
    header("Location: login.php");
    exit;
}

?>

<link
    rel="stylesheet"
    href="/smartgram/admin/style.css?v=20261008"
>

<div class="admin-header">

    <div>

        <h1>🏡 Smart Gram</h1>

        <p>
            Administrator Panel

            <?php if (isset($_SESSION["admin_username"])): ?>

                · Logged in as
                <?php echo htmlspecialchars($_SESSION["admin_username"]); ?>

            <?php endif; ?>

        </p>

    </div>

</div>


<div class="admin-nav">

    <a href="../index.php">
        🏠 Main Website
    </a>

    <a href="dashboard.php">
        📊 Dashboard
    </a>

    <a href="applications.php">
        📋 Applications
    </a>

    <a href="citizens.php">
        👥 Citizens
    </a>

    <a href="services.php">
        🏛️ Services
    </a>

    <a href="notices.php">
        📢 Notices
    </a>

    <a href="schemes.php">
        💰 Schemes
    </a>

    <a href="grievances.php">
        📣 Grievances
    </a>

    <a
        href="logout.php"
        onclick="return confirm('Are you sure you want to logout?');"
    >
        🚪 Logout
    </a>

</div>