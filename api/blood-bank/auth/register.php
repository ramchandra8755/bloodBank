<?php
require_once '../../../api/utils/ApiResponse.php';
require_once '../../../config/Database.php';

ApiResponse::init();

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Get POST data
    $data = json_decode(file_get_contents("php://input"));
    
    // Validate required fields
    if (!isset($data->blood_bank) || !isset($data->operating_hours) || !isset($data->admin)) {
        ApiResponse::error("Missing required data", 400);
    }
    
    // Check if email already exists
    $query = "SELECT id FROM blood_bank_admins WHERE email = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([strtolower($data->admin->email)]);
    if ($stmt->fetch(PDO::FETCH_ASSOC)) {
        ApiResponse::error("Email already registered", 400);
    }
    
    // Start transaction
    $db->beginTransaction();
    
    try {
        // Insert blood bank
        $query = "INSERT INTO blood_banks (name, address, contact_number, latitude, longitude) 
                  VALUES (?, ?, ?, ?, ?)";
        $stmt = $db->prepare($query);
        $stmt->execute([
            htmlspecialchars(strip_tags($data->blood_bank->name)),
            htmlspecialchars(strip_tags($data->blood_bank->address)),
            htmlspecialchars(strip_tags($data->blood_bank->contact_number)),
            $data->blood_bank->latitude,
            $data->blood_bank->longitude
        ]);
        
        $blood_bank_id = $db->lastInsertId();
        
        // Insert operating hours
        $query = "INSERT INTO available_slots 
                  (blood_bank_id, day_of_week, start_time, end_time, slot_duration, max_appointments) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($query);
        
        foreach ($data->operating_hours as $hours) {
            $stmt->execute([
                $blood_bank_id,
                $hours->day_of_week,
                $hours->start_time,
                $hours->end_time,
                $hours->slot_duration,
                $hours->max_appointments
            ]);
        }
        
        // Insert admin account
        $query = "INSERT INTO blood_bank_admins 
                  (blood_bank_id, name, email, password, role) 
                  VALUES (?, ?, ?, ?, 'admin')";
        $stmt = $db->prepare($query);
        $stmt->execute([
            $blood_bank_id,
            htmlspecialchars(strip_tags($data->admin->name)),
            strtolower($data->admin->email),
            password_hash($data->admin->password, PASSWORD_DEFAULT)
        ]);
        
        // Commit transaction
        $db->commit();
        
        ApiResponse::send([
            "success" => true,
            "message" => "Blood bank registered successfully"
        ]);
        
    } catch (Exception $e) {
        $db->rollBack();
        throw $e;
    }
    
} catch (Exception $e) {
    error_log("Blood Bank Registration Error: " . $e->getMessage());
    ApiResponse::error("Server error", 500);
} 