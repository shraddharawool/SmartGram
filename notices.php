<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

require_once "includes/db.php";

$notices = null;
$error = "";

$stmt = $conn->prepare(
    "SELECT
        id,
        title,
        content,
        created_at
     FROM notices
     ORDER BY created_at DESC"
);

if (!$stmt) {

    error_log(
        "Smart Gram Notices Prepare Error: " .
        $conn->error
    );

    $error =
        "Unable to load notices right now. Please try again.";

} elseif (!$stmt->execute()) {

    error_log(
        "Smart Gram Notices Execute Error: " .
        $stmt->error
    );

    $error =
        "Unable to load notices right now. Please try again.";

} else {

    $notices = $stmt->get_result();
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
                Smart Gram Updates
            </span>

            <h2>
                Village Notices
            </h2>

            <p>
                Stay informed about important Panchayat announcements,
                meetings and citizen updates.
            </p>

        </div>

    </section>

    <?php if ($error): ?>

        <div class="alert alert-error">
            <?php echo htmlspecialchars($error); ?>
        </div>

    <?php endif; ?>

    <?php if ($notices && $notices->num_rows > 0): ?>

        <section class="content-grid">

            <?php while ($notice = $notices->fetch_assoc()): ?>

                <article class="content-card">

                    <div class="content-card-icon">
                        📢
                    </div>

                    <div class="content-card-body">

                        <span class="content-date">

                            <?php

                            $notice_time = strtotime(
                                $notice["created_at"]
                            );

                            echo $notice_time
                                ? date(
                                    "d M Y",
                                    $notice_time
                                )
                                : "Date unavailable";

                            ?>

                        </span>

                        <h3>
                            <?php echo htmlspecialchars(
                                $notice["title"]
                            ); ?>
                        </h3>

                        <p>
                            <?php echo nl2br(
                                htmlspecialchars(
                                    $notice["content"]
                                )
                            ); ?>
                        </p>

                    </div>

                </article>

            <?php endwhile; ?>

        </section>

    <?php else: ?>

        <section class="empty-state">

            <div class="empty-icon">
                📢
            </div>

            <h3>
                No notices available
            </h3>

            <p>
                New Panchayat announcements will appear here.
            </p>

        </section>

    <?php endif; ?>

</main>

<?php

if ($stmt) {
    $stmt->close();
}

include "footer.php";

?>