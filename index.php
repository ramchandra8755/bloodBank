<?php include 'includes/header.php'; ?>

<!-- Hero Section -->
<!-- <div class="bg-danger text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="display-4 fw-bold mb-4">Save Lives Through Blood Donation</h1>
                <p class="lead mb-4">Connect with blood donors in your area and help save lives. Every drop counts!</p>
                <div class="d-flex gap-3">
                    <a href="#search" class="btn btn-light btn-lg px-4">Find Donors</a>
                    <a href="register.php" class="btn btn-outline-light btn-lg px-4">Become a Donor</a>
                </div>
            </div>
            <div class="col-md-6 text-center">
                <img src="assets/blood-donation.svg" alt="Blood Donation" class="img-fluid" style="max-width: 400px;">
            </div>
        </div>
    </div>
</div> -->

<!-- Stats Section -->
<!-- <div class="bg-light py-5">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-3">
                <div class="display-4 fw-bold text-danger mb-2">1000+</div>
                <p class="text-muted">Registered Donors</p>
            </div>
            <div class="col-md-3">
                <div class="display-4 fw-bold text-danger mb-2">500+</div>
                <p class="text-muted">Successful Donations</p>
            </div>
            <div class="col-md-3">
                <div class="display-4 fw-bold text-danger mb-2">50+</div>
                <p class="text-muted">Partner Hospitals</p>
            </div>
            <div class="col-md-3">
                <div class="display-4 fw-bold text-danger mb-2">24/7</div>
                <p class="text-muted">Emergency Support</p>
            </div>
        </div>
    </div>
</div> -->
<!-- <div class="col-md-4 mb-4">
    <div class="card h-100">
        <div class="card-body">
            <h5 class="card-title">
                <i class="bi bi-trophy text-warning"></i> My Rewards
            </h5>
            <div class="text-center my-3">
                <div class="level-badge" id="rewardLevelBadge">
                    <span id="rewardLevel">-</span>
                </div>
                <p class="text-muted mb-2">Current Level</p>
                <div class="h4" id="rewardPoints">0 pts</div>
                <div class="progress mb-2" style="height: 10px;">
                    <div class="progress-bar bg-success" id="rewardProgress" role="progressbar" style="width: 0%"></div>
                </div>
                <small class="text-muted" id="nextLevelInfo">Loading...</small>
            </div>
            <div class="text-center mt-3">
                <a href="rewards.php" class="btn btn-primary">
                    View My Rewards
                </a>
            </div>
        </div>
    </div>
</div> -->


<div class="bg-danger text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="display-4 fw-bold mb-4">Save Lives Through Blood Donation</h1>
                <p class="lead mb-4">Connect with blood donors in your area and help save lives. Every drop counts!</p>
            </div>
            <div class="col-md-6 text-center">
                <img src="assets/banner.svg" alt="Blood Donation" class="img-fluid" style="max-width: 400px;">
            </div>
        </div>
    </div>
</div>

<!-- Search Section -->
<div class="container py-5" id="search">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-lg border-0 rounded-lg position-relative" style="margin-top: -80px; background: rgba(255,255,255,0.95);">
                <div class="card-body p-5">
                    <h4 class="card-title text-center mb-4 fw-bold">Find Blood Donors</h4>
                    <form id="searchForm" class="row g-4">
                        <div class="col-md-5">
                            <select class="form-select form-select-lg shadow-sm border-0" id="bloodGroup">
                                <option value="">All Blood Groups</option>
                                <option value="A+">A Positive (A+)</option>
                                <option value="A-">A Negative (A-)</option>
                                <option value="B+">B Positive (B+)</option>
                                <option value="B-">B Negative (B-)</option>
                                <option value="AB+">AB Positive (AB+)</option>
                                <option value="AB-">AB Negative (AB-)</option>
                                <option value="O+">O Positive (O+)</option>
                                <option value="O-">O Negative (O-)</option>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text border-0 bg-white">
                                    <i class="bi bi-geo-alt-fill text-danger"></i>
                                </span>
                                <input type="text" class="form-control shadow-sm border-0" id="location" placeholder="Enter location">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-danger btn-lg w-100 shadow-sm">
                                <i class="bi bi-search me-2"></i>Search
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Results Section -->
    <div class="row mt-5" id="searchResults">
        <!-- Results will be dynamically inserted here -->
    </div>
</div>
<!-- Why Donate Section -->
<div class="container py-5">
    <h2 class="text-center mb-5">Why Should You Donate Blood?</h2>
    <div class="row g-4">
        <div class="col-md-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-heart-pulse text-danger display-4 mb-3"></i>
                    <h5 class="card-title">Save Lives</h5>
                    <p class="card-text">One donation can save up to three lives</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-clipboard2-pulse text-danger display-4 mb-3"></i>
                    <h5 class="card-title">Health Check</h5>
                    <p class="card-text">Free mini health screening with each donation</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-people-fill text-danger display-4 mb-3"></i>
                    <h5 class="card-title">Community</h5>
                    <p class="card-text">Join a community of lifesavers</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-award text-danger display-4 mb-3"></i>
                    <h5 class="card-title">Recognition</h5>
                    <p class="card-text">Earn badges and certificates</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    async function loadRewardsPreview() {
        try {
            const response = await fetch('api/rewards/manage.php', {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`
                }
            });
            
            const data = await response.json();
            
            if (response.ok && data.rewards) {
                // Update level badge
                const levelBadge = document.getElementById('rewardLevelBadge');
                const level = data.rewards.level || 'Bronze';
                levelBadge.className = `level-badge ${level.toLowerCase()}`;
                document.getElementById('rewardLevel').textContent = level;
                
                // Update points
                document.getElementById('rewardPoints').textContent = 
                    `${data.rewards.points} pts`;
                
                // Update progress bar and next level info
                if (data.rewards.next_level) {
                    const progress = (data.rewards.points / (data.rewards.next_level.points_needed + data.rewards.points)) * 100;
                    document.getElementById('rewardProgress').style.width = `${progress}%`;
                    document.getElementById('nextLevelInfo').textContent = 
                        `${data.rewards.next_level.points_needed} points to ${data.rewards.next_level.next_level}`;
                } else {
                    document.getElementById('rewardProgress').style.width = '100%';
                    document.getElementById('nextLevelInfo').textContent = 'Maximum level achieved!';
                }
            }
        } catch (error) {
            console.error('Error loading rewards:', error);
            document.getElementById('rewardLevel').textContent = '-';
            document.getElementById('nextLevelInfo').textContent = 'Error loading rewards';
        }
    }

    // Check authentication
    function checkAuth() {
        const token = localStorage.getItem('token');
        if (!token) {
            window.location.href = 'login.html';
        }
    }

    // Search functionality
    document.getElementById('searchForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const bloodGroup = document.getElementById('bloodGroup').value;
        const location = document.getElementById('location').value;
        
        try {
            const searchParams = new URLSearchParams();
            if (bloodGroup) searchParams.append('blood_group', bloodGroup);
            if (location) searchParams.append('location', location);
            
            const response = await fetch(`api/search.php?${searchParams.toString()}`, {
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('token')}`
                }
            });
            
            const data = await response.json();
            
            if (response.ok) {
                displayResults(data.donors);
            } else {
                showAlert('error', data.error || 'Search failed!');
            }
        } catch (error) {
            console.error('Error:', error);
            showAlert('error', 'An error occurred during search');
        }
    });

    function showAlert(type, message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type === 'error' ? 'danger' : 'info'} alert-dismissible fade show animate__animated animate__fadeIn`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.querySelector('.container').insertBefore(alertDiv, document.getElementById('searchResults'));
        setTimeout(() => alertDiv.remove(), 5000);
    }

    // Display search results
    function displayResults(donors) {
        const resultsDiv = document.getElementById('searchResults');
        resultsDiv.innerHTML = '';
        
        donors.forEach(donor => {
            const defaultAvatar = 'assets/default-avatar.png';
            const profileImage = donor.profile_picture_url || defaultAvatar;
            
            resultsDiv.innerHTML += `
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 border-0 shadow-sm hover-shadow transition-all">
                        <div class="card-body p-4">
                            <div class="text-center mb-4">
                                <div class="position-relative d-inline-block">
                                    <img src="${profileImage}" 
                                         alt="${donor.name}'s profile"
                                         class="rounded-circle mb-3 shadow" 
                                         style="width: 120px; height: 120px; object-fit: cover;"
                                         onerror="this.src='${defaultAvatar}'">
                                    <span class="position-absolute bottom-0 end-0 p-2 rounded-circle 
                                        ${donor.is_available ? 'bg-success' : 'bg-secondary'} shadow-sm"
                                        style="width: 20px; height: 20px;"></span>
                                </div>
                                <h5 class="card-title mb-1 fw-bold">${donor.name}</h5>
                                <span class="badge bg-danger px-3 py-2 rounded-pill fs-6 mb-3 shadow-sm">
                                    ${donor.blood_group}
                                </span>
                            </div>
                            
                            <div class="donor-info bg-light p-3 rounded-3">
                                <div class="d-flex align-items-center mb-3">
                                    <i class="bi bi-geo-alt-fill text-danger me-2"></i>
                                    <span class="text-muted">${donor.location}</span>
                                </div>
                                
                                ${donor.share_contact ? `
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="bi bi-telephone-fill text-success me-2"></i>
                                        <span class="text-muted">${donor.phone}</span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-envelope-fill text-primary me-2"></i>
                                        <span class="text-muted">${donor.email}</span>
                                    </div>
                                ` : `
                                    <div class="alert alert-light mb-0 text-center border">
                                        <i class="bi bi-shield-lock-fill text-secondary me-2"></i>
                                        Contact information private
                                    </div>
                                `}
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });

        if (donors.length === 0) {
            resultsDiv.innerHTML = `
                <div class="col-12">
                    <div class="alert alert-info text-center p-4 shadow-sm">
                        <i class="bi bi-info-circle-fill me-2"></i>
                        No donors found matching your criteria
                    </div>
                </div>
            `;
        }
    }

    // Logout functionality
    document.getElementById('logoutBtn').addEventListener('click', (e) => {
        e.preventDefault();
        localStorage.removeItem('token');
        window.location.href = 'login.html';
    });

    // Check authentication on page load
    checkAuth();
    document.addEventListener('DOMContentLoaded', loadRewardsPreview);
</script>

<style>
/* Add these styles if not already in your CSS */
.level-badge {
    font-size: 24px;
    padding: 20px;
    border-radius: 50%;
    width: 80px;
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 15px;
}

.bronze { background: #cd7f32; color: white; }
.silver { background: #c0c0c0; color: white; }
.gold { background: #ffd700; color: black; }
.platinum { background: #e5e4e2; color: black; }
.diamond { background: #b9f2ff; color: black; }

#rewardProgress {
    transition: width 0.3s ease;
}
</style>

<?php include 'includes/footer.php'; ?>