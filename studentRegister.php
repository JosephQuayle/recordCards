<?php
session_start();
require_once __DIR__ . '/../includes/recordConnection.php';

$redirectTarget = $_GET['redirect'] ?? $_POST['redirect'] ?? 'studentLogin.php';
$signupSuccess = false;
$signupError = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = $_POST['firstName'] ?? '';
    $surname   = $_POST['surname'] ?? '';
    $signature = $_POST['signature'] ?? '';
    $password  = $_POST['password'] ?? '';
    $confirm   = $_POST['confirmPassword'] ?? '';
    $redirect  = $_POST['redirect'] ?? 'studentLogin.php';

    if ($password !== $confirm) {
        $signupError = "Passwords do not match.";
    } else {
        // Hash password
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        // Insert into DB
        $sql = "INSERT INTO students (firstName, surname, signature, passwordHash) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'ssss', $firstName, $surname, $signature, $passwordHash);

        if (mysqli_stmt_execute($stmt)) {
            // Success — redirect to login page
            header("Location: studentLogin.php?redirect=" . urlencode($redirect));
            exit;
        } else {
            $signupError = "Signup failed. Signature might already be in use.";
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Student Sign Up</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./styles/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap" rel="stylesheet">
</head>

<body>
    <h2>Student Sign Up</h2>

    <?php if ($signupError): ?>
        <div class="error"><?= htmlspecialchars($signupError) ?></div>
    <?php endif; ?>

    <form method="POST" action="studentRegister.php">
        <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirectTarget) ?>">

        <label for="firstName">First Name:</label>
        <input type="text" name="firstName" id="firstName" required>

        <label for="surname">Surname:</label>
        <input type="text" name="surname" id="surname" required>

        <label for="signature">Signature:</label>
        <input type="text" name="signature" id="signature" maxlength="15" required>

        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required>

        <label for="confirmPassword">Confirm Password:</label>
        <input type="password" name="confirmPassword" id="confirmPassword" required>

        <button type="submit" style="margin-top: 1em;">Sign Up</button>
    </form>
</body>

</html>