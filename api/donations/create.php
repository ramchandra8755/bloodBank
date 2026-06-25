<?php
require_once '../../config/Database.php';
require_once '../../api/utils/ApiResponse.php';
require_once '../../middleware/AuthMiddleware.php';
require_once '../../classes/RewardsManager.php';

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
    $required_fields = ['donation_date', 'blood_group', 'units', 'hospital_name'];
    foreach ($required_fields as $field) {
        if (!isset($data->$field) || empty($data->$field)) {
            ApiResponse::error("Missing required field: $field", 400);
        }
    }
    
    // Calculate next eligible date (3 months from donation date)
    $next_eligible_date = date('Y-m-d', strtotime($data->donation_date . ' + 3 months'));
    
    // Start transaction
    $db->beginTransaction();
    
    try {
        // Insert donation record
        $query = "INSERT INTO donation_history 
                  (donor_id, donation_date, blood_group, units, hospital_name, 
                   certificate_number, next_eligible_date, notes) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                  
        $stmt = $db->prepare($query);
        
        $stmt->execute([
            $user_id,
            $data->donation_date,
            htmlspecialchars(strip_tags($data->blood_group)),
            (int)$data->units,
            htmlspecialchars(strip_tags($data->hospital_name)),
            isset($data->certificate_number) ? htmlspecialchars(strip_tags($data->certificate_number)) : null,
            $next_eligible_date,
            isset($data->notes) ? htmlspecialchars(strip_tags($data->notes)) : null
        ]);
        
        // Update user's blood group if not set
        $query = "UPDATE users SET blood_group = ? WHERE id = ? AND blood_group IS NULL";
        $stmt = $db->prepare($query);
        $stmt->execute([$data->blood_group, $user_id]);
        
        // Update rewards
        $rewardsManager = new RewardsManager($db);
        $rewards = $rewardsManager->updateDonorRewards($user_id);
        
        // If we got here, commit the transaction
        $db->commit();
        
        ApiResponse::send([
            "success" => true,
            "message" => "Donation record added successfully",
            "rewards" => $rewards
        ]);
        
    } catch (Exception $e) {
        // Something went wrong, rollback transaction
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        throw $e;
    }
    
} catch (Exception $e) {
    // Ensure transaction is rolled back
    if ($db && $db->inTransaction()) {
        $db->rollBack();
    }
    error_log("Donation Create Error: " . $e->getMessage());
    ApiResponse::error("Server error: " . $e->getMessage(), 500);
}
?> 