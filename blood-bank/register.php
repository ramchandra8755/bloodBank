<?php
session_start();
if (isset($_SESSION['bank_admin_id'])) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood Bank Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #dc3545 0%, #ff6b6b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            backdrop-filter: blur(10px);
            background: rgba(255,255,255,0.95);
        }
        .card-header {
            background: none;
            border: none;
            padding-top: 30px;
        }
        .form-control {
            border-radius: 8px;
            padding: 12px 15px;
            border: 1px solid #e0e0e0;
            transition: all 0.2s ease;
            font-size: 0.95rem;
        }
        .form-control:focus {
            border-color: #dc3545;
            box-shadow: none;
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
        .blood-icon {
            color: #dc3545;
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        .tagline {
            color: #6c757d;
            font-size: 1.1rem;
            margin-bottom: 2rem;
        }
        a {
            color: #dc3545;
            text-decoration: none;
            transition: all 0.3s;
        }
        a:hover {
            color: #c82333;
        }
        .alert {
            border-radius: 10px;
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
        .input-group-text {
            border: none;
            color: #6c757d;
            padding-left: 15px;
        }
        .input-group .form-control {
            border: none;
            background: transparent;
            padding-left: 5px;
        }
        .form-label {
            font-weight: 500;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
            color: #495057;
        }
        .section-title {
            color: #dc3545;
            font-size: 1.2rem;
            font-weight: 600;
            margin: 1.5rem 0 1rem;
        }
        .operating-hours-row {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 0.5rem !important;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center mt-5 mb-5">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header text-center">
                        <i class="bi bi-hospital blood-icon"></i>
                        <h2 class="mb-3">Blood Bank Registration</h2>
                        <p class="tagline">Join our network of blood banks and help save lives</p>
                    </div>
                    <div class="card-body p-4">
                        <div class="alert alert-danger d-none" id="errorMessage"></div>
                        <form id="registrationForm">
                            <div class="section-title">Blood Bank Information</div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="bankName" class="form-label">Blood Bank Name</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-transparent border-end-0">
                                            <i class="bi bi-building"></i>
                                        </span>
                                        <input type="text" class="form-control border-start-0" id="bankName" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="contactNumber" class="form-label">Contact Number</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-transparent border-end-0">
                                            <i class="bi bi-telephone"></i>
                                        </span>
                                        <input type="tel" class="form-control border-start-0" id="contactNumber" required>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-transparent border-end-0">
                                        <i class="bi bi-geo-alt"></i>
                                    </span>
                                    <textarea class="form-control border-start-0" id="address" rows="2" required></textarea>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="latitude" class="form-label">Latitude</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-transparent border-end-0">
                                            <i class="bi bi-geo"></i>
                                        </span>
                                        <input type="text" class="form-control border-start-0" id="latitude" step="any" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="longitude" class="form-label">Longitude</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-transparent border-end-0">
                                            <i class="bi bi-geo"></i>
                                        </span>
                                        <input type="text" class="form-control border-start-0" id="longitude" step="any" required>
                                    </div>
                                </div>
                            </div>

                            <div class="section-title">Operating Hours</div>
                            <div id="operatingHours">
                                <?php
                                $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                                foreach ($days as $index => $day) {
                                    echo "
                                    <div class='row mb-3 operating-hours-row'>
                                        <div class='col-md-3'>
                                            <div class='form-check'>
                                                <input class='form-check-input day-checkbox' type='checkbox' 
                                                       id='day{$index}' data-day='{$index}'>
                                                <label class='form-check-label' for='day{$index}'>
                                                    {$day}
                                                </label>
                                            </div>
                                        </div>
                                        <div class='col-md-4'>
                                            <input type='time' class='form-control start-time' 
                                                   id='start{$index}' disabled>
                                        </div>
                                        <div class='col-md-4'>
                                            <input type='time' class='form-control end-time' 
                                                   id='end{$index}' disabled>
                                        </div>
                                    </div>";
                                }
                                ?>
                            </div>

                            <div class="section-title">Administrator Account</div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="adminName" class="form-label">Admin Name</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-transparent border-end-0">
                                            <i class="bi bi-person"></i>
                                        </span>
                                        <input type="text" class="form-control border-start-0" id="adminName" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="adminEmail" class="form-label">Email</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-transparent border-end-0">
                                            <i class="bi bi-envelope"></i>
                                        </span>
                                        <input type="email" class="form-control border-start-0" id="adminEmail" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="adminPassword" class="form-label">Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-transparent border-end-0">
                                            <i class="bi bi-lock"></i>
                                        </span>
                                        <input type="password" class="form-control border-start-0" id="adminPassword" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="confirmPassword" class="form-label">Confirm Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-transparent border-end-0">
                                            <i class="bi bi-lock-fill"></i>
                                        </span>
                                        <input type="password" class="form-control border-start-0" id="confirmPassword" required>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 mb-3">
                                <i class="bi bi-person-plus me-2"></i>Register Blood Bank
                            </button>
                            <div class="text-center">
                                <a href="login.php" class="d-block mb-2">
                                    <i class="bi bi-box-arrow-in-right me-1"></i>
                                    Already registered? Login here
                                </a>
                                <small class="text-muted">Join us in our mission to save lives</small>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Handle operating hours checkboxes
        document.querySelectorAll('.day-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const dayIndex = this.dataset.day;
                const startTime = document.getElementById(`start${dayIndex}`);
                const endTime = document.getElementById(`end${dayIndex}`);
                
                startTime.disabled = !this.checked;
                endTime.disabled = !this.checked;
                
                if (this.checked) {
                    startTime.required = true;
                    endTime.required = true;
                    startTime.value = '09:00';
                    endTime.value = '17:00';
                } else {
                    startTime.required = false;
                    endTime.required = false;
                    startTime.value = '';
                    endTime.value = '';
                }
            });
        });

        // Form submission
        document.getElementById('registrationForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            // Validate passwords match
            const password = document.getElementById('adminPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            
            if (password !== confirmPassword) {
                showError('Passwords do not match');
                return;
            }
            
            // Collect operating hours
            const operatingHours = [];
            document.querySelectorAll('.operating-hours-row').forEach((row, index) => {
                const checkbox = row.querySelector('.day-checkbox');
                if (checkbox.checked) {
                    operatingHours.push({
                        day_of_week: parseInt(checkbox.dataset.day) + 1, // 1 (Monday) to 7 (Sunday)
                        start_time: row.querySelector('.start-time').value,
                        end_time: row.querySelector('.end-time').value,
                        slot_duration: 30, // Default 30-minute slots
                        max_appointments: 2 // Default 2 appointments per slot
                    });
                }
            });
            
            if (operatingHours.length === 0) {
                showError('Please select at least one operating day');
                return;
            }
            
            const formData = {
                blood_bank: {
                    name: document.getElementById('bankName').value,
                    address: document.getElementById('address').value,
                    contact_number: document.getElementById('contactNumber').value,
                    latitude: parseFloat(document.getElementById('latitude').value),
                    longitude: parseFloat(document.getElementById('longitude').value)
                },
                operating_hours: operatingHours,
                admin: {
                    name: document.getElementById('adminName').value,
                    email: document.getElementById('adminEmail').value.toLowerCase(),
                    password: password
                }
            };
            
            try {
                const response = await fetch('../api/blood-bank/auth/register.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    window.location.href = 'login.php?registered=1';
                } else {
                    showError(data.error);
                }
            } catch (error) {
                console.error('Error:', error);
                showError('An error occurred during registration');
            }
        });

        function showError(message) {
            const errorDiv = document.getElementById('errorMessage');
            errorDiv.textContent = message;
            errorDiv.classList.remove('d-none');
        }
    </script>
</body>
</html>