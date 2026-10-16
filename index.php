<?php

session_start();

?>

<?php include "header.php"; ?>


<main class="container">


    <!-- CONNECTION STATUS -->

    <div
        id="connectionStatus"
        class="connection-status online"
    >

        <span class="connection-dot"></span>

        <span id="connectionText">
            Connection Available
        </span>

    </div>


    <!-- HERO -->

    <section class="hero-section">


        <div class="hero-content">


            <span class="eyebrow">
                Digital Panchayat Platform
            </span>


            <h2>
                Connecting Citizens.<br>
                Empowering Villages.
            </h2>


            <p>
                Smart Gram makes essential Gram Panchayat
                services, applications, notices and government
                information accessible online.
            </p>


            <div class="hero-actions">

                <?php if (isset($_SESSION["user_id"])): ?>

                    <a
                        href="dashboard.php"
                        class="btn"
                    >
                        📊 Go to Dashboard
                    </a>

                    <a
                        href="services.php"
                        class="btn btn-secondary"
                    >
                        🏛️ Explore Services
                    </a>

                <?php else: ?>

                    <a
                        href="register.php"
                        class="btn"
                    >
                        📝 Create Account
                    </a>

                    <a
                        href="login.php"
                        class="btn btn-secondary"
                    >
                        🔐 Citizen Login
                    </a>

                <?php endif; ?>

            </div>


        </div>


        <div class="hero-visual">

            <div class="hero-card">

                <div class="hero-card-icon">
                    🏡
                </div>

                <h3>
                    Smart Gram
                </h3>

                <p>
                    Your digital connection to the Gram Panchayat.
                </p>

                <div class="hero-mini-grid">

                    <span>📋 Applications</span>
                    <span>🔎 Tracking</span>
                    <span>📢 Notices</span>
                    <span>💰 Schemes</span>

                </div>

            </div>

        </div>


    </section>


    <!-- ESSENTIAL SERVICES -->

    <section class="dashboard-section">


        <div class="section-heading">

            <div>

                <span class="eyebrow">
                    Citizen Services
                </span>

                <h3>
                    Essential Services
                </h3>

            </div>

            <a
                href="services.php"
                class="section-link"
            >
                View All →
            </a>

        </div>


        <div class="feature-grid">


            <a
                href="<?php echo isset($_SESSION["user_id"]) ? "services.php" : "login.php"; ?>"
                class="feature-card"
            >

                <div class="feature-icon">
                    📜
                </div>

                <h3>
                    Certificates
                </h3>

                <p>
                    Access birth, death, residence and other
                    Panchayat certificate services.
                </p>

            </a>


            <a
                href="<?php echo isset($_SESSION["user_id"]) ? "apply.php" : "login.php"; ?>"
                class="feature-card"
            >

                <div class="feature-icon">
                    📝
                </div>

                <h3>
                    Online Applications
                </h3>

                <p>
                    Submit applications for Panchayat services
                    conveniently from anywhere.
                </p>

            </a>


            <a
                href="<?php echo isset($_SESSION["user_id"]) ? "track.php" : "login.php"; ?>"
                class="feature-card"
            >

                <div class="feature-icon">
                    🔎
                </div>

                <h3>
                    Track Application
                </h3>

                <p>
                    Check the latest status of your submitted
                    applications.
                </p>

            </a>


            <a
                href="<?php echo isset($_SESSION["user_id"]) ? "grievance.php" : "login.php"; ?>"
                class="feature-card"
            >

                <div class="feature-icon">
                    📣
                </div>

                <h3>
                    Citizen Grievance
                </h3>

                <p>
                    Report issues and share concerns directly
                    with the Panchayat administration.
                </p>

            </a>


        </div>


    </section>


    <!-- VILLAGE INFORMATION -->

    <section class="dashboard-section">


        <div class="section-heading">

            <div>

                <span class="eyebrow">
                    Stay Informed
                </span>

                <h3>
                    Village Information
                </h3>

            </div>

        </div>


        <div class="feature-grid two-column">


            <a
                href="<?php echo isset($_SESSION["user_id"]) ? "notices.php" : "login.php"; ?>"
                class="feature-card"
            >

                <div class="feature-icon">
                    📢
                </div>

                <h3>
                    Village Notices
                </h3>

                <p>
                    Stay updated with important Panchayat
                    announcements and village activities.
                </p>

                <span class="feature-link">
                    View Notices →
                </span>

            </a>


            <a
                href="<?php echo isset($_SESSION["user_id"]) ? "schemes.php" : "login.php"; ?>"
                class="feature-card"
            >

                <div class="feature-icon">
                    💰
                </div>

                <h3>
                    Government Schemes
                </h3>

                <p>
                    Explore government programmes and benefits
                    available to eligible citizens.
                </p>

                <span class="feature-link">
                    View Schemes →
                </span>

            </a>


        </div>


    </section>


    <!-- WHY SMART GRAM -->

    <section class="dashboard-section">


        <div class="section-heading">

            <div>

                <span class="eyebrow">
                    Why Smart Gram
                </span>

                <h3>
                    Simple. Secure. Accessible.
                </h3>

            </div>

        </div>


        <div class="benefits-grid">


            <div class="benefit-card">

                <div class="benefit-icon">
                    ⚡
                </div>

                <h3>
                    Simple
                </h3>

                <p>
                    Easy-to-use digital services designed
                    for every citizen.
                </p>

            </div>


            <div class="benefit-card">

                <div class="benefit-icon">
                    🔒
                </div>

                <h3>
                    Secure
                </h3>

                <p>
                    Your account and application information
                    is protected.
                </p>

            </div>


            <div class="benefit-card">

                <div class="benefit-icon">
                    📱
                </div>

                <h3>
                    Accessible
                </h3>

                <p>
                    Access Panchayat information and services
                    from your device.
                </p>

            </div>


            <div class="benefit-card">

                <div class="benefit-icon">
                    🤝
                </div>

                <h3>
                    Citizen Focused
                </h3>

                <p>
                    Built to make communication between citizens
                    and Panchayat easier.
                </p>

            </div>


        </div>


    </section>


</main>


<script>

/*
|--------------------------------------------------------------------------
| Smart Gram Connection Detection
|--------------------------------------------------------------------------
*/

function updateConnectionStatus() {

    const status = document.getElementById("connectionStatus");
    const text = document.getElementById("connectionText");

    if (!status || !text) {
        return;
    }

    if (navigator.onLine) {

        status.classList.remove("offline");
        status.classList.add("online");

        text.textContent = "Connection Available";

    } else {

        status.classList.remove("online");
        status.classList.add("offline");

        text.textContent = "Connection Offline";

    }

}


window.addEventListener(
    "online",
    updateConnectionStatus
);


window.addEventListener(
    "offline",
    updateConnectionStatus
);


updateConnectionStatus();

</script>


<?php include "footer.php"; ?>