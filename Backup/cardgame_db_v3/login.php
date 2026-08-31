<?php
header('Content-Type: application/json');
require_once 'db.php';

$data = json_decode(file_get_contents('php://input'), true);

$email = filter_var($data['email'] ?? '', FILTER_VALIDATE_EMAIL);
$password = $data['password'] ?? '';

if (!$email || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Email and password required']);
    exit;
}

$stmt = $pdo->prepare("SELECT Account_ID, Password, Email FROM ACCOUNT WHERE Email = :email");
$stmt->execute(['email' => $email]);
$account = $stmt->fetch();

if ($account && password_verify($password, $account['Password'])) {
    $profileStmt = $pdo->prepare("SELECT Profile_ID, Profile_Name, Profile_HighScore, Crayon_Balance, CurrentLevel_Loss FROM PROFILE WHERE Account_ID = :account_id");
    $profileStmt->execute(['account_id' => $account['Account_ID']]);
    $profiles = $profileStmt->fetchAll();

    echo json_encode([
        'success' => true,
        'message' => 'Login successful',
        'account' => [
            'Account_ID' => $account['Account_ID'],
            'Email' => $account['Email']
        ],
        'profiles' => $profiles
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
}
?>


