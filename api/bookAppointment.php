<?php

header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json");

require_once '../db.php';

$data = json_decode(file_get_contents("php://input"), true);

$counsellor_id = $data['counsellor_id'];
$name = $data['name'];
$email = $data['email'];
$phone = $data['phone'];
$type = $data['type'];
$date = $data['date'];
$time = $data['time'];
$issue = $data['issue'];

$stmt = $conn->prepare("
INSERT INTO appointments 
(counsellor_id, name, email, phone, type, date, time, issue)
VALUES (?, ?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
"isssssss",
$counsellor_id,
$name,
$email,
$phone,
$type,
$date,
$time,
$issue
);

if($stmt->execute()){
    echo json_encode(["success"=>true]);
}else{
    echo json_encode(["success"=>false]);
}