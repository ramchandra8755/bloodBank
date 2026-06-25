<?php
require_once '../../api/utils/ApiResponse.php';
require_once '../../config/Database.php';
require_once '../../middleware/SharedAuthMiddleware.php';

ApiResponse::init();

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Get POST data
    $data = json_decode(file_get_contents("php://input"));
    
    if (!isset($data->email) || !isset($data->password)) {
        ApiResponse::error("Email and password are required", 400);
    }
    
    // Check user type
    $auth = new SharedAuthMiddleware($db);
    $user_info = $auth->getUserType($data->email);
    
    if (!$user_info) {
        ApiResponse::error("Invalid email or password", 401);
    }
    
    // Verify password based on user type
    if ($user_info['type'] === 'bank_admin') {
        $query = "SELECT * FROM blood_bank_admins WHERE email = ?";
    } else {
        $query = "SELECT * FROM users WHERE email = ?";
    }
    
    $stmt = $db->prepare($query);
    $stmt->execute([strtolower($data->email)]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user || !password_verify($data->password, $user['password'])) {
        ApiResponse::error("Invalid email or password", 401);
    }
    
    // Generate token with user type
    $token = generateJWT([
        'id' => $user['id'],
        'type' => $user_info['type'],
        'data' => $user_info['data']
    ]);
    
    // Set session data
    session_start();
    if ($user_info['type'] === 'bank_admin') {
        $_SESSION['bank_admin_id'] = $user['id'];
        $_SESSION['blood_bank_id'] = $user['blood_bank_id'];
        $_SESSION['bank_admin_role'] = $user['role'];
    } else {
        $_SESSION['user_id'] = $user['id'];
    }
    
    ApiResponse::send([
        "success" => true,
        "token" => $token,
        "user" => [
            "type" => $user_info['type'],
            "data" => $user_info['data']
        ]
    ]);
    
} catch (Exception $e) {
    error_log("Login Error: " . $e->getMessage());
    ApiResponse::error("Server error", 500);
} 