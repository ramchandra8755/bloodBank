<?php
require_once 'includes/auth_check.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood Bank Dashboard - <?php echo htmlspecialchars($admin['blood_bank_name']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
        }
        .navbar {
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            background: linear-gradient(135deg, #dc3545 0%, #ff6b6b 100%) !important;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: transform 0.2s;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .stat-card {
            background: white;
            border-left: 4px solid;
        }
        .stat-card.primary {
            border-color: #dc3545;
        }
        .stat-card.success {
            border-color: #28a745;
        }
        .stat-card.info {
            border-color: #17a2b8;
        }
        .stat-card.secondary {
            border-color: #6c757d;
        }
        .stat-card .card-body {
            padding: 1.5rem;
        }
        .stat-card h5 {
            color: #6c757d;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .stat-card h2 {
            color: #343a40;
            font-weight: 600;
        }
        .nav-tabs {
            border: none;
            margin-bottom: -1px;
        }
        .nav-tabs .nav-link {
            border: none;
            color: #6c757d;
            padding: 1rem 1.5rem;
            font-weight: 500;
            transition: all 0.2s;
        }
        .nav-tabs .nav-link:hover {
            color: #dc3545;
        }
        .nav-tabs .nav-link.active {
            color: #dc3545;
            border-bottom: 2px solid #dc3545;
            background: transparent;
        }
        .table {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        .table thead th {
            background: #f8f9fa;
            color: #495057;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
            border: none;
        }
        .btn-sm {
            border-radius: 20px;
            padding: 0.4rem 1rem;
            font-size: 0.8rem;
            font-weight: 500;
        }
        .badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-danger">
        <div class="container">
            <a class="navbar-brand" href="#"><?php echo htmlspecialchars($admin['blood_bank_name']); ?></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <span class="nav-link">Welcome, <?php echo htmlspecialchars($admin['name']); ?></span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card stat-card primary">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-calendar-check me-2"></i>Today's Appointments</h5>
                        <h2 class="mb-0" id="todayCount">0</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card success">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-calendar-date me-2"></i>Upcoming</h5>
                        <h2 class="mb-0" id="upcomingCount">0</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card info">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-calendar-month me-2"></i>This Month</h5>
                        <h2 class="mb-0" id="monthCount">0</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card secondary">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-droplet-fill me-2"></i>Total Donations</h5>
                        <h2 class="mb-0" id="totalCount">0</h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Appointments Tabs -->
        <div class="card">
            <div class="card-body">
                <ul class="nav nav-tabs" id="appointmentTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="today-tab" data-bs-toggle="tab" href="#today" role="tab">
                            <i class="bi bi-clock me-2"></i>Today's Appointments
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="upcoming-tab" data-bs-toggle="tab" href="#upcoming" role="tab">
                            <i class="bi bi-calendar-week me-2"></i>Upcoming
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="past-tab" data-bs-toggle="tab" href="#past" role="tab">
                            <i class="bi bi-clock-history me-2"></i>Past
                        </a>
                    </li>
                </ul>

                <div class="tab-content mt-4">
                    <div class="tab-pane fade show active" id="today" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Time</th>
                                        <th>Donor Name</th>
                                        <th>Blood Group</th>
                                        <th>Contact</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="todayAppointments"></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="upcoming" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Date & Time</th>
                                        <th>Donor Name</th>
                                        <th>Blood Group</th>
                                        <th>Contact</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="upcomingAppointments"></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="past" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Date & Time</th>
                                        <th>Donor Name</th>
                                        <th>Blood Group</th>
                                        <th>Contact</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="pastAppointments"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
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
            window.addEventListener('beforeunload', () => this.cleanup());
            document.addEventListener('visibilitychange', () => {
                if (document.hidden) {
                    this.cleanup();
                } else {
                    this.startRefreshCycle();
                }
            });
        }

        async loadDashboardData() {
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
            }
        }

        updateDashboard(data) {
            document.getElementById('todayCount').textContent = data.stats.today;
            document.getElementById('upcomingCount').textContent = data.stats.upcoming;
            document.getElementById('monthCount').textContent = data.stats.month;
            document.getElementById('totalCount').textContent = data.stats.total;

            this.updateAppointmentTable('todayAppointments', data.today);
            this.updateAppointmentTable('upcomingAppointments', data.upcoming);
            this.updateAppointmentTable('pastAppointments', data.past);
        }

        updateAppointmentTable(tableId, appointments) {
            const tbody = document.getElementById(tableId);
            if (!tbody) return;

            const html = appointments.length ? appointments.map(apt => `
                <tr>
                    <td>${this.formatDateTime(apt.appointment_date, apt.time_slot)}</td>
                    <td>${apt.donor_name}</td>
                    <td><span class="badge bg-light text-dark">${apt.blood_group}</span></td>
                    <td>
                        <a href="tel:${apt.phone}" class="text-decoration-none">
                            <i class="bi bi-telephone me-1"></i>${apt.phone}
                        </a><br>
                        <small class="text-muted">
                            <i class="bi bi-envelope me-1"></i>${apt.email}
                        </small>
                    </td>
                    <td>
                        <span class="badge bg-${this.getStatusColor(apt.status)}">
                            ${apt.status}
                        </span>
                    </td>
                    <td>${this.getActionButtons(apt)}</td>
                </tr>
            `).join('') : '<tr><td colspan="6" class="text-center text-muted py-4">No appointments found</td></tr>';

            tbody.innerHTML = html;
        }

        formatDateTime(date, time) {
            const dt = new Date(date + 'T' + time);
            return dt.toLocaleString('en-US', {
                month: 'short',
                day: 'numeric',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        getStatusColor(status) {
            const colors = {
                'Scheduled': 'primary',
                'Completed': 'success',
                'Cancelled': 'danger',
                'Missed': 'warning'
            };
            return colors[status] || 'secondary';
        }

        getActionButtons(appointment) {
            if (appointment.status !== 'Scheduled') return '';
            
            return `
                <div class="btn-group">
                    <button class="btn btn-sm btn-outline-success me-2" onclick="dashboard.updateStatus(${appointment.id}, 'Completed')">
                        <i class="bi bi-check-circle me-1"></i>Complete
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="dashboard.updateStatus(${appointment.id}, 'Missed')">
                        <i class="bi bi-x-circle me-1"></i>Missed
                    </button>
                </div>
            `;
        }

        async updateStatus(appointmentId, status) {
            if (!confirm(`Are you sure you want to mark this appointment as ${status}?`)) {
                return;
            }

            try {
                const response = await fetch('../api/blood-bank/appointments/update-status.php', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${this.token}`,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        appointment_id: appointmentId,
                        status: status
                    })
                });

                const data = await response.json();

                if (response.ok) {
                    let message = 'Status updated successfully';
                    if (status === 'Completed') {
                        message = 'Donation recorded successfully. The donor\'s next eligible date has been updated.';
                    } else if (status === 'Missed') {
                        message = 'Appointment marked as missed.';
                    }
                    alert(message);
                    
                    this.loadDashboardData();
                } else {
                    alert(data.error || 'Failed to update appointment status');
                }
            } catch (error) {
                console.error('Error updating status:', error);
                alert('An error occurred while updating the status');
            }
        }

        startRefreshCycle() {
            this.loadDashboardData();
            if (this.refreshInterval) {
                clearInterval(this.refreshInterval);
            }
            this.refreshInterval = setInterval(() => this.loadDashboardData(), 300000);
        }

        cleanup() {
            if (this.refreshInterval) {
                clearInterval(this.refreshInterval);
                this.refreshInterval = null;
            }
        }
    }

    let dashboard;
    document.addEventListener('DOMContentLoaded', () => {
        dashboard = new DashboardManager();
    });
    </script>
</body>
</html>