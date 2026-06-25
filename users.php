<?php include 'includes/header.php'; ?>

<!-- Add this after the navigation and before the filters section -->
<div class="bg-light py-4 mb-3">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-3 mb-3 mb-md-0">
                    <div class="card border-0 bg-transparent">
                        <div class="card-body">
                            <h3 class="text-danger mb-0" id="totalDonors">0</h3>
                            <p class="text-muted mb-0">Total Donors</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3 mb-md-0">
                    <div class="card border-0 bg-transparent">
                        <div class="card-body">
                            <h3 class="text-success mb-0" id="availableDonors">0</h3>
                            <p class="text-muted mb-0">Available Donors</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3 mb-md-0">
                    <div class="card border-0 bg-transparent">
                        <div class="card-body">
                            <h3 class="text-primary mb-0" id="totalLocations">0</h3>
                            <p class="text-muted mb-0">Locations</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 bg-transparent">
                        <div class="card-body">
                            <h3 class="text-info mb-0" id="bloodGroups">0</h3>
                            <p class="text-muted mb-0">Blood Groups</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filters">
        <div class="container">
            <div class="row g-3">
                <div class="col-md-3">
                    <select class="form-select" id="bloodGroupFilter">
                        <option value="">All Blood Groups</option>
                        <option value="A+">A+</option>
                        <option value="A-">A-</option>
                        <option value="B+">B+</option>
                        <option value="B-">B-</option>
                        <option value="AB+">AB+</option>
                        <option value="AB-">AB-</option>
                        <option value="O+">O+</option>
                        <option value="O-">O-</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="text" class="form-control" id="locationFilter" placeholder="Location">
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="availabilityFilter">
                        <option value="">All Availability</option>
                        <option value="1">Available</option>
                        <option value="0">Not Available</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-primary w-100" id="applyFilters">Apply Filters</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Results -->
    <div class="container mt-4">
        <div class="row" id="usersList"></div>
        <div id="loading">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentPage = 1;
        let loading = false;
        let hasMore = true;
        
        // Check authentication
        function checkAuth() {
            if (!localStorage.getItem('token')) {
                window.location.href = 'login.html';
            }
        }

        // Load users
        async function loadUsers(page = 1, reset = false) {
            if (loading || (!hasMore && !reset)) return;
            
            loading = true;
            document.getElementById('loading').style.display = 'block';
            
            const bloodGroup = document.getElementById('bloodGroupFilter').value;
            const location = document.getElementById('locationFilter').value;
            const availability = document.getElementById('availabilityFilter').value;
            
            try {
                const params = new URLSearchParams({
                    page: page,
                    blood_group: bloodGroup,
                    location: location,
                    availability: availability
                });
                
                const response = await fetch(`api/users.php?${params}`, {
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('token')}`
                    }
                });
                
                const data = await response.json();
                
                if (response.ok) {
                    if (reset) {
                        document.getElementById('usersList').innerHTML = '';
                        currentPage = 1;
                        hasMore = true;
                    }
                    
                    displayUsers(data.users);
                    initTooltips();
                    hasMore = currentPage < data.total_pages;
                    
                } else {
                    alert(data.error || 'Failed to load users');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while loading users');
            } finally {
                loading = false;
                document.getElementById('loading').style.display = 'none';
            }
        }

        // Display users
        function displayUsers(users) {
            const container = document.getElementById('usersList');
            
            if (users.length === 0) {
                container.innerHTML = '<div class="alert alert-info">No donors found.</div>';
                return;
            }
            
            container.innerHTML = users.map(user => `
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card donor-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h5 class="card-title mb-0">
                                    <span class="badge bg-danger me-2">${user.blood_group}</span>
                                    ${user.name}
                                </h5>
                                ${getDonationStatusBadge(user)}
                            </div>
                            <p class="card-text">
                                <i class="bi bi-geo-alt-fill text-danger"></i> ${user.location}<br>
                                ${user.share_contact ? `
                                    <i class="bi bi-telephone-fill text-success"></i> ${user.phone}<br>
                                    <i class="bi bi-envelope-fill text-primary"></i> ${user.email}
                                ` : '<small class="text-muted">Contact details hidden</small>'}
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge ${user.is_available ? 'bg-success' : 'bg-secondary'}">
                                    ${user.is_available ? 'Available' : 'Not Available'}
                                </span>
                                ${getNextDonationInfo(user)}
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');
        }
        function getDonationStatusBadge(user) {
            if (user.donation_status === 'waiting') {
                return `
                    <span class="badge bg-warning" data-bs-toggle="tooltip" 
                        title="Next eligible on ${formatDate(user.next_eligible_date)}">
                        <i class="bi bi-hourglass-split"></i> Cooling Period
                    </span>
                `;
            } else {
                return `
                    <span class="badge bg-success">
                        <i class="bi bi-droplet-fill"></i> Ready to Donate
                    </span>
                `;
            }
        }
        function getNextDonationInfo(user) {
            if (user.donation_status === 'waiting') {
                const daysLeft = getDaysUntilEligible(user.next_eligible_date);
                return `
                    <small class="text-muted">
                        <i class="bi bi-calendar-event"></i>
                        Available in ${daysLeft} days
                    </small>
                `;
            }
            return '';
        }

        function getDaysUntilEligible(nextDate) {
            if (!nextDate) return 0;
            const today = new Date();
            const eligible = new Date(nextDate);
            const diffTime = eligible - today;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            return diffDays > 0 ? diffDays : 0;
        }

        function formatDate(dateString) {
            if (!dateString) return '';
            const options = { year: 'numeric', month: 'short', day: 'numeric' };
            return new Date(dateString).toLocaleDateString(undefined, options);
        }
        // Initialize tooltips after displaying users
        function initTooltips() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }

        // Infinite scroll handler
        function handleScroll() {
            if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 500) {
                currentPage++;
                loadUsers(currentPage);
            }
        }

        // Filter handler
        document.getElementById('applyFilters').addEventListener('click', () => {
            loadUsers(1, true);
        });

        // Logout handler
        document.getElementById('logoutBtn').addEventListener('click', (e) => {
            e.preventDefault();
            localStorage.removeItem('token');
            window.location.href = 'login.html';
        });

        // Initialize
        checkAuth();
        loadUsers(1);
        loadStatistics();
        window.addEventListener('scroll', handleScroll);

        // Update the loadStatistics function
        async function loadStatistics() {
            try {
                const response = await fetch('api/statistics.php', {
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('token')}`
                    }
                });
                
                const data = await response.json();
                console.log('Statistics data:', data); // Debug log
                
                if (response.ok && data.success) {
                    const stats = data.statistics;
                    
                    // Directly update the values first (fallback)
                    document.getElementById('totalDonors').textContent = stats.total_donors;
                    document.getElementById('availableDonors').textContent = stats.available_donors;
                    document.getElementById('totalLocations').textContent = stats.total_locations;
                    document.getElementById('bloodGroups').textContent = stats.blood_groups.length;
                    
                    // Then try to animate them
                    try {
                        animateNumber('totalDonors', stats.total_donors);
                        animateNumber('availableDonors', stats.available_donors);
                        animateNumber('totalLocations', stats.total_locations);
                        animateNumber('bloodGroups', stats.blood_groups.length);
                        
                        // Add blood group distribution tooltip
                        const bloodGroupsElement = document.getElementById('bloodGroups');
                        if (stats.blood_groups.length > 0) {
                            const distribution = stats.blood_groups
                                .map(bg => `${bg.blood_group}: ${bg.count} (${bg.percentage}%)`)
                                .join('\n');
                            bloodGroupsElement.setAttribute('title', distribution);
                            bloodGroupsElement.setAttribute('data-bs-toggle', 'tooltip');
                            bloodGroupsElement.setAttribute('data-bs-placement', 'bottom');
                            
                            // Initialize tooltip
                            new bootstrap.Tooltip(bloodGroupsElement);
                        }
                    } catch (animationError) {
                        console.error('Animation error:', animationError);
                    }
                    
                } else {
                    console.error('Failed to load statistics:', data.error);
                }
            } catch (error) {
                console.error('Error loading statistics:', error);
            }
        }

        // Update the animateNumber function
        function animateNumber(elementId, finalNumber) {
            const element = document.getElementById(elementId);
            if (!element) return;
            
            // Convert to number and ensure it's not NaN
            finalNumber = parseInt(finalNumber) || 0;
            const startNumber = parseInt(element.textContent) || 0;
            
            // Don't animate if the numbers are the same
            if (startNumber === finalNumber) return;
            
            const duration = 1000; // Animation duration in milliseconds
            const steps = 60; // Number of steps in animation
            const increment = (finalNumber - startNumber) / steps;
            let currentNumber = startNumber;
            let currentStep = 0;
            
            const animate = () => {
                currentStep++;
                currentNumber += increment;
                
                if (currentStep >= steps) {
                    element.textContent = finalNumber;
                } else {
                    element.textContent = Math.round(currentNumber);
                    requestAnimationFrame(animate);
                }
            };
            
            animate();
        }

        // Make sure statistics are loaded after DOM is ready
        document.addEventListener('DOMContentLoaded', () => {
            checkAuth();
            loadStatistics();
            loadUsers(1);
            window.addEventListener('scroll', handleScroll);
        });
    </script>

<?php include 'includes/footer.php'; ?> 