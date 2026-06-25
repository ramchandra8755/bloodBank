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
    
    // Get POST data
    $data = json_decode(file_get_contents("php://input"));
    
    // Validate required fields
    $required_fields = ['blood_bank_id', 'appointment_date', 'time_slot'];
    foreach ($required_fields as $field) {
        if (!isset($data->$field) || empty($data->$field)) {
            ApiResponse::error("Missing required field: $field", 400);
        }
    }
    
    // Check if user is eligible for donation
    $query = "SELECT next_eligible_date FROM donation_history 
              WHERE donor_id = ? ORDER BY donation_date DESC LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->execute([$user_id]);
    $last_donation = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($last_donation && $last_donation['next_eligible_date'] > $data->appointment_date) {
        ApiResponse::error("You are not eligible for donation until " . $last_donation['next_eligible_date'], 400);
    }
    
    // Check if slot is still available
    $query = "SELECT COUNT(*) as count FROM appointments 
              WHERE blood_bank_id = ? AND appointment_date = ? 
              AND time_slot = ? AND status = 'Scheduled'";
    $stmt = $db->prepare($query);
    $stmt->execute([$data->blood_bank_id, $data->appointment_date, $data->time_slot]);
    $slot_check = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Get max appointments for this slot
    $query = "SELECT max_appointments FROM available_slots 
              WHERE blood_bank_id = ? AND 
              DAYOFWEEK(?) = day_of_week AND 
              ? BETWEEN start_time AND end_time";
    $stmt = $db->prepare($query);
    $stmt->execute([$data->blood_bank_id, $data->appointment_date, $data->time_slot]);
    $slot_info = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$slot_info || $slot_check['count'] >= $slot_info['max_appointments']) {
        ApiResponse::error("This time slot is no longer available", 400);
    }
    
    // Insert appointment
    $query = "INSERT INTO appointments 
              (donor_id, blood_bank_id, appointment_date, time_slot, notes) 
              VALUES (?, ?, ?, ?, ?)";
    $stmt = $db->prepare($query);
    $stmt->execute([
        $user_id,
        $data->blood_bank_id,
        $data->appointment_date,
        $data->time_slot,
        isset($data->notes) ? htmlspecialchars(strip_tags($data->notes)) : null
    ]);
    
    ApiResponse::send([
        "success" => true,
        "message" => "Appointment scheduled successfully"
    ]);
    
} catch (Exception $e) {
    error_log("Appointment Create Error: " . $e->getMessage());
    ApiResponse::error("Server error", 500);
} 