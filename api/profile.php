<?php
require_once '../api/utils/ApiResponse.php';
require_once '../config/Database.php';
require_once '../models/User.php';
require_once '../middleware/AuthMiddleware.php';

ApiResponse::init();

// Add file upload configuration
define('UPLOAD_DIR', '../uploads/profile_pictures/');
if (!file_exists(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0777, true);
}

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
    
    $method = $_SERVER['REQUEST_METHOD'];
    
    switch($method) {
        case 'GET':
            $query = "SELECT id, name, email, phone, age, gender, blood_group, location, 
                            is_available, share_contact, profile_picture 
                     FROM users WHERE id = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$user_id]);
            
            if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                // Add full URL for profile picture
                if ($row['profile_picture']) {
                    $row['profile_picture_url'] = '/blood_donor/uploads/profile_pictures/' . $row['profile_picture'];
                }
                ApiResponse::send([
                    "success" => true,
                    "profile" => $row
                ]);
            } else {
                ApiResponse::error("Profile not found", 404);
            }
            break;
            
        case 'POST':
            // Handle file upload
            if (isset($_FILES['profile_picture'])) {
                $file = $_FILES['profile_picture'];
                $fileName = $user_id . '_' . time() . '_' . basename($file['name']);
                $targetPath = UPLOAD_DIR . $fileName;
                
                // Validate file type
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                if (!in_array($file['type'], $allowedTypes)) {
                    ApiResponse::error("Invalid file type. Only JPG, PNG and GIF allowed.", 400);
                }
                
                // Validate file size (max 5MB)
                if ($file['size'] > 5 * 1024 * 1024) {
                    ApiResponse::error("File too large. Maximum size is 5MB.", 400);
                }
                
                if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                    // Update database with new file name
                    $query = "UPDATE users SET profile_picture = ? WHERE id = ?";
                    $stmt = $db->prepare($query);
                    if ($stmt->execute([$fileName, $user_id])) {
                        ApiResponse::send([
                            "success" => true,
                            "message" => "Profile picture updated successfully",
                            "profile_picture_url" => '/blood_donor/uploads/profile_pictures/' . $fileName
                        ]);
                    } else {
                        unlink($targetPath); // Remove uploaded file if DB update fails
                        ApiResponse::error("Failed to update profile picture in database", 500);
                    }
                } else {
                    ApiResponse::error("Failed to upload file", 500);
                }
            }
            break;
            
        case 'PUT':
            $data = json_decode(file_get_contents("php://input"));
            
            $updateFields = [];
            $params = [];
            
            if (isset($data->name)) {
                $updateFields[] = "name = ?";
                $params[] = htmlspecialchars(strip_tags($data->name));
            }
            if (isset($data->phone)) {
                $updateFields[] = "phone = ?";
                $params[] = htmlspecialchars(strip_tags($data->phone));
            }
            if (isset($data->age)) {
                $updateFields[] = "age = ?";
                $params[] = (int)$data->age;
            }
            if (isset($data->gender)) {
                $updateFields[] = "gender = ?";
                $params[] = htmlspecialchars(strip_tags($data->gender));
            }
            if (isset($data->location)) {
                $updateFields[] = "location = ?";
                $params[] = htmlspecialchars(strip_tags($data->location));
            }
            if (isset($data->is_available)) {
                $updateFields[] = "is_available = ?";
                $params[] = (bool)$data->is_available;
            }
            if (isset($data->share_contact)) {
                $updateFields[] = "share_contact = ?";
                $params[] = (bool)$data->share_contact;
            }
            
            if (empty($updateFields)) {
                ApiResponse::error("No fields to update", 400);
            }
            
            $params[] = $user_id;
            
            $query = "UPDATE users SET " . implode(", ", $updateFields) . " WHERE id = ?";
            $stmt = $db->prepare($query);
            
            if ($stmt->execute($params)) {
                ApiResponse::send([
                    "success" => true,
                    "message" => "Profile updated successfully"
                ]);
            } else {
                ApiResponse::error("Failed to update profile", 500);
            }
            break;
    }
    
} catch (Exception $e) {
    error_log("Profile API Error: " . $e->getMessage());
    ApiResponse::error("Server error: " . $e->getMessage(), 500);
} 