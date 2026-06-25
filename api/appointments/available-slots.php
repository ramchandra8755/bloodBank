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
    
    // Get parameters
    $bank_id = isset($_GET['bank_id']) ? $_GET['bank_id'] : null;
    $date = isset($_GET['date']) ? $_GET['date'] : null;
    
    if (!$bank_id || !$date) {
        ApiResponse::error("Missing required parameters", 400);
    }
    
    // Get available slots for the day
    $query = "SELECT start_time, end_time, slot_duration, max_appointments 
              FROM available_slots 
              WHERE blood_bank_id = ? AND day_of_week = DAYOFWEEK(?)";
    
    $stmt = $db->prepare($query);
    $stmt->execute([$bank_id, $date]);
    $slot_config = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$slot_config) {
        ApiResponse::error("No slots available for this day", 404);
    }
    
    // Generate time slots
    $start = new DateTime($slot_config['start_time']);
    $end = new DateTime($slot_config['end_time']);
    $duration = $slot_config['slot_duration'];
    $max_appointments = $slot_config['max_appointments'];
    
    $slots = [];
    $current = clone $start;
    
    while ($current < $end) {
        $time = $current->format('H:i:s');
        
        // Check existing appointments for this slot
        $query = "SELECT COUNT(*) as count FROM appointments 
                  WHERE blood_bank_id = ? AND appointment_date = ? 
                  AND time_slot = ? AND status = 'Scheduled'";
        $stmt = $db->prepare($query);
        $stmt->execute([$bank_id, $date, $time]);
        $booked = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        if ($booked < $max_appointments) {
            $slots[] = ['time' => $time, 'available' => $max_appointments - $booked];
        }
        
        $current->modify("+{$duration} minutes");
    }
    
    ApiResponse::send([
        "success" => true,
        "slots" => $slots
    ]);
    
} catch (Exception $e) {
    error_log("Available Slots Error: " . $e->getMessage());
    ApiResponse::error("Server error", 500);
} 