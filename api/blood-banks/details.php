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
    
    // Get bank ID
    $bank_id = isset($_GET['id']) ? $_GET['id'] : null;
    
    if (!$bank_id) {
        ApiResponse::error("Missing blood bank ID", 400);
    }
    
    // Get blood bank details
    $query = "SELECT * FROM blood_banks WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$bank_id]);
    
    $bank = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$bank) {
        ApiResponse::error("Blood bank not found", 404);
    }
    
    // Get operating hours
    $query = "SELECT day_of_week, 
                     TIME_FORMAT(start_time, '%H:%i') as start_time, 
                     TIME_FORMAT(end_time, '%H:%i') as end_time 
              FROM available_slots 
              WHERE blood_bank_id = ? 
              ORDER BY day_of_week";
    $stmt = $db->prepare($query);
    $stmt->execute([$bank_id]);
    
    $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    $operating_hours = [];
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $operating_hours[] = sprintf(
            "%s: %s - %s",
            $days[$row['day_of_week'] - 1],
            $row['start_time'],
            $row['end_time']
        );
    }
    
    $bank['operating_hours'] = implode("<br>", $operating_hours);
    
    ApiResponse::send([
        "success" => true,
        "bank" => $bank
    ]);
    
} catch (Exception $e) {
    error_log("Blood Bank Details Error: " . $e->getMessage());
    ApiResponse::error("Server error", 500);
} 