<?php
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Content-Type: application/json");

require_once '../db.php';

/* Join users and counsellor_details */
$sql = "
SELECT u.id, u.name, c.languages, c.description
FROM user u
JOIN counsellor c ON u.id = c.user_id
WHERE u.role = 2 and c.status='approved'
";

$result = $conn->query($sql);

$data = [];

while($row = $result->fetch_assoc()){

    $languages = !empty($row['languages']) ? explode(",", $row['languages']) : [];

    $data[] = [
        "id" => $row["id"],
        "name" => $row["name"],
        "title" => "Student Bachelor of Counselling",
        "languages" => $languages,
        "description" => $row["description"]
    ];
}

echo json_encode($data);
?>