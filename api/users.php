<?php
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once '../db.php';

$method = $_SERVER['REQUEST_METHOD'];
$id = $_GET['id'] ?? null; // use query string ?id=1 instead of PATH_INFO

switch ($method) {

    case 'GET':
        if ($id) {
            $stmt = $conn->prepare("SELECT id, name, email, role FROM user WHERE id=?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            echo json_encode($user ?: ['message' => 'User not found']);
        } else {
            $result = $conn->query("SELECT id, name, email, role FROM user");
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

        $role = $data['role'] ?? null; // NULL, 1, or 2

        // Check duplicate email
        $stmt = $conn->prepare("SELECT id FROM user WHERE email=?");
        $stmt->bind_param("s", $data['email']);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            echo json_encode(['error' => 'Email already exists']);
            exit;
        }

        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO user (name, email, password, role, created, updated) VALUES (?, ?, ?, ?, NOW(), NOW())");
        $stmt->bind_param("sssi", $data['name'], $data['email'], $hashedPassword, $role);
        $stmt->execute();

        $user_id = $stmt->insert_id;

        // Optional: create extra row in counsellor_details if role=2
        if ($role == 2) {
            $languages = !empty($data['languages']) ? implode(",", $data['languages']) : null;
            $description = $data['description'] ?? null;

            $stmt2 = $conn->prepare("INSERT INTO counsellor (user_id, languages, description) VALUES (?, ?, ?)");
            $stmt2->bind_param("iss", $user_id, $languages, $description);
            $stmt2->execute();
        }

        echo json_encode(['message' => 'User created', 'user_id' => $user_id]);
        break;

    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$id) {
            echo json_encode(['error' => 'ID required']);
            exit;
        }

        $fields = [];
        $types = '';
        $values = [];

        if (isset($data['name'])) { $fields[] = "name=?"; $types .= 's'; $values[] = $data['name']; }
        if (isset($data['email'])) { $fields[] = "email=?"; $types .= 's'; $values[] = $data['email']; }
        if (isset($data['password'])) { $fields[] = "password=?"; $types .= 's'; $values[] = password_hash($data['password'], PASSWORD_DEFAULT); }
        if (isset($data['role'])) { $fields[] = "role=?"; $types .= 'i'; $values[] = $data['role']; }

        if (empty($fields)) {
            echo json_encode(['error' => 'No fields to update']);
            exit;
        }

        $fields[] = "updated=NOW()";
        $sql = "UPDATE user SET " . implode(',', $fields) . " WHERE id=?";
        $types .= 'i';
        $values[] = $id;

        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$values);
        $stmt->execute();

        echo json_encode(['message' => 'User updated']);
        break;

    case 'DELETE':
        if (!$id) {
            echo json_encode(['error' => 'ID required']);
            exit;
        }

        // Optionally delete role-specific table first
        $stmt = $conn->prepare("DELETE FROM counsellor WHERE user_id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        // Delete from users
        $stmt = $conn->prepare("DELETE FROM user WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        echo json_encode(['message' => 'User deleted']);
        break;

    default:
        echo json_encode(['message' => 'Method not allowed']);
        break;
}
?>