<?php

header("Access-Control-Allow-Origin: http://localhost:5173"); //prevent the api error
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json");

require_once '../db.php'; //db setting

//field to take
$counsellor_id = $_POST['counsellor_id'];
$name = $_POST['name'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$type = $_POST['type'];
$date = $_POST['date'];
$time = $_POST['time'];
$issue = $_POST['issue'];

//insert to databse
$stmt = $conn->prepare("
INSERT INTO appointment 
(counsellor_id, name, email, phone, type, date, time, issue)
VALUES (?, ?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
"isssssss",// data type 
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