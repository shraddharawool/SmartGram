<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

require_once "includes/db.php";

$user_id = $_SESSION["user_id"];

$error = "";
$success = "";

$applicant_name = $_SESSION["user_name"] ?? "";
$applicant_phone = "";

$selected_service = "";
$address = "";


/*
|--------------------------------------------------------------------------
| Load Current User
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare(
    "SELECT name, phone
     FROM users
     WHERE id = ?
     LIMIT 1"
);

if (!$stmt) {

    error_log(
        "Smart Gram Apply User Prepare Error: " . $conn->error
    );

    die("Smart Gram is temporarily unable to load your account. Please try again.");
}

$stmt->bind_param("i", $user_id);

if (!$stmt->execute()) {

    error_log(
        "Smart Gram Apply User Execute Error: " . $stmt->error
    );

    $stmt->close();

    die("A connection problem occurred. Please try again.");
}

$result = $stmt->get_result();

if ($result && $result->num_rows === 1) {

    $user = $result->fetch_assoc();

    $applicant_name = $user["name"];
    $applicant_phone = $user["phone"];

} else {

    $stmt->close();

    $_SESSION = [];

    session_destroy();

    header("Location: login.php");
    exit;
}

$stmt->close();


/*
|--------------------------------------------------------------------------
| Load Services
|--------------------------------------------------------------------------
*/

$services = $conn->query(
    "SELECT title
     FROM services
     ORDER BY title ASC"
);

if (!$services) {

    error_log(
        "Smart Gram Apply Services Query Error: " . $conn->error
    );
}


/*
|--------------------------------------------------------------------------
| Submit Application
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $selected_service = trim($_POST["service"] ?? "");
    $applicant_name = trim($_POST["applicant_name"] ?? "");
    $applicant_phone = trim($_POST["applicant_phone"] ?? "");
    $address = trim($_POST["address"] ?? "");


    if (
        $selected_service === "" ||
        $applicant_name === "" ||
        $applicant_phone === "" ||
        $address === ""
    ) {

        $error = "Please fill in all required fields.";

    } elseif (!preg_match("/^[0-9]{10}$/", $applicant_phone)) {

        $error = "Please enter a valid 10-digit mobile number.";

    } else {

        /*
        |--------------------------------------------------------------------------
        | Verify Selected Service Exists
        |--------------------------------------------------------------------------
        */

        $service_check = $conn->prepare(
            "SELECT id
             FROM services
             WHERE title = ?
             LIMIT 1"
        );

        if (!$service_check) {

            error_log(
                "Smart Gram Service Check Prepare Error: " . $conn->error
            );

            $error = "Smart Gram is temporarily unable to process your application. Please try again.";

        } else {

            $service_check->bind_param(
                "s",
                $selected_service
            );

            if (!$service_check->execute()) {

                error_log(
                    "Smart Gram Service Check Execute Error: " . $service_check->error
                );

                $error = "A connection problem occurred. Please try again.";

            } else {

                $service_result = $service_check->get_result();

                if (!$service_result || $service_result->num_rows !== 1) {

                    $error = "The selected service is no longer available. Please select another service.";

                } else {

                    /*
                    |--------------------------------------------------------------------------
                    | Generate Unique Application Number
                    |--------------------------------------------------------------------------
                    */

                    $application_number = "";

                    $insert = $conn->prepare(
                        "INSERT INTO applications
                        (
                            application_number,
                            user_id,
                            service,
                            applicant_name,
                            phone,
                            address,
                            status
                        )
                        VALUES (?, ?, ?, ?, ?, ?, 'Pending')"
                    );

                    if (!$insert) {

                        error_log(
                            "Smart Gram Application Prepare Error: " . $conn->error
                        );

                        $error = "Unable to prepare your application. Please try again.";

                    } else {

                        $submitted = false;

                        /*
                        |--------------------------------------------------------------------------
                        | Retry In Case Application Number Already Exists
                        |--------------------------------------------------------------------------
                        */

                        for ($attempt = 0; $attempt < 3; $attempt++) {

                            $application_number =
                                "SG-" .
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

                            $insert->bind_param(
                                "sissss",
                                $application_number,
                                $user_id,
                                $selected_service,
                                $applicant_name,
                                $applicant_phone,
                                $address
                            );

                            if ($insert->execute()) {

                                $submitted = true;
                                break;

                            }

                            /*
                            | 1062 = duplicate entry
                            */

                            if ($conn->errno != 1062) {
                                break;
                            }
                        }


                        if ($submitted) {

                            $success =
                                "Application submitted successfully. Your application number is " .
                                $application_number . ".";

                            $selected_service = "";
                            $address = "";

                        } else {

                            error_log(
                                "Smart Gram Application Insert Error: " .
                                $insert->error
                            );

                            $error =
                                "Unable to submit your application. Please try again.";
                        }

                        $insert->close();
                    }
                }
            }

            $service_check->close();
        }
    }
}

?>

<?php include "header.php"; ?>


<main class="container">


    <a
        href="services.php"
        class="page-back"
    >
        ← Back to Services
    </a>


    <!-- PAGE HEADER -->

    <section class="page-hero">

        <div>

            <span class="eyebrow">
                Online Application
            </span>

            <h2>
                Apply for a Panchayat Service
            </h2>

            <p>
                Submit your service request online through Smart Gram.
            </p>

        </div>

    </section>


    <!-- SUCCESS -->

    <?php if ($success): ?>

        <div class="alert alert-success">

            <strong>
                Application Submitted
            </strong>

            <br>

            <?php echo htmlspecialchars($success); ?>

            <br><br>

            <a
                href="track.php"
                class="btn"
            >
                🔎 Track Application
            </a>

        </div>

    <?php endif; ?>


    <!-- ERROR -->

    <?php if ($error): ?>

        <div class="alert alert-error">
            <?php echo htmlspecialchars($error); ?>
        </div>

    <?php endif; ?>


    <!-- APPLICATION FORM -->

    <section class="form-layout">


        <div class="form-card">


            <h3>
                📝 Application Details
            </h3>


            <p class="form-intro">
                Please provide accurate information before submitting
                your application.
            </p>


            <form
                method="POST"
                action=""
            >


                <div class="form-group">

                    <label for="service">
                        Select Service
                    </label>

                    <select
                        id="service"
                        name="service"
                        required
                    >

                        <option value="">
                            -- Select a Service --
                        </option>


                        <?php if ($services && $services->num_rows > 0): ?>

                            <?php while ($service_row = $services->fetch_assoc()): ?>

                                <option
                                    value="<?php echo htmlspecialchars($service_row["title"]); ?>"
                                    <?php
                                    if (
                                        $selected_service ===
                                        $service_row["title"]
                                    ) {
                                        echo "selected";
                                    }
                                    ?>
                                >
                                    <?php
                                    echo htmlspecialchars(
                                        $service_row["title"]
                                    );
                                    ?>
                                </option>

                            <?php endwhile; ?>

                        <?php endif; ?>


                    </select>

                </div>


                <div class="form-group">

                    <label for="applicant_name">
                        Applicant Name
                    </label>

                    <input
                        type="text"
                        id="applicant_name"
                        name="applicant_name"
                        value="<?php echo htmlspecialchars($applicant_name); ?>"
                        placeholder="Enter applicant name"
                        autocomplete="name"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="applicant_phone">
                        Mobile Number
                    </label>

                    <input
                        type="tel"
                        id="applicant_phone"
                        name="applicant_phone"
                        value="<?php echo htmlspecialchars($applicant_phone); ?>"
                        placeholder="Enter 10-digit mobile number"
                        maxlength="10"
                        inputmode="numeric"
                        autocomplete="tel"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="address">
                        Address
                    </label>

                    <textarea
                        id="address"
                        name="address"
                        rows="5"
                        placeholder="Enter your complete address"
                        required
                    ><?php echo htmlspecialchars($address); ?></textarea>

                </div>


                <button
                    type="submit"
                    class="btn"
                >
                    📝 Submit Application
                </button>


            </form>


        </div>


        <!-- INFORMATION -->

        <aside class="info-card">


            <div class="info-icon">
                💡
            </div>


            <h3>
                Before You Submit
            </h3>


            <ul>

                <li>
                    Make sure all information is correct.
                </li>

                <li>
                    Use an active mobile number.
                </li>

                <li>
                    Provide your complete address.
                </li>

                <li>
                    Save your application number after submission.
                </li>

                <li>
                    Use Track Application to check the status.
                </li>

            </ul>


        </aside>


    </section>


</main>


<?php include "footer.php"; ?>