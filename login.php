<?php
header('Content-Type: application/json');
$db = new mysqli('localhost', 'dbuser', 'dbpass', 'dbname');
if ($db->connect_error) {
    echo json_encode(['success' => false, 'message' => 'DB error']);
    exit;
}

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !$password) {
    echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
    exit;
}

$stmt = $db->prepare("SELECT id, password_hash FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($userId, $passwordHash);
if ($stmt->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'User not found']);
    exit;
}
$stmt->fetch();
if (!password_verify($password, $passwordHash)) {
    echo json_encode(['success' => false, 'message' => 'Wrong password']);
    exit;
}
$stmt->close();

// Create Redis session
$sessionToken = bin2hex(random_bytes(32));

$redis = new Redis();
$redis->connect('127.0.0.1', 6379);
$redis->setex("session:$sessionToken", 3600, $userId); // 1 hour expiry

$db->close();

echo json_encode(['success' => true, 'sessionToken' => $sessionToken]);
?>
