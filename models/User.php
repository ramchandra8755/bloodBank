<?php
class User {
    private $conn;
    private $table_name = "users";
    
    public $id;
    public $name;
    public $email;
    public $password;
    public $phone;
    public $age;
    public $gender;
    public $blood_group;
    public $location;
    public $latitude;
    public $longitude;
    public $is_available;
    public $share_contact;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                (name, email, password, phone, age, gender, blood_group, location)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        
        $this->password = password_hash($this->password, PASSWORD_DEFAULT);
        
        return $stmt->execute([
            $this->name,
            $this->email,
            $this->password,
            $this->phone,
            $this->age,
            $this->gender,
            $this->blood_group,
            $this->location
        ]);
    }
    
    public function login($email, $password) {
        $query = "SELECT id, name, email, password FROM " . $this->table_name . " WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$email]);
        
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if (password_verify($password, $row['password'])) {
                $token = $this->generateToken($row['id']);
                if ($token) {
                    return [
                        'token' => $token,
                        'user_id' => $row['id'],
                        'name' => $row['name'],
                        'email' => $row['email']
                    ];
                }
            }
        }
        return false;
    }
    
    private function generateToken($user_id) {
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+30 days'));
        
        $query = "INSERT INTO auth_tokens (user_id, token, expires_at) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        
        if ($stmt->execute([$user_id, $token, $expires])) {
            return $token;
        }
        return false;
    }
    
    public function emailExists($email) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$email]);
        return $stmt->rowCount() > 0;
    }
} 