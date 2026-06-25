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
    
    // Get active emergency requests
    $query = "SELECT er.*, u.name as requester_name 
              FROM emergency_requests er 
              JOIN users u ON er.requester_id = u.id 
              WHERE er.status = 'Active' 
              ORDER BY 
                CASE er.urgency_level 
                    WHEN 'High' THEN 1 
                    WHEN 'Medium' THEN 2 
                    WHEN 'Low' THEN 3 
                END, 
                er.created_at DESC";
                
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    $requests = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $requests[] = $row;
    }
    
    ApiResponse::send([
        "success" => true,
        "requests" => $requests
    ]);
    
} catch (Exception $e) {
    error_log("Emergency Request List Error: " . $e->getMessage());
    ApiResponse::error("Server error", 500);
} 