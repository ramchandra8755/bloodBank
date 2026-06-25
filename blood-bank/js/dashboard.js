// Create this new file for dashboard JavaScript
let dashboardRefreshInterval = null;
let isLoading = false;

class DashboardManager {
    constructor() {
        this.token = sessionStorage.getItem('bank_admin_token');
        if (!this.token) {
            window.location.href = 'login.php';
            return;
        }
        
        this.initializeEventListeners();
        this.startRefreshCycle();
    }

    initializeEventListeners() {
        // Cleanup on page unload
        window.addEventListener('beforeunload', () => this.cleanup());
        
        // Handle visibility change
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                this.cleanup();
            } else {
                this.startRefreshCycle();
            }
        });
    }

    async loadDashboardData() {
        if (isLoading) return;
        isLoading = true;

        try {
            const response = await fetch('../api/blood-bank/appointments/dashboard.php', {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${this.token}`,
                    'Content-Type': 'application/json'
                }
            });
            
            if (response.status === 401) {
                window.location.href = 'login.php';
                return;
            }
            
            const data = await response.json();
            if (response.ok) {
                this.updateDashboard(data);
            }
        } catch (error) {
            console.error('Dashboard loading error:', error);
        } finally {
            isLoading = false;
        }
    }

    startRefreshCycle() {
        this.loadDashboardData();
        if (dashboardRefreshInterval) {
            clearInterval(dashboardRefreshInterval);
        }
        dashboardRefreshInterval = setInterval(() => this.loadDashboardData(), 300000);
    }

    cleanup() {
        if (dashboardRefreshInterval) {
            clearInterval(dashboardRefreshInterval);
            dashboardRefreshInterval = null;
        }
    }

    updateDashboard(data) {
        // Your existing update logic here
    }
}

// Initialize dashboard when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    new DashboardManager();
}); 