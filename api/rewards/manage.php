<?php
require_once '../../config/Database.php';
require_once '../../classes/RewardsManager.php';
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
    
    $rewardsManager = new RewardsManager($db);
    $rewards = $rewardsManager->updateDonorRewards($user_id);
    
    ApiResponse::send([
        "success" => true,
        "rewards" => $rewards
    ]);
    
} catch (Exception $e) {
    error_log("Rewards Error: " . $e->getMessage());
    ApiResponse::error("Server error", 500);
}
?> 