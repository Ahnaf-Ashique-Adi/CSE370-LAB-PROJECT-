<?php
// end_match.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'db.php';

// Safely parse both standard POST parameters and JSON payloads
$data = json_decode(file_get_contents('php://input'), true);
$matchId = intval($_POST['match_id'] ?? $data['match_id'] ?? 0);
$profileId = intval($_POST['profile_id'] ?? $data['profile_id'] ?? 0);
$result = trim($_POST['result'] ?? $data['result'] ?? 'DEFEAT');
$crayonsEarned = intval($_POST['crayons_earned'] ?? $data['crayons_earned'] ?? 0);

// Only fail if the Profile ID is missing; allow Match ID to be 0 for tutorials
if ($profileId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid Profile ID.']);
    exit;
}

try {
    $pdo->beginTransaction();

    // 1. Update Match record ONLY if a valid Match ID was provided
    if ($matchId > 0) {
        $stmt = $pdo->prepare("
            UPDATE `MATCH`
            SET Result = :result, Crayons_Earned = :earned
            WHERE Match_ID = :mid
        ");
        $stmt->execute([
            ':result' => $result,
            ':earned' => $crayonsEarned,
            ':mid' => $matchId
        ]);
    }

    // 2. Credit profile rewards if won
    if ($result === 'VICTORY') {
        $profStmt = $pdo->prepare("
            UPDATE PROFILE
            SET Crayon_Balance = Crayon_Balance + :earned,
                Profile_HighScore = Profile_HighScore + 100
            WHERE Profile_ID = :pid
        ");
        $profStmt->execute([
            ':earned' => $crayonsEarned, 
            ':pid' => $profileId
        ]);
    }

    // 3. Fetch the updated stats to sync the frontend state for the merchant
    $syncStmt = $pdo->prepare("SELECT Crayon_Balance, Profile_HighScore FROM PROFILE WHERE Profile_ID = :pid");
    $syncStmt->execute([':pid' => $profileId]);
    $updatedProfile = $syncStmt->fetch(PDO::FETCH_ASSOC);

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Match processed successfully.',
        'updated_crayons' => $updatedProfile['Crayon_Balance'] ?? 0,
        'updated_score' => $updatedProfile['Profile_HighScore'] ?? 0
    ]);

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>