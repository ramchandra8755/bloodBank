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
    
    $blood_bank_id = $admin_data['blood_bank_id'];
    $today = date('Y-m-d');
    
    // Get today's appointments
    $query = "SELECT a.*, u.name as donor_name, u.blood_group, u.phone, u.email 
              FROM appointments a 
              JOIN users u ON a.donor_id = u.id 
              WHERE a.blood_bank_id = ? 
              AND a.appointment_date = ? 
              ORDER BY a.time_slot ASC";
    
    $stmt = $db->prepare($query);
    $stmt->execute([$blood_bank_id, $today]);
    $today_appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get upcoming appointments
    $query = "SELECT a.*, u.name as donor_name, u.blood_group, u.phone, u.email 
              FROM appointments a 
              JOIN users u ON a.donor_id = u.id 
              WHERE a.blood_bank_id = ? 
              AND a.appointment_date > ? 
              AND a.status = 'Scheduled'
              ORDER BY a.appointment_date ASC, a.time_slot ASC 
              LIMIT 50";
    
    $stmt = $db->prepare($query);
    $stmt->execute([$blood_bank_id, $today]);
    $upcoming_appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get past appointments
    $query = "SELECT a.*, u.name as donor_name, u.blood_group, u.phone, u.email 
              FROM appointments a 
              JOIN users u ON a.donor_id = u.id 
              WHERE a.blood_bank_id = ? 
              AND (a.appointment_date < ? OR 
                   (a.appointment_date = ? AND a.status IN ('Completed', 'Missed', 'Cancelled')))
              ORDER BY a.appointment_date DESC, a.time_slot DESC 
              LIMIT 50";
    
    $stmt = $db->prepare($query);
    $stmt->execute([$blood_bank_id, $today, $today]);
    $past_appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get statistics
    $stats = [
        'today' => count(array_filter($today_appointments, fn($a) => $a['status'] === 'Scheduled')),
        'upcoming' => count($upcoming_appointments),
        'month' => getMonthlyAppointments($db, $blood_bank_id),
        'total' => getTotalCompletedAppointments($db, $blood_bank_id)
    ];
    
    ApiResponse::send([
        "success" => true,
        "today" => $today_appointments,
        "upcoming" => $upcoming_appointments,
        "past" => $past_appointments,
        "stats" => $stats
    ]);
    
} catch (Exception $e) {
    error_log("Dashboard Error: " . $e->getMessage());
    ApiResponse::error("Server error", 500);
}

function getMonthlyAppointments($db, $blood_bank_id) {
    $query = "SELECT COUNT(*) as count 
              FROM appointments 
              WHERE blood_bank_id = ? 
              AND MONTH(appointment_date) = MONTH(CURRENT_DATE()) 
              AND YEAR(appointment_date) = YEAR(CURRENT_DATE()) 
              AND status = 'Completed'";
    
    $stmt = $db->prepare($query);
    $stmt->execute([$blood_bank_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['count'];
}

function getTotalCompletedAppointments($db, $blood_bank_id) {
    $query = "SELECT COUNT(*) as count 
              FROM appointments 
              WHERE blood_bank_id = ? 
              AND status = 'Completed'";
    
    $stmt = $db->prepare($query);
    $stmt->execute([$blood_bank_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['count'];
} 