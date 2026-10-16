<?php

session_start();

if (!isset($_SESSION["admin_logged_in"])) {
    header("Location: login.php");
    exit;
}

require_once "../includes/db.php";


/*
|--------------------------------------------------------------------------
| Dashboard Statistics
|--------------------------------------------------------------------------
*/

$total_applications = 0;
$pending_applications = 0;
$approved_applications = 0;
$registered_citizens = 0;
$total_grievances = 0;
$total_notices = 0;
$total_schemes = 0;
$total_services = 0;


/*
|--------------------------------------------------------------------------
| Helper Function
|--------------------------------------------------------------------------
*/

function get_count($conn, $query)
{
    $result = $conn->query($query);

    if (!$result) {

        error_log(
            "Smart Gram Admin Dashboard Query Error: " .
            $conn->error
        );

        return 0;
    }

    $row = $result->fetch_assoc();

    return (int) ($row["total"] ?? 0);
}


/*
|--------------------------------------------------------------------------
| Get Statistics
|--------------------------------------------------------------------------
*/

$total_applications = get_count(
    $conn,
    "SELECT COUNT(*) AS total FROM applications"
);

$pending_applications = get_count(
    $conn,
    "SELECT COUNT(*) AS total
     FROM applications
     WHERE status = 'Pending'"
);

$approved_applications = get_count(
    $conn,
    "SELECT COUNT(*) AS total
     FROM applications
     WHERE status = 'Approved'"
);

$registered_citizens = get_count(
    $conn,
    "SELECT COUNT(*) AS total FROM users"
);

$total_grievances = get_count(
    $conn,
    "SELECT COUNT(*) AS total FROM grievances"
);

$total_notices = get_count(
    $conn,
    "SELECT COUNT(*) AS total FROM notices"
);

$total_schemes = get_count(
    $conn,
    "SELECT COUNT(*) AS total FROM schemes"
);

$total_services = get_count(
    $conn,
    "SELECT COUNT(*) AS total FROM services"
);

?>

<?php include "nav.php"; ?>

<main class="admin-container">

    <div class="admin-page-heading">

        <div>

            <h2>
                Smart Gram Dashboard
            </h2>

            <p>
                Overview of Smart Gram administration and citizen services.
            </p>

        </div>

    </div>


    <!-- STATISTICS -->

    <section class="admin-stats">

        <div class="stat-card">

            <div class="stat-icon">
                📋
            </div>

            <div>
                <span>Total Applications</span>

                <strong>
                    <?php echo $total_applications; ?>
                </strong>
            </div>

        </div>


        <div class="stat-card">

            <div class="stat-icon">
                ⏳
            </div>

            <div>
                <span>Pending Applications</span>

                <strong>
                    <?php echo $pending_applications; ?>
                </strong>
            </div>

        </div>


        <div class="stat-card">

            <div class="stat-icon">
                ✅
            </div>

            <div>
                <span>Approved Applications</span>

                <strong>
                    <?php echo $approved_applications; ?>
                </strong>
            </div>

        </div>


        <div class="stat-card">

            <div class="stat-icon">
                👥
            </div>

            <div>
                <span>Registered Citizens</span>

                <strong>
                    <?php echo $registered_citizens; ?>
                </strong>
            </div>

        </div>


        <div class="stat-card">

            <div class="stat-icon">
                📣
            </div>

            <div>
                <span>Grievances</span>

                <strong>
                    <?php echo $total_grievances; ?>
                </strong>
            </div>

        </div>


        <div class="stat-card">

            <div class="stat-icon">
                🏛️
            </div>

            <div>
                <span>Services</span>

                <strong>
                    <?php echo $total_services; ?>
                </strong>
            </div>

        </div>


        <div class="stat-card">

            <div class="stat-icon">
                📢
            </div>

            <div>
                <span>Notices</span>

                <strong>
                    <?php echo $total_notices; ?>
                </strong>
            </div>

        </div>


        <div class="stat-card">

            <div class="stat-icon">
                💰
            </div>

            <div>
                <span>Schemes</span>

                <strong>
                    <?php echo $total_schemes; ?>
                </strong>
            </div>

        </div>

    </section>


    <!-- MANAGEMENT -->

    <section class="admin-section">

        <div class="section-heading">

            <div>

                <h2>
                    Management Center
                </h2>

                <p>
                    Manage citizen services and Smart Gram information.
                </p>

            </div>

        </div>


        <div class="management-grid">


            <a
                href="applications.php"
                class="management-card"
            >

                <span class="management-icon">
                    📋
                </span>

                <div>

                    <h3>
                        Applications
                    </h3>

                    <p>
                        Review and update citizen applications.
                    </p>

                </div>

            </a>


            <a
                href="citizens.php"
                class="management-card"
            >

                <span class="management-icon">
                    👥
                </span>

                <div>

                    <h3>
                        Citizens
                    </h3>

                    <p>
                        View registered citizen accounts.
                    </p>

                </div>

            </a>


            <a
                href="services.php"
                class="management-card"
            >

                <span class="management-icon">
                    🏛️
                </span>

                <div>

                    <h3>
                        Services
                    </h3>

                    <p>
                        Add or manage Panchayat services.
                    </p>

                </div>

            </a>


            <a
                href="notices.php"
                class="management-card"
            >

                <span class="management-icon">
                    📢
                </span>

                <div>

                    <h3>
                        Notices
                    </h3>

                    <p>
                        Publish and manage village notices.
                    </p>

                </div>

            </a>


            <a
                href="schemes.php"
                class="management-card"
            >

                <span class="management-icon">
                    💰
                </span>

                <div>

                    <h3>
                        Schemes
                    </h3>

                    <p>
                        Manage government scheme information.
                    </p>

                </div>

            </a>


            <a
                href="grievances.php"
                class="management-card"
            >

                <span class="management-icon">
                    📣
                </span>

                <div>

                    <h3>
                        Grievances
                    </h3>

                    <p>
                        Review and update citizen grievances.
                    </p>

                </div>

            </a>


        </div>

    </section>

</main>