<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require_once 'db.php';

try {
    $sql = "
        SELECT 
            c.Card_ID,
            c.Card_Name,
            c.Upgrade_Level,
            c.Card_Image,
            c.Slot_ID,
            m.Health,
            m.Attack,
            m.Secret_Speed,
            s.SpellEffect_Desc,
            CASE 
                WHEN m.Card_ID IS NOT NULL THEN 'monster'
                WHEN s.Card_ID IS NOT NULL THEN 'spell'
                ELSE 'unknown'
            END AS card_type
        FROM CARD c
        LEFT JOIN MONSTER_CARD m ON c.Card_ID = m.Card_ID
        LEFT JOIN SPELL_CARD s ON c.Card_ID = s.Card_ID
        ORDER BY c.Card_ID ASC
    ";

    $stmt = $pdo->query($sql);
    $cards = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'count'   => count($cards),
        'cards'   => $cards
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to fetch inventory cards: ' . $e->getMessage()
    ]);
}
?>