<?php

header("Access-Control-Allow-Origin: http://localhost:5173");
header("Content-Type: application/json");

require_once '../db.php';

$id = $_GET['id'];

$stmt = $conn->prepare("SELECT name,languages,description FROM admin WHERE id=?");
$stmt->bind_param("i",$id);
$stmt->execute();

$result = $stmt->get_result();
$row = $result->fetch_assoc();

$languages = explode(",", $row['languages']);

$data = [
    "name"=>$row["name"],
    "languages"=>$languages,
    "description"=>$row["description"]
];

echo json_encode($data);