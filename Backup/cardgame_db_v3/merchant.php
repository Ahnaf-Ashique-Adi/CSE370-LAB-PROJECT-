<?php
// merchant.php
header('Content-Type: application/json');
require_once 'db.php';

$profileId = intval($_GET['profile_id'] ?? 0);

if ($profileId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid Profile ID.']);
    exit;
}

try {
    // Fetch profile balance and account details
    $stmtProf = $pdo->prepare("SELECT Profile_ID, Profile_Name, Crayon_Balance, Account_ID FROM PROFILE WHERE Profile_ID = :pid");
    $stmtProf->execute([':pid' => $profileId]);
    $profile = $stmtProf->fetch(PDO::FETCH_ASSOC);

    if (!$profile) {
        echo json_encode(['success' => false, 'message' => 'Profile not found.']);
        exit;
    }

    // The VISITS_SHOP insertion block has been removed to keep the merchant persistent.

    // Fetch owned card IDs for this profile with integer casting
    $stmtOwned = $pdo->prepare("SELECT Card_ID FROM OWN_CARDS WHERE Profile_ID = :pid");
    $stmtOwned->execute([':pid' => $profileId]);
    $ownedCardIds = array_map('intval', $stmtOwned->fetchAll(PDO::FETCH_COLUMN));

    // Fetch cards available for purchase
    $stmtCards = $pdo->query("
        SELECT c.Card_ID, c.Card_Name, c.Upgrade_Level, c.Card_Image,
               CASE WHEN m.Card_ID IS NOT NULL THEN 'monster' ELSE 'spell' END AS card_type,
               m.Health, m.Attack, m.Secret_Speed, s.SpellEffect_Desc
        FROM CARD c
        LEFT JOIN MONSTER_CARD m ON c.Card_ID = m.Card_ID
        LEFT JOIN SPELL_CARD s ON c.Card_ID = s.Card_ID
        ORDER BY c.Card_ID ASC
    ");
    $allCards = $stmtCards->fetchAll(PDO::FETCH_ASSOC);

    foreach ($allCards as &$card) {
        $card['Card_ID'] = intval($card['Card_ID']);
        $card['price'] = 25;
        $card['is_owned'] = in_array($card['Card_ID'], $ownedCardIds, true);
    }

    echo json_encode([
        'success' => true,
        'profile' => $profile,
        'merchant_name' => 'Hallway Merchant',
        'dialogue' => 'Got spare Crayons under your desk? Trade \'em for extra monsters and spells before Mr. Vance walks by!',
        'cards' => $allCards
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>