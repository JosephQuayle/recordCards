<?php
header('Content-Type: application/json');

// 1. Bootstrap DB
require_once __DIR__ . '/../includes/recordConnection.php';
if (! $conn) {
    echo json_encode(['success' => false, 'error' => 'DB connect failed: ' . mysqli_connect_error()]);
    exit;
}

// 2. Gather + validate inputs
$clientId         = isset($_POST['clientId'])         ? (int)$_POST['clientId']         : 0;
$treatment        = trim($_POST['treatment'] ?? '');
$productsUsed     = trim($_POST['productsUsed'] ?? '');
$studentDate        = $_POST['studentDate'] ?? '';
// Validate date format (YYYY-MM-DD)
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $studentDate)) {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid date format. Please use YYYY-MM-DD.'
    ]);
    exit;
}

$studentFirstName = trim($_POST['studentFirstName'] ?? '');
$studentSurname   = trim($_POST['studentSurname'] ?? '');
$studentSignature = trim($_POST['studentSignature'] ?? '');

// quick validation
if (! $clientId || ! $treatment || ! $studentDate) {
    echo json_encode([
        'success' => false,
        'error'   => 'Missing required fields (clientId, treatment, date).'
    ]);
    exit;
}

// 3. Prepare INSERT
$sql  = "INSERT INTO studentadditional
           (recordId, studentTreatment, studentProductsUsed, studentDate,
            studentFirstName, studentSurname, studentSignature)
         VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($conn, $sql);
if (! $stmt) {
    echo json_encode([
        'success' => false,
        'error'   => 'Prepare failed: ' . mysqli_error($conn)
    ]);
    exit;
}

// 4. Bind & execute
mysqli_stmt_bind_param(
    $stmt,
    'issssss',
    $clientId,
    $treatment,
    $productsUsed,
    $studentDate,
    $studentFirstName,
    $studentSurname,
    $studentSignature
);

if (! mysqli_stmt_execute($stmt)) {
    echo json_encode([
        'success' => false,
        'error'   => 'Execute failed: ' . mysqli_stmt_error($stmt)
    ]);
    exit;
}

// 5. Success
$newId = mysqli_stmt_insert_id($stmt);
mysqli_stmt_close($stmt);
mysqli_close($conn);

echo json_encode([
    'success'  => true,
    'recordId' => $newId
]);
exit;
