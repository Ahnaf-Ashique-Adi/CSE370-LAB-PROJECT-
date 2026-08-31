<?php
header('Content-Type: application/json');

$conn = new mysqli("localhost", "root", "", "things_we_show_db");

if ($conn->connect_error) {
    echo json_encode(["success" => false, "error" => $conn->connect_error]);
    exit();
}

$sql = "SELECT Dialogue_ID, Is_Initial_Greeting, Opponent_Speech, Opponent_ID 
        FROM dialogue_node 
        ORDER BY Dialogue_ID ASC";

$result = $conn->query($sql);
$dialogues = [];

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $dialogues[] = $row;
    }
}

echo json_encode(["success" => true, "dialogues" => $dialogues]);
$conn->close();
?>