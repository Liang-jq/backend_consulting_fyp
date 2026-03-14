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
    $languages = implode(",", $data['languages'])? implode(",", $data['languages']) : "";
    $description = $data['description'];

    $stmt = $conn->prepare(
        "INSERT INTO admin 
        (name,email,password,phone,languages,description)
        VALUES (?,?,?,?,?,?)"
    );

    $stmt->bind_param(
    "ssssss",
    $name,
    $email,
    $password,
    $phone,
    $languages,
    $description
    );

    if($stmt->execute()){
        
        echo json_encode([
            "success"=>true,
            "message"=>"Admin registered",
            "id"=>$conn->insert_id
        ]);

    }else{

        echo json_encode([
            "success"=>false,
            "error"=>$stmt->error
        ]);
    }
?>