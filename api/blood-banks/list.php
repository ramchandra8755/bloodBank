<?php
require_once '../../api/utils/ApiResponse.php';
require_once '../../config/Database.php';

ApiResponse::init();

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Get search parameters
    $blood_group = isset($_GET['blood_group']) ? $_GET['blood_group'] : '';
    $location = isset($_GET['location']) ? $_GET['location'] : '';
    
    // Base query
    $query = "SELECT DISTINCT bb.* FROM blood_banks bb";
    
    // Add join with donation_history if blood group is specified
    if (!empty($blood_group)) {
        $query .= " LEFT JOIN appointments a ON bb.id = a.blood_bank_id
                   LEFT JOIN users u ON a.donor_id = u.id";
    }
    
    // Build WHERE clause
    $where_conditions = [];
    $params = [];
    
    if (!empty($blood_group)) {
        // Use prepared statement parameter for blood group
        $where_conditions[] = "u.blood_group = ?";
        $params[] = $blood_group;
    }
    
    if (!empty($location)) {
        $where_conditions[] = "(bb.address LIKE ? OR bb.name LIKE ?)";
        $params[] = "%$location%";
        $params[] = "%$location%";
    }
    
    // Add WHERE clause if conditions exist
    if (!empty($where_conditions)) {
        $query .= " WHERE " . implode(" AND ", $where_conditions);
    }
    
    // Add group by and order
    $query .= " GROUP BY bb.id ORDER BY bb.name";
    
    // Prepare and execute query
    $stmt = $db->prepare($query);
    if (!empty($params)) {
        $stmt->execute($params);
    } else {
        $stmt->execute();
    }
    
    $blood_banks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get operating hours for each blood bank
    foreach ($blood_banks as &$bank) {
        $query = "SELECT * FROM available_slots WHERE blood_bank_id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$bank['id']]);
        $bank['operating_hours'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    ApiResponse::send([
        "success" => true,
        "blood_banks" => $blood_banks
    ]);
    
} catch (Exception $e) {
    error_log("Blood Bank List Error: " . $e->getMessage());
    ApiResponse::error("Server error", 500);
} 