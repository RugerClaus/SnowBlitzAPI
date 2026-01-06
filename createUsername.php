<?php
include('db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"));
    $username = trim($data->username);

    if (!empty($username)) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $count = $stmt->fetchColumn();

        if ($count == 0) {
            $stmt = $pdo->prepare("INSERT INTO users (username, created_at) VALUES (:username, NOW())");
            if ($stmt->execute(['username' => $username])) {
                echo json_encode(["success" => true, "message" => "Username created successfully"]);
            } else {
                echo json_encode(["success" => false, "message" => "Failed to create username"]);
            }
        } else {
            echo json_encode(["success" => false, "message" => "Username already exists"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Username cannot be empty"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method"]);
}
?>
