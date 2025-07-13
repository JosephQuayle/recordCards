<?php
session_start();
if (!isset($_SESSION['adminId'])) {
    header("Location: adminLogin.php");
    exit;
}

require_once __DIR__ . '/../includes/recordConnection.php';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="./js/recordDisplay.js"></script>
    <link rel="stylesheet" href="./styles/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap" rel="stylesheet">
    <title>Record Cards</title>
</head>

<body>
    <ul class="nav-bar">
        <div class="nav-links">
            <li><a href="createClient.php">Create New Client</a></li>
            <li><a href="viewClients.php">View Client Records</a></li>
        </div>

    </ul>
    <div class="create-client-wrapper">
        <h1 id="heading">Record Cards</h1>

        <div id="recordForm">
            <label for="fName">First Name</label>
            <input type="text" name="fName" id="fname">
            <br>
            <label for="sName">Second Name</label>
            <input type="text" name="sName" id="sname">
            <br>
            <label for="email">Email</label>
            <input type="email" name="email" id="email">
            <br>
            <label for="dob">Date of Birth</label>
            <input type="date" name="dob" id="dob">
            <br>
            <label for="phone">Phone</label>
            <input type="text" name="phone" id="phone">
            <br>
            <label for="address">Address</label>
            <input type="text" name="address" id="address">
            <br>
            <label for="gpaddress">GP Address</label>
            <input type="text" name="gpaddress" id="gpaddress">
            <br>
            <label for="occupation">Occupation</label>
            <input type="text" name="occupation" id="occupation">
            <br>
            <label for="medicalInput">Medical Conditions</label>
            <input type="text" id="medicalInput" placeholder="Enter condition">
            <button style="margin-top: 5px;" type="button" id="addMedical">Add</button>
            <ul id="medicalList"></ul>
            <textarea name="medical" id="medical" hidden></textarea>
            <br>
            <label for="sign">Sign</label>
            <input type="text" name="sign" id="sign">
            <br>
            <label for="dateofsign">Date</label>
            <input type="date" name="dateofsign" id="date">
            <br>
            <button id="saveDetails">Save Details</button>
        </div>
    </div>

</body>

</html>