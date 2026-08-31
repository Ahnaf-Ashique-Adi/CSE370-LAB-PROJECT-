<?php
// complete_tutorial.php
header('Content-Type: application/json');
require_once 'db.php';

// Read JSON input from fetch()
$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);

$profileId = isset($data['profile_id']) ? intval($data['profile_id']) : 0;

if ($profileId <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid Profile ID provided.'
    ]);
    exit;
}

try {
    // Ensure profile exists and initialize starter balance / progress
    $stmt = $pdo->prepare("
        UPDATE PROFILE 
        SET Crayon_Balance = GREATEST(COALESCE(Crayon_Balance, 0), 50)
        WHERE Profile_ID = :profile_id
    ");
    $stmt->execute([':profile_id' => $profileId]);

    echo json_encode([
        'success' => true,
        'message' => 'Tutorial completion recorded in database.',
        'profile_id' => $profileId
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'SQL Execution error: ' . $e->getMessage()
    ]);
}