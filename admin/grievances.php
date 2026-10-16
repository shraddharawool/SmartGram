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
| Update Grievance Status
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $grievance_id = (int) (
        $_POST["grievance_id"] ?? 0
    );

    $status = trim(
        $_POST["status"] ?? ""
    );

    $allowed_statuses = [
        "Pending",
        "In Progress",
        "Resolved",
        "Rejected"
    ];


    if (
        $grievance_id <= 0 ||
        !in_array(
            $status,
            $allowed_statuses,
            true
        )
    ) {

        $error =
            "Invalid grievance details.";

    } else {

        $stmt = $conn->prepare(
            "UPDATE grievances
             SET status = ?
             WHERE id = ?"
        );

        if (!$stmt) {

            error_log(
                "Smart Gram Admin Grievance Update Prepare Error: " .
                $conn->error
            );

            $error =
                "Unable to update grievance status. Please try again.";

        } else {

            $stmt->bind_param(
                "si",
                $status,
                $grievance_id
            );

            if ($stmt->execute()) {

                if ($stmt->affected_rows >= 0) {

                    $message =
                        "Grievance status updated successfully.";

                }

            } else {

                error_log(
                    "Smart Gram Admin Grievance Update Error: " .
                    $stmt->error
                );

                $error =
                    "Unable to update grievance status. Please try again.";
            }

            $stmt->close();
        }
    }
}


/*
|--------------------------------------------------------------------------
| Load Grievances
|--------------------------------------------------------------------------
*/

$grievances = $conn->query(
    "SELECT
        g.id,
        g.grievance_number,
        g.subject,
        g.description,
        g.status,
        g.created_at,
        u.name,
        u.email,
        u.phone
     FROM grievances g
     LEFT JOIN users u
        ON g.user_id = u.id
     ORDER BY g.created_at DESC"
);

if (!$grievances) {

    error_log(
        "Smart Gram Admin Grievances Query Error: " .
        $conn->error
    );

    $error =
        "Unable to load grievances right now. Please try again.";
}

?>

<?php include "nav.php"; ?>

<main class="admin-container">

    <div class="admin-page-heading">

        <div>

            <h2>
                Citizen Grievances
            </h2>

            <p>
                Review and manage grievances submitted through Smart Gram.
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

                        <th>Grievance No.</th>
                        <th>Citizen</th>
                        <th>Subject</th>
                        <th>Description</th>
                        <th>Submitted</th>
                        <th>Status</th>
                        <th>Action</th>

                    </tr>

                </thead>


                <tbody>

                <?php if ($grievances && $grievances->num_rows > 0): ?>

                    <?php while ($row = $grievances->fetch_assoc()): ?>

                        <tr>

                            <td>

                                <strong>
                                    <?php
                                    echo htmlspecialchars(
                                        $row["grievance_number"]
                                    );
                                    ?>
                                </strong>

                            </td>


                            <td>

                                <strong>
                                    <?php
                                    echo htmlspecialchars(
                                        $row["name"] ?? "Unknown"
                                    );
                                    ?>
                                </strong>

                                <?php if (!empty($row["email"])): ?>

                                    <br>

                                    <small>
                                        <?php
                                        echo htmlspecialchars(
                                            $row["email"]
                                        );
                                        ?>
                                    </small>

                                <?php endif; ?>


                                <?php if (!empty($row["phone"])): ?>

                                    <br>

                                    <small>
                                        <?php
                                        echo htmlspecialchars(
                                            $row["phone"]
                                        );
                                        ?>
                                    </small>

                                <?php endif; ?>

                            </td>


                            <td>

                                <strong>
                                    <?php
                                    echo htmlspecialchars(
                                        $row["subject"]
                                    );
                                    ?>
                                </strong>

                            </td>


                            <td>

                                <?php
                                echo nl2br(
                                    htmlspecialchars(
                                        $row["description"]
                                    )
                                );
                                ?>

                            </td>


                            <td>

                                <?php

                                $created_time =
                                    strtotime(
                                        $row["created_at"]
                                    );

                                if ($created_time) {

                                    echo date(
                                        "d M Y",
                                        $created_time
                                    );

                                    echo "<br>";

                                    echo "<small>";

                                    echo date(
                                        "h:i A",
                                        $created_time
                                    );

                                    echo "</small>";

                                } else {

                                    echo "Not available";

                                }

                                ?>

                            </td>


                            <td>

                                <?php

                                $status_class =
                                    "status-pending";

                                if (
                                    $row["status"] ===
                                    "In Progress"
                                ) {

                                    $status_class =
                                        "status-progress";

                                } elseif (
                                    $row["status"] ===
                                    "Resolved"
                                ) {

                                    $status_class =
                                        "status-approved";

                                } elseif (
                                    $row["status"] ===
                                    "Rejected"
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
                                        name="grievance_id"
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
                                            value="In Progress"
                                            <?php
                                            echo $row["status"] === "In Progress"
                                                ? "selected"
                                                : "";
                                            ?>
                                        >
                                            In Progress
                                        </option>

                                        <option
                                            value="Resolved"
                                            <?php
                                            echo $row["status"] === "Resolved"
                                                ? "selected"
                                                : "";
                                            ?>
                                        >
                                            Resolved
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
                            colspan="7"
                            class="empty-state"
                        >

                            <div>
                                📣
                            </div>

                            <strong>
                                No grievances found
                            </strong>

                            <p>
                                Citizen grievances will appear here.
                            </p>

                        </td>

                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </section>

</main>