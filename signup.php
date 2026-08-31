<?php
header('Content-Type: application/json');
require_once 'db.php';

$data = json_decode(file_get_contents('php://input'), true);

$email = filter_var($data['email'] ?? '', FILTER_VALIDATE_EMAIL);
$password = $data['password'] ?? '';

if (!$email || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Valid email and password required']);
    exit;
}

$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

try {
    $stmt = $pdo->prepare("INSERT INTO ACCOUNT (Email, Password) VALUES (:email, :password)");
    $stmt->execute(['email' => $email, 'password' => $hashedPassword]);

    echo json_encode(['success' => true, 'message' => 'Account created successfully']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Registration failed. Email may already exist.']);
}
?>