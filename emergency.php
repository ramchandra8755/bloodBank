<?php include 'includes/header.php'; ?>

<div class="container mt-4">
    <div class="row">
        <!-- Emergency Request Form -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">Create Emergency Request</h5>
                </div>
                <div class="card-body">
                    <form id="emergencyRequestForm">
                        <div class="mb-3">
                            <label for="patientName" class="form-label">Patient Name</label>
                            <input type="text" class="form-control" id="patientName" required>
                        </div>
                        <div class="mb-3">
                            <label for="bloodGroup" class="form-label">Blood Group Needed</label>
                            <select class="form-select" id="bloodGroup" required>
                                <option value="">Select Blood Group</option>
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
                        <div class="mb-3">
                            <label for="unitsNeeded" class="form-label">Units Needed</label>
                            <input type="number" class="form-control" id="unitsNeeded" min="1" required>
                        </div>
                        <div class="mb-3">
                            <label for="hospitalName" class="form-label">Hospital Name</label>
                            <input type="text" class="form-control" id="hospitalName" required>
                        </div>
                        <div class="mb-3">
                            <label for="location" class="form-label">Location</label>
                            <input type="text" class="form-control" id="location" required>
                        </div>
                        <div class="mb-3">
                            <label for="contactNumber" class="form-label">Contact Number</label>
                            <input type="tel" class="form-control" id="contactNumber" required>
                        </div>
                        <div class="mb-3">
                            <label for="urgencyLevel" class="form-label">Urgency Level</label>
                            <select class="form-select" id="urgencyLevel" required>
                                <option value="High">High</option>
                                <option value="Medium">Medium</option>
                                <option value="Low">Low</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="additionalNotes" class="form-label">Additional Notes</label>
                            <textarea class="form-control" id="additionalNotes" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-danger w-100">Submit Emergency Request</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Emergency Requests List -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#activeRequests">Active Requests</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#myRequests">My Requests</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="activeRequests">
                            <div id="emergencyRequestsList"></div>
                        </div>
                        <div class="tab-pane fade" id="myRequests">
                            <div id="myRequestsList"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadEmergencyRequests();
    loadMyRequests();
    
    // Handle form submission
    document.getElementById('emergencyRequestForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = {
            patient_name: document.getElementById('patientName').value,
            blood_group: document.getElementById('bloodGroup').value,
            units_needed: document.getElementById('unitsNeeded').value,
            hospital_name: document.getElementById('hospitalName').value,
            location: document.getElementById('location').value,
            contact_number: document.getElementById('contactNumber').value,
            urgency_level: document.getElementById('urgencyLevel').value,
            additional_notes: document.getElementById('additionalNotes').value
        };
        
        try {
            const response = await fetch('api/emergency/create.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${localStorage.getItem('token')}`
                },
                body: JSON.stringify(formData)
            });
            
            const data = await response.json();
            
            if (response.ok) {
                alert('Emergency request created successfully');
                this.reset();
                loadEmergencyRequests();
                loadMyRequests();
            } else {
                alert(data.error || 'Failed to create emergency request');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred while creating the request');
        }
    });
});

async function loadEmergencyRequests() {
    try {
        const response = await fetch('api/emergency/list.php', {
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('token')}`
            }
        });
        
        const data = await response.json();
        
        if (response.ok) {
            displayRequests(data.requests, 'emergencyRequestsList');
        } else {
            console.error('Failed to load requests:', data.error);
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

async function loadMyRequests() {
    try {
        const response = await fetch('api/emergency/my-requests.php', {
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('token')}`
            }
        });
        
        const data = await response.json();
        
        if (response.ok) {
            displayRequests(data.requests, 'myRequestsList', true);
        } else {
            console.error('Failed to load requests:', data.error);
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

function displayRequests(requests, containerId, showActions = false) {
    const container = document.getElementById(containerId);
    
    if (requests.length === 0) {
        container.innerHTML = '<div class="alert alert-info">No emergency requests found.</div>';
        return;
    }
    
    container.innerHTML = requests.map(request => `
        <div class="card mb-3 border-${getUrgencyColor(request.urgency_level)}">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="card-title mb-0">
                        <span class="badge bg-danger">${request.blood_group}</span>
                        ${request.patient_name}
                    </h5>
                    <span class="badge bg-${getStatusColor(request.status)}">${request.status}</span>
                </div>
                <p class="card-text">
                    <strong>Hospital:</strong> ${request.hospital_name}<br>
                    <strong>Location:</strong> ${request.location}<br>
                    <strong>Units Needed:</strong> ${request.units_needed}<br>
                    <strong>Contact:</strong> ${request.contact_number}
                    ${request.additional_notes ? `<br><strong>Notes:</strong> ${request.additional_notes}` : ''}
                </p>
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">Posted ${timeAgo(request.created_at)}</small>
                    ${showActions && request.status === 'Active' ? `
                        <button class="btn btn-sm btn-danger" onclick="updateRequestStatus(${request.id}, 'Fulfilled')">
                            Mark as Fulfilled
                        </button>
                    ` : ''}
                </div>
            </div>
        </div>
    `).join('');
}

function getUrgencyColor(urgency) {
    switch(urgency) {
        case 'High': return 'danger';
        case 'Medium': return 'warning';
        case 'Low': return 'info';
        default: return 'secondary';
    }
}

function getStatusColor(status) {
    switch(status) {
        case 'Active': return 'success';
        case 'Fulfilled': return 'primary';
        case 'Expired': return 'secondary';
        default: return 'secondary';
    }
}

function timeAgo(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const seconds = Math.floor((now - date) / 1000);
    
    let interval = Math.floor(seconds / 31536000);
    if (interval > 1) return interval + ' years ago';
    
    interval = Math.floor(seconds / 2592000);
    if (interval > 1) return interval + ' months ago';
    
    interval = Math.floor(seconds / 86400);
    if (interval > 1) return interval + ' days ago';
    
    interval = Math.floor(seconds / 3600);
    if (interval > 1) return interval + ' hours ago';
    
    interval = Math.floor(seconds / 60);
    if (interval > 1) return interval + ' minutes ago';
    
    return 'just now';
}

async function updateRequestStatus(requestId, status) {
    if (!confirm('Are you sure you want to mark this request as fulfilled?')) {
        return;
    }
    
    try {
        const response = await fetch('api/emergency/update-status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${localStorage.getItem('token')}`
            },
            body: JSON.stringify({ request_id: requestId, status: status })
        });
        
        const data = await response.json();
        
        if (response.ok) {
            loadEmergencyRequests();
            loadMyRequests();
        } else {
            alert(data.error || 'Failed to update request status');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred while updating the request');
    }
}
</script>

<?php include 'includes/footer.php'; ?> 