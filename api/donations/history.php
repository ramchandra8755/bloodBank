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
    
    // Get donation history
    $query = "SELECT * FROM donation_history 
              WHERE donor_id = ? 
              ORDER BY donation_date DESC";
              
    $stmt = $db->prepare($query);
    $stmt->execute([$user_id]);
    
    $donations = [];
    $total_units = 0;
    $latest_donation = null;
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if (!$latest_donation) {
            $latest_donation = $row;
        }
        $total_units += $row['units'];
        $donations[] = $row;
    }
    
    // Calculate statistics
    $stats = [
        "total_donations" => count($donations),
        "total_units" => $total_units,
        "next_eligible_date" => $latest_donation ? $latest_donation['next_eligible_date'] : null
    ];
    
    ApiResponse::send([
        "success" => true,
        "donations" => $donations,
        "stats" => $stats
    ]);
    
} catch (Exception $e) {
    error_log("Donation History Error: " . $e->getMessage());
    ApiResponse::error("Server error", 500);
} 