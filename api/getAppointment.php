<?php
// MUST be at the very top (no space, no HTML before this)
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

require_once '../db.php';

// Handle preflight (important for some browsers)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$counsellor_id = $_GET['counsellor_id'];

$stmt = $conn->prepare("SELECT * FROM appointment WHERE counsellor_id=?");
$stmt->bind_param("i", $counsellor_id);
$stmt->execute();

$result = $stmt->get_result();

$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);