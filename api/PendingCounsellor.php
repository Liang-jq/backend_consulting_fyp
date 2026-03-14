<?php

header("Access-Control-Allow-Origin: http://localhost:5173");
header("Content-Type: application/json");

require_once '../db.php';

$result = $conn->query("SELECT id,name,matric_number,email,phone,year,languages,description FROM counsellor WHERE status='pending'");

$data = [];

while($row = $result->fetch_assoc()){
    $data[] = [
        "id"=>$row["id"],
        "name"=>$row["name"],
        "matric"=>$row["matric_number"],
        "email"=>$row["email"],
        "phone"=>$row["phone"],
        "year"=>$row["year"],
        "language"=>$row["languages"],
        "description"=>$row["description"]
    ];
}

echo json_encode($data);