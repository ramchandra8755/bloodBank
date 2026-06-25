<?php
session_start();
if (!isset($_SESSION['bank_admin_id']) || !isset($_SESSION['blood_bank_id'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/Database.php';
$database = new Database();
$db = $database->getConnection();

// Verify admin exists and is active
$query = "SELECT ba.*, bb.name as blood_bank_name 
          FROM blood_bank_admins ba 
          JOIN blood_banks bb ON ba.blood_bank_id = bb.id 
          WHERE ba.id = ? AND ba.blood_bank_id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$_SESSION['bank_admin_id'], $_SESSION['blood_bank_id']]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$admin) {
    session_destroy();
    header('Location: login.php');
    exit;
} 