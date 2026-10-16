<?php

session_start();

if (!isset($_SESSION["admin_logged_in"])) {
    header("Location: login.php");
    exit;
}

require_once "../includes/db.php";

$error = "";


/*
|--------------------------------------------------------------------------
| Load Registered Citizens
|--------------------------------------------------------------------------
*/

$citizens = $conn->query(
    "SELECT
        id,
        name,
        email,
        phone,
        created_at
     FROM users
     ORDER BY created_at DESC"
);

if (!$citizens) {

    error_log(
        "Smart Gram Admin Citizens Query Error: " .
        $conn->error
    );

    $error =
        "Unable to load registered citizens right now. Please try again.";
}

?>

<?php include "nav.php"; ?>

<main class="admin-container">

    <div class="admin-page-heading">

        <div>

            <h2>
                Registered Citizens
            </h2>

            <p>
                View citizens registered on the Smart Gram platform.
            </p>

        </div>

    </div>


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

                        <th>#</th>
                        <th>Citizen</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Registered On</th>

                    </tr>

                </thead>

                <tbody>

                <?php if ($citizens && $citizens->num_rows > 0): ?>

                    <?php while ($row = $citizens->fetch_assoc()): ?>

                        <tr>

                            <td>

                                <strong>
                                    <?php
                                    echo (int) $row["id"];
                                    ?>
                                </strong>

                            </td>


                            <td>

                                <strong>
                                    <?php
                                    echo htmlspecialchars(
                                        $row["name"]
                                    );
                                    ?>
                                </strong>

                            </td>


                            <td>

                                <?php
                                echo htmlspecialchars(
                                    $row["email"]
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

                                $registered_time =
                                    strtotime(
                                        $row["created_at"]
                                    );

                                if ($registered_time) {

                                    echo date(
                                        "d M Y",
                                        $registered_time
                                    );

                                    echo "<br>";

                                    echo "<small>";

                                    echo date(
                                        "h:i A",
                                        $registered_time
                                    );

                                    echo "</small>";

                                } else {

                                    echo "Not available";

                                }

                                ?>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                <?php else: ?>

                    <tr>

                        <td
                            colspan="5"
                            class="empty-state"
                        >

                            <div>
                                👥
                            </div>

                            <strong>
                                No citizens registered
                            </strong>

                            <p>
                                Registered citizens will appear here.
                            </p>

                        </td>

                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </section>

</main>