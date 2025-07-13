<?php
session_start();
require_once __DIR__ . '/../includes/recordConnection.php';

$loginError = '';
$redirectTarget = $_GET['redirect'] ?? 'adminDashboard.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $redirect = $_POST['redirect'] ?? 'adminDashboard.php';

    $sql = "SELECT * FROM admins WHERE username = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 's', $username);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $admin = mysqli_fetch_assoc($res);

    if ($admin && password_verify($password, $admin['passwordHash'])) {
        session_regenerate_id(true);
        $_SESSION['adminId'] = $admin['adminId'];
        $_SESSION['adminUser'] = $admin['username'];
        header("Location: $redirect");
        exit;
    } else {
        $loginError = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
</head>

<body>
    <h2>Admin Login</h2>

    <?php if ($loginError): ?>
        <div class="error"><?= htmlspecialchars($loginError) ?></div>
    <?php endif; ?>

    <form method="POST" action="adminLogin.php">
        <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirectTarget) ?>">

        <label for="username">Username:</label>
        <input type="text" name="username" id="username" required>

        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required>

        <button type="submit">Login</button>
    </form>
</body>

</html>