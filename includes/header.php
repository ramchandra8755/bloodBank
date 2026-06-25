<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Function to check if current page matches given path
function isCurrentPage($path) {
    $current_page = basename($_SERVER['PHP_SELF']);
    return $current_page === $path ? 'active' : '';
}

// Function to get active emergency requests count
function getActiveEmergencyCount($db) {
    try {
        $query = "SELECT COUNT(*) as count FROM emergency_requests WHERE status = 'Active'";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    } catch (Exception $e) {
        return 0;
    }
}

// Initialize database connection for emergency count
require_once __DIR__ . '/../config/Database.php';
$database = new Database();
$db = $database->getConnection();
$emergencyCount = $db ? getActiveEmergencyCount($db) : 0;

// Check if user is a blood bank admin
$is_bank_admin = false;
if (isset($_SESSION['user_id'])) {
    $query = "SELECT id FROM blood_bank_admins WHERE email = (SELECT email FROM users WHERE id = ?)";
    $stmt = $db->prepare($query);
    $stmt->execute([$_SESSION['user_id']]);
    $is_bank_admin = $stmt->fetch(PDO::FETCH_ASSOC) ? true : false;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood Donor Directory</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
        }

        .navbar {
            background: rgb(220 53 69);
            padding: 1rem 0;
        }

        .navbar-brand {
            font-weight: 600;
            font-size: 1.4rem;
        }

        .nav-link {
            font-weight: 500;
            padding: 0.5rem 1rem !important;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, 0.5);
            transform: translateY(-1px);
        }

        .nav-link.active {
            background: rgba(255, 255, 255, 0.15);
            font-weight: 600;
        }

        .emergency-badge {
            position: relative;
            padding: 0.5rem 1rem;
            border-radius: 8px;
        }

        .emergency-badge .badge {
            position: absolute;
            top: -8px;
            right: -8px;
            padding: 0.35em 0.65em;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 6px;
            background: #ffc107;
            color: #000;
            box-shadow: 0 2px 6px rgba(255, 193, 7, 0.3);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            backdrop-filter: blur(10px);
            background: rgba(255,255,255,0.95);
        }

        .btn-primary {
            background: #dc3545;
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-primary:hover {
            background: #c82333;
            transform: translateY(-2px);
        }

        .input-group {
            border-radius: 8px;
            background: #f8f9fa;
            border: 1px solid #e0e0e0;
            transition: all 0.2s ease;
        }

        .input-group:focus-within {
            border-color: #dc3545;
            background: white;
            box-shadow: 0 0 0 0.15rem rgba(220,53,69,0.15);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">Blood Donor Directory</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo isCurrentPage('index.php'); ?>" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo isCurrentPage('users.php'); ?>" href="users.php">Donors</a>
                    </li>
                    <li class="nav-item emergency-nav-item">
                        <a class="nav-link <?php echo isCurrentPage('emergency.php'); ?>" href="emergency.php">
                            <span class="emergency-badge">
                                Emergency Requests
                                <?php if ($emergencyCount > 0): ?>
                                <span class="badge"><?php echo $emergencyCount; ?></span>
                                <?php endif; ?>
                            </span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo isCurrentPage('profile.php'); ?>" href="profile.php">Profile</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo isCurrentPage('donation_history.php'); ?>" href="donation_history.php">
                            History
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo isCurrentPage('appointments.php'); ?>" href="appointments.php">
                            Appointments
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" id="logoutBtn">Logout</a>
                    </li>
                    <?php if ($is_bank_admin): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="blood-bank/dashboard.php">
                            <i class="bi bi-hospital"></i> Blood Bank Dashboard
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <script>
        // Check if user is authenticated
        function checkAuth() {
            if (!localStorage.getItem('token')) {
                window.location.href = 'login.html';
            }
        }

        // Handle logout
        document.getElementById('logoutBtn').addEventListener('click', (e) => {
            e.preventDefault();
            localStorage.removeItem('token');
            window.location.href = 'login.html';
        });

        // Check auth on page load (except for login and register pages)
        if (!window.location.pathname.includes('login.html') && 
            !window.location.pathname.includes('register.html')) {
            checkAuth();
        }

        // Update emergency count
        async function updateEmergencyCount() {
            try {
                const response = await fetch('api/emergency/count.php', {
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('token')}`
                    }
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    const badge = document.querySelector('.emergency-badge .badge');
                    const count = data.count;
                    
                    if (count > 0) {
                        if (!badge) {
                            const newBadge = document.createElement('span');
                            newBadge.className = 'badge';
                            newBadge.textContent = count;
                            document.querySelector('.emergency-badge').appendChild(newBadge);
                        } else {
                            badge.textContent = count;
                        }
                    } else if (badge) {
                        badge.remove();
                    }
                }
            } catch (error) {
                console.error('Error updating emergency count:', error);
            }
        }

        // Update count every 30 seconds
        setInterval(updateEmergencyCount, 30000);
    </script>