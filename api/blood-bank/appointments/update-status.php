<?php
require_once '../../../api/utils/ApiResponse.php';
require_once '../../../config/Database.php';
require_once '../../../middleware/BankAuthMiddleware.php';
require_once '../../../config/env.php';

ApiResponse::init();

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Verify token
    $auth = new BankAuthMiddleware($db);
    $admin_data = $auth->authenticateToken();
    
    if (!$admin_data) {
        ApiResponse::error("Unauthorized access", 401);
        exit;
    }
    
    // Get POST data
    $data = json_decode(file_get_contents("php://input"));
    
    if (!isset($data->appointment_id) || !isset($data->status)) {
        ApiResponse::error("Missing required fields", 400);
        exit;
    }
    
    // Validate status
    $valid_statuses = ['Scheduled', 'Completed', 'Cancelled', 'Missed'];
    if (!in_array($data->status, $valid_statuses)) {
        ApiResponse::error("Invalid status", 400);
        exit;
    }
    
    // Start transaction
    $db->beginTransaction();
    
    try {
        // Get appointment and donor details
        $query = "SELECT a.*, u.blood_group, bb.name as hospital_name 
                  FROM appointments a 
                  JOIN users u ON a.donor_id = u.id 
                  JOIN blood_banks bb ON a.blood_bank_id = bb.id 
                  WHERE a.id = ? AND a.blood_bank_id = ? AND a.status = 'Scheduled'";
        $stmt = $db->prepare($query);
        $stmt->execute([$data->appointment_id, $admin_data['blood_bank_id']]);
        $appointment = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$appointment) {
            $db->rollBack();
            ApiResponse::error("Appointment not found or cannot be updated", 404);
            exit;
        }
        
        // Update appointment status
        $query = "UPDATE appointments 
                  SET status = ?, updated_at = CURRENT_TIMESTAMP 
                  WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([
            $data->status,
            $data->appointment_id
        ]);
        
        // If marked as completed, create donation record
        if ($data->status === 'Completed') {
            $query = "INSERT INTO donation_history 
                      (donor_id, donation_date, blood_group, units, 
                       hospital_name, next_eligible_date) 
                      VALUES (?, CURRENT_DATE, ?, 1, ?, 
                             DATE_ADD(CURRENT_DATE, INTERVAL 3 MONTH))";
            $stmt = $db->prepare($query);
            $stmt->execute([
                $appointment['donor_id'],
                $appointment['blood_group'],
                $appointment['hospital_name']
            ]);
        }
        
        $db->commit();
        
        ApiResponse::send([
            "success" => true,
            "message" => "Appointment status updated successfully",
            "status" => $data->status
        ]);
        
    } catch (Exception $e) {
        $db->rollBack();
        throw $e;
    }
    
} catch (Exception $e) {
    error_log("Update Status Error: " . $e->getMessage());
    ApiResponse::error("Server error: " . $e->getMessage(), 500);
} 