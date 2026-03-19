<?php

header("Access-Control-Allow-Origin: http://localhost:5173");
header("Content-Type: application/json");

require_once '../db.php';

$id = $_GET['id'];

$stmt = $conn->prepare("SELECT c.id AS counsellor_id, u.name, c.languages, c.description 
FROM counsellor c 
JOIN user u ON c.user_id = u.id 
WHERE c.id = ?
");
$stmt->bind_param("i",$id);
$stmt->execute();

$result = $stmt->get_result();
$row = $result->fetch_assoc();

$languages = explode(",", $row['languages']);

$data = [
    "id"=>$row["counsellor_id"],
    "name"=>$row["name"],
    "languages"=>$languages,
    "description"=>$row["description"]
];

echo json_encode($data);