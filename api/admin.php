<?php
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once '../db.php';

$data = json_decode(file_get_contents("php://input"), true);

if(!$data){
    echo json_encode(["error"=>"No data received"]);
    exit;
}

$name = $data['name'];
$email = $data['email'];
$password = password_hash($data['password'], PASSWORD_DEFAULT);
$phone = $data['phone'];
$languages = !empty($data['languages']) ? implode(",", $data['languages']) : "";
$description = $data['description'];

$role = 1; // Admin role = 1

/* INSERT INTO USERS TABLE */
$stmt = $conn->prepare(
    "INSERT INTO user (name,email,password,role) VALUES (?,?,?,?)"
);

$stmt->bind_param("sssi",$name,$email,$password,$role);

if(!$stmt->execute()){
    echo json_encode([
        "success"=>false,
        "error"=>$stmt->error
    ]);
    exit;
}

$user_id = $conn->insert_id;

/* If you want, you can also have an admin_details table for extra info */
$stmt2 = $conn->prepare(
    "INSERT INTO admin (user_id,phone,languages,description) VALUES (?,?,?,?)"
);

$stmt2->bind_param("isss",$user_id,$phone,$languages,$description);
$stmt2->execute();

echo json_encode([
    "success"=>true,
    "message"=>"Admin registered",
    "user_id"=>$user_id
]);
?>