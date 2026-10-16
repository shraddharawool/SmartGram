<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

require_once "includes/db.php";

$user_id = $_SESSION["user_id"];

$total_applications = 0;
$pending_applications = 0;
$approved_applications = 0;
$total_grievances = 0;


/*
|--------------------------------------------------------------------------
| Total Applications
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare(
    "SELECT COUNT(*) AS total
     FROM applications
     WHERE user_id = ?"
);

if ($stmt) {

    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {

        $result = $stmt->get_result();

        if ($result) {
            $row = $result->fetch_assoc();
            $total_applications = (int)($row["total"] ?? 0);
        }

    } else {

        error_log(
            "Smart Gram Dashboard Total Applications Error: " . $stmt->error
        );
    }

    $stmt->close();
}


/*
|--------------------------------------------------------------------------
| Pending Applications
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare(
    "SELECT COUNT(*) AS total
     FROM applications
     WHERE user_id = ?
     AND status = 'Pending'"
);

if ($stmt) {

    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {

        $result = $stmt->get_result();

        if ($result) {
            $row = $result->fetch_assoc();
            $pending_applications = (int)($row["total"] ?? 0);
        }

    } else {

        error_log(
            "Smart Gram Dashboard Pending Applications Error: " . $stmt->error
        );
    }

    $stmt->close();
}


/*
|--------------------------------------------------------------------------
| Approved Applications
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare(
    "SELECT COUNT(*) AS total
     FROM applications
     WHERE user_id = ?
     AND status = 'Approved'"
);

if ($stmt) {

    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {

        $result = $stmt->get_result();

        if ($result) {
            $row = $result->fetch_assoc();
            $approved_applications = (int)($row["total"] ?? 0);
        }

    } else {

        error_log(
            "Smart Gram Dashboard Approved Applications Error: " . $stmt->error
        );
    }

    $stmt->close();
}


/*
|--------------------------------------------------------------------------
| Total Grievances
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare(
    "SELECT COUNT(*) AS total
     FROM grievances
     WHERE user_id = ?"
);

if ($stmt) {

    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {

        $result = $stmt->get_result();

        if ($result) {
            $row = $result->fetch_assoc();
            $total_grievances = (int)($row["total"] ?? 0);
        }

    } else {

        error_log(
            "Smart Gram Dashboard Grievances Error: " . $stmt->error
        );
    }

    $stmt->close();
}


/*
|--------------------------------------------------------------------------
| Recent Applications
|--------------------------------------------------------------------------
*/

$recent_applications = false;

$stmt = $conn->prepare(
    "SELECT
        application_number,
        service,
        status,
        submitted_at
     FROM applications
     WHERE user_id = ?
     ORDER BY submitted_at DESC
     LIMIT 5"
);

if ($stmt) {

    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {

        $recent_applications = $stmt->get_result();

    } else {

        error_log(
            "Smart Gram Dashboard Recent Applications Error: " . $stmt->error
        );
    }

}

?>

<?php include "header.php"; ?>


<main class="container">


    <!-- PAGE HEADER -->

    <section class="page-hero">

        <div>

            <span class="eyebrow">
                Citizen Dashboard
            </span>

            <h2>
                Welcome,
                <?php echo htmlspecialchars($_SESSION["user_name"]); ?> 👋
            </h2>

            <p>
                Manage your Panchayat services, applications and grievances
                from one place.
            </p>

        </div>

    </section>


    <!-- STATISTICS -->

    <section class="stats-grid">


        <div class="stat-card">

            <div class="stat-icon">
                📋
            </div>

            <div>

                <span>
                    Total Applications
                </span>

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

                <span>
                    Pending Applications
                </span>

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

                <span>
                    Approved Applications
                </span>

                <strong>
                    <?php echo $approved_applications; ?>
                </strong>

            </div>

        </div>


        <div class="stat-card">

            <div class="stat-icon">
                📣
            </div>

            <div>

                <span>
                    My Grievances
                </span>

                <strong>
                    <?php echo $total_grievances; ?>
                </strong>

            </div>

        </div>


    </section>


    <!-- QUICK ACCESS -->

    <section class="dashboard-section">

        <div class="section-heading">

            <div>

                <span class="eyebrow">
                    Quick Access
                </span>

                <h3>
                    Citizen Services
                </h3>

            </div>

        </div>


        <div class="quick-actions">


            <a
                href="services.php"
                class="quick-card"
            >

                <span>
                    🏛️
                </span>

                <strong>
                    Browse Services
                </strong>

                <small>
                    Explore available Panchayat services
                </small>

            </a>


            <a
                href="apply.php"
                class="quick-card"
            >

                <span>
                    📝
                </span>

                <strong>
                    Apply for Service
                </strong>

                <small>
                    Submit a new online application
                </small>

            </a>


            <a
                href="track.php"
                class="quick-card"
            >

                <span>
                    🔎
                </span>

                <strong>
                    Track Application
                </strong>

                <small>
                    Check your application status
                </small>

            </a>


            <a
                href="grievance.php"
                class="quick-card"
            >

                <span>
                    📣
                </span>

                <strong>
                    Submit Grievance
                </strong>

                <small>
                    Report an issue or share a concern
                </small>

            </a>


        </div>

    </section>


    <!-- RECENT APPLICATIONS -->

    <section class="dashboard-section">


        <div class="section-heading">

            <div>

                <span class="eyebrow">
                    Recent Activity
                </span>

                <h3>
                    Recent Applications
                </h3>

            </div>


            <a
                href="track.php"
                class="section-link"
            >
                View All →
            </a>

        </div>


        <div class="table-card">


            <?php if ($recent_applications && $recent_applications->num_rows > 0): ?>


                <div class="table-responsive">

                    <table>

                        <thead>

                            <tr>

                                <th>
                                    Application No.
                                </th>

                                <th>
                                    Service
                                </th>

                                <th>
                                    Status
                                </th>

                                <th>
                                    Submitted
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            <?php while ($application = $recent_applications->fetch_assoc()): ?>

                                <tr>

                                    <td>

                                        <strong>
                                            <?php
                                            echo htmlspecialchars(
                                                $application["application_number"]
                                            );
                                            ?>
                                        </strong>

                                    </td>


                                    <td>
                                        <?php
                                        echo htmlspecialchars(
                                            $application["service"]
                                        );
                                        ?>
                                    </td>


                                    <td>

                                        <?php

                                        $status = $application["status"];

                                        $status_class = "status-pending";

                                        if ($status === "Approved") {

                                            $status_class = "status-approved";

                                        } elseif ($status === "Rejected") {

                                            $status_class = "status-rejected";

                                        } elseif (
                                            strtolower($status) === "in progress"
                                        ) {

                                            $status_class = "status-in-progress";

                                        }

                                        ?>

                                        <span
                                            class="status <?php echo $status_class; ?>"
                                        >
                                            <?php echo htmlspecialchars($status); ?>
                                        </span>

                                    </td>


                                    <td>

                                        <?php

                                        $submitted_time = strtotime(
                                            $application["submitted_at"]
                                        );

                                        echo $submitted_time
                                            ? date("d M Y", $submitted_time)
                                            : "—";

                                        ?>

                                    </td>

                                </tr>

                            <?php endwhile; ?>

                        </tbody>

                    </table>

                </div>


            <?php else: ?>


                <div class="empty-state">

                    <div class="empty-icon">
                        📋
                    </div>

                    <h4>
                        No applications yet
                    </h4>

                    <p>
                        Your submitted applications will appear here.
                    </p>

                    <a
                        href="services.php"
                        class="btn"
                    >
                        Explore Services
                    </a>

                </div>


            <?php endif; ?>


        </div>

    </section>


    <!-- INFORMATION -->

    <section class="dashboard-section">


        <div class="info-banner">

            <div class="info-icon">
                💡
            </div>


            <div>

                <h3>
                    Need help?
                </h3>

                <p>
                    Check village notices and government schemes for the
                    latest information and citizen benefits.
                </p>

            </div>


            <div class="info-actions">

                <a
                    href="notices.php"
                    class="btn btn-secondary"
                >
                    View Notices
                </a>


                <a
                    href="schemes.php"
                    class="btn"
                >
                    View Schemes
                </a>

            </div>

        </div>


    </section>


</main>


<?php

if ($stmt) {
    $stmt->close();
}

include "footer.php";

?>