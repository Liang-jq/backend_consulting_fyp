<?php

header("Access-Control-Allow-Origin: http://localhost:5173");
header("Content-Type: application/json");

require_once '../db.php';

$id = $_GET['id'];

// 1️⃣ get base user
$stmt = $conn->prepare("SELECT id, name, email, role FROM user WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows == 0){
    echo json_encode(["error"=>"User not found"]);
    exit;
}

$user = $result->fetch_assoc();

// 2️⃣ counsellor extra
if($user['role'] = "2"){
    $stmt2 = $conn->prepare("
        SELECT matric_number, phone, year, description, languages 
        FROM counsellor 
        WHERE user_id=?
    ");
    $stmt2->bind_param("i", $id);
    $stmt2->execute();
    $result2 = $stmt2->get_result();

    if($result2->num_rows > 0){
        $user = array_merge($user, $result2->fetch_assoc());
    }
}

// 3️⃣ admin extra (OPTIONAL)
if($user['role'] = "1"){
    $stmt3 = $conn->prepare("
        SELECT phone, description, languages 
        FROM admin 
        WHERE user_id=?
    ");
    $stmt3->bind_param("i", $id);
    $stmt3->execute();
    $result3 = $stmt3->get_result();

    if($result3->num_rows > 0){
        $user = array_merge($user, $result3->fetch_assoc());
    }
}

// 4️⃣ return
echo json_encode($user);