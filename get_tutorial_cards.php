<?php
// get_tutorial_cards.php
header('Content-Type: application/json');
require_once 'db.php';

try {
    // Fetch 2 Monster cards for the table demo
    $stmtMonster = $pdo->query("
        SELECT c.Card_ID, c.Card_Name, c.Card_Image, c.Upgrade_Level, 'MONSTER' AS card_type,
               m.Health, m.Attack, m.Secret_Speed
        FROM CARD c
        INNER JOIN MONSTER_CARD m ON c.Card_ID = m.Card_ID
        ORDER BY c.Card_ID ASC
        LIMIT 2
    ");
    $monsters = $stmtMonster->fetchAll();

    // Fetch 1 Spell card for the table demo
    $stmtSpell = $pdo->query("
        SELECT c.Card_ID, c.Card_Name, c.Card_Image, c.Upgrade_Level, 'SPELL' AS card_type,
               s.SpellEffect_Desc
        FROM CARD c
        INNER JOIN SPELL_CARD s ON c.Card_ID = s.Card_ID
        ORDER BY c.Card_ID ASC
        LIMIT 1
    ");
    $spells = $stmtSpell->fetchAll();

    echo json_encode([
        'success' => true,
        'monster_1' => $monsters[0] ?? null,
        'monster_2' => $monsters[1] ?? $monsters[0] ?? null,
        'spell_1' => $spells[0] ?? null
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}