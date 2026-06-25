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
    
    // Get active emergency requests count
    $query = "SELECT COUNT(*) as count FROM emergency_requests WHERE status = 'Active'";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    ApiResponse::send([
        "success" => true,
        "count" => (int)$result['count']
    ]);
    
} catch (Exception $e) {
    error_log("Emergency Count Error: " . $e->getMessage());
    ApiResponse::error("Server error", 500);
} 