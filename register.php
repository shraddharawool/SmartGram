<?php

session_start();

if (isset($_SESSION["user_id"])) {
    header("Location: dashboard.php");
    exit;
}

require_once "includes/db.php";

$error = "";
$success = "";

$name = "";
$email = "";
$phone = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";


    if (
        $name === "" ||
        $email === "" ||
        $phone === "" ||
        $password === "" ||
        $confirm_password === ""
    ) {

        $error = "Please fill in all fields.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Please enter a valid email address.";

    } elseif (!preg_match("/^[0-9]{10}$/", $phone)) {

        $error = "Please enter a valid 10-digit phone number.";

    } elseif (strlen($password) < 6) {

        $error = "Password must contain at least 6 characters.";

    } elseif ($password !== $confirm_password) {

        $error = "Passwords do not match.";

    } else {

        $check = $conn->prepare(
            "SELECT id
             FROM users
             WHERE email = ? OR phone = ?
             LIMIT 1"
        );

        if (!$check) {

            error_log(
                "Smart Gram Register Prepare Error: " . $conn->error
            );

            $error = "Smart Gram is temporarily unable to process registration. Please try again.";

        } else {

            $check->bind_param(
                "ss",
                $email,
                $phone
            );

            if (!$check->execute()) {

                error_log(
                    "Smart Gram Register Execute Error: " . $check->error
                );

                $error = "A connection problem occurred. Please try again.";

            } else {

                $result = $check->get_result();

                if ($result && $result->num_rows > 0) {

                    $error = "An account with this email or phone number already exists.";

                } else {

                    $hashed_password = password_hash(
                        $password,
                        PASSWORD_DEFAULT
                    );

                    $insert = $conn->prepare(
                        "INSERT INTO users
                        (name, email, phone, password)
                        VALUES (?, ?, ?, ?)"
                    );

                    if (!$insert) {

                        error_log(
                            "Smart Gram Register Insert Prepare Error: " . $conn->error
                        );

                        $error = "Unable to create your account. Please try again.";

                    } else {

                        $insert->bind_param(
                            "ssss",
                            $name,
                            $email,
                            $phone,
                            $hashed_password
                        );

                        if ($insert->execute()) {

                            $success = "Account created successfully. You can now login.";

                            $name = "";
                            $email = "";
                            $phone = "";

                        } else {

                            error_log(
                                "Smart Gram Register Insert Error: " . $insert->error
                            );

                            $error = "Unable to create your account. Please try again.";

                        }

                        $insert->close();
                    }
                }
            }

            $check->close();
        }
    }
}

?>

<?php include "header.php"; ?>


<main class="auth-container">


    <div class="auth-card">


        <div class="auth-icon">
            📝
        </div>


        <h2>
            Create Citizen Account
        </h2>


        <p class="auth-subtitle">
            Register with Smart Gram to access Panchayat services online.
        </p>


        <?php if ($error): ?>

            <div class="alert alert-error">
                <?php echo htmlspecialchars($error); ?>
            </div>

        <?php endif; ?>


        <?php if ($success): ?>

            <div class="alert alert-success">
                <?php echo htmlspecialchars($success); ?>
            </div>

        <?php endif; ?>


        <form
            method="POST"
            action=""
            autocomplete="on"
        >


            <div class="form-group">

                <label>
                    Full Name
                </label>

                <input
                    type="text"
                    name="name"
                    placeholder="Enter your full name"
                    value="<?php echo htmlspecialchars($name); ?>"
                    autocomplete="name"
                    required
                >

            </div>


            <div class="form-group">

                <label>
                    Email Address
                </label>

                <input
                    type="email"
                    name="email"
                    placeholder="Enter your email"
                    value="<?php echo htmlspecialchars($email); ?>"
                    autocomplete="email"
                    required
                >

            </div>


            <div class="form-group">

                <label>
                    Mobile Number
                </label>

                <input
                    type="tel"
                    name="phone"
                    placeholder="Enter 10-digit mobile number"
                    value="<?php echo htmlspecialchars($phone); ?>"
                    maxlength="10"
                    inputmode="numeric"
                    autocomplete="tel"
                    required
                >

            </div>


            <div class="form-group">

                <label>
                    Password
                </label>

                <input
                    type="password"
                    name="password"
                    placeholder="Minimum 6 characters"
                    minlength="6"
                    autocomplete="new-password"
                    required
                >

            </div>


            <div class="form-group">

                <label>
                    Confirm Password
                </label>

                <input
                    type="password"
                    name="confirm_password"
                    placeholder="Re-enter your password"
                    minlength="6"
                    autocomplete="new-password"
                    required
                >

            </div>


            <button
                type="submit"
                class="btn auth-button"
            >
                📝 Create Account
            </button>


        </form>


        <div class="auth-footer">

            <p>
                Already have an account?
            </p>

            <a href="login.php">
                🔐 Login to Smart Gram
            </a>

        </div>


    </div>


</main>


<?php include "footer.php"; ?>