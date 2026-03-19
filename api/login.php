<?php
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

require_once '../db.php';

$data = json_decode(file_get_contents("php://input"), true);

$email = $data['email'];
$password = $data['password'];

// Check user table
$stmt = $conn->prepare("SELECT * FROM user WHERE email=?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows > 0){
    $user = $result->fetch_assoc();

    if(password_verify($password, $user['password'])){
        $role = $user['role'] == 1 ? "admin" : ($user['role'] == 2 ? "counsellor" : "user");
        $user_id = $user['id'];
        $name = $user['name'];
        $counsellor_id = null;

        if($user['role'] == 2){ // counsellor
            $stmt2 = $conn->prepare("SELECT id AS counsellor_id FROM counsellor WHERE user_id=?");
            $stmt2->bind_param("i", $user['id']);
            $stmt2->execute();
            $result2 = $stmt2->get_result();
            $counsellor_row = $result2->fetch_assoc();
            $counsellor_id = $counsellor_row['counsellor_id'];
        }

        echo json_encode([
            "success" => true,
            "user_id" => $user['id'],
            "counsellor_id" => $counsellor_id,
            "name" => $user['name'],
            "role" => $role
        ]);
        exit;
    }
}

// If login fails
echo json_encode([
    "success" => false,
    "error" => "Invalid email or password"
]);