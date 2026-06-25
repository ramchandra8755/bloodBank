<?php include 'includes/header.php'; ?>

<!-- Profile Content -->
 <!-- Profile Section -->
 <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0">My Profile</h3>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <div class="position-relative d-inline-block">
                                <img id="profileImage" src="assets/default-avatar.png" 
                                     class="rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                                <label for="profilePicture" class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle p-2" 
                                       style="cursor: pointer;">
                                    <i class="bi bi-camera-fill"></i>
                                </label>
                            </div>
                            <input type="file" id="profilePicture" accept="image/*" style="display: none;">
                        </div>
                        <form id="profileForm">
                            <div class="mb-3">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="name" name="name">
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="tel" class="form-control" id="phone" name="phone">
                            </div>
                            <div class="mb-3">
                                <label for="age" class="form-label">Age</label>
                                <input type="number" class="form-control" id="age" name="age">
                            </div>
                            <div class="mb-3">
                                <label for="gender" class="form-label">Gender</label>
                                <select class="form-select" id="gender" name="gender">
                                    <option value="">Select Gender</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="blood_group" class="form-label">Blood Group</label>
                                <input type="text" class="form-control" id="blood_group" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="location" class="form-label">Location</label>
                                <input type="text" class="form-control" id="location" name="location">
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="is_available" name="is_available">
                                <label class="form-check-label" for="is_available">Available for donation</label>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="share_contact" name="share_contact">
                                <label class="form-check-label" for="share_contact">Share contact information</label>
                            </div>
                            <button type="submit" class="btn btn-primary">Update Profile</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Check authentication
        function checkAuth() {
            if (!localStorage.getItem('token')) {
                window.location.href = 'login.html';
            }
        }

        // Fetch profile data
        async function fetchProfile() {
            try {
                const response = await fetch('api/profile.php', {
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('token')}`
                    }
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    // Update profile picture if exists
                    if (data.profile.profile_picture_url) {
                        document.getElementById('profileImage').src = data.profile.profile_picture_url;
                    }
                    
                    // Populate other form fields
                    const form = document.getElementById('profileForm');
                    for (const [key, value] of Object.entries(data.profile)) {
                        const input = form.elements[key];
                        if (input) {
                            if (input.type === 'checkbox') {
                                input.checked = value;
                            } else {
                                input.value = value;
                            }
                        }
                    }
                } else {
                    alert(data.error || 'Failed to load profile');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while loading profile');
            }
        }

        // Handle form submission
        document.getElementById('profileForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = {
                name: document.getElementById('name').value,
                phone: document.getElementById('phone').value,
                age: document.getElementById('age').value,
                gender: document.getElementById('gender').value,
                location: document.getElementById('location').value,
                is_available: document.getElementById('is_available').checked,
                share_contact: document.getElementById('share_contact').checked
            };
            
            try {
                const response = await fetch('api/profile.php', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${localStorage.getItem('token')}`
                    },
                    body: JSON.stringify(formData)
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    alert('Profile updated successfully');
                } else {
                    alert(data.error || 'Failed to update profile');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while updating profile');
            }
        });

        // Handle logout
        document.getElementById('logoutBtn').addEventListener('click', (e) => {
            e.preventDefault();
            localStorage.removeItem('token');
            window.location.href = 'login.html';
        });

        // Handle profile picture upload
        document.getElementById('profilePicture').addEventListener('change', async (e) => {
            const file = e.target.files[0];
            if (!file) return;
            
            const formData = new FormData();
            formData.append('profile_picture', file);
            
            try {
                const response = await fetch('api/profile.php', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('token')}`
                    },
                    body: formData
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    document.getElementById('profileImage').src = data.profile_picture_url;
                    alert('Profile picture updated successfully');
                } else {
                    alert(data.error || 'Failed to update profile picture');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while updating profile picture');
            }
        });

        // Initialize page
        checkAuth();
        fetchProfile();
    </script>

<?php include 'includes/footer.php'; ?> 