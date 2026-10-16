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
| Add / Delete Notice
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $action = $_POST["action"] ?? "";


    /*
    |--------------------------------------------------------------------------
    | Add Notice
    |--------------------------------------------------------------------------
    */

    if ($action === "add") {

        $title = trim($_POST["title"] ?? "");
        $content = trim($_POST["content"] ?? "");

        if ($title === "" || $content === "") {

            $error = "Please fill in all fields.";

        } else {

            $stmt = $conn->prepare(
                "INSERT INTO notices (title, content)
                 VALUES (?, ?)"
            );

            if (!$stmt) {

                error_log(
                    "Smart Gram Admin Notice Insert Prepare Error: " .
                    $conn->error
                );

                $error =
                    "Unable to publish notice. Please try again.";

            } else {

                $stmt->bind_param(
                    "ss",
                    $title,
                    $content
                );

                if ($stmt->execute()) {

                    $message =
                        "Notice published successfully.";

                } else {

                    error_log(
                        "Smart Gram Admin Notice Insert Error: " .
                        $stmt->error
                    );

                    $error =
                        "Unable to publish notice. Please try again.";
                }

                $stmt->close();
            }
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Delete Notice
    |--------------------------------------------------------------------------
    */

    elseif ($action === "delete") {

        $notice_id = (int) (
            $_POST["notice_id"] ?? 0
        );

        if ($notice_id <= 0) {

            $error = "Invalid notice details.";

        } else {

            $stmt = $conn->prepare(
                "DELETE FROM notices
                 WHERE id = ?"
            );

            if (!$stmt) {

                error_log(
                    "Smart Gram Admin Notice Delete Prepare Error: " .
                    $conn->error
                );

                $error =
                    "Unable to delete notice. Please try again.";

            } else {

                $stmt->bind_param(
                    "i",
                    $notice_id
                );

                if ($stmt->execute()) {

                    if ($stmt->affected_rows > 0) {

                        $message =
                            "Notice deleted successfully.";

                    } else {

                        $error =
                            "Notice was not found.";
                    }

                } else {

                    error_log(
                        "Smart Gram Admin Notice Delete Error: " .
                        $stmt->error
                    );

                    $error =
                        "Unable to delete notice. Please try again.";
                }

                $stmt->close();
            }
        }
    }
}


/*
|--------------------------------------------------------------------------
| Load Notices
|--------------------------------------------------------------------------
*/

$notices = $conn->query(
    "SELECT
        id,
        title,
        content,
        created_at
     FROM notices
     ORDER BY created_at DESC"
);

if (!$notices) {

    error_log(
        "Smart Gram Admin Notices Query Error: " .
        $conn->error
    );

    $error =
        "Unable to load notices right now. Please try again.";
}

?>

<?php include "nav.php"; ?>

<main class="admin-container">

    <div class="admin-page-heading">

        <div>

            <h2>
                Notices
            </h2>

            <p>
                Publish important announcements for Smart Gram citizens.
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


    <!-- ADD NOTICE -->

    <section class="admin-form-card">

        <div class="section-heading">

            <div>

                <h2>
                    Publish New Notice
                </h2>

                <p>
                    Add an announcement that citizens should know about.
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

                <label for="notice-title">
                    Notice Title
                </label>

                <input
                    type="text"
                    id="notice-title"
                    name="title"
                    placeholder="Example: Village Development Meeting"
                    maxlength="200"
                    required
                >

            </div>


            <div class="form-group">

                <label for="notice-content">
                    Notice Content
                </label>

                <textarea
                    id="notice-content"
                    name="content"
                    rows="5"
                    placeholder="Write the notice details..."
                    required
                ></textarea>

            </div>


            <button
                type="submit"
                class="admin-button"
            >
                📢 Publish Notice
            </button>

        </form>

    </section>


    <!-- NOTICE LIST -->

    <section class="admin-section">

        <div class="section-heading">

            <div>

                <h2>
                    Published Notices
                </h2>

                <p>
                    Notices currently available on the citizen portal.
                </p>

            </div>

        </div>


        <div class="management-grid">

            <?php if ($notices && $notices->num_rows > 0): ?>

                <?php while ($notice = $notices->fetch_assoc()): ?>

                    <div class="management-card">

                        <span class="management-icon">
                            📢
                        </span>


                        <div style="flex:1;">

                            <h3>
                                <?php
                                echo htmlspecialchars(
                                    $notice["title"]
                                );
                                ?>
                            </h3>


                            <p>
                                <?php
                                echo nl2br(
                                    htmlspecialchars(
                                        $notice["content"]
                                    )
                                );
                                ?>
                            </p>


                            <small>

                                Published

                                <?php

                                $notice_time =
                                    strtotime(
                                        $notice["created_at"]
                                    );

                                echo $notice_time
                                    ? date(
                                        "d M Y",
                                        $notice_time
                                    )
                                    : "Not available";

                                ?>

                            </small>


                            <form
                                method="POST"
                                style="margin-top:14px;"
                                onsubmit="return confirm('Delete this notice?');"
                            >

                                <input
                                    type="hidden"
                                    name="action"
                                    value="delete"
                                >

                                <input
                                    type="hidden"
                                    name="notice_id"
                                    value="<?php
                                        echo (int) $notice["id"];
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
                        📢
                    </div>

                    <strong>
                        No notices published
                    </strong>

                    <p>
                        Publish your first notice above.
                    </p>

                </div>

            <?php endif; ?>

        </div>

    </section>

</main>