<?php
session_start();
if (!isset($_SESSION['adminId'])) {
    header("Location: adminLogin.php");
    exit;
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
    <h2>Welcome, <?= htmlspecialchars($_SESSION['adminUser']) ?></h2>

    <ul>
        <li><a href="createClient.php">Create New Client</a></li>
        <li><a href="viewClients.php">View Client Records</a></li>
        <li>
            <form method="POST" action="adminLogout.php" style="display:inline;">
                <button type="submit">Logout</button>
            </form>
        </li>
    </ul>
</body>

</html>