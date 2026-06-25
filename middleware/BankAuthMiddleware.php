<?php
require_once __DIR__ . '/../config/env.php';

class BankAuthMiddleware {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function authenticateToken() {
        $headers = getallheaders();
        
        if (!isset($headers['Authorization'])) {
            return false;
        }
        
        $auth_header = $headers['Authorization'];
        if (!preg_match('/Bearer\s(\S+)/', $auth_header, $matches)) {
            return false;
        }

        $token = $matches[1];
        
        try {
            $token_parts = explode('.', $token);
            
            if (count($token_parts) !== 3) {
                return false;
            }
            
            $payload = json_decode(base64_decode($token_parts[1]), true);
            
            if (!isset($payload['admin_id']) || !isset($payload['blood_bank_id'])) {
                return false;
            }
            
            // Verify token signature
            $header = base64_decode($token_parts[0]);
            $signature = base64_decode(strtr($token_parts[2], '-_', '+/'));
            
            $expected_signature = hash_hmac('sha256', 
                $token_parts[0] . "." . $token_parts[1], 
                $_ENV['JWT_SECRET'], 
                true
            );
            
            if (!hash_equals($signature, $expected_signature)) {
                return false;
            }
            
            // Verify admin exists and is active
            $query = "SELECT * FROM blood_bank_admins 
                     WHERE id = ? AND blood_bank_id = ? AND role = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                $payload['admin_id'], 
                $payload['blood_bank_id'],
                $payload['role']
            ]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$admin) {
                return false;
            }
            
            return [
                'admin_id' => $payload['admin_id'],
                'blood_bank_id' => $payload['blood_bank_id'],
                'role' => $payload['role']
            ];
            
        } catch (Exception $e) {
            error_log("Token verification error: " . $e->getMessage());
            return false;
        }
    }
} 