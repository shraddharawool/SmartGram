<?php

session_start();

if (isset($_SESSION["user_id"])) {
    header("Location: dashboard.php");
    exit;
}

require_once "includes/db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($email === "" || $password === "") {

        $error = "Please enter your email and password.";

    } else {

        $stmt = $conn->prepare(
            "SELECT id, name, email, password
             FROM users
             WHERE email = ?
             LIMIT 1"
        );

        if (!$stmt) {

            error_log(
                "Smart Gram Login Prepare Error: " . $conn->error
            );

            $error = "Smart Gram is temporarily unable to process your login. Please try again.";

        } else {

            $stmt->bind_param("s", $email);

            if (!$stmt->execute()) {

                error_log(
                    "Smart Gram Login Execute Error: " . $stmt->error
                );

                $error = "A connection problem occurred. Please try again.";

            } else {

                $result = $stmt->get_result();

                if ($result && $result->num_rows === 1) {

                    $user = $result->fetch_assoc();

                    if (password_verify(
                        $password,
                        $user["password"]
                    )) {

                        session_regenerate_id(true);

                        $_SESSION["user_id"] = $user["id"];
                        $_SESSION["user_name"] = $user["name"];
                        $_SESSION["user_email"] = $user["email"];

                        header("Location: dashboard.php");
                        exit;

                    } else {

                        $error = "Invalid email or password.";

                    }

                } else {

                    $error = "Invalid email or password.";

                }

            }

            $stmt->close();
        }
    }
}

?>

<?php include "header.php"; ?>


<main class="auth-container">


    <div class="auth-card">


        <div class="auth-icon">
            🔐
        </div>


        <h2>
            Citizen Login
        </h2>


        <p class="auth-subtitle">
            Sign in to access your Smart Gram account.
        </p>


        <?php if ($error): ?>

            <div class="alert alert-error">
                <?php echo htmlspecialchars($error); ?>
            </div>

        <?php endif; ?>


        <form
            method="POST"
            action=""
            autocomplete="on"
        >


            <div class="form-group">

                <label>
                    Email Address
                </label>

                <input
                    type="email"
                    name="email"
                    placeholder="Enter your email"
                    value="<?php echo htmlspecialchars(
                        $_POST["email"] ?? ""
                    ); ?>"
                    autocomplete="email"
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
                    placeholder="Enter your password"
                    autocomplete="current-password"
                    required
                >

            </div>


            <button
                type="submit"
                class="btn auth-button"
            >
                🔐 Login
            </button>


        </form>


        <div class="auth-footer">

            <p>
                Don't have an account?
            </p>

            <a href="register.php">
                Create a Citizen Account
            </a>

        </div>


    </div>


</main>


<?php include "footer.php"; ?>