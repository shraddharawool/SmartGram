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
| Add / Delete Scheme
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $action = $_POST["action"] ?? "";


    /*
    |--------------------------------------------------------------------------
    | Add Scheme
    |--------------------------------------------------------------------------
    */

    if ($action === "add") {

        $title = trim($_POST["title"] ?? "");
        $description = trim($_POST["description"] ?? "");

        if ($title === "" || $description === "") {

            $error = "Please fill in all fields.";

        } else {

            $stmt = $conn->prepare(
                "INSERT INTO schemes (title, description)
                 VALUES (?, ?)"
            );

            if (!$stmt) {

                error_log(
                    "Smart Gram Admin Scheme Insert Prepare Error: " .
                    $conn->error
                );

                $error =
                    "Unable to add scheme. Please try again.";

            } else {

                $stmt->bind_param(
                    "ss",
                    $title,
                    $description
                );

                if ($stmt->execute()) {

                    $message =
                        "Scheme added successfully.";

                } else {

                    error_log(
                        "Smart Gram Admin Scheme Insert Error: " .
                        $stmt->error
                    );

                    $error =
                        "Unable to add scheme. Please try again.";
                }

                $stmt->close();
            }
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Delete Scheme
    |--------------------------------------------------------------------------
    */

    elseif ($action === "delete") {

        $scheme_id = (int) (
            $_POST["scheme_id"] ?? 0
        );

        if ($scheme_id <= 0) {

            $error = "Invalid scheme details.";

        } else {

            $stmt = $conn->prepare(
                "DELETE FROM schemes
                 WHERE id = ?"
            );

            if (!$stmt) {

                error_log(
                    "Smart Gram Admin Scheme Delete Prepare Error: " .
                    $conn->error
                );

                $error =
                    "Unable to delete scheme. Please try again.";

            } else {

                $stmt->bind_param(
                    "i",
                    $scheme_id
                );

                if ($stmt->execute()) {

                    if ($stmt->affected_rows > 0) {

                        $message =
                            "Scheme deleted successfully.";

                    } else {

                        $error =
                            "Scheme was not found.";
                    }

                } else {

                    error_log(
                        "Smart Gram Admin Scheme Delete Error: " .
                        $stmt->error
                    );

                    $error =
                        "Unable to delete scheme. Please try again.";
                }

                $stmt->close();
            }
        }
    }
}


/*
|--------------------------------------------------------------------------
| Load Schemes
|--------------------------------------------------------------------------
*/

$schemes = $conn->query(
    "SELECT
        id,
        title,
        description,
        created_at
     FROM schemes
     ORDER BY created_at DESC"
);

if (!$schemes) {

    error_log(
        "Smart Gram Admin Schemes Query Error: " .
        $conn->error
    );

    $error =
        "Unable to load schemes right now. Please try again.";
}

?>

<?php include "nav.php"; ?>

<main class="admin-container">

    <div class="admin-page-heading">

        <div>

            <h2>
                Government Schemes
            </h2>

            <p>
                Manage welfare schemes and programmes displayed to citizens.
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


    <!-- ADD SCHEME -->

    <section class="admin-form-card">

        <div class="section-heading">

            <div>

                <h2>
                    Add New Scheme
                </h2>

                <p>
                    Add information about a government scheme or programme.
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

                <label for="scheme-title">
                    Scheme Title
                </label>

                <input
                    type="text"
                    id="scheme-title"
                    name="title"
                    placeholder="Example: PM Awas Yojana"
                    maxlength="200"
                    required
                >

            </div>


            <div class="form-group">

                <label for="scheme-description">
                    Description
                </label>

                <textarea
                    id="scheme-description"
                    name="description"
                    rows="5"
                    placeholder="Describe the scheme..."
                    required
                ></textarea>

            </div>


            <button
                type="submit"
                class="admin-button"
            >
                ➕ Add Scheme
            </button>

        </form>

    </section>


    <!-- SCHEME LIST -->

    <section class="admin-section">

        <div class="section-heading">

            <div>

                <h2>
                    Available Schemes
                </h2>

                <p>
                    Government schemes currently shown on Smart Gram.
                </p>

            </div>

        </div>


        <div class="management-grid">

            <?php if ($schemes && $schemes->num_rows > 0): ?>

                <?php while ($scheme = $schemes->fetch_assoc()): ?>

                    <div class="management-card">

                        <span class="management-icon">
                            💰
                        </span>


                        <div style="flex:1;">

                            <h3>
                                <?php
                                echo htmlspecialchars(
                                    $scheme["title"]
                                );
                                ?>
                            </h3>


                            <p>
                                <?php
                                echo nl2br(
                                    htmlspecialchars(
                                        $scheme["description"]
                                    )
                                );
                                ?>
                            </p>


                            <small>

                                Added

                                <?php

                                $scheme_time =
                                    strtotime(
                                        $scheme["created_at"]
                                    );

                                echo $scheme_time
                                    ? date(
                                        "d M Y",
                                        $scheme_time
                                    )
                                    : "Not available";

                                ?>

                            </small>


                            <form
                                method="POST"
                                style="margin-top:14px;"
                                onsubmit="return confirm('Delete this scheme?');"
                            >

                                <input
                                    type="hidden"
                                    name="action"
                                    value="delete"
                                >

                                <input
                                    type="hidden"
                                    name="scheme_id"
                                    value="<?php
                                        echo (int) $scheme["id"];
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
                        💰
                    </div>

                    <strong>
                        No schemes available
                    </strong>

                    <p>
                        Add your first government scheme above.
                    </p>

                </div>

            <?php endif; ?>

        </div>

    </section>

</main>