<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

require_once "includes/db.php";

$user_id = $_SESSION["user_id"];

$success = "";
$error = "";

$stmt = $conn->prepare(
    "SELECT name, email, phone, created_at
     FROM users
     WHERE id = ?
     LIMIT 1"
);

if (!$stmt) {
    error_log(
        "Smart Gram Profile Prepare Error: " . $conn->error
    );

    die("Smart Gram is temporarily unable to load your profile. Please try again.");
}

$stmt->bind_param("i", $user_id);

if (!$stmt->execute()) {

    error_log(
        "Smart Gram Profile Execute Error: " . $stmt->error
    );

    $stmt->close();

    die("A connection problem occurred. Please try again.");
}

$result = $stmt->get_result();

if (!$result || $result->num_rows !== 1) {

    $stmt->close();

    $_SESSION = [];

    session_destroy();

    header("Location: login.php");
    exit;
}

$user = $result->fetch_assoc();

$name = $user["name"];
$email = $user["email"];
$phone = $user["phone"];
$created_at = $user["created_at"];

$stmt->close();


/*
|--------------------------------------------------------------------------
| UPDATE PROFILE
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $new_name = trim($_POST["name"] ?? "");
    $new_phone = trim($_POST["phone"] ?? "");

    if ($new_name === "" || $new_phone === "") {

        $error = "Name and phone number are required.";

    } elseif (!preg_match("/^[0-9]{10}$/", $new_phone)) {

        $error = "Please enter a valid 10-digit mobile number.";

    } else {

        $check = $conn->prepare(
            "SELECT id
             FROM users
             WHERE phone = ?
             AND id != ?
             LIMIT 1"
        );

        if (!$check) {

            error_log(
                "Smart Gram Profile Phone Check Error: " . $conn->error
            );

            $error = "Smart Gram is temporarily unable to update your profile. Please try again.";

        } else {

            $check->bind_param(
                "si",
                $new_phone,
                $user_id
            );

            if (!$check->execute()) {

                error_log(
                    "Smart Gram Profile Phone Check Execute Error: " . $check->error
                );

                $error = "A connection problem occurred. Please try again.";

            } else {

                $check_result = $check->get_result();

                if ($check_result && $check_result->num_rows > 0) {

                    $error = "This mobile number is already registered.";

                } else {

                    $update = $conn->prepare(
                        "UPDATE users
                         SET name = ?, phone = ?
                         WHERE id = ?"
                    );

                    if (!$update) {

                        error_log(
                            "Smart Gram Profile Update Prepare Error: " . $conn->error
                        );

                        $error = "Unable to update your profile. Please try again.";

                    } else {

                        $update->bind_param(
                            "ssi",
                            $new_name,
                            $new_phone,
                            $user_id
                        );

                        if ($update->execute()) {

                            $name = $new_name;
                            $phone = $new_phone;

                            $_SESSION["user_name"] = $new_name;

                            $success = "Profile updated successfully.";

                        } else {

                            error_log(
                                "Smart Gram Profile Update Error: " . $update->error
                            );

                            $error = "Unable to update your profile. Please try again.";
                        }

                        $update->close();
                    }
                }
            }

            $check->close();
        }
    }
}

?>

<?php include "header.php"; ?>


<main class="container">


    <a
        href="dashboard.php"
        class="page-back"
    >
        ← Back to Dashboard
    </a>


    <div class="page-title">

        <h2>
            👤 My Profile
        </h2>

        <p>
            View and manage your Smart Gram citizen account information.
        </p>

    </div>


    <?php if ($success): ?>

        <div class="success">
            <?php echo htmlspecialchars($success); ?>
        </div>

    <?php endif; ?>


    <?php if ($error): ?>

        <div class="error">
            <?php echo htmlspecialchars($error); ?>
        </div>

    <?php endif; ?>


    <div style="
        display:grid;
        grid-template-columns:1fr 1.5fr;
        gap:25px;
        align-items:start;
    ">


        <!-- PROFILE SUMMARY -->

        <div class="card">

            <div style="
                width:80px;
                height:80px;
                border-radius:50%;
                background:#e8f5ef;
                color:#087f5b;
                display:flex;
                align-items:center;
                justify-content:center;
                font-size:38px;
                margin-bottom:20px;
            ">
                👤
            </div>


            <h2 style="margin-bottom:5px;">
                <?php echo htmlspecialchars($name); ?>
            </h2>

            <p style="color:#667085;">
                Smart Gram Citizen Account
            </p>


            <hr style="
                border:none;
                border-top:1px solid #e5ebe8;
                margin:20px 0;
            ">


            <p>

                <strong>
                    📧 Email
                </strong>

                <br>

                <span style="color:#667085;">
                    <?php echo htmlspecialchars($email); ?>
                </span>

            </p>


            <p>

                <strong>
                    📱 Phone
                </strong>

                <br>

                <span style="color:#667085;">
                    <?php echo htmlspecialchars($phone); ?>
                </span>

            </p>


            <p>

                <strong>
                    📅 Account Created
                </strong>

                <br>

                <span style="color:#667085;">

                    <?php

                    $created_timestamp = strtotime($created_at);

                    echo $created_timestamp
                        ? date("d M Y", $created_timestamp)
                        : "—";

                    ?>

                </span>

            </p>

        </div>


        <!-- EDIT PROFILE -->

        <div class="card">

            <h2>
                ✏️ Edit Profile
            </h2>

            <p style="color:#667085;">
                Update your name and mobile number.
            </p>


            <form
                method="POST"
                action=""
            >


                <label for="name">
                    Full Name
                </label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    value="<?php echo htmlspecialchars($name); ?>"
                    placeholder="Enter your full name"
                    autocomplete="name"
                    required
                >


                <label for="email">
                    Email Address
                </label>

                <input
                    type="email"
                    id="email"
                    value="<?php echo htmlspecialchars($email); ?>"
                    disabled
                    style="
                        background:#f4f6f5;
                        cursor:not-allowed;
                    "
                >

                <small style="
                    display:block;
                    color:#7b8794;
                    margin-top:-10px;
                    margin-bottom:18px;
                ">
                    Email address cannot be changed.
                </small>


                <label for="phone">
                    Mobile Number
                </label>

                <input
                    type="tel"
                    id="phone"
                    name="phone"
                    value="<?php echo htmlspecialchars($phone); ?>"
                    maxlength="10"
                    inputmode="numeric"
                    autocomplete="tel"
                    placeholder="10-digit mobile number"
                    required
                >


                <button
                    type="submit"
                    style="width:100%;"
                >
                    💾 Save Changes
                </button>


            </form>

        </div>


    </div>


    <!-- ACCOUNT ACTIONS -->

    <div class="card">

        <h2>
            ⚡ Account Actions
        </h2>

        <div class="quick-actions">


            <a
                href="dashboard.php"
                class="quick-action"
            >
                📊 Dashboard
            </a>


            <a
                href="apply.php"
                class="quick-action"
            >
                📝 Apply for Service
            </a>


            <a
                href="track.php"
                class="quick-action"
            >
                🔎 Track Application
            </a>


            <a
                href="grievance.php"
                class="quick-action"
            >
                📣 Submit Grievance
            </a>


        </div>

    </div>


</main>


<style>

@media (max-width: 750px) {

    .container > div[style*="grid-template-columns:1fr 1.5fr"] {
        grid-template-columns: 1fr !important;
    }

}

</style>


<?php include "footer.php"; ?>