<?php include 'includes/header.php'; ?>

<div class="container mt-4">
    <div class="row">
        <!-- Add Donation Form -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Add Donation Record</h5>
                </div>
                <div class="card-body">
                    <form id="donationForm">
                        <div class="mb-3">
                            <label for="donationDate" class="form-label">Donation Date</label>
                            <input type="date" class="form-control" id="donationDate" 
                                   max="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="bloodGroup" class="form-label">Blood Group</label>
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
                            <label for="units" class="form-label">Units Donated</label>
                            <input type="number" class="form-control" id="units" min="1" max="4" required>
                        </div>
                        <div class="mb-3">
                            <label for="hospitalName" class="form-label">Hospital Name</label>
                            <input type="text" class="form-control" id="hospitalName" required>
                        </div>
                        <div class="mb-3">
                            <label for="certificateNumber" class="form-label">Certificate Number</label>
                            <input type="text" class="form-control" id="certificateNumber">
                        </div>
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="notes" rows="2"></textarea>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Add Donation Record</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Donation History -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Donation History</h5>
                        <div class="donation-stats">
                            <span class="badge bg-primary" id="totalDonations">0 Donations</span>
                            <span class="badge bg-success" id="totalUnits">0 Units</span>
                            <span class="badge bg-info" id="nextEligible"></span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="timeline" id="donationHistory"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding: 20px 0;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 50px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-left: 80px;
    background: #fff;
    border-radius: 6px;
    padding: 15px;
    margin-bottom: 20px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.timeline-item::before {
    content: '';
    position: absolute;
    left: -30px;
    top: 50%;
    transform: translateY(-50%);
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #dc3545;
    border: 2px solid #fff;
}

.donation-badge {
    position: absolute;
    left: -75px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 0.8rem;
    width: 35px;
    height: 35px;
    line-height: 35px;
    text-align: center;
    border-radius: 50%;
    background: #dc3545;
    color: #fff;
}

.next-donation {
    background: #f8f9fa;
    border: 2px dashed #dee2e6;
    padding: 15px;
    border-radius: 6px;
    text-align: center;
    margin-top: 20px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadDonationHistory();
    
    // Handle form submission
    document.getElementById('donationForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = {
            donation_date: document.getElementById('donationDate').value,
            blood_group: document.getElementById('bloodGroup').value,
            units: document.getElementById('units').value,
            hospital_name: document.getElementById('hospitalName').value,
            certificate_number: document.getElementById('certificateNumber').value,
            notes: document.getElementById('notes').value
        };
        
        try {
            const response = await fetch('api/donations/create.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${localStorage.getItem('token')}`
                },
                body: JSON.stringify(formData)
            });
            
            const data = await response.json();
            
            if (response.ok) {
                alert('Donation record added successfully');
                this.reset();
                loadDonationHistory();
            } else {
                alert(data.error || 'Failed to add donation record');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred while adding the record');
        }
    });
});

async function loadDonationHistory() {
    try {
        const response = await fetch('api/donations/history.php', {
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('token')}`
            }
        });
        
        const data = await response.json();
        
        if (response.ok) {
            displayDonationHistory(data.donations);
            updateDonationStats(data.stats);
        } else {
            console.error('Failed to load donation history:', data.error);
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

function displayDonationHistory(donations) {
    const container = document.getElementById('donationHistory');
    
    if (donations.length === 0) {
        container.innerHTML = `
            <div class="text-center text-muted my-4">
                <i class="bi bi-droplet" style="font-size: 2rem;"></i>
                <p class="mt-2">No donation records found. Start by adding your first donation!</p>
            </div>
        `;
        return;
    }
    
    container.innerHTML = donations.map((donation, index) => `
        <div class="timeline-item">
            <div class="donation-badge">#${donations.length - index}</div>
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="mb-1">
                        <span class="badge bg-danger">${donation.blood_group}</span>
                        ${donation.hospital_name}
                    </h6>
                    <small class="text-muted">
                        ${new Date(donation.donation_date).toLocaleDateString()}
                    </small>
                </div>
                <span class="badge bg-success">${donation.units} Units</span>
            </div>
            ${donation.certificate_number ? `
                <div class="mt-2">
                    <small class="text-muted">Certificate: ${donation.certificate_number}</small>
                </div>
            ` : ''}
            ${donation.notes ? `
                <div class="mt-2">
                    <small class="text-muted">${donation.notes}</small>
                </div>
            ` : ''}
        </div>
    `).join('');
    
    // Add next eligible donation date if available
    if (donations[0].next_eligible_date) {
        const nextDate = new Date(donations[0].next_eligible_date);
        if (nextDate > new Date()) {
            container.innerHTML += `
                <div class="next-donation">
                    <i class="bi bi-calendar-check text-success"></i>
                    Next eligible donation date: ${nextDate.toLocaleDateString()}
                </div>
            `;
        }
    }
}

function updateDonationStats(stats) {
    document.getElementById('totalDonations').textContent = `${stats.total_donations} Donations`;
    document.getElementById('totalUnits').textContent = `${stats.total_units} Units`;
    
    const nextEligible = document.getElementById('nextEligible');
    if (stats.next_eligible_date) {
        const nextDate = new Date(stats.next_eligible_date);
        if (nextDate > new Date()) {
            nextEligible.textContent = `Next: ${nextDate.toLocaleDateString()}`;
        } else {
            nextEligible.textContent = 'Eligible Now';
            nextEligible.classList.remove('bg-info');
            nextEligible.classList.add('bg-success');
        }
    }
}
</script>

<?php include 'includes/footer.php'; ?> 