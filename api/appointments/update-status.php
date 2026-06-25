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
    
    if (!isset($data->appointment_id) || !isset($data->status)) {
        ApiResponse::error("Missing required fields", 400);
    }
    
    // Verify ownership
    $query = "SELECT donor_id FROM appointments WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$data->appointment_id]);
    $appointment = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$appointment || $appointment['donor_id'] != $user_id) {
        ApiResponse::error("Unauthorized to update this appointment", 403);
    }
    
    // Update status
    $query = "UPDATE appointments SET status = ? WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([
        htmlspecialchars(strip_tags($data->status)),
        $data->appointment_id
    ]);
    
    ApiResponse::send([
        "success" => true,
        "message" => "Appointment status updated successfully"
    ]);
    
} catch (Exception $e) {
    error_log("Appointment Update Status Error: " . $e->getMessage());
    ApiResponse::error("Server error", 500);
} 