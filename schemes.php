<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

require_once "includes/db.php";

$error = "";
$schemes = [];

/*
|--------------------------------------------------------------------------
| Load Government Schemes
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare(
    "SELECT
        id,
        title,
        description,
        created_at
     FROM schemes
     ORDER BY created_at DESC"
);

if (!$stmt) {

    error_log(
        "Smart Gram Schemes Prepare Error: " .
        $conn->error
    );

    $error =
        "Unable to load government schemes right now. Please try again.";

} else {

    if (!$stmt->execute()) {

        error_log(
            "Smart Gram Schemes Execute Error: " .
            $stmt->error
        );

        $error =
            "Unable to load government schemes right now. Please try again.";

    } else {

        $result = $stmt->get_result();

        if ($result) {

            while ($row = $result->fetch_assoc()) {
                $schemes[] = $row;
            }

        } else {

            error_log(
                "Smart Gram Schemes Result Error: " .
                $stmt->error
            );

            $error =
                "Unable to load government schemes right now. Please try again.";
        }
    }

    $stmt->close();
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
                Smart Gram Services
            </span>

            <h2>
                Government Schemes
            </h2>

            <p>
                Explore government schemes and welfare programmes
                available to eligible citizens.
            </p>

        </div>

    </section>

    <?php if ($error): ?>

        <div class="alert alert-error">
            <?php echo htmlspecialchars($error); ?>
        </div>

    <?php endif; ?>


    <?php if (!$error && empty($schemes)): ?>

        <section class="empty-state">

            <div class="empty-icon">
                💰
            </div>

            <h3>
                No Schemes Available
            </h3>

            <p>
                Government scheme information will appear here
                when it is published by the Panchayat.
            </p>

        </section>

    <?php elseif (!empty($schemes)): ?>

        <section class="content-grid">

            <?php foreach ($schemes as $scheme): ?>

                <article class="content-card">

                    <div class="content-card-icon">
                        💰
                    </div>

                    <div class="content-card-body">

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

                        <div class="content-date">

                            📅 Published:

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

                        </div>

                    </div>

                </article>

            <?php endforeach; ?>

        </section>

    <?php endif; ?>

</main>

<?php include "footer.php"; ?>