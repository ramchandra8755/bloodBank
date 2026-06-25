<?php
class AuthMiddleware {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function authenticateToken() {
        // Get all headers
        $headers = getallheaders();
        
        // Debug: Log headers
        error_log("Request Headers: " . print_r($headers, true));
        
        // Check if Authorization header exists
        if (!isset($headers['Authorization']) && !isset($headers['authorization'])) {
            error_log("No Authorization header found");
            return false;
        }
        
        // Get token from header (case-insensitive check)
        $auth_header = isset($headers['Authorization']) ? $headers['Authorization'] : $headers['authorization'];
        $token = str_replace('Bearer ', '', $auth_header);
        
        // Debug: Log token
        error_log("Token received: " . $token);
        
        if (empty($token)) {
            error_log("Empty token");
            return false;
        }
        
        try {
            // Check token in database
            $query = "SELECT user_id FROM auth_tokens WHERE token = ? AND expires_at > NOW()";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$token]);
            
            if ($stmt->rowCount() > 0) {
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                return $result['user_id'];
            }
            
            error_log("Token not found or expired");
            return false;
            
        } catch (PDOException $e) {
            error_log("Database error in token verification: " . $e->getMessage());
            return false;
        }
    }
} 