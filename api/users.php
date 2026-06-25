<?php
require_once '../api/utils/ApiResponse.php';
require_once '../config/Database.php';
require_once '../models/User.php';
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
    
    // Get pagination parameters
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 20;
    $offset = ($page - 1) * $limit;
    
    // Get and sanitize filter parameters
    $blood_group = isset($_GET['blood_group']) ? urldecode(trim($_GET['blood_group'])) : '';
    $location = isset($_GET['location']) ? htmlspecialchars(strip_tags($_GET['location'])) : '';
    $availability = isset($_GET['availability']) && $_GET['availability'] !== '' ? (int)$_GET['availability'] : null;
    
    // Debug log
    error_log("Filters - Blood Group: $blood_group, Location: $location, Availability: " . var_export($availability, true));
    
    // Build base query
    $query = "SELECT u.*, 
              (SELECT next_eligible_date 
               FROM donation_history 
               WHERE donor_id = u.id 
               ORDER BY donation_date DESC 
               LIMIT 1) as next_eligible_date
              FROM users u
              WHERE 1=1";
              
    $countQuery = "SELECT COUNT(*) as total FROM users WHERE 1=1";
    $params = [];
    
    // Add filters to both queries
    if (!empty($blood_group)) {
        $whereClause = " AND blood_group = :blood_group";
        $query .= $whereClause;
        $countQuery .= $whereClause;
        $params[':blood_group'] = str_replace(' ', '+', $blood_group);
    }
    
    if (!empty($location)) {
        $whereClause = " AND location LIKE :location";
        $query .= $whereClause;
        $countQuery .= $whereClause;
        $params[':location'] = "%{$location}%";
    }
    
    if ($availability !== null) {
        $whereClause = " AND is_available = :availability";
        $query .= $whereClause;
        $countQuery .= $whereClause;
        $params[':availability'] = $availability;
    }
    
    // Add pagination
    $query .= " ORDER BY name ASC LIMIT :limit OFFSET :offset";
    
    // Debug log
    error_log("Query: " . $query);
    error_log("Params: " . print_r($params, true));
    
    // Get total count
    $countStmt = $db->prepare($countQuery);
    foreach ($params as $key => $value) {
        $countStmt->bindValue($key, $value);
    }
    $countStmt->execute();
    $totalCount = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Execute main query
    $stmt = $db->prepare($query);
    
    // Bind filter parameters
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    
    // Bind pagination parameters
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    
    $stmt->execute();
    
    // Process results
    $users = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Add profile picture URL
        if ($row['profile_picture']) {
            $row['profile_picture_url'] = '/blood_donor/uploads/profile_pictures/' . $row['profile_picture'];
        } else {
            $row['profile_picture_url'] = '/blood_donor/assets/default-avatar.png';
        }
        
        // Filter sensitive information
        if (!$row['share_contact']) {
            unset($row['phone'], $row['email']);
        }
        
        // Remove sensitive fields
        unset($row['id']);
        
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
        
        $users[] = $row;
    }
    
    // Debug log
    error_log("Found " . count($users) . " users");
    
    ApiResponse::send([
        "success" => true,
        "total" => (int)$totalCount,
        "page" => $page,
        "total_pages" => ceil($totalCount / $limit),
        "users" => $users,
        "filters" => [
            "blood_group" => $blood_group,
            "location" => $location,
            "availability" => $availability
        ],
        "debug" => [
            "page" => $page,
            "limit" => $limit,
            "offset" => $offset
        ]
    ]);
    
} catch (Exception $e) {
    error_log("Users API Error: " . $e->getMessage());
    ApiResponse::error("Server error: " . $e->getMessage(), 500);
} 