<?php
require_once '../../config/Database.php';
require_once '../../api/utils/ApiResponse.php';
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
    
    // Get points history
    $query = "SELECT 
                ph.id,
                ph.points,
                ph.description,
                ph.type,
                ph.created_at as date
              FROM points_history ph
              WHERE ph.donor_id = ?
              ORDER BY ph.created_at DESC
              LIMIT 50";
              
    $stmt = $db->prepare($query);
    $stmt->execute([$user_id]);
    $history = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    ApiResponse::send([
        "success" => true,
        "history" => $history
    ]);
    
} catch (Exception $e) {
    error_log("Points History Error: " . $e->getMessage());
    ApiResponse::error("Server error", 500);
}
?> 