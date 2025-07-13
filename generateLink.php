<?php
header('Content-Type: application/json');

// You can also move this into an include/config file
define('LINK_SECRET', 'c9b2a3d4e5f60789bd1c2e3f4a5b6c7d8e9f0a1b2c3d4e5f6a7b8c9d0e1f2a3');

// 1. Validate client ID
$clientId = isset($_GET['client']) ? (int)$_GET['client'] : 0;
if ($clientId < 1) {
    echo json_encode(['error' => 'Invalid client ID']);
    exit;
}

// 2. Compute expiry = now + 1 hour
//$expires = time() + 3600;   // 3600 seconds = 1 hour
// $expires = time() + 60;
$expires = time() + (3600 * 12);


// 3. Build the localhost link
// sign with your secret
$data = $clientId . '|' . $expires;
$sig  = hash_hmac('sha256', $data, LINK_SECRET);

// build and return the URL
$link = sprintf(
    'http://localhost/recordCards/studentInput.php?client=%d'
        . '&expires=%d&sig=%s',
    $clientId,
    $expires,
    $sig
);

echo json_encode(['link' => $link]);
