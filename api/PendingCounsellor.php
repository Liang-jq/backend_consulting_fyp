<?php

header("Access-Control-Allow-Origin: http://localhost:5173");
header("Content-Type: application/json");

require_once '../db.php';

$sql = "
SELECT c.id, u.name, u.email,c.matric_number, c.phone, c.year,c.languages, c.description
FROM counsellor c
JOIN user u ON c.user_id = u.id
WHERE c.status = 'pending'
";

$result = $conn->query($sql);

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