<?php
require_once '../api/utils/ApiResponse.php';
require_once '../config/Database.php';
require_once '../middleware/AuthMiddleware.php';

ApiResponse::init();

try {
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        ApiResponse::error("Database connection failed", 500);
    }
    
    // Verify token
    $auth = new AuthMiddleware($db);
    $user_id = $auth->authenticateToken();
    
    if (!$user_id) {
        ApiResponse::error("Unauthorized access", 401);
    }
    
    // Get search parameters
    $blood_group = isset($_GET['blood_group']) ? trim($_GET['blood_group']) : '';
    $location = isset($_GET['location']) ? trim($_GET['location']) : '';
    
    // Build query
    $query = "SELECT u.*, 
              (SELECT next_eligible_date 
               FROM donation_history 
               WHERE donor_id = u.id 
               ORDER BY donation_date DESC 
               LIMIT 1) as next_eligible_date
              FROM users u 
              WHERE 1=1";
    
    $params = [];
    
    if (!empty($blood_group)) {
        // Use exact match for blood group
        $query .= " AND u.blood_group = ?";
        $params[] = $blood_group;
    }
    
    if (!empty($location)) {
        $query .= " AND u.location LIKE ?";
        $params[] = "%$location%";
    }
    
    $query .= " AND u.id != ? ORDER BY u.is_available DESC, u.name ASC";
    $params[] = $user_id;
    
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    
    $donors = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Process sensitive data
        if (!$row['share_contact']) {
            $row['phone'] = '***********';
            $row['email'] = '***********';
        }
        
        // Add donation eligibility status
        $next_eligible_date = $row['next_eligible_date'];
        if ($next_eligible_date) {
            $today = new DateTime();
            $eligible_date = new DateTime($next_eligible_date);
            $row['donation_status'] = $today > $eligible_date ? 'eligible' : 'waiting';
            $row['next_eligible_date'] = $next_eligible_date;
        } else {
            $row['donation_status'] = 'eligible';
            $row['next_eligible_date'] = null;
        }
        
        $donors[] = $row;
    }
    
    ApiResponse::send([
        "success" => true,
        "donors" => $donors
    ]);
    
} catch (Exception $e) {
    error_log("Search API Error: " . $e->getMessage());
    ApiResponse::error("Server error: " . $e->getMessage(), 500);
} 