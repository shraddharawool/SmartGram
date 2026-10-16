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
| Add / Delete Service
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $action = $_POST["action"] ?? "";


    /*
    |--------------------------------------------------------------------------
    | Add Service
    |--------------------------------------------------------------------------
    */

    if ($action === "add") {

        $title = trim($_POST["title"] ?? "");
        $description = trim($_POST["description"] ?? "");

        if ($title === "" || $description === "") {

            $error = "Please fill in all fields.";

        } else {

            $stmt = $conn->prepare(
                "INSERT INTO services (title, description)
                 VALUES (?, ?)"
            );

            if (!$stmt) {

                error_log(
                    "Smart Gram Admin Service Insert Prepare Error: " .
                    $conn->error
                );

                $error =
                    "Unable to add service. Please try again.";

            } else {

                $stmt->bind_param(
                    "ss",
                    $title,
                    $description
                );

                if ($stmt->execute()) {

                    $message =
                        "Service added successfully.";

                } else {

                    error_log(
                        "Smart Gram Admin Service Insert Error: " .
                        $stmt->error
                    );

                    $error =
                        "Unable to add service. Please try again.";
                }

                $stmt->close();
            }
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Delete Service
    |--------------------------------------------------------------------------
    */

    elseif ($action === "delete") {

        $service_id = (int) (
            $_POST["service_id"] ?? 0
        );

        if ($service_id <= 0) {

            $error = "Invalid service details.";

        } else {

            $stmt = $conn->prepare(
                "DELETE FROM services
                 WHERE id = ?"
            );

            if (!$stmt) {

                error_log(
                    "Smart Gram Admin Service Delete Prepare Error: " .
                    $conn->error
                );

                $error =
                    "Unable to delete service. Please try again.";

            } else {

                $stmt->bind_param(
                    "i",
                    $service_id
                );

                if ($stmt->execute()) {

                    if ($stmt->affected_rows > 0) {

                        $message =
                            "Service deleted successfully.";

                    } else {

                        $error =
                            "Service was not found.";
                    }

                } else {

                    error_log(
                        "Smart Gram Admin Service Delete Error: " .
                        $stmt->error
                    );

                    $error =
                        "Unable to delete service. Please try again.";
                }

                $stmt->close();
            }
        }
    }
}


/*
|--------------------------------------------------------------------------
| Load Services
|--------------------------------------------------------------------------
*/

$services = $conn->query(
    "SELECT
        id,
        title,
        description,
        created_at
     FROM services
     ORDER BY created_at DESC"
);

if (!$services) {

    error_log(
        "Smart Gram Admin Services Query Error: " .
        $conn->error
    );

    $error =
        "Unable to load services right now. Please try again.";
}

?>

<?php include "nav.php"; ?>

<main class="admin-container">

    <div class="admin-page-heading">

        <div>

            <h2>
                Services
            </h2>

            <p>
                Manage the services available to Smart Gram citizens.
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


    <!-- ADD SERVICE -->

    <section class="admin-form-card">

        <div class="section-heading">

            <div>

                <h2>
                    Add New Service
                </h2>

                <p>
                    Create a service that citizens can apply for.
                </p>

            </div>

        </div>


        <form
            method="POST"
            class="admin-form"
        >

            <input
                type="hidden"
                name="action"
                value="add"
            >


            <div class="form-group">

                <label for="service-title">
                    Service Title
                </label>

                <input
                    type="text"
                    id="service-title"
                    name="title"
                    placeholder="Example: Birth Certificate"
                    maxlength="200"
                    required
                >

            </div>


            <div class="form-group">

                <label for="service-description">
                    Description
                </label>

                <textarea
                    id="service-description"
                    name="description"
                    rows="4"
                    placeholder="Describe the service..."
                    required
                ></textarea>

            </div>


            <button
                type="submit"
                class="admin-button"
            >
                ➕ Add Service
            </button>

        </form>

    </section>


    <!-- SERVICE LIST -->

    <section class="admin-section">

        <div class="section-heading">

            <div>

                <h2>
                    Available Services
                </h2>

                <p>
                    Services currently visible to citizens.
                </p>

            </div>

        </div>


        <div class="management-grid">

            <?php if ($services && $services->num_rows > 0): ?>

                <?php while ($service = $services->fetch_assoc()): ?>

                    <div class="management-card">

                        <span class="management-icon">
                            🏛️
                        </span>


                        <div style="flex:1;">

                            <h3>
                                <?php
                                echo htmlspecialchars(
                                    $service["title"]
                                );
                                ?>
                            </h3>


                            <p>
                                <?php
                                echo nl2br(
                                    htmlspecialchars(
                                        $service["description"]
                                    )
                                );
                                ?>
                            </p>


                            <small>

                                Added

                                <?php

                                $service_time =
                                    strtotime(
                                        $service["created_at"]
                                    );

                                echo $service_time
                                    ? date(
                                        "d M Y",
                                        $service_time
                                    )
                                    : "Not available";

                                ?>

                            </small>


                            <form
                                method="POST"
                                style="margin-top:14px;"
                                onsubmit="return confirm('Delete this service?');"
                            >

                                <input
                                    type="hidden"
                                    name="action"
                                    value="delete"
                                >

                                <input
                                    type="hidden"
                                    name="service_id"
                                    value="<?php
                                        echo (int) $service["id"];
                                    ?>"
                                >

                                <button
                                    type="submit"
                                    class="admin-button admin-button-danger"
                                >
                                    🗑️ Delete
                                </button>

                            </form>

                        </div>

                    </div>

                <?php endwhile; ?>

            <?php else: ?>

                <div class="empty-state">

                    <div>
                        🏛️
                    </div>

                    <strong>
                        No services available
                    </strong>

                    <p>
                        Add your first Panchayat service above.
                    </p>

                </div>

            <?php endif; ?>

        </div>

    </section>

</main>