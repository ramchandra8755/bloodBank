<?php
require_once '../../../api/utils/ApiResponse.php';
require_once '../../../config/Database.php';
require_once '../../../config/env.php';

ApiResponse::init();

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Get POST data
    $data = json_decode(file_get_contents("php://input"));
    
    if (!isset($data->email) || !isset($data->password)) {
        ApiResponse::error("Email and password are required", 400);
    }
    
    // Get admin user
    $query = "SELECT ba.*, bb.name as blood_bank_name 
              FROM blood_bank_admins ba 
              JOIN blood_banks bb ON ba.blood_bank_id = bb.id 
              WHERE ba.email = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([strtolower($data->email)]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$admin || !password_verify($data->password, $admin['password'])) {
        ApiResponse::error("Invalid email or password", 401);
    }
    
    // Start session
    session_start();
    $_SESSION['bank_admin_id'] = $admin['id'];
    $_SESSION['blood_bank_id'] = $admin['blood_bank_id'];
    $_SESSION['bank_admin_role'] = $admin['role'];
    
    // Generate JWT token
    $token = generateJWT([
        'admin_id' => $admin['id'],
        'blood_bank_id' => $admin['blood_bank_id'],
        'role' => $admin['role']
    ]);
    
    ApiResponse::send([
        "success" => true,
        "token" => $token,
        "admin" => [
            "name" => $admin['name'],
            "blood_bank" => $admin['blood_bank_name'],
            "role" => $admin['role']
        ],
        "redirect" => "dashboard.php"
    ]);
    
} catch (Exception $e) {
    error_log("Blood Bank Login Error: " . $e->getMessage());
    ApiResponse::error("Server error", 500);
}

function generateJWT($payload) {
    if (!isset($_ENV['JWT_SECRET'])) {
        throw new Exception("JWT_SECRET not configured");
    }
    
    $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
    $payload = json_encode($payload);
    
    $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
    $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
    
    $signature = hash_hmac('sha256', 
        $base64UrlHeader . "." . $base64UrlPayload, 
        $_ENV['JWT_SECRET'], 
        true
    );
    $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
    
    return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
} 