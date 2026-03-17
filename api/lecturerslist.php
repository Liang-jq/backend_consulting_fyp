<?php

header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../db.php';

$result = $conn->query("SELECT u.id, u.name, a.languages, a.description
FROM user u
JOIN admin a ON u.id = a.user_id
WHERE u.role = 1");

$data = [];

while($row = $result->fetch_assoc()){

    $languages = explode(",", $row['languages']);

    $data[] = [
        "id"=>$row["id"],
        "name"=>$row["name"],
        "title"=>"Licensed & Registered Counsellor",
        "languages"=>$languages,
        "description"=>$row["description"]
    ];
}

echo json_encode($data);