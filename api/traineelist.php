<?php

header("Access-Control-Allow-Origin: http://localhost:5173");
header("Content-Type: application/json");

require_once '../db.php';

$result = $conn->query("SELECT id,name,languages,description FROM counsellor WHERE status='approved'");

$data = [];

while($row = $result->fetch_assoc()){

    $languages = explode(",", $row['languages']);

    $data[] = [
        "id"=>$row["id"],
        "name"=>$row["name"],
        "title"=>"Student Bachelor of Counselling",
        "languages"=>$languages,
        "description"=>$row["description"]
    ];
}

echo json_encode($data);