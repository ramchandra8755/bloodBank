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
    
    // Get total donors
    $totalQuery = "SELECT COUNT(*) as total FROM users";
    $stmt = $db->prepare($totalQuery);
    $stmt->execute();
    $totalDonors = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Debug log
    error_log("Total donors: " . $totalDonors);
    
    // Get available donors
    $availableQuery = "SELECT COUNT(*) as available FROM users WHERE is_available = 1";
    $stmt = $db->prepare($availableQuery);
    $stmt->execute();
    $availableDonors = $stmt->fetch(PDO::FETCH_ASSOC)['available'];
    
    // Debug log
    error_log("Available donors: " . $availableDonors);
    
    // Get unique locations (excluding null and empty values)
    $locationsQuery = "SELECT COUNT(DISTINCT location) as locations 
                      FROM users 
                      WHERE location IS NOT NULL 
                      AND TRIM(location) != ''";
    $stmt = $db->prepare($locationsQuery);
    $stmt->execute();
    $totalLocations = $stmt->fetch(PDO::FETCH_ASSOC)['locations'];
    
    // Debug log
    error_log("Total locations: " . $totalLocations);
    
    // Get blood group distribution
    $bloodGroupsQuery = "SELECT 
                            blood_group, 
                            COUNT(*) as count,
                            COUNT(CASE WHEN is_available = 1 THEN 1 END) as available_count
                        FROM users 
                        WHERE blood_group IS NOT NULL
                        GROUP BY blood_group 
                        ORDER BY count DESC";
    $stmt = $db->prepare($bloodGroupsQuery);
    $stmt->execute();
    $bloodGroups = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Debug log
    error_log("Blood groups distribution: " . print_r($bloodGroups, true));
    
    // Calculate percentages for blood groups
    $bloodGroupStats = array_map(function($group) use ($totalDonors) {
        $group['percentage'] = $totalDonors > 0 ? 
            round(($group['count'] / $totalDonors) * 100, 1) : 0;
        return $group;
    }, $bloodGroups);
    
    ApiResponse::send([
        "success" => true,
        "statistics" => [
            "total_donors" => (int)$totalDonors,
            "available_donors" => (int)$availableDonors,
            "total_locations" => (int)$totalLocations,
            "blood_groups" => $bloodGroupStats,
            "availability_rate" => $totalDonors > 0 ? 
                round(($availableDonors / $totalDonors) * 100, 1) : 0
        ]
    ]);
    
} catch (Exception $e) {
    error_log("Statistics API Error: " . $e->getMessage());
    ApiResponse::error("Server error: " . $e->getMessage(), 500);
} 