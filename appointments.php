<?php include 'includes/header.php'; ?>

<div class="container mt-4">
    <div class="row">
        <!-- Schedule Appointment Form -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Schedule Donation Appointment</h5>
                </div>
                <div class="card-body">
                    <form id="appointmentForm">
                        <div class="mb-3">
                            <label for="bloodBank" class="form-label">Blood Bank</label>
                            <select class="form-select" id="bloodBank" required>
                                <option value="">Select Blood Bank</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="appointmentDate" class="form-label">Date</label>
                            <input type="date" class="form-control" id="appointmentDate" 
                                   min="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="timeSlot" class="form-label">Time Slot</label>
                            <select class="form-select" id="timeSlot" required disabled>
                                <option value="">Select Date First</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes (Optional)</label>
                            <textarea class="form-control" id="notes" rows="2"></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">Schedule Appointment</button>
                    </form>
                </div>
            </div>
            
            <!-- Blood Bank Details -->
            <div id="bloodBankDetails" class="card mt-3 d-none">
                <div class="card-body">
                    <h6 class="card-title">Blood Bank Details</h6>
                    <div id="bloodBankInfo"></div>
                </div>
            </div>
        </div>

        <!-- Appointments List -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#upcomingAppointments">
                                Upcoming
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#pastAppointments">
                                Past
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="upcomingAppointments">
                            <div id="upcomingAppointmentsList"></div>
                        </div>
                        <div class="tab-pane fade" id="pastAppointments">
                            <div id="pastAppointmentsList"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadBloodBanks();
    loadAppointments();
    
    // Event Listeners
    document.getElementById('bloodBank').addEventListener('change', function() {
        showBloodBankDetails(this.value);
        updateAvailableSlots();
    });
    
    document.getElementById('appointmentDate').addEventListener('change', updateAvailableSlots);
    
    document.getElementById('appointmentForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = {
            blood_bank_id: document.getElementById('bloodBank').value,
            appointment_date: document.getElementById('appointmentDate').value,
            time_slot: document.getElementById('timeSlot').value,
            notes: document.getElementById('notes').value
        };
        
        try {
            const response = await fetch('api/appointments/create.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${localStorage.getItem('token')}`
                },
                body: JSON.stringify(formData)
            });
            
            const data = await response.json();
            
            if (response.ok) {
                alert('Appointment scheduled successfully');
                this.reset();
                loadAppointments();
            } else {
                alert(data.error || 'Failed to schedule appointment');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred while scheduling the appointment');
        }
    });
});

async function loadBloodBanks() {
    try {
        const response = await fetch('api/blood-banks/list.php', {
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('token')}`
            }
        });
        
        const data = await response.json();
        
        if (response.ok) {
            const select = document.getElementById('bloodBank');
            select.innerHTML = '<option value="">Select Blood Bank</option>' +
                data.blood_banks.map(bank => 
                    `<option value="${bank.id}">${bank.name}</option>`
                ).join('');
        }
    } catch (error) {
        console.error('Error loading blood banks:', error);
    }
}

async function showBloodBankDetails(bankId) {
    if (!bankId) {
        document.getElementById('bloodBankDetails').classList.add('d-none');
        return;
    }
    
    try {
        const response = await fetch(`api/blood-banks/details.php?id=${bankId}`, {
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('token')}`
            }
        });
        
        const data = await response.json();
        
        if (response.ok) {
            const detailsDiv = document.getElementById('bloodBankDetails');
            const infoDiv = document.getElementById('bloodBankInfo');
            
            infoDiv.innerHTML = `
                <p class="mb-2"><i class="bi bi-geo-alt-fill text-primary"></i> ${data.bank.address}</p>
                <p class="mb-2"><i class="bi bi-telephone-fill text-primary"></i> ${data.bank.contact_number}</p>
                <p class="mb-2"><i class="bi bi-envelope-fill text-primary"></i> ${data.bank.email}</p>
                <p class="mb-0"><i class="bi bi-clock-fill text-primary"></i> ${data.bank.operating_hours}</p>
            `;
            
            detailsDiv.classList.remove('d-none');
        }
    } catch (error) {
        console.error('Error loading blood bank details:', error);
    }
}

async function updateAvailableSlots() {
    const bankId = document.getElementById('bloodBank').value;
    const date = document.getElementById('appointmentDate').value;
    const select = document.getElementById('timeSlot');
    
    select.disabled = true;
    select.innerHTML = '<option value="">Loading slots...</option>';
    
    if (!bankId || !date) {
        select.innerHTML = '<option value="">Select Date First</option>';
        return;
    }
    
    try {
        const response = await fetch(`api/appointments/available-slots.php?bank_id=${bankId}&date=${date}`, {
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('token')}`
            }
        });
        
        const data = await response.json();
        
        if (response.ok) {
            select.innerHTML = '<option value="">Select Time Slot</option>' +
                data.slots.map(slot => 
                    `<option value="${slot.time}">${formatTime(slot.time)}</option>`
                ).join('');
            select.disabled = false;
        }
    } catch (error) {
        console.error('Error loading time slots:', error);
        select.innerHTML = '<option value="">Error loading slots</option>';
    }
}

async function loadAppointments() {
    try {
        const response = await fetch('api/appointments/list.php', {
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('token')}`
            }
        });
        
        const data = await response.json();
        
        if (response.ok) {
            displayAppointments(data.upcoming, 'upcomingAppointmentsList', true);
            displayAppointments(data.past, 'pastAppointmentsList', false);
        }
    } catch (error) {
        console.error('Error loading appointments:', error);
    }
}

function displayAppointments(appointments, containerId, allowActions) {
    const container = document.getElementById(containerId);
    
    if (appointments.length === 0) {
        container.innerHTML = '<div class="alert alert-info">No appointments found.</div>';
        return;
    }
    
    container.innerHTML = appointments.map(apt => `
        <div class="card mb-3 border-${getStatusColor(apt.status)}">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="card-title mb-1">${apt.blood_bank_name}</h6>
                        <p class="card-text mb-2">
                            <small class="text-muted">
                                <i class="bi bi-calendar"></i> 
                                ${formatDate(apt.appointment_date)} at ${formatTime(apt.time_slot)}
                            </small>
                        </p>
                    </div>
                    <span class="badge bg-${getStatusColor(apt.status)}">${apt.status}</span>
                </div>
                ${apt.notes ? `
                    <p class="card-text small mb-2">${apt.notes}</p>
                ` : ''}
                ${allowActions && apt.status === 'Scheduled' ? `
                    <div class="mt-2">
                        <button class="btn btn-sm btn-danger" 
                                onclick="updateAppointmentStatus(${apt.id}, 'Cancelled')">
                            Cancel Appointment
                        </button>
                    </div>
                ` : ''}
            </div>
        </div>
    `).join('');
}

function getStatusColor(status) {
    switch(status) {
        case 'Scheduled': return 'primary';
        case 'Completed': return 'success';
        case 'Cancelled': return 'danger';
        case 'Missed': return 'warning';
        default: return 'secondary';
    }
}

async function updateAppointmentStatus(appointmentId, status) {
    if (!confirm(`Are you sure you want to ${status.toLowerCase()} this appointment?`)) {
        return;
    }
    
    try {
        const response = await fetch('api/appointments/update-status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${localStorage.getItem('token')}`
            },
            body: JSON.stringify({ appointment_id: appointmentId, status: status })
        });
        
        const data = await response.json();
        
        if (response.ok) {
            loadAppointments();
        } else {
            alert(data.error || 'Failed to update appointment status');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred while updating the appointment');
    }
}

function formatDate(dateString) {
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString(undefined, options);
}

function formatTime(timeString) {
    return new Date(`2000-01-01T${timeString}`).toLocaleTimeString([], 
        { hour: '2-digit', minute: '2-digit' });
}
</script>

<?php include 'includes/footer.php'; ?> 