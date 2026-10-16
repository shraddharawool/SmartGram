<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

require_once "includes/db.php";


/*
|--------------------------------------------------------------------------
| Load Services
|--------------------------------------------------------------------------
*/

$services = false;

$query = $conn->query(
    "SELECT id, title, description
     FROM services
     ORDER BY id DESC"
);

if ($query) {

    $services = $query;

} else {

    error_log(
        "Smart Gram Services Query Error: " . $conn->error
    );
}

?>

<?php include "header.php"; ?>


<main class="container">


    <!-- PAGE HEADER -->

    <section class="page-hero">

        <div>

            <span class="eyebrow">
                Smart Gram Services
            </span>

            <h2>
                Panchayat Services
            </h2>

            <p>
                Access essential Gram Panchayat services online
                without unnecessary visits to the office.
            </p>

        </div>

    </section>


    <!-- SERVICES -->

    <?php if ($services && $services->num_rows > 0): ?>


        <section class="service-grid">


            <?php while ($service = $services->fetch_assoc()): ?>


                <article class="service-card">


                    <div class="service-icon">
                        🏛️
                    </div>


                    <h3>
                        <?php
                        echo htmlspecialchars(
                            $service["title"]
                        );
                        ?>
                    </h3>


                    <p>
                        <?php
                        echo htmlspecialchars(
                            $service["description"]
                        );
                        ?>
                    </p>


                    <a
                        href="apply.php"
                        class="service-link"
                    >
                        Apply for Service →
                    </a>


                </article>


            <?php endwhile; ?>


        </section>


    <?php else: ?>


        <section class="empty-state">


            <div class="empty-icon">
                🏛️
            </div>


            <h3>
                No services available
            </h3>


            <p>
                Panchayat services will be listed here
                when they become available.
            </p>


        </section>


    <?php endif; ?>


    <!-- ASSISTANCE -->

    <section class="dashboard-section">


        <div class="info-banner">


            <div class="info-icon">
                💡
            </div>


            <div>

                <h3>
                    Need assistance?
                </h3>

                <p>
                    If you are unsure which service you need,
                    contact the Gram Panchayat office for guidance.
                </p>

            </div>


        </div>


    </section>


</main>


<?php include "footer.php"; ?>