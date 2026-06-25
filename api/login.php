<?php
require_once '../api/utils/ApiResponse.php';
require_once '../config/Database.php';
require_once '../models/User.php';

ApiResponse::init();

try {
    // Get posted data
    $data = json_decode(file_get_contents("php://input"));
    
    // Validate required fields
    if (empty($data->email) || empty($data->password)) {
        ApiResponse::error("Email and password are required");
    }
    
    // Initialize database connection
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        ApiResponse::error("Database connection failed", 500);
    }
    
    $user = new User($db);
    
    // Attempt login
    $result = $user->login($data->email, $data->password);
    
    if ($result) {
        ApiResponse::send([
            "message" => "Login successful",
            "token" => $result['token'],
            "user" => [
                "id" => $result['user_id'],
                "name" => $result['name'],
                "email" => $result['email']
            ]
        ]);
    }
    
    ApiResponse::error("Invalid credentials", 401);
    
} catch (Exception $e) {
    ApiResponse::error("Server error: " . $e->getMessage(), 500);
} 