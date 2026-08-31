<?php
// update_board_slot.php
header('Content-Type: application/json');
require_once 'db.php';

$data = json_decode(file_get_contents('php://input'), true);
$matchId = intval($data['match_id'] ?? 0);
$cardId = isset($data['card_id']) ? intval($data['card_id']) : null;
$slotIndex = intval($data['slot_index'] ?? 0);
$sideOwner = trim($data['side_owner'] ?? 'PLAYER'); // PLAYER or OPPONENT
$action = trim($data['action'] ?? 'PLACE'); // PLACE or CLEAR

if ($matchId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid Match ID.']);
    exit;
}

try {
    // 1. Locate the target BOARD_SLOT ID for this match
    $stmtSlot = $pdo->prepare("
        SELECT Slot_ID FROM BOARD_SLOT 
        WHERE Match_ID = :mid AND Side_Owner = :owner AND Slot_Type = :slot_type 
        LIMIT 1
    ");
    $stmtSlot->execute([
        ':mid' => $matchId,
        ':owner' => $sideOwner,
        ':slot_type' => "SLOT_$slotIndex"
    ]);
    $slot = $stmtSlot->fetch();

    if ($slot) {
        $slotId = $slot['Slot_ID'];

        if ($action === 'PLACE' && $cardId > 0) {
            // Bind Card to Slot
            $updateCard = $pdo->prepare("UPDATE CARD SET Slot_ID = :slot_id WHERE Card_ID = :card_id");
            $updateCard->execute([':slot_id' => $slotId, ':card_id' => $cardId]);
        } else {
            // Detach Card from Slot
            $clearCard = $pdo->prepare("UPDATE CARD SET Slot_ID = NULL WHERE Slot_ID = :slot_id");
            $clearCard->execute([':slot_id' => $slotId]);
        }

        echo json_encode(['success' => true, 'slot_id' => $slotId]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Slot not found in database.']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>

