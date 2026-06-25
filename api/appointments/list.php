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
    
    // Get appointments
    $query = "SELECT a.*, bb.name as blood_bank_name 
              FROM appointments a 
              JOIN blood_banks bb ON a.blood_bank_id = bb.id 
              WHERE a.donor_id = ? 
              ORDER BY a.appointment_date DESC, a.time_slot DESC";
    
    $stmt = $db->prepare($query);
    $stmt->execute([$user_id]);
    
    $upcoming = [];
    $past = [];
    $today = new DateTime();
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $apt_date = new DateTime($row['appointment_date']);
        
        if ($apt_date >= $today && $row['status'] === 'Scheduled') {
            $upcoming[] = $row;
        } else {
            $past[] = $row;
        }
    }
    
    ApiResponse::send([
        "success" => true,
        "upcoming" => $upcoming,
        "past" => $past
    ]);
    
} catch (Exception $e) {
    error_log("Appointments List Error: " . $e->getMessage());
    ApiResponse::error("Server error", 500);
} 