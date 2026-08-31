<?php
// init_match.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'db.php';

$data = json_decode(file_get_contents('php://input'), true);
$profileId = intval($_GET['profile_id'] ?? $_POST['profile_id'] ?? $data['profile_id'] ?? 0);
$isTutorial = !empty($_GET['is_tutorial']) || !empty($_POST['is_tutorial']) || !empty($data['is_tutorial']);

if ($profileId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid Profile ID.']);
    exit;
}

try {
    $pdo->beginTransaction();

    // --- 1. PLAYER DECK SETUP ---
    $stmtOwned = $pdo->prepare("
        SELECT c.Card_ID, c.Card_Name, c.Upgrade_Level, c.Card_Image,
               CASE WHEN m.Card_ID IS NOT NULL THEN 'monster' ELSE 'spell' END AS card_type,
               m.Health, m.Attack, m.Secret_Speed, s.SpellEffect_Desc
        FROM OWN_CARDS oc
        JOIN CARD c ON oc.Card_ID = c.Card_ID
        LEFT JOIN MONSTER_CARD m ON c.Card_ID = m.Card_ID
        LEFT JOIN SPELL_CARD s ON c.Card_ID = s.Card_ID
        WHERE oc.Profile_ID = :pid
    ");
    $stmtOwned->execute([':pid' => $profileId]);
    $ownedCards = $stmtOwned->fetchAll(PDO::FETCH_ASSOC);

    $ownedMonsters = array_values(array_filter($ownedCards, function($c) { return $c['card_type'] === 'monster'; }));
    $ownedSpells   = array_values(array_filter($ownedCards, function($c) { return $c['card_type'] === 'spell'; }));

    if (empty($ownedCards)) {
        $defaultMonsters = $pdo->query("SELECT c.Card_ID, c.Card_Name, c.Upgrade_Level, c.Card_Image, 'monster' AS card_type, m.Health, m.Attack, m.Secret_Speed FROM CARD c JOIN MONSTER_CARD m ON c.Card_ID = m.Card_ID LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);
        $defaultSpells = $pdo->query("SELECT c.Card_ID, c.Card_Name, c.Upgrade_Level, c.Card_Image, 'spell' AS card_type, s.SpellEffect_Desc FROM CARD c JOIN SPELL_CARD s ON c.Card_ID = s.Card_ID LIMIT 2")->fetchAll(PDO::FETCH_ASSOC);
        
        $insertStmt = $pdo->prepare("INSERT IGNORE INTO OWN_CARDS (Profile_ID, Card_ID) VALUES (:pid, :cid)");
        foreach (array_merge($defaultMonsters, $defaultSpells) as $dc) {
            $insertStmt->execute([':pid' => $profileId, ':cid' => $dc['Card_ID']]);
        }
        $ownedMonsters = $defaultMonsters;
        $ownedSpells = $defaultSpells;
    }

    $targetMonsters = $isTutorial ? 2 : 3;
    $targetSpells = 2;

    shuffle($ownedMonsters);
    shuffle($ownedSpells);

    $playerMonsters = array_slice($ownedMonsters, 0, $targetMonsters);
    $playerSpells   = array_slice($ownedSpells, 0, $targetSpells);

    // --- 2. OPPONENT DECK SETUP ---
    // Pull a random assortment of cards for the AI to use
    $oppMonsters = $pdo->query("
        SELECT c.Card_ID, c.Card_Name, c.Upgrade_Level, c.Card_Image, 
               'monster' AS card_type, m.Health, m.Attack, m.Secret_Speed 
        FROM CARD c 
        JOIN MONSTER_CARD m ON c.Card_ID = m.Card_ID 
        ORDER BY RAND() LIMIT 3
    ")->fetchAll(PDO::FETCH_ASSOC);

    $oppSpells = $pdo->query("
        SELECT c.Card_ID, c.Card_Name, c.Upgrade_Level, c.Card_Image, 
               'spell' AS card_type, s.SpellEffect_Desc 
        FROM CARD c 
        JOIN SPELL_CARD s ON c.Card_ID = s.Card_ID 
        ORDER BY RAND() LIMIT 2
    ")->fetchAll(PDO::FETCH_ASSOC);

    $pdo->commit();

    // --- 3. SEND FULL BOARD DATA ---
    echo json_encode([
        'success' => true,
        'player_monsters' => $playerMonsters,
        'player_spells' => $playerSpells,
        'opponent_monsters' => $oppMonsters,
        'opponent_spells' => $oppSpells
    ]);

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500); 
    echo json_encode([
        'success' => false, 
        'message' => 'Database error occurred while fetching decks.',
        'error_details' => $e->getMessage() 
    ]);
}
?>