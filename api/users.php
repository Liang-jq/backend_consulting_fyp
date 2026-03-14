<?php
// C:\xampp\htdocs\backend_consult\api\users.php

// Allow CORS for React frontend
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");


// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

header('Content-Type: application/json');

// Include database connection
require_once '../db.php';  // Make sure path is correct

$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'] ?? '', '/'));
$id = $request[0] ?? null;

switch ($method) {
    case 'GET':
        if ($id) {
            $stmt = $conn->prepare("SELECT id, name, email FROM user WHERE id=?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            echo json_encode($user ?: ['message' => 'User not found']);
        } else {
            $result = $conn->query("SELECT id, name, email FROM user");
            $users = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode($users);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) {
            echo json_encode(['error' => 'No data']);
            exit;
        }

        // Check duplicate email
        $stmt = $conn->prepare("SELECT id FROM user WHERE email=?");
        $stmt->bind_param("s", $data['email']);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            echo json_encode(['error' => 'Email already exists']);
            exit;
        }

        $stmt = $conn->prepare("INSERT INTO user (name, email, password, created, updated) VALUES (?, ?, ?, NOW(), NOW())");
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        $stmt->bind_param("sss", $data['name'], $data['email'], $hashedPassword);
        $stmt->execute();

        echo json_encode(['message' => 'User created', 'id' => $stmt->insert_id]);
        break;

    case 'UPDATE':
        if (!$id) {
            echo json_encode(['error' => 'ID required']);
            exit;
        }
        $data = json_decode(file_get_contents('php://input'), true);

        $stmt = $conn->prepare("UPDATE user SET name=?, email=?, password=?, updated=NOW() WHERE id=?");
        $hashedPassword = isset($data['password']) ? password_hash($data['password'], PASSWORD_DEFAULT) : null;
        $stmt->bind_param(
            "sssi",
            $data['name'] ?? null,
            $data['email'] ?? null,
            $hashedPassword,
            $id
        );
        $stmt->execute();
        echo json_encode(['message' => 'User updated']);
        break;

    case 'DELETE':
        if (!$id) {
            echo json_encode(['error' => 'ID required']);
            exit;
        }
        $stmt = $conn->prepare("DELETE FROM user WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        echo json_encode(['message' => 'User deleted']);
        exit;
        break;

    default:
        echo json_encode(['message' => 'Method not allowed']);
        break;
}


// // ---------------- POST ----------------
// if ($method === 'POST') {

//     exit;
// }

// if ($method === 'PUT') {
// }

// if ($method === 'DELETE') {
// }

// exit;
