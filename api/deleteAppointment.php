<?php
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

require_once '../db.php';

$id = $_POST['id'];

$stmt = $conn->prepare("DELETE FROM appointment WHERE id=?");
$stmt->bind_param("i", $id);

if($stmt->execute()){
    echo json_encode(["success"=>true]);
}else{
    echo json_encode(["success"=>false]);
}