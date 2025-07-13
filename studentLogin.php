<?php
session_start();
require_once __DIR__ . '/../includes/recordConnection.php';

// If already logged in, redirect to default or requested page
if (isset($_SESSION['studentId'])) {
    $target = $_GET['redirect'] ?? 'studentInput.php';
    header("Location: $target");
    exit;
}

// Handle login submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $signature = $_POST['signature'] ?? '';
    $password  = $_POST['password'] ?? '';
    $redirect  = $_POST['redirect'] ?? 'studentInput.php';

    // Prepare query
    $sql = "SELECT * FROM students WHERE signature = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 's', $signature);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $student = mysqli_fetch_assoc($res);

    if ($student && password_verify($password, $student['passwordHash'])) {
        // Valid login
        session_regenerate_id(true); // prevent session fixation
        $_SESSION['studentId']        = $student['studentId'];
        $_SESSION['studentFirstName'] = $student['firstName'];
        $_SESSION['studentSurname']   = $student['surname'];
        $_SESSION['studentSignature'] = $student['signature'];
        $_SESSION['lastActivity']     = time();

        // Redirect to intended page
        header("Location: $redirect");
        exit;
    } else {
        $loginError = "Login failed. Check your signature and password.";
    }
}

// Get redirect query param for form reuse
$redirectTarget = $_GET['redirect'] ?? 'studentInput.php';
?>

<!DOCTYPE html>
<html>

<head>
    <title>Student Login</title>
    <link rel="stylesheet" href="./styles/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap" rel="stylesheet">
</head>

<body>
    <div class="container">


        <h2>Student Login</h2>

        <?php if (isset($_GET['timeout'])): ?>
            <div class="notice">
                Your session expired due to inactivity. Please log in again.
            </div>
        <?php endif; ?>

        <?php if (!empty($loginError)): ?>
            <div class="error"><?= htmlspecialchars($loginError) ?></div>
        <?php endif; ?>

        <form method="POST" action="studentLogin.php">
            <label for="signature">Signature:</label><br>
            <input type="text" name="signature" id="signature" required><br><br>

            <label for="password">Password:</label><br>
            <input type="password" name="password" id="password" required><br><br>

            <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirectTarget) ?>">

            <button type="submit">Login</button>
        </form>
        <p>
            Don't have an account?
            <br>
            <a href="studentRegister.php?redirect=<?= urlencode($_GET['redirect'] ?? 'studentInput.php') ?>">Create one here</a>
        </p>
    </div>
</body>

</html>