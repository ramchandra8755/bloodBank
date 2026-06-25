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
    
    if (!isset($data->request_id) || !isset($data->status)) {
        ApiResponse::error("Missing required fields", 400);
    }
    
    // Verify ownership
    $query = "SELECT requester_id FROM emergency_requests WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$data->request_id]);
    $request = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$request || $request['requester_id'] != $user_id) {
        ApiResponse::error("Unauthorized to update this request", 403);
    }
    
    // Update status
    $query = "UPDATE emergency_requests SET status = ? WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([
        htmlspecialchars(strip_tags($data->status)),
        $data->request_id
    ]);
    
    ApiResponse::send([
        "success" => true,
        "message" => "Request status updated successfully"
    ]);
    
} catch (Exception $e) {
    error_log("Emergency Request Update Status Error: " . $e->getMessage());
    ApiResponse::error("Server error", 500);
} 