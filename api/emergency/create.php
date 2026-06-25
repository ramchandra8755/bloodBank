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
    $required_fields = ['patient_name', 'blood_group', 'units_needed', 'hospital_name', 
                       'location', 'contact_number', 'urgency_level'];
    
    foreach ($required_fields as $field) {
        if (!isset($data->$field) || empty($data->$field)) {
            ApiResponse::error("Missing required field: $field", 400);
        }
    }
    
    // Insert emergency request
    $query = "INSERT INTO emergency_requests 
              (requester_id, patient_name, blood_group, units_needed, hospital_name, 
               location, contact_number, urgency_level, additional_notes) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
              
    $stmt = $db->prepare($query);
    
    $stmt->execute([
        $user_id,
        htmlspecialchars(strip_tags($data->patient_name)),
        htmlspecialchars(strip_tags($data->blood_group)),
        (int)$data->units_needed,
        htmlspecialchars(strip_tags($data->hospital_name)),
        htmlspecialchars(strip_tags($data->location)),
        htmlspecialchars(strip_tags($data->contact_number)),
        htmlspecialchars(strip_tags($data->urgency_level)),
        isset($data->additional_notes) ? htmlspecialchars(strip_tags($data->additional_notes)) : null
    ]);
    
    ApiResponse::send([
        "success" => true,
        "message" => "Emergency request created successfully"
    ]);
    
} catch (Exception $e) {
    error_log("Emergency Request Create Error: " . $e->getMessage());
    ApiResponse::error("Server error", 500);
} 