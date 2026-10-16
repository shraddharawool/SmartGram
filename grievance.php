<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

require_once "includes/db.php";

$user_id = (int) $_SESSION["user_id"];

$error = "";
$success = "";

$subject = "";
$description = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $subject = trim($_POST["subject"] ?? "");
    $description = trim($_POST["description"] ?? "");

    if ($subject === "" || $description === "") {

        $error = "Please fill in all required fields.";

    } elseif (strlen($subject) > 200) {

        $error = "Subject cannot exceed 200 characters.";

    } else {

        $grievance_number = "";

        $insert_success = false;

        /*
        |--------------------------------------------------------------------------
        | Generate a unique grievance number
        |--------------------------------------------------------------------------
        */

        for ($attempt = 1; $attempt <= 3; $attempt++) {

            $grievance_number =
                "GR-" .
                date("Ymd") .
                "-" .
                strtoupper(
                    substr(
                        str_shuffle(
                            "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"
                        ),
                        0,
                        6
                    )
                );

            $check = $conn->prepare(
                "SELECT id
                 FROM grievances
                 WHERE grievance_number = ?
                 LIMIT 1"
            );

            if (!$check) {

                error_log(
                    "Smart Gram Grievance Check Prepare Error: " .
                    $conn->error
                );

                $error =
                    "Unable to submit your grievance right now. Please try again.";

                break;
            }

            $check->bind_param(
                "s",
                $grievance_number
            );

            if (!$check->execute()) {

                error_log(
                    "Smart Gram Grievance Check Execute Error: " .
                    $check->error
                );

                $check->close();

                $error =
                    "Unable to submit your grievance right now. Please try again.";

                break;
            }

            $check_result = $check->get_result();

            $number_exists =
                $check_result && $check_result->num_rows > 0;

            $check->close();

            if ($number_exists) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Insert grievance
            |--------------------------------------------------------------------------
            */

            $stmt = $conn->prepare(
                "INSERT INTO grievances
                (
                    grievance_number,
                    user_id,
                    subject,
                    description,
                    status
                )
                VALUES (?, ?, ?, ?, 'Pending')"
            );

            if (!$stmt) {

                error_log(
                    "Smart Gram Grievance Prepare Error: " .
                    $conn->error
                );

                $error =
                    "Unable to submit your grievance right now. Please try again.";

                break;
            }

            $stmt->bind_param(
                "siss",
                $grievance_number,
                $user_id,
                $subject,
                $description
            );

            if ($stmt->execute()) {

                $insert_success = true;

                $stmt->close();

                break;

            } else {

                $error_code = $stmt->errno;

                error_log(
                    "Smart Gram Grievance Insert Error: " .
                    $stmt->error
                );

                $stmt->close();

                /*
                | Retry only for duplicate grievance number.
                */

                if ($error_code == 1062) {
                    continue;
                }

                $error =
                    "Unable to submit your grievance. Please try again.";

                break;
            }
        }

        if ($insert_success) {

            $success =
                "Grievance submitted successfully. Your grievance number is " .
                $grievance_number .
                ".";

            $subject = "";
            $description = "";

        } elseif ($error === "") {

            $error =
                "Unable to generate a unique grievance number. Please try again.";
        }
    }
}


/*
|--------------------------------------------------------------------------
| Load user's grievances
|--------------------------------------------------------------------------
*/

$grievances = null;

$stmt = $conn->prepare(
    "SELECT
        grievance_number,
        subject,
        description,
        status,
        created_at
     FROM grievances
     WHERE user_id = ?
     ORDER BY created_at DESC"
);

if (!$stmt) {

    error_log(
        "Smart Gram Grievance List Prepare Error: " .
        $conn->error
    );

    $error =
        $error !== ""
            ? $error
            : "Unable to load your grievances right now.";

} else {

    $stmt->bind_param(
        "i",
        $user_id
    );

    if (!$stmt->execute()) {

        error_log(
            "Smart Gram Grievance List Execute Error: " .
            $stmt->error
        );

        $error =
            $error !== ""
                ? $error
                : "Unable to load your grievances right now.";

        $stmt->close();
        $stmt = null;

    } else {

        $grievances = $stmt->get_result();
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
                Smart Gram Citizen Support
            </span>

            <h2>
                Grievance Management
            </h2>

            <p>
                Report issues, share concerns and track your grievances
                through Smart Gram.
            </p>

        </div>

    </section>

    <?php if ($success): ?>

        <div class="alert alert-success">

            <strong>Success!</strong><br>

            <?php echo htmlspecialchars($success); ?>

        </div>

    <?php endif; ?>

    <?php if ($error): ?>

        <div class="alert alert-error">

            <?php echo htmlspecialchars($error); ?>

        </div>

    <?php endif; ?>

    <section class="form-layout">

        <div class="form-card">

            <h3>
                📣 Submit a Grievance
            </h3>

            <p class="form-intro">
                Provide clear details so the Panchayat administration
                can review and respond to your concern.
            </p>

            <form method="POST">

                <div class="form-group">

                    <label for="subject">
                        Subject
                    </label>

                    <input
                        type="text"
                        id="subject"
                        name="subject"
                        placeholder="Enter grievance subject"
                        value="<?php echo htmlspecialchars($subject); ?>"
                        maxlength="200"
                        required
                    >

                </div>

                <div class="form-group">

                    <label for="description">
                        Description
                    </label>

                    <textarea
                        id="description"
                        name="description"
                        rows="7"
                        placeholder="Describe your issue in detail"
                        required
                    ><?php echo htmlspecialchars($description); ?></textarea>

                </div>

                <button
                    type="submit"
                    class="btn"
                >
                    📣 Submit Grievance
                </button>

            </form>

        </div>

        <aside class="info-card">

            <div class="info-icon">
                💡
            </div>

            <h3>
                Helpful Information
            </h3>

            <ul>

                <li>
                    Describe the issue clearly.
                </li>

                <li>
                    Include important location or service details.
                </li>

                <li>
                    Keep your grievance number for reference.
                </li>

                <li>
                    You can check the latest status from this page.
                </li>

            </ul>

        </aside>

    </section>

    <section class="dashboard-section">

        <div class="section-heading">

            <div>

                <span class="eyebrow">
                    Your Requests
                </span>

                <h3>
                    My Grievances
                </h3>

            </div>

        </div>

        <div class="table-card">

            <?php if ($grievances && $grievances->num_rows > 0): ?>

                <div class="table-responsive">

                    <table>

                        <thead>

                            <tr>

                                <th>
                                    Grievance No.
                                </th>

                                <th>
                                    Subject
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

                            <?php while (
                                $grievance = $grievances->fetch_assoc()
                            ): ?>

                                <tr>

                                    <td>

                                        <strong>
                                            <?php echo htmlspecialchars(
                                                $grievance["grievance_number"]
                                            ); ?>
                                        </strong>

                                    </td>

                                    <td>

                                        <?php echo htmlspecialchars(
                                            $grievance["subject"]
                                        ); ?>

                                    </td>

                                    <td>

                                        <?php

                                        $status =
                                            trim(
                                                $grievance["status"]
                                            );

                                        $status_class =
                                            "status-pending";

                                        if ($status === "Resolved") {

                                            $status_class =
                                                "status-approved";

                                        } elseif (
                                            $status === "Rejected"
                                        ) {

                                            $status_class =
                                                "status-rejected";

                                        } elseif (
                                            $status === "In Progress" ||
                                            $status === "Processing"
                                        ) {

                                            $status_class =
                                                "status-in-progress";
                                        }

                                        ?>

                                        <span
                                            class="status <?php echo $status_class; ?>"
                                        >
                                            <?php echo htmlspecialchars(
                                                $status
                                            ); ?>
                                        </span>

                                    </td>

                                    <td>

                                        <?php

                                        $created_time = strtotime(
                                            $grievance["created_at"]
                                        );

                                        echo $created_time
                                            ? date(
                                                "d M Y",
                                                $created_time
                                            )
                                            : "Not available";

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
                        📣
                    </div>

                    <h4>
                        No grievances submitted
                    </h4>

                    <p>
                        Your submitted grievances will appear here.
                    </p>

                </div>

            <?php endif; ?>

        </div>

    </section>

</main>

<?php

if ($stmt) {
    $stmt->close();
}

include "footer.php";

?>