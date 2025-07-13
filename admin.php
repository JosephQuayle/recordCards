<?php
// admin.php

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
    <meta charset="UTF-8">
    <link rel="stylesheet" href="./styles/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap" rel="stylesheet">
    <title>Client Records</title>
</head>

<body>
    <h1>All Clients</h1>

    <?php if (mysqli_num_rows($result) === 0): ?>
        <p>No client records found.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>First Name</th>
                    <th>Surname</th>
                    <th>Email</th>
                    <th>DOB</th>
                    <th>Occupation</th>
                    <th>Mobile</th>
                    <th>Address</th>
                    <th>GP Address</th>
                    <th>Medical</th>
                    <th>Signature</th>
                    <th>Date Signed</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['recordId']) ?></td>
                        <td><?= htmlspecialchars($row['clientFirstName']) ?></td>
                        <td><?= htmlspecialchars($row['clientSurname']) ?></td>
                        <td><?= htmlspecialchars($row['clientEmail']) ?></td>
                        <td><?= htmlspecialchars($row['clientDOB']) ?></td>
                        <td><?= htmlspecialchars($row['clientOccupation']) ?></td>
                        <td><?= htmlspecialchars($row['clientMobile']) ?></td>
                        <td><?= htmlspecialchars($row['clientAddress']) ?></td>
                        <td><?= htmlspecialchars($row['clientGPAddress']) ?></td>
                        <td><?= htmlspecialchars($row['clientMedical']) ?></td>
                        <td><?= htmlspecialchars($row['clientSignature']) ?></td>
                        <td><?= htmlspecialchars($row['clientSignedDate']) ?></td>
                        <td>
                            <button
                                class="genLink"
                                data-clientid="<?= (int)$row['recordId'] ?>">Generate Link</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <?php
    mysqli_free_result($result);
    mysqli_close($conn);
    ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="./js/recordDisplay.js"></script>
</body>

</html>