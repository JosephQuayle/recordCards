<?php
/*
require_once __DIR__ . '/../includes/recordConnection.php';

$username = 'admin';
$passwordHash = password_hash('recAdminPass', PASSWORD_DEFAULT);

$sql = "INSERT INTO admins (username, passwordHash) VALUES (?, ?)";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'ss', $username, $passwordHash);
mysqli_stmt_execute($stmt);
echo "Admin created.";
*/
