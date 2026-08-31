<?php
// get_account_runs.php
header('Content-Type: application/json');
require_once 'db.php';

$accountId = isset($_GET['account_id']) ? intval($_GET['account_id']) : 0;

if ($accountId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid Account ID.']);
    exit;
}

try {
    // 1. Fetch Account Info
    $stmtAccount = $pdo->prepare("SELECT Account_ID, Email FROM ACCOUNT WHERE Account_ID = :account_id LIMIT 1");
    $stmtAccount->execute([':account_id' => $accountId]);
    $account = $stmtAccount->fetch();

    if (!$account) {
        echo json_encode(['success' => false, 'message' => 'Account not found.']);
        exit;
    }

    // 2. Fetch all Profile Run Desks owned by this Account
    $stmtProfiles = $pdo->prepare("
        SELECT Profile_ID, Profile_Name, Crayon_Balance, Profile_HighScore 
        FROM PROFILE 
        WHERE Account_ID = :account_id 
        ORDER BY Profile_ID DESC
    ");
    $stmtProfiles->execute([':account_id' => $accountId]);
    $profiles = $stmtProfiles->fetchAll();

    echo json_encode([
        'success' => true,
        'account' => $account,
        'profiles' => $profiles
    ]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}