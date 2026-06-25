<?php
require_once 'includes/header.php';
?>

<div class="container py-5">
    <!-- Info Section -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h4 class="card-title mb-4">
                        <i class="bi bi-info-circle-fill text-primary"></i> 
                        How Rewards Work
                    </h4>
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="mb-3">Points System</h5>
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <i class="bi bi-check-circle-fill text-success"></i>
                                    100 points for each donation
                                </li>
                                <li class="mb-2">
                                    <i class="bi bi-check-circle-fill text-success"></i>
                                    50 bonus points per unit donated
                                </li>
                                <li class="mb-2">
                                    <i class="bi bi-check-circle-fill text-success"></i>
                                    200 streak points for regular donations
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5 class="mb-3">Donor Levels</h5>
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <i class="bi bi-trophy-fill text-bronze"></i>
                                    Bronze: 0-499 points
                                </li>
                                <li class="mb-2">
                                    <i class="bi bi-trophy-fill text-silver"></i>
                                    Silver: 500-999 points
                                </li>
                                <li class="mb-2">
                                    <i class="bi bi-trophy-fill text-gold"></i>
                                    Gold: 1,000-1,999 points
                                </li>
                                <li class="mb-2">
                                    <i class="bi bi-trophy-fill text-platinum"></i>
                                    Platinum: 2,000-4,999 points
                                </li>
                                <li class="mb-2">
                                    <i class="bi bi-trophy-fill text-diamond"></i>
                                    Diamond: 5,000+ points
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Spinner -->
    <div id="rewardsLoading" class="text-center py-5" style="display: none;">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
    
    <!-- Rest of your existing content -->
    <div id="rewardsContent">
        <!-- Stats Cards Row -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="display-6 text-primary mb-2">
                            <span id="totalPoints">0</span>
                        </div>
                        <p class="text-muted mb-0">Total Points</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="display-6 text-success mb-2" id="totalDonations">0</div>
                        <p class="text-muted mb-0">Total Donations</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="display-6 text-warning mb-2" id="badgeCount">0</div>
                        <p class="text-muted mb-0">Badges Earned</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="display-6 text-info mb-2" id="streakPoints">0</div>
                        <p class="text-muted mb-0">Streak Points</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Level and Badges Section -->
        <div class="row">
            <!-- Level Card -->
            <div class="col-md-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body text-center p-4">
                        <div class="level-badge-wrapper mb-4">
                            <div class="level-badge" id="levelBadge">
                                <span id="donorLevel">-</span>
                            </div>
                            <div class="level-glow"></div>
                        </div>
                        <h4 class="card-title mb-4">Current Level</h4>
                        
                        <div class="progress mb-3" style="height: 10px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                 id="levelProgress" role="progressbar" style="width: 0%"></div>
                        </div>
                        
                        <p class="text-muted" id="nextLevelText"></p>
                    </div>
                </div>
            </div>
            
            <!-- Badges Card -->
            <div class="col-md-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h4 class="card-title mb-4">
                            <i class="bi bi-award"></i> 
                            Your Achievements
                        </h4>
                        
                        <ul class="nav nav-pills mb-4" id="badgeTabs">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#allBadges">All Badges</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#achievementBadges">Achievements</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#rankBadges">Ranks</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#specialBadges">Special</a>
                            </li>
                        </ul>
                        
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="allBadges"></div>
                            <div class="tab-pane fade" id="achievementBadges"></div>
                            <div class="tab-pane fade" id="rankBadges"></div>
                            <div class="tab-pane fade" id="specialBadges"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add this after your existing rewards content -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h4 class="card-title d-flex justify-content-between align-items-center mb-4">
                        <span>
                            <i class="bi bi-clock-history text-primary"></i> 
                            Points History
                        </span>
                    </h4>

                    <div class="points-history-timeline" id="pointsHistory">
                        <div class="text-center py-3" id="historyLoading">
                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Modern styling for rewards page */
.level-badge-wrapper {
    position: relative;
    width: 150px;
    height: 150px;
    margin: 0 auto;
}

.level-badge {
    position: relative;
    z-index: 2;
    font-size: 28px;
    font-weight: bold;
    padding: 20px;
    border-radius: 50%;
    width: 150px;
    height: 150px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.level-glow {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border-radius: 50%;
    z-index: 1;
    filter: blur(20px);
    opacity: 0.5;
}

.bronze { 
    background: linear-gradient(145deg, #cd7f32, #e8a15d);
    color: white;
}
.bronze + .level-glow { background: #cd7f32; }

.silver { 
    background: linear-gradient(145deg, #c0c0c0, #e3e3e3);
    color: white;
}
.silver + .level-glow { background: #c0c0c0; }

.gold { 
    background: linear-gradient(145deg, #ffd700, #ffed4a);
    color: black;
}
.gold + .level-glow { background: #ffd700; }

.platinum { 
    background: linear-gradient(145deg, #e5e4e2, #ffffff);
    color: black;
}
.platinum + .level-glow { background: #e5e4e2; }

.diamond { 
    background: linear-gradient(145deg, #b9f2ff, #e3faff);
    color: black;
}
.diamond + .level-glow { background: #b9f2ff; }

.badge-item {
    background: white;
    border-radius: 15px;
    padding: 25px;
    margin: 10px;
    display: inline-flex;
    flex-direction: column;
    align-items: center;
    width: 200px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    position: relative;
    overflow: hidden;
}

.badge-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.badge-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(to right, #2193b0, #6dd5ed);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.badge-item:hover::before {
    opacity: 1;
}

.badge-icon {
    width: 80px;
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: rgba(0,0,0,0.03);
    transition: transform 0.3s ease;
}

.badge-item:hover .badge-icon {
    transform: scale(1.1);
}

.badge-item h5 {
    font-size: 1.1rem;
    margin: 0;
    color: #333;
    font-weight: 600;
}

.badge-item p {
    font-size: 0.9rem;
    color: #666;
    margin: 8px 0 0;
    text-align: center;
}

.progress {
    background-color: #f0f0f0;
    border-radius: 10px;
    overflow: hidden;
}

.progress-bar {
    background: linear-gradient(45deg, #2193b0, #6dd5ed);
    transition: width 0.6s ease;
}

.nav-pills .nav-link {
    color: #666;
    border-radius: 20px;
    padding: 8px 20px;
    margin: 0 5px;
    transition: all 0.3s ease;
}

.nav-pills .nav-link.active {
    background: linear-gradient(45deg, #2193b0, #6dd5ed);
    color: white;
    box-shadow: 0 2px 10px rgba(33, 147, 176, 0.3);
}

/* Card hover effects */
.card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
}

/* Stats cards */
.display-6 {
    font-weight: 600;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .badge-item {
        width: calc(50% - 20px);
    }
}

@media (max-width: 576px) {
    .badge-item {
        width: 100%;
    }
    
    .level-badge {
        width: 120px;
        height: 120px;
        font-size: 24px;
    }
}

/* Add these new styles to your existing CSS */
.text-bronze { color: #cd7f32; }
.text-silver { color: #c0c0c0; }
.text-gold { color: #ffd700; }
.text-platinum { color: #e5e4e2; }
.text-diamond { color: #b9f2ff; }

.list-unstyled li {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px;
    border-radius: 8px;
    transition: background-color 0.2s ease;
}

.list-unstyled li:hover {
    background-color: rgba(0,0,0,0.02);
}

/* Add shimmer effect for special badges */
@keyframes shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

.badge-item.special::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(
        90deg,
        rgba(255,255,255,0) 0%,
        rgba(255,255,255,0.2) 50%,
        rgba(255,255,255,0) 100%
    );
    animation: shimmer 2s infinite;
    pointer-events: none;
}

/* Points History Timeline Styles */
.points-history-timeline {
    position: relative;
    padding: 20px 0;
}

.points-history-timeline::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.history-item {
    position: relative;
    padding-left: 30px;
    margin-bottom: 25px;
    animation: fadeIn 0.5s ease;
}

.history-item::before {
    content: '';
    position: absolute;
    left: -5px;
    top: 0;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #fff;
    border: 2px solid #2193b0;
}

.history-item.donation::before { border-color: #28a745; }
.history-item.streak::before { border-color: #ffc107; }
.history-item.achievement::before { border-color: #17a2b8; }

.history-item .date {
    font-size: 0.85rem;
    color: #6c757d;
    margin-bottom: 5px;
}

.history-item .points {
    font-weight: 600;
    color: #28a745;
}

.history-item .description {
    color: #495057;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Empty state styling */
.empty-state {
    text-align: center;
    padding: 40px 20px;
    color: #6c757d;
}

.empty-state i {
    font-size: 3rem;
    margin-bottom: 1rem;
}

/* Filter active state */
.dropdown-item.active {
    background-color: #2193b0;
    color: white;
}
</style>

<script>
async function loadRewards() {
    try {
        const loadingEl = document.getElementById('rewardsLoading');
        const contentEl = document.getElementById('rewardsContent');
        
        // Show loading, hide content
        loadingEl.style.display = 'block';
        contentEl.style.display = 'none';
        
        const response = await fetch('api/rewards/manage.php', {
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('token')}`
            }
        });
        
        const data = await response.json();
        
        if (!response.ok) {
            throw new Error(data.error || 'Failed to load rewards');
        }
        
        if (data.rewards) {
            updateRewardsDisplay(data.rewards);
            // Show content, hide loading
            contentEl.style.display = 'block';
            loadingEl.style.display = 'none';
        } else {
            throw new Error('Invalid rewards data received');
        }
        
    } catch (error) {
        console.error('Error loading rewards:', error);
        showAlert('error', 'Failed to load rewards. Please try again later.');
        // Hide loading on error
        document.getElementById('rewardsLoading').style.display = 'none';
    }
}

function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'error' ? 'danger' : 'success'} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.querySelector('.container').insertBefore(alertDiv, document.querySelector('.container').firstChild);
}

function updateRewardsDisplay(rewards) {
    try {
        // Update stats
        document.getElementById('totalPoints').textContent = rewards.points;
        document.getElementById('totalDonations').textContent = rewards.total_donations;
        document.getElementById('badgeCount').textContent = rewards.badges.length;
        document.getElementById('streakPoints').textContent = rewards.streak_points;
        
        // Update level
        document.getElementById('donorLevel').textContent = rewards.level;
        const levelBadge = document.getElementById('levelBadge');
        levelBadge.className = 'level-badge ' + rewards.level.toLowerCase();
        
        // Update progress
        if (rewards.next_level) {
            const progress = (rewards.points / (rewards.next_level.points_needed + rewards.points)) * 100;
            document.getElementById('levelProgress').style.width = `${progress}%`;
            document.getElementById('nextLevelText').textContent = 
                `${rewards.next_level.points_needed} points to ${rewards.next_level.next_level}`;
        } else {
            document.getElementById('levelProgress').style.width = '100%';
            document.getElementById('nextLevelText').textContent = 'Maximum level achieved!';
        }
        
        // Update badges
        const achievementBadges = rewards.badges.filter(b => b.type === 'achievement');
        const rankBadges = rewards.badges.filter(b => b.type === 'rank') || [];
        const specialBadges = rewards.badges.filter(b => b.type === 'special') || [];
        
        updateBadgeSection('allBadges', rewards.badges);
        updateBadgeSection('achievementBadges', achievementBadges);
        updateBadgeSection('rankBadges', rankBadges);
        updateBadgeSection('specialBadges', specialBadges);
        
    } catch (error) {
        console.error('Error updating rewards display:', error);
        showAlert('error', 'Error updating rewards display');
    }
}

function updateBadgeSection(sectionId, badges) {
    const section = document.getElementById(sectionId);
    if (!section) return;
    
    if (!badges || badges.length === 0) {
        section.innerHTML = '<p class="text-muted text-center py-4">No badges earned yet</p>';
        return;
    }
    
    section.innerHTML = badges.map(badge => {
        // Determine badge icon and color class based on type
        let iconClass = 'text-bronze'; // default color
        let icon = 'trophy-fill';
        
        switch(badge.name.toLowerCase()) {
            case 'bronze':
            case '1 donations':
                iconClass = 'text-bronze';
                break;
            case 'silver':
            case '5 donations':
                iconClass = 'text-silver';
                break;
            case 'gold':
            case '10 donations':
                iconClass = 'text-gold';
                break;
            case 'platinum':
            case '25 donations':
                iconClass = 'text-platinum';
                break;
            case 'diamond':
            case '50 donations':
                iconClass = 'text-diamond';
                break;
            case 'regular donor':
                icon = 'calendar-check-fill';
                iconClass = 'text-success';
                break;
            default:
                icon = 'award-fill';
                iconClass = 'text-primary';
        }

        return `
            <div class="badge-item">
                <div class="badge-icon mb-3">
                    <i class="bi bi-${icon} ${iconClass}" style="font-size: 2.5rem;"></i>
                </div>
                <h5>${badge.name}</h5>
                <p>${badge.description}</p>
            </div>
        `;
    }).join('');
}

// Add this function to show tooltips
function initializeTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

// Modified DOMContentLoaded event listener
document.addEventListener('DOMContentLoaded', () => {
    loadRewards();
    initializeTooltips();
});

async function loadPointsHistory() {
    const historyContainer = document.getElementById('pointsHistory');
    const loadingEl = document.getElementById('historyLoading');
    
    try {
        loadingEl.style.display = 'block';
        
        const response = await fetch('api/rewards/history.php', {
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('token')}`
            }
        });
        
        const data = await response.json();
        
        if (!response.ok) {
            throw new Error(data.error || 'Failed to load points history');
        }
        
        displayPointsHistory(data.history || []);
        
    } catch (error) {
        console.error('Error loading points history:', error);
        historyContainer.innerHTML = `
            <div class="empty-state">
                <i class="bi bi-exclamation-circle"></i>
                <p>Failed to load points history. Please try again later.</p>
            </div>
        `;
    } finally {
        loadingEl.style.display = 'none';
    }
}

function displayPointsHistory(history) {
    const container = document.getElementById('pointsHistory');
    
    if (!history.length) {
        container.innerHTML = `
            <div class="empty-state">
                <i class="bi bi-calendar-x"></i>
                <p>No points history available yet.</p>
                <small>Start donating to earn points!</small>
            </div>
        `;
        return;
    }
    
    container.innerHTML = history.map(item => `
        <div class="history-item ${item.type}" data-type="${item.type}">
            <div class="date">
                ${new Date(item.date).toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                })}
            </div>
            <div class="points">
                +${item.points} points
            </div>
            <div class="description">
                ${getHistoryItemIcon(item.type)} ${item.description}
            </div>
        </div>
    `).join('');
}

function getHistoryItemIcon(type) {
    const icons = {
        donation: '<i class="bi bi-droplet-fill text-danger"></i>',
        streak: '<i class="bi bi-calendar-check-fill text-warning"></i>',
        achievement: '<i class="bi bi-trophy-fill text-info"></i>'
    };
    return icons[type] || '<i class="bi bi-circle-fill"></i>';
}

// Filter functionality
document.addEventListener('DOMContentLoaded', () => {
    // Load points history when page loads
    loadPointsHistory();
});
</script>

<?php require_once 'includes/footer.php'; ?>