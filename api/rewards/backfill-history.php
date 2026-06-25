<?php
require_once '../../config/Database.php';
require_once '../../api/utils/ApiResponse.php';
require_once '../../middleware/AuthMiddleware.php';

ApiResponse::init();

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Get all donations that don't have history entries
    $query = "SELECT 
                dh.donor_id,
                dh.units,
                dh.donation_date
              FROM donation_history dh
              LEFT JOIN points_history ph 
                ON ph.donor_id = dh.donor_id 
                AND DATE(ph.created_at) = DATE(dh.donation_date)
              WHERE ph.id IS NULL
              ORDER BY dh.donation_date";
              
    $stmt = $db->prepare($query);
    $stmt->execute();
    $donations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $db->beginTransaction();
    
    try {
        foreach ($donations as $donation) {
            // Add base donation points
            $query = "INSERT INTO points_history 
                     (donor_id, points, description, type, created_at) 
                     VALUES (?, 100, 'Blood donation completed', 'donation', ?)";
            $stmt = $db->prepare($query);
            $stmt->execute([$donation['donor_id'], $donation['donation_date']]);
            
            // Add unit bonus points if any
            if ($donation['units'] > 0) {
                $unitPoints = $donation['units'] * 50;
                $query = "INSERT INTO points_history 
                         (donor_id, points, description, type, created_at) 
                         VALUES (?, ?, 'Bonus points for units donated', 'donation', ?)";
                $stmt = $db->prepare($query);
                $stmt->execute([$donation['donor_id'], $unitPoints, $donation['donation_date']]);
            }
        }
        
        $db->commit();
        ApiResponse::send([
            "success" => true,
            "message" => "History backfilled successfully",
            "records_processed" => count($donations)
        ]);
        
    } catch (Exception $e) {
        $db->rollBack();
        throw $e;
    }
    
} catch (Exception $e) {
    error_log("Backfill Error: " . $e->getMessage());
    ApiResponse::error("Server error: " . $e->getMessage(), 500);
}
?> 