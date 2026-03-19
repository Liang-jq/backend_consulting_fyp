<?php

header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$id = $_POST['id'];

$stmt = $conn->prepare("UPDATE appointment SET status='approved' WHERE id=?");
$stmt->bind_param("i", $id);

if($stmt->execute()){
    echo json_encode(["success"=>true]);
}else{
    echo json_encode(["success"=>false]);
}