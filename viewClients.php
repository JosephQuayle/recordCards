<?php
// viewClients.php
session_start();
if (!isset($_SESSION['adminId'])) {
    header("Location: adminLogin.php");
    exit;
}

require_once __DIR__ . '/../includes/recordConnection.php';

$sql = "SELECT 
          recordId,
          clientFirstName,
          clientSurname,
          clientEmail,
          clientDOB,
          clientOccupation,
          clientMobile,
          clientAddress,
          clientGPAddress,
          clientMedical,
          clientSignature,
          clientSignedDate
        FROM clientdetails
        ORDER BY recordId ASC";
$result = mysqli_query($conn, $sql);
if (!$result) {
    die('Query Error: ' . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <link rel="stylesheet" href="./styles/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap" rel="stylesheet">
    <title>Client Records</title>
</head>

<body>
    <ul class="nav-bar">
        <div class="nav-links">
            <li><a href="createClient.php">Create New Client</a></li>
            <li><a href="viewClients.php">View Client Records</a></li>
        </div>

    </ul>
    <div class="view-client-wrapper">
        <h1 id="heading">All Clients</h1>

        <?php if (mysqli_num_rows($result) === 0): ?>
            <p>No client records found.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th data-label="ID">ID</th>
                        <th data-label="First Name">First Name</th>
                        <th data-label="Surname">Surname</th>
                        <th data-label="Email">Email</th>
                        <th data-label="DOB">DOB</th>
                        <th data-label="Occupation">Occupation</th>
                        <th data-label="Mobile">Mobile</th>
                        <th data-label="Address">Address</th>
                        <th data-label="GP Address">GP Address</th>
                        <th data-label="Medical">Medical</th>
                        <th data-label="Signature">Signature</th>
                        <th data-label="Date Signed">Date Signed</th>
                        <th data-label="Action">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td data-label="ID"><?= htmlspecialchars($row['recordId']) ?></td>
                            <td data-label="First Name"><?= htmlspecialchars($row['clientFirstName']) ?></td>
                            <td data-label="Surname"><?= htmlspecialchars($row['clientSurname']) ?></td>
                            <td data-label="Email"><?= htmlspecialchars($row['clientEmail']) ?></td>
                            <td data-label="DOB"><?= htmlspecialchars($row['clientDOB']) ?></td>
                            <td data-label="Occupation"><?= htmlspecialchars($row['clientOccupation']) ?></td>
                            <td data-label="Mobile"><?= htmlspecialchars($row['clientMobile']) ?></td>
                            <td data-label="Address"><?= htmlspecialchars($row['clientAddress']) ?></td>
                            <td data-label="GP Address"><?= htmlspecialchars($row['clientGPAddress']) ?></td>
                            <td data-label="Medical"><?= htmlspecialchars($row['clientMedical']) ?></td>
                            <td data-label="Signature"><?= htmlspecialchars($row['clientSignature']) ?></td>
                            <td data-label="Date Signed"><?= htmlspecialchars($row['clientSignedDate']) ?></td>
                            <td data-label="Action">
                                <button class="genLink" data-clientid="<?= (int)$row['recordId'] ?>">Generate Link</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    <?php
    mysqli_free_result($result);
    mysqli_close($conn);
    ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="./js/recordDisplay.js"></script>
</body>

</html>