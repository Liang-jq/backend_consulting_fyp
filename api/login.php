<?php
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

require_once '../db.php';

$data = json_decode(file_get_contents("php://input"), true);

$email = $data['email'];
$password = $data['password'];

/* CHECK USERS TABLE ONLY */
$stmt = $conn->prepare("SELECT * FROM user WHERE email=?");
$stmt->bind_param("s",$email);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows > 0){
    $user = $result->fetch_assoc();

    if(password_verify($password,$user['password'])){
        echo json_encode([
            "success" => true,
            "role" => $user['role'] == 1 ? "admin" : ($user['role'] == 2 ? "counsellor" : "user"),
            "name" => $user['name'],
            "user_id" => $user['id']
        ]);
        exit;
    }
}

echo json_encode([
    "success" => false,
    "message" => "Invalid email or password"
]);
?>