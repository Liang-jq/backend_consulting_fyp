<?php

header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

require_once '../db.php';

$data = json_decode(file_get_contents("php://input"), true);

$id = $data['id'];
$name = $data['name'];
$email = $data['email'];
$matric = $data['matric_number'];
$phone = $data['phone'];
$languages = implode(",", $data['languages']);
$year = $data['year'];
$description = $data['description'];

// update user table
$stmt1 = $conn->prepare("UPDATE user SET name=? WHERE id=?");
$stmt1->bind_param("si", $name,$id);
$stmt1->execute();

// update counsellor table
$stmt2 = $conn->prepare("
UPDATE counsellor 
SET matric_number=?, phone=?, languages=?, year=?, description=? 
WHERE user_id=?
");

$stmt2->bind_param(
"sssssi",
$matric,
$phone,
$languages,
$year,
$description,
$id
);

$stmt2->execute();

if($stmt1 && $stmt2){
    echo json_encode(["success"=>true]);
}else{
    echo json_encode(["success"=>false,"error"=>$stmt->error]);
}