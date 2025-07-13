<?php
// recordPost.php
ini_set('display_errors', 1);
error_reporting(E_ALL);


header('Content-Type: application/json');

// 1. Include your DB-connection (must define $conn via mysqli_connect)
require_once __DIR__ . '/../includes/recordConnection.php';

// 2. Collect & sanitize POST inputs
$first    = mysqli_real_escape_string($conn, $_POST['clientFirstName']  ?? '');
$sur      = mysqli_real_escape_string($conn, $_POST['clientSurname']    ?? '');
$email    = mysqli_real_escape_string($conn, $_POST['clientEmail']      ?? '');
$dob      = mysqli_real_escape_string($conn, $_POST['clientDOB']        ?? '');
$occ      = mysqli_real_escape_string($conn, $_POST['clientOccupation'] ?? '');
$mobile   = mysqli_real_escape_string($conn, $_POST['clientMobile']     ?? '');
$address  = mysqli_real_escape_string($conn, $_POST['clientAddress']    ?? '');
$gp       = mysqli_real_escape_string($conn, $_POST['clientGPAddress']  ?? '');
$med      = mysqli_real_escape_string($conn, $_POST['clientMedical']    ?? '');
$sig      = mysqli_real_escape_string($conn, $_POST['clientSignature']  ?? '');
$signed   = mysqli_real_escape_string($conn, $_POST['clientSignedDate'] ?? '');

// 3. Prepare & bind
$sql = "INSERT INTO clientdetails
           (clientFirstName, clientSurname, clientEmail,
            clientDOB, clientOccupation, clientMobile, clientAddress,
            clientGPAddress, clientMedical,
            clientSignature, clientSignedDate)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $sql);

// types: 11× 's' (all strings)
mysqli_stmt_bind_param(
    $stmt,
    'sssssssssss',
    $first,
    $sur,
    $email,
    $dob,
    $occ,
    $mobile,
    $address,
    $gp,
    $med,
    $sig,
    $signed
);

// 4. Execute & respond
if (mysqli_stmt_execute($stmt)) {
    $id = mysqli_insert_id($conn);
    echo json_encode([
        'success'  => true,
        'recordId' => $id
    ]);
} else {
    echo json_encode([
        'success' => false,
        'error'   => mysqli_error($conn)
    ]);
}

// 5. Cleanup
mysqli_stmt_close($stmt);
mysqli_close($conn);
