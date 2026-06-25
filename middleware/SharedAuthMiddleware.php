<?php
class SharedAuthMiddleware {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function getUserType($email) {
        try {
            // Check if user is a blood bank admin
            $query = "SELECT 'bank_admin' as type, ba.id, ba.blood_bank_id, ba.role, bb.name as blood_bank_name 
                      FROM blood_bank_admins ba 
                      JOIN blood_banks bb ON ba.blood_bank_id = bb.id 
                      WHERE ba.email = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$email]);
            $bank_admin = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($bank_admin) {
                return [
                    'type' => 'bank_admin',
                    'data' => $bank_admin
                ];
            }
            
            // Check if user is a regular user
            $query = "SELECT 'user' as type, id, name FROM users WHERE email = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                return [
                    'type' => 'user',
                    'data' => $user
                ];
            }
            
            return null;
        } catch (Exception $e) {
            error_log("User type check error: " . $e->getMessage());
            return null;
        }
    }
} 