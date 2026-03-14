<?php

header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

require_once '../db.php';

$data = json_decode(file_get_contents("php://input"), true);

$id = $data['id'];

$stmt = $conn->prepare("UPDATE counsellor SET status='approved' WHERE id=?");
$stmt->bind_param("i",$id);

if($stmt->execute()){
    echo json_encode(["success"=>true]);
}else{
    echo json_encode(["success"=>false]);
}