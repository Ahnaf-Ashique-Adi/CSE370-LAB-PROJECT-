<?php
// buy_card.php
header('Content-Type: application/json');
require_once 'db.php';

$data = json_decode(file_get_contents('php://input'), true);
$profileId = intval($data['profile_id'] ?? 0);
$cardId = intval($data['card_id'] ?? 0);
$cardPrice = 25;

if ($profileId <= 0 || $cardId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters.']);
    exit;
}

try {
    $pdo->beginTransaction();

    // Verify balance
    $stmt = $pdo->prepare("SELECT Crayon_Balance FROM PROFILE WHERE Profile_ID = :pid FOR UPDATE");
    $stmt->execute([':pid' => $profileId]);
    $prof = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$prof || intval($prof['Crayon_Balance']) < $cardPrice) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Not enough Crayons!']);
        exit;
    }

    // Check if already owned
    $stmtOwned = $pdo->prepare("SELECT COUNT(*) FROM OWN_CARDS WHERE Profile_ID = :pid AND Card_ID = :cid");
    $stmtOwned->execute([':pid' => $profileId, ':cid' => $cardId]);
    if ($stmtOwned->fetchColumn() > 0) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Card already in deck binder!']);
        exit;
    }

    // Deduct balance and insert card ownership
    $pdo->prepare("UPDATE PROFILE SET Crayon_Balance = Crayon_Balance - :price WHERE Profile_ID = :pid")
        ->execute([':price' => $cardPrice, ':pid' => $profileId]);

    $pdo->prepare("INSERT INTO OWN_CARDS (Profile_ID, Card_ID) VALUES (:pid, :cid)")
        ->execute([':pid' => $profileId, ':cid' => $cardId]);

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Card acquired!',
        'new_balance' => intval($prof['Crayon_Balance']) - $cardPrice
    ]);
} catch (PDOException $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Transaction failed: ' . $e->getMessage()]);
}
?>