<?php

header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

require_once '../db.php';

$data = json_decode(file_get_contents("php://input"), true);

$email = $data['email'];
$password = $data['password'];


// CHECK USERS TABLE
$stmt = $conn->prepare("SELECT * FROM user WHERE email=?");
$stmt->bind_param("s",$email);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows > 0){

    $user = $result->fetch_assoc();

    if(password_verify($password,$user['password'])){
        echo json_encode([
            "success"=>true,
            "role"=>"user",
            "name"=>$user["name"]
        ]);
        exit;
    }
}


// CHECK COUNSELLOR TABLE
$stmt = $conn->prepare("SELECT * FROM counsellor WHERE email=? AND status='approved'");
$stmt->bind_param("s",$email);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows > 0){

    $counsellor = $result->fetch_assoc();

    if(password_verify($password,$counsellor['password'])){
        echo json_encode([
            "success"=>true,
            "role"=>"counsellor",
            "name"=>$counsellor['name']
        ]);
        exit;
    }
}

// CHECK ADMIN TABLE
$stmt = $conn->prepare("SELECT * FROM admin WHERE email=?");
$stmt->bind_param("s",$email);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows > 0){

    $admin = $result->fetch_assoc();

    if(password_verify($password,$admin['password'])){
        echo json_encode([
            "success"=>true,
            "role"=>"admin",
            "name"=>$admin["name"]
        ]);
        exit;
    }
}

echo json_encode([
    "success"=>false,
    "message"=>"Invalid email or password"
]);