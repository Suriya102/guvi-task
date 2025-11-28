<?php
header('Content-Type: application/json');

$authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
    echo json_encode(['success' => false, 'message' => 'No token provided']);
    exit;
}
$sessionToken = $matches[1];

// Redis connection to validate session
$redis = new Redis();
$redis->connect('127.0.0.1', 6379);
$userId = $redis->get("session:$sessionToken");
if (!$userId) {
    echo json_encode(['success' => false, 'message' => 'Invalid session']);
    exit;
}

require 'vendor/autoload.php'; // MongoDB PHP Library via Composer

$mongoClient = new MongoDB\Client("mongodb://localhost:27017");
$profileCollection = $mongoClient->userdb->profiles;

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $profile = $profileCollection->findOne(['userId' => (int)$userId]);
    echo json_encode(['success' => true, 'profile' => $profile ?: new stdClass()]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    $age = isset($input['age']) ? intval($input['age']) : null;
    $dob = $input['dob'] ?? null;
    $contact = $input['contact'] ?? null;

    $updateResult = $profileCollection->updateOne(
        ['userId' => (int)$userId],
        ['$set' => ['age' => $age, 'dob' => $dob, 'contact' => $contact]],
        ['upsert' => true]
    );

    echo json_encode(['success' => true, 'updatedCount' => $updateResult->getModifiedCount()]);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Unsupported request']);
?>
