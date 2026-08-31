# Things We Show and Tell 🎴

An endless roguelike strategy-based card game with turn-based tactical combat, player session tracking, dynamic shop mechanics, and interactive dialogues.

---

## 🚀 Overview

**Things We Show and Tell** combines turn-based card battles with dynamic database state management. Players manage multi-run profiles, fight opponents across an 8-slot battlefield, earn classroom currency (*Crayons*), and buy/upgrade cards from the Hallway Merchant.

### Key Game Mechanics
* **Monsters & Spells**: Monsters provide attack/health stats for tactical grid combat; Spells trigger dynamic effects and stat modifications.
* **Crayon Economy**: Earn *Crayons* from victorious matches to spend at the merchant shop.
* **Tactical Grid**: Battlefield consists of 8 slots (4 slots per side: 2 monster zones and 2 spell zones).
* **Multi-Run System**: Accounts can host multiple run slots with distinct progress, inventories, and high scores.

---

## 🛠 Tech Stack

* **Frontend**: HTML5, CSS3, JavaScript (Vanilla ES6)
* **Backend**: PHP 8.x
* **Database**: MySQL 8.0 (Normalized to **3NF**)

---

## ✨ Features & Module Breakdown

| Feature | Description | Lead Contributor |
| :--- | :--- | :--- |
| **Player Profile Tracking** | User registration, authentication, multi-run profile slots, and high-score recording. | Mohammad Arafat (`24301051`) |
| **Merchant Shop & Economy** | Dynamic shop offerings, purchase validation, Crayon balance handling, and inventory updates. | Mohammad Arafat (`24301051`) |
| **Combat & Board Tracking** | Real-time battlefield state, 8-slot positioning, attack/health recalculations, and move resolution. | Ahnaf Ashique Adi (`24101165`) |
| **Tutorial & Dialogues** | Guided interactive lessons and story dialogues fetched dynamically from database nodes. | Ahnaf Ashique Adi (`24101165`) |
| **Game Session Tracking** | Session initialization, run lifecycle, Crayon rewards, and win/loss state management. | Mahdin Samiul Haque (`23301435`) |
| **Card Template Registry** | Centralized entity-attribute registry (EER hierarchy for Monsters and Spells) to eliminate redundancy. | Mahdin Samiul Haque (`23301435`) |

---

## 🗄️ Database Architecture

The schema follows Enhanced Entity-Relationship (EER) modeling rules and is fully normalized to **3NF**:

* **Entities & Specializations**: `ACCOUNT`, `PROFILE`, `CARD` (`MONSTER_CARD` & `SPELL_CARD` subtypes), `OWN_CARDS`, `MERCHANT`, `LEVEL`, `OPPONENT` (`REGULAR_OPPONENT` & `BOSS_OPPONENT`), `DIALOGUE_NODE`, `DIALOGUE_OPTION`, `BOARD_SLOT`, `MATCH`.
* **Key Queries**: Dynamic prepared SQL statements handle session updates, transactional safe-buys (`FOR UPDATE`), card initialization, and battle grid synchronization.

---

## 📂 Repository Structure

```text
.
├── backend/
│   ├── login.php
│   ├── signup.php
│   ├── create_profile.php
│   ├── init_match.php
│   ├── merchant.php
│   ├── buy_card.php
│   ├── update_board_slot.php
│   ├── end_match.php
│   ├── get_dialogues.php
│   └── get_cards.php
├── frontend/
│   ├── index.html
│   ├── styles/
│   └── js/
└── database/
    └── schema.sql
```

---

## 👥 Team (Group 01)

* **Mohammad Arafat** - ID: `24301051`
* **Ahnaf Ashique Adi** - ID: `24101165`
* **Mahdin Samiul Haque** - ID: `23301435` / `23101997`

*Course Instructor / Section: CSE370 Lab Section 11*
