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

$matric = $data['matric_number'];
$phone = $data['phone'];
$languages = !empty($data['languages']) ? implode(",", $data['languages']) : "";
$year = $data['year'];
$description = $data['description'];

$role = 2; // counsellor role

/* INSERT INTO USERS TABLE FIRST */

$stmt1 = $conn->prepare("INSERT INTO user (name,email,password,role) VALUES (?,?,?,?)");

$stmt1->bind_param(
"sssi",
$name,
$email,
$password,
$role
);

if(!$stmt1->execute()){
    echo json_encode([
        "success"=>false,
        "error"=>$stmt1->error
    ]);
    exit;
}

$user_id = $conn->insert_id;

/* INSERT INTO COUNSELLOR TABLE */

$status = "pending";

$stmt2 = $conn->prepare(
"INSERT INTO counsellor(user_id,matric_number,phone,languages,year,description,status) VALUES (?,?,?,?,?,?,?)");

$stmt2->bind_param(
"issssss",
$user_id,
$matric,
$phone,
$languages,
$year,
$description,
$status
);

if($stmt2->execute()){

    echo json_encode([
        "success"=>true,
        "message"=>"Counsellor registered",
        "user_id"=>$user_id
    ]);

}else{

    echo json_encode([
        "success"=>false,
        "error"=>$stmt2->error
    ]);
}
?>