<?php
header('Content-Type: application/json');
require_once 'db.php';

$data = json_decode(file_get_contents('php://input'), true);

$accountId = intval($data['account_id'] ?? 0);
$profileName = trim($data['profile_name'] ?? '');

if ($accountId <= 0 || empty($profileName)) {
    echo json_encode(['success' => false, 'message' => 'Account ID and Profile Name are required']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO PROFILE (Profile_Name, Account_ID) VALUES (:profile_name, :account_id)");
    $stmt->execute(['profile_name' => $profileName, 'account_id' => $accountId]);

    $newProfileId = $pdo->lastInsertId();

    echo json_encode([
        'success' => true,
        'message' => 'New run slot created!',
        'profile' => [
            'Profile_ID' => $newProfileId,
            'Profile_Name' => $profileName,
            'Profile_HighScore' => 0,
            'Crayon_Balance' => 50,
            'CurrentLevel_Loss' => 0
        ]
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Failed to create profile: ' . $e->getMessage()]);
}
?>