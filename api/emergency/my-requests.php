<?php
require_once '../../api/utils/ApiResponse.php';
require_once '../../config/Database.php';
require_once '../../middleware/AuthMiddleware.php';

ApiResponse::init();

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Verify token
    $auth = new AuthMiddleware($db);
    $user_id = $auth->authenticateToken();
    
    if (!$user_id) {
        ApiResponse::error("Unauthorized access", 401);
    }
    
    // Get user's emergency requests
    $query = "SELECT * FROM emergency_requests 
              WHERE requester_id = ? 
              ORDER BY created_at DESC";
              
    $stmt = $db->prepare($query);
    $stmt->execute([$user_id]);
    
    $requests = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $requests[] = $row;
    }
    
    ApiResponse::send([
        "success" => true,
        "requests" => $requests
    ]);
    
} catch (Exception $e) {
    error_log("My Emergency Requests Error: " . $e->getMessage());
    ApiResponse::error("Server error", 500);
} 