<?php

session_start();

if (!isset($_SESSION["admin_logged_in"])) {
    header("Location: login.php");
    exit;
}

require_once "../includes/db.php";

$message = "";
$error = "";


/*
|--------------------------------------------------------------------------
| Update Application Status
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $application_id = (int) ($_POST["application_id"] ?? 0);
    $status = trim($_POST["status"] ?? "");

    $allowed_statuses = [
        "Pending",
        "Approved",
        "Rejected"
    ];

    if (
        $application_id <= 0 ||
        !in_array($status, $allowed_statuses, true)
    ) {

        $error = "Invalid application details.";

    } else {

        $stmt = $conn->prepare(
            "UPDATE applications
             SET status = ?
             WHERE id = ?"
        );

        if (!$stmt) {

            error_log(
                "Smart Gram Admin Application Update Prepare Error: " .
                $conn->error
            );

            $error =
                "Unable to update application status. Please try again.";

        } else {

            $stmt->bind_param(
                "si",
                $status,
                $application_id
            );

            if ($stmt->execute()) {

                $message =
                    "Application status updated successfully.";

            } else {

                error_log(
                    "Smart Gram Admin Application Update Error: " .
                    $stmt->error
                );

                $error =
                    "Unable to update application status. Please try again.";
            }

            $stmt->close();
        }
    }
}


/*
|--------------------------------------------------------------------------
| Load Applications
|--------------------------------------------------------------------------
*/

$applications = $conn->query(
    "SELECT
        a.id,
        a.application_number,
        a.service,
        a.applicant_name,
        a.phone,
        a.address,
        a.status,
        a.submitted_at,
        u.email
     FROM applications a
     LEFT JOIN users u
        ON a.user_id = u.id
     ORDER BY a.submitted_at DESC"
);

if (!$applications) {

    error_log(
        "Smart Gram Admin Applications Query Error: " .
        $conn->error
    );

    $error =
        "Unable to load applications right now. Please try again.";
}

?>

<?php include "nav.php"; ?>

<main class="admin-container">

    <div class="admin-page-heading">

        <div>

            <h2>
                Applications
            </h2>

            <p>
                Review and manage Smart Gram citizen service applications.
            </p>

        </div>

    </div>


    <?php if ($message): ?>

        <div class="alert alert-success">
            <?php echo htmlspecialchars($message); ?>
        </div>

    <?php endif; ?>


    <?php if ($error): ?>

        <div class="alert alert-error">
            <?php echo htmlspecialchars($error); ?>
        </div>

    <?php endif; ?>


    <section class="admin-table-card">

        <div class="table-wrapper">

            <table class="admin-table">

                <thead>

                    <tr>

                        <th>Application No.</th>
                        <th>Applicant</th>
                        <th>Service</th>
                        <th>Contact</th>
                        <th>Address</th>
                        <th>Submitted</th>
                        <th>Status</th>
                        <th>Action</th>

                    </tr>

                </thead>

                <tbody>

                <?php if ($applications && $applications->num_rows > 0): ?>

                    <?php while ($row = $applications->fetch_assoc()): ?>

                        <tr>

                            <td>

                                <strong>
                                    <?php
                                    echo htmlspecialchars(
                                        $row["application_number"]
                                    );
                                    ?>
                                </strong>

                            </td>


                            <td>

                                <strong>
                                    <?php
                                    echo htmlspecialchars(
                                        $row["applicant_name"]
                                    );
                                    ?>
                                </strong>

                                <br>

                                <small>
                                    <?php
                                    echo htmlspecialchars(
                                        $row["email"] ?? ""
                                    );
                                    ?>
                                </small>

                            </td>


                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $row["service"]
                                );
                                ?>
                            </td>


                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $row["phone"]
                                );
                                ?>
                            </td>


                            <td>
                                <?php
                                echo nl2br(
                                    htmlspecialchars(
                                        $row["address"]
                                    )
                                );
                                ?>
                            </td>


                            <td>

                                <?php

                                $submitted_time =
                                    strtotime(
                                        $row["submitted_at"]
                                    );

                                if ($submitted_time) {

                                    echo date(
                                        "d M Y",
                                        $submitted_time
                                    );

                                    echo "<br>";

                                    echo "<small>";

                                    echo date(
                                        "h:i A",
                                        $submitted_time
                                    );

                                    echo "</small>";

                                } else {

                                    echo "Not available";
                                }

                                ?>

                            </td>


                            <td>

                                <?php

                                $status_class = "status-pending";

                                if ($row["status"] === "Approved") {
                                    $status_class =
                                        "status-approved";
                                } elseif (
                                    $row["status"] === "Rejected"
                                ) {
                                    $status_class =
                                        "status-rejected";
                                }

                                ?>

                                <span
                                    class="status-badge <?php echo $status_class; ?>"
                                >
                                    <?php
                                    echo htmlspecialchars(
                                        $row["status"]
                                    );
                                    ?>
                                </span>

                            </td>


                            <td>

                                <form
                                    method="POST"
                                    class="status-form"
                                >

                                    <input
                                        type="hidden"
                                        name="application_id"
                                        value="<?php
                                            echo (int) $row["id"];
                                        ?>"
                                    >

                                    <select
                                        name="status"
                                        required
                                    >

                                        <option
                                            value="Pending"
                                            <?php
                                            echo $row["status"] === "Pending"
                                                ? "selected"
                                                : "";
                                            ?>
                                        >
                                            Pending
                                        </option>

                                        <option
                                            value="Approved"
                                            <?php
                                            echo $row["status"] === "Approved"
                                                ? "selected"
                                                : "";
                                            ?>
                                        >
                                            Approved
                                        </option>

                                        <option
                                            value="Rejected"
                                            <?php
                                            echo $row["status"] === "Rejected"
                                                ? "selected"
                                                : "";
                                            ?>
                                        >
                                            Rejected
                                        </option>

                                    </select>

                                    <button
                                        type="submit"
                                        class="admin-button"
                                    >
                                        Update
                                    </button>

                                </form>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>

                        <td
                            colspan="8"
                            class="empty-state"
                        >

                            <div>
                                📋
                            </div>

                            <strong>
                                No applications found
                            </strong>

                            <p>
                                Citizen applications will appear here.
                            </p>

                        </td>

                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </section>

</main>