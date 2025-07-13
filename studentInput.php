<?php
// studentInput.php
session_start();
$timeout = 900;

if (!isset($_SESSION['studentId'])) {
    $redirect = urlencode($_SERVER['REQUEST_URI']);
    header("Location: studentLogin.php?redirect={$redirect}");
    exit;
}

if (isset($_SESSION['lastActivity']) && time() - $_SESSION['lastActivity'] > $timeout) {
    session_destroy();
    header("Location: studentLogin.php?timeout=1&redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

$_SESSION['lastActivity'] = time();


// 0. Your link secret (must match generateLink.php)
define('LINK_SECRET', 'c9b2a3d4e5f60789bd1c2e3f4a5b6c7d8e9f0a1b2c3d4e5f6a7b8c9d0e1f2a3');

// 1. Pull & sanitize URL params
$clientId = isset($_GET['client']) ? (int) $_GET['client'] : 0;
$expires = isset($_GET['expires']) ? (int) $_GET['expires'] : 0;
$sig = isset($_GET['sig']) ? $_GET['sig'] : '';

// 2. Verify signature and expiry
$data = $clientId . '|' . $expires;
$expected = hash_hmac('sha256', $data, LINK_SECRET);

if (! hash_equals($expected, $sig) || time() > $expires) {
    http_response_code(403);
    die('<h1>Link invalid or expired</h1>');
}

// 3. Load DB & fetch client info
require_once __DIR__ . '/../includes/recordConnection.php';

// 3.a Prepare & execute client query
$sql1 = "SELECT * FROM clientdetails WHERE recordId = ? LIMIT 1";
$stmt1 = mysqli_prepare($conn, $sql1);
if (! $stmt1) {
    die('Prepare failed (clientdetails): ' . mysqli_error($conn));
}
mysqli_stmt_bind_param($stmt1, 'i', $clientId);
mysqli_stmt_execute($stmt1);
$res1 = mysqli_stmt_get_result($stmt1);
$client = mysqli_fetch_assoc($res1);
mysqli_stmt_close($stmt1);

if (! $client) {
    http_response_code(404);
    die('<h1>Client not found</h1>');
}

// 3.b Prepare & execute treatments query
$sql2 = "
SELECT
studentTreatment,
studentProductsUsed,
studentFirstName,
studentSurname,
studentDate,
studentSignature
FROM studentadditional
WHERE recordId = ?
ORDER BY studentDate DESC
";
$stmt2 = mysqli_prepare($conn, $sql2);
if (! $stmt2) {
    die('Prepare failed (studentadditional): ' . mysqli_error($conn));
}
mysqli_stmt_bind_param($stmt2, 'i', $clientId);
mysqli_stmt_execute($stmt2);
$res2 = mysqli_stmt_get_result($stmt2);

$treatments = [];
while ($row = mysqli_fetch_assoc($res2)) {
    $treatments[] = $row;
}
mysqli_stmt_close($stmt2);
mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Treatment for <?= htmlspecialchars($client['clientFirstName']) ?></title>
    <link rel="stylesheet" href="./styles/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="./js/recordDisplay.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap" rel="stylesheet">
</head>

<body>
    <form method="POST" action="logout.php" style="text-align: left;">
        <button type="submit">Logout</button>
    </form>
    <h1>Update Record Card</h1>
    <div class="client-info">
        <h2>Client Details</h2>
        <p><strong>Name:</strong>
            <?= htmlspecialchars($client['clientFirstName'] . ' ' . $client['clientSurname']) ?>
        </p>
        <p><strong>Email:</strong>
            <?= htmlspecialchars($client['clientEmail']) ?>
        </p>
        <p><strong>DOB:</strong>
            <?= htmlspecialchars($client['clientDOB']) ?>
        </p>
        <p><strong>Address:</strong>
            <?= htmlspecialchars($client['clientAddress']) ?>
        </p>
        <p><strong>Medical Conditions:</strong>
            <?= htmlspecialchars($client['clientMedical']) ?>
        </p>
    </div>

    <h2>Treatment Records</h2>
    <form id="treatmentForm">
        <input type="hidden" name="clientId" value="<?= $clientId ?>">
        <input type="hidden" name="expires" value="<?= $expires ?>">
        <input type="hidden" name="sig" value="<?= htmlspecialchars($sig) ?>">

        <table>
            <thead>
                <tr>
                    <th>Treatment</th>
                    <th>Products Used</th>
                    <th>Date</th>
                    <th>Student First Name</th>
                    <th>Student Surname</th>
                    <th>Student Signature</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="treatmentRows">
                <!-- NEW ENTRY -->
                <tr id="newEntryRow">
                    <td><input type="text" name="treatment" id="treatment"></td>
                    <!-- <td><textarea name="productsUsed" id="productsUsed"></textarea></td> -->
                    <td>
                        <input type="text" id="productInput" placeholder="Enter product">
                        <button type="button" id="addProduct">Add</button>
                        <ul id="productList"></ul>
                        <textarea name="productsUsed" id="productsUsed" hidden></textarea>
                    </td>
                    <td><input type="date" name="studentDate" id="studentDate" value="<?= date('Y-m-d') ?>" required></td>
                    <td><input type="text" name="studentFirstName" value="<?= htmlspecialchars($_SESSION['studentFirstName']) ?>" id="studentFirstName" readonly></td>
                    <td><input type="text" name="studentSurname" id="studentSurname" value="<?= htmlspecialchars($_SESSION['studentSurname']) ?>" readonly></td>
                    <td><input type="text" name="studentSignature" id="studentSignature" maxlength="15" value="<?= htmlspecialchars($_SESSION['studentSignature']) ?>" readonly></td>
                    <td><button type="button" id="saveTreatment">Save</button></td>
                </tr>

                <!-- PAST TREATMENTS -->
                <?php if (empty($treatments)): ?>
                    <tr>
                        <td id="no-prev-recs" colspan="7">No previous treatments on record.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($treatments as $t): ?>
                        <tr>
                            <td><?= htmlspecialchars($t['studentTreatment']) ?></td>
                            <td><?= nl2br(htmlspecialchars($t['studentProductsUsed'])) ?></td>
                            <td>
                                <?= date('j F Y', strtotime($t['studentDate'])) ?>
                            </td>
                            <td><?= htmlspecialchars($t['studentFirstName']) ?></td>
                            <td><?= htmlspecialchars($t['studentSurname']) ?></td>
                            <td><?= htmlspecialchars($t['studentSignature']) ?></td>
                            <td></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </form>
</body>

</html>