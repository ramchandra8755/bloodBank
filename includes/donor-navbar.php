
<nav class="navbar navbar-expand-lg navbar-dark bg-danger">
    <div class="container">
        <a class="navbar-brand" href="../index.php">Blood Donation</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>" 
                       href="dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'appointments.php' ? 'active' : ''; ?>" 
                       href="appointments.php">My Appointments</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'donation-history.php' ? 'active' : ''; ?>" 
                       href="donation-history.php">Donation History</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'rewards.php' ? 'active' : ''; ?>" 
                       href="rewards.php">
                        <i class="bi bi-trophy"></i> My Rewards
                        <span class="badge bg-light text-danger" id="rewardsPoints"></span>
                    </a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : ''; ?>" 
                       href="profile.php">Profile</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<script>
// Add this to show rewards points in the navbar
async function loadRewardsPoints() {
    try {
        const response = await fetch(`../api/rewards/manage.php?donor_id=<?php echo $_SESSION['user_id']; ?>`);
        const data = await response.json();
        
        if (data.success && data.rewards) {
            const pointsElement = document.getElementById('rewardsPoints');
            if (pointsElement) {
                pointsElement.textContent = `${data.rewards.points} pts`;
            }
        }
    } catch (error) {
        console.error('Error loading rewards points:', error);
    }
}

// Load rewards points when page loads
document.addEventListener('DOMContentLoaded', loadRewardsPoints);
</script> 