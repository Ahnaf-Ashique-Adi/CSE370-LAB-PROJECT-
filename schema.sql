CREATE DATABASE IF NOT EXISTS things_we_show_db;
USE things_we_show_db;

-- 1. ACCOUNT
CREATE TABLE IF NOT EXISTS ACCOUNT (
    Account_ID INT AUTO_INCREMENT PRIMARY KEY,
    Email VARCHAR(255) UNIQUE NOT NULL,
    Password VARCHAR(255) NOT NULL,
    SignUp_Date DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 2. PROFILE
CREATE TABLE IF NOT EXISTS PROFILE (
    Profile_ID INT AUTO_INCREMENT PRIMARY KEY,
    Profile_Name VARCHAR(100) NOT NULL,
    Profile_HighScore INT DEFAULT 0,
    Crayon_Balance INT DEFAULT 50,
    CurrentLevel_Loss INT DEFAULT 0,
    Account_ID INT NOT NULL,
    FOREIGN KEY (Account_ID) REFERENCES ACCOUNT(Account_ID) ON DELETE CASCADE
);

-- 3. LEVEL
CREATE TABLE IF NOT EXISTS LEVEL (
    Level_ID INT AUTO_INCREMENT PRIMARY KEY,
    Theme_Name VARCHAR(100) NOT NULL
);

-- 4. OPPONENT
CREATE TABLE IF NOT EXISTS OPPONENT (
    Opponent_ID INT AUTO_INCREMENT PRIMARY KEY,
    Opponent_Name VARCHAR(100) NOT NULL,
    Level_ID INT,
    FOREIGN KEY (Level_ID) REFERENCES LEVEL(Level_ID) ON DELETE SET NULL
);

-- 5. REGULAR_OPPONENT
CREATE TABLE IF NOT EXISTS REGULAR_OPPONENT (
    Opponent_ID INT PRIMARY KEY,
    Minion_Order INT DEFAULT 1,
    FOREIGN KEY (Opponent_ID) REFERENCES OPPONENT(Opponent_ID) ON DELETE CASCADE
);

-- 6. BOSS_OPPONENT
CREATE TABLE IF NOT EXISTS BOSS_OPPONENT (
    Opponent_ID INT PRIMARY KEY,
    Boss_Special_Ability VARCHAR(255),
    Boss_Dialogue TEXT,
    FOREIGN KEY (Opponent_ID) REFERENCES OPPONENT(Opponent_ID) ON DELETE CASCADE
);

-- 7. DIALOGUE_NODE
CREATE TABLE IF NOT EXISTS DIALOGUE_NODE (
    Dialogue_ID INT AUTO_INCREMENT PRIMARY KEY,
    Is_Initial_Greeting BOOLEAN DEFAULT FALSE,
    Opponent_Speech TEXT,
    Opponent_ID INT,
    FOREIGN KEY (Opponent_ID) REFERENCES OPPONENT(Opponent_ID) ON DELETE CASCADE
);

-- 8. DIALOGUE_OPTION
CREATE TABLE IF NOT EXISTS DIALOGUE_OPTION (
    Option_ID INT AUTO_INCREMENT PRIMARY KEY,
    Triggers_Battle BOOLEAN DEFAULT FALSE,
    Player_Response TEXT,
    Dialogue_ID INT,
    FOREIGN KEY (Dialogue_ID) REFERENCES DIALOGUE_NODE(Dialogue_ID) ON DELETE CASCADE
);

-- 9. MERCHANT
CREATE TABLE IF NOT EXISTS MERCHANT (
    Merchant_ID INT AUTO_INCREMENT PRIMARY KEY,
    Merchant_Name VARCHAR(100) NOT NULL,
    Dialogue_Text TEXT,
    Level_ID INT,
    FOREIGN KEY (Level_ID) REFERENCES LEVEL(Level_ID) ON DELETE SET NULL
);

-- 10. VISITS_SHOP
CREATE TABLE IF NOT EXISTS VISITS_SHOP (
    Merchant_ID INT,
    Profile_ID INT,
    PRIMARY KEY (Merchant_ID, Profile_ID),
    FOREIGN KEY (Merchant_ID) REFERENCES MERCHANT(Merchant_ID) ON DELETE CASCADE,
    FOREIGN KEY (Profile_ID) REFERENCES PROFILE(Profile_ID) ON DELETE CASCADE
);

-- 11. MATCH
CREATE TABLE IF NOT EXISTS `MATCH` (
    Match_ID INT AUTO_INCREMENT PRIMARY KEY,
    Profile_ID INT NOT NULL,
    Opponent_ID INT NOT NULL,
    Result VARCHAR(50),
    Match_Date DATETIME DEFAULT CURRENT_TIMESTAMP,
    Crayons_Earned INT DEFAULT 0,
    FOREIGN KEY (Profile_ID) REFERENCES PROFILE(Profile_ID) ON DELETE CASCADE,
    FOREIGN KEY (Opponent_ID) REFERENCES OPPONENT(Opponent_ID) ON DELETE CASCADE
);

-- 12. BOARD_SLOT
CREATE TABLE IF NOT EXISTS BOARD_SLOT (
    Slot_ID INT AUTO_INCREMENT PRIMARY KEY,
    Slot_Type VARCHAR(50),
    Side_Owner VARCHAR(50),
    Match_ID INT,
    FOREIGN KEY (Match_ID) REFERENCES `MATCH`(Match_ID) ON DELETE CASCADE
);

-- 13. CARD
CREATE TABLE IF NOT EXISTS CARD (
    Card_ID INT AUTO_INCREMENT PRIMARY KEY,
    Card_Name VARCHAR(100) NOT NULL,
    Upgrade_Level INT DEFAULT 1,
    Card_Image VARCHAR(255),
    Slot_ID INT NULL,
    FOREIGN KEY (Slot_ID) REFERENCES BOARD_SLOT(Slot_ID) ON DELETE SET NULL
);

-- 14. MONSTER_CARD
CREATE TABLE IF NOT EXISTS MONSTER_CARD (
    Card_ID INT PRIMARY KEY,
    Health INT NOT NULL,
    Attack INT NOT NULL,
    Secret_Speed INT NOT NULL,
    FOREIGN KEY (Card_ID) REFERENCES CARD(Card_ID) ON DELETE CASCADE
);

-- 15. SPELL_CARD
CREATE TABLE IF NOT EXISTS SPELL_CARD (
    Card_ID INT PRIMARY KEY,
    SpellEffect_Desc TEXT NOT NULL,
    FOREIGN KEY (Card_ID) REFERENCES CARD(Card_ID) ON DELETE CASCADE
);

-- 16. OWN_CARDS
CREATE TABLE IF NOT EXISTS OWN_CARDS (
    Profile_ID INT,
    Card_ID INT,
    PRIMARY KEY (Profile_ID, Card_ID),
    FOREIGN KEY (Profile_ID) REFERENCES PROFILE(Profile_ID) ON DELETE CASCADE,
    FOREIGN KEY (Card_ID) REFERENCES CARD(Card_ID) ON DELETE CASCADE
);

-- =========================================================================
-- SQL SCRIPT TO POPULATE 'card', 'monster_card', AND 'spell_card' TABLES
-- =========================================================================

START TRANSACTION;

INSERT INTO card (Card_ID, Card_Name, Upgrade_Level, Card_Image, Slot_ID) VALUES
(1, 'Flame Drake', 3, 'https://picsum.photos/seed/monster_1/300/200', NULL),
(2, 'Shadow Stalker', 2, 'https://picsum.photos/seed/monster_2/300/200', NULL),
(3, 'Crystal Golem', 5, 'https://picsum.photos/seed/monster_3/300/200', NULL),
(4, 'Frost Valkyrie', 4, 'https://picsum.photos/seed/monster_4/300/200', NULL),
(5, 'Void Watcher', 1, 'https://picsum.photos/seed/monster_5/300/200', NULL),
(6, 'Abyssal Leviathan', 5, 'https://picsum.photos/seed/monster_6/300/200', NULL),
(7, 'Thunder Behemoth', 3, 'https://picsum.photos/seed/monster_7/300/200', NULL),
(8, 'Infernal Demon', 4, 'https://picsum.photos/seed/monster_8/300/200', NULL),
(9, 'Venomous Hydra', 2, 'https://picsum.photos/seed/monster_9/300/200', NULL),
(10, 'Solar Phoenix', 5, 'https://picsum.photos/seed/monster_10/300/200', NULL),
(11, 'Spectral Knight', 3, 'https://picsum.photos/seed/monster_11/300/200', NULL),
(12, 'Stone Guardian', 1, 'https://picsum.photos/seed/monster_12/300/200', NULL),
(13, 'Storm Elemental', 4, 'https://picsum.photos/seed/monster_13/300/200', NULL),
(14, 'Blood Werewolf', 2, 'https://picsum.photos/seed/monster_14/300/200', NULL),
(15, 'Corrupted Treant', 3, 'https://picsum.photos/seed/monster_15/300/200', NULL),
(16, 'Dread Chimera', 4, 'https://picsum.photos/seed/monster_16/300/200', NULL),
(17, 'Archon Mage', 5, 'https://picsum.photos/seed/monster_17/300/200', NULL),
(18, 'Fireball', 2, 'https://picsum.photos/seed/spell_18/300/200', NULL),
(19, 'Healing Wave', 1, 'https://picsum.photos/seed/spell_19/300/200', NULL),
(20, 'Lightning Bolt', 4, 'https://picsum.photos/seed/spell_20/300/200', NULL),
(21, 'Arcane Shield', 3, 'https://picsum.photos/seed/spell_21/300/200', NULL),
(22, 'Time Warp', 5, 'https://picsum.photos/seed/spell_22/300/200', NULL),
(23, 'Frost Nova', 2, 'https://picsum.photos/seed/spell_23/300/200', NULL),
(24, 'Poison Cloud', 3, 'https://picsum.photos/seed/spell_24/300/200', NULL),
(25, 'Divine Protection', 4, 'https://picsum.photos/seed/spell_25/300/200', NULL),
(26, 'Shadow Cloak', 1, 'https://picsum.photos/seed/spell_26/300/200', NULL),
(27, 'Meteor Strike', 5, 'https://picsum.photos/seed/spell_27/300/200', NULL),
(28, 'Teleport', 2, 'https://picsum.photos/seed/spell_28/300/200', NULL),
(29, 'Blood Pact', 3, 'https://picsum.photos/seed/spell_29/300/200', NULL),
(30, 'Earthquake', 4, 'https://picsum.photos/seed/spell_30/300/200', NULL),
(31, 'Wind Gust', 1, 'https://picsum.photos/seed/spell_31/300/200', NULL),
(32, 'Mind Control', 5, 'https://picsum.photos/seed/spell_32/300/200', NULL),
(33, 'Silence', 2, 'https://picsum.photos/seed/spell_33/300/200', NULL),
(34, 'Chain Lightning', 4, 'https://picsum.photos/seed/spell_34/300/200', NULL),
(35, 'Summon Familiar', 2, 'https://picsum.photos/seed/spell_35/300/200', NULL),
(36, 'Ice Barrier', 3, 'https://picsum.photos/seed/spell_36/300/200', NULL),
(37, 'Mana Surge', 5, 'https://picsum.photos/seed/spell_37/300/200', NULL),
(38, 'Curse of Agony', 3, 'https://picsum.photos/seed/spell_38/300/200', NULL),
(39, 'Resurrection', 5, 'https://picsum.photos/seed/spell_39/300/200', NULL),
(40, 'Solar Flare', 4, 'https://picsum.photos/seed/spell_40/300/200', NULL);

INSERT INTO monster_card (Card_ID, Health, Attack, Secret_Speed) VALUES
(1, 180, 120, 45),
(2, 95, 140, 88),
(3, 240, 75, 15),
(4, 160, 110, 62),
(5, 110, 85, 50),
(6, 250, 150, 30),
(7, 210, 135, 40),
(8, 195, 145, 55),
(9, 175, 105, 68),
(10, 150, 130, 95),
(11, 130, 90, 70),
(12, 220, 60, 20),
(13, 140, 115, 75),
(14, 125, 125, 82),
(15, 200, 80, 25),
(16, 185, 130, 48),
(17, 105, 140, 90);

INSERT INTO spell_card (Card_ID, SpellEffect_Desc) VALUES
(18, 'Unleashes a fire blast dealing 120 fire damage to a single enemy target.'),
(19, 'Restores 80 health to all friendly units in the battlefield.'),
(20, 'Strikes a random enemy for 150 electric damage with a chance to stun.'),
(21, 'Grants a barrier that absorbs up to 200 damage for 3 turns.'),
(22, 'Grants an extra turn and resets spell cooldowns for 1 turn.'),
(23, 'Freezes all enemies in place, dealing 50 damage and delaying their turns.'),
(24, 'Deals 30 poison damage per turn to all enemy monsters for 3 turns.'),
(25, 'Renders the selected ally invulnerable to all damage for 2 turns.'),
(26, 'Increases speed by 50% and dodges the next 2 physical attacks.'),
(27, 'Summons a meteor shower dealing 180 heavy area-of-effect damage.'),
(28, 'Teleports selected unit out of combat to dodge target attacks.'),
(29, 'Sacrifices 20% health to gain 50% increased attack damage for 3 turns.'),
(30, 'Shakes the ground dealing 90 damage to all ground-based enemies.'),
(31, 'Pushes back all enemies on the timeline, reducing speed by 30%.'),
(32, 'Takes control of an enemy monster with lower level for 2 turns.'),
(33, 'Prevents target enemy monster or caster from using skills for 2 turns.'),
(34, 'Fires lightning that bounces between up to 4 targets with declining damage.'),
(35, 'Summons a familiar pet that assists in combat for 3 combat turns.'),
(36, 'Creates an icy shield that reflects 40% of melee damage back to attackers.'),
(37, 'Instantly restores 100 mana and boosts magic efficiency by 25%.'),
(38, 'Inflicts a curse that increases damage taken by target enemy by 40%.'),
(39, 'Revives a fallen monster with 50% maximum health restored.'),
(40, 'Blinds all enemy monsters for 2 turns and deals 110 radiant damage.');

COMMIT;

-- =========================================================================
-- DIALOGUE & MERCHANT INSERTION SCRIPT
-- =========================================================================

START TRANSACTION;

-- 1. Create the single level
INSERT IGNORE INTO LEVEL (Level_ID, Theme_Name) VALUES (1, 'Classroom & Hallway');

-- 2. Create the first opponent linked to Level 1
INSERT IGNORE INTO OPPONENT (Opponent_ID, Opponent_Name, Level_ID) VALUES (1, 'Seatmate Guide', 1);

-- 3. Insert into DIALOGUE_NODE
INSERT INTO DIALOGUE_NODE (Dialogue_ID, Is_Initial_Greeting, Opponent_Speech, Opponent_ID) 
VALUES
(1, 1, "Psst... slide your math book over! If Mr. Vance catches us with these cards, he'll confiscate the whole deck until June. Keep 'em tucked under your textbook cover!", 1),
(2, 0, "Look, here's the deal: every legit desk duelist carries 40 cards. Exactly 17 Monsters for heavy hitting, and 23 Spells tucked under your sleeve. Balanced and ready for action!", 1),
(3, 0, "Check out this Flame Drake! See those numbers? Health, Attack, and Speed. Speed is the secret sauce here—if you're faster, you slap 'em before they even know what hit 'em!", 1),
(4, 0, "Spells are total game-changers. You don't leave 'em sitting on the desk—you snap 'em straight out of your hand! Shield your monsters, freeze their front row, or steal an extra turn.", 1),
(5, 0, "Crayons are our classroom currency. You start every fresh run with 50 Crayons. Clean wins claim your opponent's balance, which you can spend with the Hallway Merchant.", 1),
(6, 0, "Keep your score sheet tally high! Every clean round win stacks points on your student ledger. Ready? Draw your opening hand!", 1)
ON DUPLICATE KEY UPDATE 
    Is_Initial_Greeting = VALUES(Is_Initial_Greeting),
    Opponent_Speech = VALUES(Opponent_Speech),
    Opponent_ID = VALUES(Opponent_ID);

-- 4. Insert Hallway Merchant (ID: 1) linked to the same Level 1
INSERT INTO MERCHANT (Merchant_ID, Merchant_Name, Dialogue_Text, Level_ID)
VALUES (1, 'Hallway Contraband Dealer', 'Psst... Got fresh cards traded from the back row. What do your Crayons look like?', 1)
ON DUPLICATE KEY UPDATE 
    Merchant_Name = VALUES(Merchant_Name),
    Dialogue_Text = VALUES(Dialogue_Text),
    Level_ID = VALUES(Level_ID);

COMMIT;