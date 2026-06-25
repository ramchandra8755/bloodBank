<?php
require_once '../api/utils/ApiResponse.php';
require_once '../config/Database.php';
require_once '../models/User.php';

ApiResponse::init();

try {
    // Debug: Log raw input
    error_log("Raw input: " . file_get_contents("php://input"));
    
    // Get posted data
    $data = json_decode(file_get_contents("php://input"));
    
    // Debug: Log decoded data
    error_log("Decoded data: " . print_r($data, true));
    
    // Validate required fields
    $required_fields = ['name', 'email', 'password', 'blood_group'];
    $missing_fields = [];
    
    foreach ($required_fields as $field) {
        if (!isset($data->$field) || empty($data->$field)) {
            $missing_fields[] = $field;
        }
    }
    
    if (!empty($missing_fields)) {
        ApiResponse::error("Missing required fields: " . implode(", ", $missing_fields));
    }
    
    // Initialize database connection
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        ApiResponse::error("Database connection failed", 500);
    }
    
    $user = new User($db);
    
    // Set user properties
    $user->name = htmlspecialchars(strip_tags($data->name));
    $user->email = $data->email;
    $user->password = $data->password;
    $user->phone = isset($data->phone) ? htmlspecialchars(strip_tags($data->phone)) : null;
    $user->blood_group = htmlspecialchars(strip_tags($data->blood_group));
    $user->location = isset($data->location) ? htmlspecialchars(strip_tags($data->location)) : null;
    
    if ($user->create()) {
        ApiResponse::send([
            "message" => "User registered successfully",
            "user" => [
                "name" => $user->name,
                "email" => $user->email,
                "blood_group" => $user->blood_group
            ]
        ], 201);
    }
    
    ApiResponse::error("Unable to register user", 503);
    
} catch (Exception $e) {
    // Debug: Log the full error
    error_log("Registration error: " . $e->getMessage());
    ApiResponse::error("Server error: " . $e->getMessage(), 500);
} 