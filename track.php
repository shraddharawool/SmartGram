<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

require_once "includes/db.php";

$user_id = (int) $_SESSION["user_id"];

$application = null;
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $application_number = trim(
        $_POST["application_number"] ?? ""
    );

    if ($application_number === "") {

        $error = "Please enter your application number.";

    } else {

        $stmt = $conn->prepare(
            "SELECT
                application_number,
                service,
                applicant_name,
                phone,
                address,
                status,
                submitted_at
             FROM applications
             WHERE application_number = ?
             AND user_id = ?
             LIMIT 1"
        );

        if (!$stmt) {

            error_log(
                "Smart Gram Track Prepare Error: " . $conn->error
            );

            $error = "Unable to process your request right now. Please try again.";

        } else {

            $stmt->bind_param(
                "si",
                $application_number,
                $user_id
            );

            if (!$stmt->execute()) {

                error_log(
                    "Smart Gram Track Execute Error: " . $stmt->error
                );

                $error = "Unable to check your application right now. Please try again.";

            } else {

                $result = $stmt->get_result();

                if ($result && $result->num_rows === 1) {

                    $application = $result->fetch_assoc();

                } else {

                    $error =
                        "No application was found with that application number.";
                }
            }

            $stmt->close();
        }
    }
}

?>

<?php include "header.php"; ?>

<main class="container">

    <a href="dashboard.php" class="page-back">
        ← Back to Dashboard
    </a>

    <section class="page-hero">

        <div>

            <span class="eyebrow">
                Smart Gram Application Tracking
            </span>

            <h2>
                Track Your Application
            </h2>

            <p>
                Enter your application number to check the latest status
                of your Panchayat service request.
            </p>

        </div>

    </section>

    <section class="form-layout">

        <div class="form-card">

            <h3>
                🔎 Search Application
            </h3>

            <p class="form-intro">
                Enter the application number received after submitting
                your service request.
            </p>

            <?php if ($error): ?>

                <div class="alert alert-error">
                    <?php echo htmlspecialchars($error); ?>
                </div>

            <?php endif; ?>

            <form method="POST">

                <div class="form-group">

                    <label for="application_number">
                        Application Number
                    </label>

                    <input
                        type="text"
                        id="application_number"
                        name="application_number"
                        placeholder="Example: SG-20261008-ABC123"
                        value="<?php echo htmlspecialchars(
                            $_POST["application_number"] ?? ""
                        ); ?>"
                        required
                        autocomplete="off"
                    >

                </div>

                <button
                    type="submit"
                    class="btn"
                >
                    🔎 Track Application
                </button>

            </form>

        </div>

        <aside class="info-card">

            <div class="info-icon">
                📌
            </div>

            <h3>
                Tracking Information
            </h3>

            <ul>

                <li>
                    Your application number is unique.
                </li>

                <li>
                    Keep it safe for future reference.
                </li>

                <li>
                    Only your applications are displayed.
                </li>

                <li>
                    Status is updated by the Panchayat administration.
                </li>

            </ul>

        </aside>

    </section>

    <?php if ($application): ?>

        <?php

        $status = trim($application["status"]);

        $status_class = "status-pending";

        if ($status === "Approved") {

            $status_class = "status-approved";

        } elseif ($status === "Rejected") {

            $status_class = "status-rejected";

        } elseif (
            $status === "In Progress" ||
            $status === "Processing"
        ) {

            $status_class = "status-in-progress";
        }

        ?>

        <section class="dashboard-section">

            <div class="section-heading">

                <div>

                    <span class="eyebrow">
                        Application Details
                    </span>

                    <h3>
                        Application Status
                    </h3>

                </div>

            </div>

            <div class="details-card">

                <div class="details-row">

                    <span>
                        Application Number
                    </span>

                    <strong>
                        <?php echo htmlspecialchars(
                            $application["application_number"]
                        ); ?>
                    </strong>

                </div>

                <div class="details-row">

                    <span>
                        Service
                    </span>

                    <strong>
                        <?php echo htmlspecialchars(
                            $application["service"]
                        ); ?>
                    </strong>

                </div>

                <div class="details-row">

                    <span>
                        Applicant Name
                    </span>

                    <strong>
                        <?php echo htmlspecialchars(
                            $application["applicant_name"]
                        ); ?>
                    </strong>

                </div>

                <div class="details-row">

                    <span>
                        Mobile Number
                    </span>

                    <strong>
                        <?php echo htmlspecialchars(
                            $application["phone"]
                        ); ?>
                    </strong>

                </div>

                <div class="details-row">

                    <span>
                        Address
                    </span>

                    <strong>
                        <?php echo nl2br(
                            htmlspecialchars(
                                $application["address"]
                            )
                        ); ?>
                    </strong>

                </div>

                <div class="details-row">

                    <span>
                        Submitted On
                    </span>

                    <strong>
                        <?php

                        $submitted_time = strtotime(
                            $application["submitted_at"]
                        );

                        echo $submitted_time
                            ? date(
                                "d M Y, h:i A",
                                $submitted_time
                            )
                            : "Not available";

                        ?>
                    </strong>

                </div>

                <div class="details-row">

                    <span>
                        Current Status
                    </span>

                    <span class="status <?php echo $status_class; ?>">
                        <?php echo htmlspecialchars($status); ?>
                    </span>

                </div>

            </div>

        </section>

    <?php endif; ?>

</main>

<?php include "footer.php"; ?>