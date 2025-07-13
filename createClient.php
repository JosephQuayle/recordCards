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
    <h1 id="heading">Record Cards</h1>
    <div id="recordForm">
        <label for="fName">First Name</label>
        <input type="text" name="fName" id="fname">
        <label for="sName">Second Name</label>
        <input type="text" name="sName" id="sname">
        <label for="email">Email</label>
        <input type="email" name="email" id="email">
        <label for="dob">Date of Birth</label>
        <input type="date" name="dob" id="dob">
        <label for="phone">Phone</label>
        <input type="text" name="phone" id="phone">
        <label for="address">Address</label>
        <input type="text" name="address" id="address">
        <label for="gpaddress">GP Address</label>
        <input type="text" name="gpaddress" id="gpaddress">
        <label for="occupation">Occupation</label>
        <input type="text" name="occupation" id="occupation">
        <label for="medical">Medical Conditions</label>
        <textarea name="medical" id="medical"></textarea>
        <label for="sign">Sign</label>
        <input type="text" name="sign" id="sign">
        <label for="dateofsign">Date</label>
        <input type="date" name="dateofsign" id="date">
        <button id="saveDetails">Save Details</button>
        <label for="treatment">Treatment</label>
        <input type="text" name="treatment" id="treatment">
        <label for="productsused">Products Used</label>
        <textarea name="productsused" id="productsUsed"></textarea>
        <label for="treatdate">Date</label>
        <input type="date" name="treatdate" id="treatdate">
        <label for="studentsign">Student Sign</label>
        <input type="text" name="studentsign" id="studentsign">
        <Button id="saveTreatment">Save Treatment Info</Button>
    </div>
</body>

</html>