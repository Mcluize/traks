@extends(backpack_view('blank'))

@section('header')
<div class="container-fluid">
    <div class="justify-content-between align-items-left">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="#">Pages</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Manage Accounts</li>
                </ol>
            </nav>
        </div>
        <div>
            <h2 class="header-container">
                <span class="manage-accounts-title">Manage Accounts</span>
            </h2>
        </div>  
    </div>
</div>
@endsection

@section('content')

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&display=swap" rel="stylesheet">
<link href="{{ asset('css/manage_tourist.css') }}" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<div class="dashboard-container" id="mainContent">
    <!-- Account Summary Section -->
    <div class="account-summary-row">
        <div class="account-card total-accounts-card">
            <div class="account-card-title">Total Accounts</div>
            <div class="account-card-number">145</div>
        </div>
        <div class="account-card destination-accounts-card">
            <div class="account-card-title">Online Created Accounts</div>
            <div class="account-card-number">89</div>
        </div>
        <div class="account-card pending-accounts-card">
            <div class="account-card-title">Onsite Created Accounts</div>
            <div class="account-card-number">50</div>
        </div>
    </div>

    <!-- Main Content with Two Columns -->
    <div class="account-tables-container">
        <!-- Tourist Accounts Column -->
        <div class="accounts-table-section tourist-accounts">
            <h3 class="section-title">Tourist Accounts</h3>
            <div class="table-actions">
                <div class="table-filters">
                    <input type="text" class="search-input" id="searchTouristInput" placeholder="Search by Tourist ID">
                </div>
            </div>

            <!-- Tourist Accounts Table -->
            <div class="accounts-table">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Tourist ID</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="touristTableBody">
                        @forelse($users as $user)
                            @if($user['user_type'] == 'user')
                            <tr class="userRow tourist-row">
                                <td>{{ $user['user_id'] }}</td>
                                <td>
                                    <button 
                                        type="button"
                                        class="btn btn-sm btn-primary view-details-btn"
                                        data-user='@json($user)' data-user-type="tourist" data-bs-toggle="modal" data-bs-target="#userModal">
                                        View Details
                                    </button>
                                </td>
                            </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="2" class="text-center">No tourist accounts found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <p id="noTouristResultMessage" style="display: none;" class="no-result-message">No tourist found with that ID.</p>
            </div>
        </div>

        <!-- Admin Accounts Column -->
        <div class="accounts-table-section admin-accounts">
            <h3 class="section-title">Admin Accounts</h3>
            <div class="table-actions">
                <div class="table-filters">
                    <input type="text" class="search-input" id="searchAdminInput" placeholder="Search by Admin ID">
                </div>
                <div>
                    <button type="button" class="create-account-btn" data-bs-toggle="modal" data-bs-target="#createAccountModal">
                        Create Account
                    </button>
                </div>
            </div>

            <!-- Admin Accounts Table -->
            <div class="accounts-table">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Admin ID</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="adminTableBody">
                        @forelse($users as $user)
                            @if($user['user_type'] == 'admin')
                            <tr class="userRow admin-row">
                                <td>{{ $user['user_id'] }}</td>
                                <td>
                                    <button 
                                        type="button"
                                        class="btn btn-sm btn-primary view-details-btn"
                                        data-user='@json($user)' data-user-type="admin" data-bs-toggle="modal" data-bs-target="#userModal">
                                        View Details
                                    </button>
                                </td>
                            </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="2" class="text-center">No admin accounts found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <p id="noAdminResultMessage" style="display: none;" class="no-result-message">No admin found with that ID.</p>
            </div>
        </div>
    </div>
</div>

<!-- Modal 1: PIN Input -->
<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #0BC8CA; color: #fff;">
                <h5 class="modal-title" id="userModalLabel">Enter PIN to View Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="password" id="pinInput" class="form-control mb-3" placeholder="••••••" maxlength="6">
                <div id="pinError" class="text-danger" style="display:none;">Incorrect PIN.</div>
                <div class="mt-3">
                    <button id="changePinBtn" class="btn btn-warning">Change PIN</button>
                </div>
                <!-- Change PIN Section -->
                <div id="pinChangeSection" style="display:none;">
                    <input type="password" id="newPinInput" class="form-control mb-3" placeholder="Enter New PIN" maxlength="6">
                    <div id="pinChangeError" class="text-danger" style="display:none;">Failed to update PIN.</div>
                    <div class="mt-2">
                        <button id="saveNewPinBtn" class="btn btn-success">Save New PIN</button>
                        <button id="cancelPinChange" class="btn btn-secondary">Cancel</button>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="unlockBtn" style="background-color: #FF7E3F;">Unlock</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal 2: User Info (after PIN unlocked) -->
<div class="modal fade" id="userInfoModal" tabindex="-1" aria-labelledby="userInfoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #0BC8CA; color: #fff;">
                <h5 class="modal-title" id="userInfoModalTitle">User Information</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="user-info-container">
                    <div class="user-id-highlight">
                        <span id="modalUserTypeLabel">Tourist</span> ID: <span id="modalUserId"></span>
                    </div>
                    <p><strong>Full Name:</strong> <span id="modalFullName"></span></p>
                    <p><strong>Contact:</strong> <span id="modalContact"></span></p>
                    <p><strong>Address:</strong> <span id="modalAddress"></span></p>
                    <p><strong>Created At:</strong> <span id="modalCreatedAt"></span></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="closeUserInfoModal" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal 3: Create Account -->
<div class="modal fade" id="createAccountModal" tabindex="-1" aria-labelledby="createAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #FF7E3F; color: #fff;">
                <h5 class="modal-title" id="createAccountModalLabel">Create New Admin Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="createAccountForm">
                    <div class="mb-3">
                        <label for="fullName" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="fullName" required>
                    </div>
                    <div class="mb-3">
                        <label for="contactDetails" class="form-label">Contact Details</label>
                        <input type="text" class="form-control" id="contactDetails" required>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" rows="3" required></textarea>
                    </div>
                    <input type="hidden" id="userType" value="admin">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveAccountBtn" style="background-color: #FF7E3F;">Save Account</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('after_scripts')
<script>
    let selectedUser = null;
    let selectedUserType = 'tourist';
    const userModalEl = document.getElementById('userModal');
    const userInfoModalEl = document.getElementById('userInfoModal');
    const userModalInstance = new bootstrap.Modal(userModalEl);
    const userInfoModalInstance = new bootstrap.Modal(userInfoModalEl);
    const mainContent = document.getElementById('mainContent');

    // Set selected user
    document.querySelectorAll('.view-details-btn').forEach(button => {
        button.addEventListener('click', function () {
            selectedUser = JSON.parse(this.dataset.user);
            selectedUserType = this.dataset.userType;
            document.getElementById('pinInput').value = '';
            document.getElementById('pinError').style.display = 'none';
            document.getElementById('newPinInput').value = '';
            document.getElementById('pinChangeError').style.display = 'none';
            document.getElementById('pinChangeSection').style.display = 'none';
            document.getElementById('unlockBtn').style.display = 'inline-block';
            document.getElementById('pinInput').style.display = 'inline-block';
            document.getElementById('changePinBtn').style.display = 'inline-block';
            document.getElementById('userModalLabel').textContent = 'Enter PIN to View Details';
            userModalInstance.show();
            mainContent.classList.add('blurred');
        });
    });

    // Verify PIN for Viewing Details
    document.getElementById('unlockBtn').addEventListener('click', function () {
        const pin = document.getElementById('pinInput').value;

        fetch(`/admin/pin/verify`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ pin })
        })
        .then(response => response.json())
        .then(data => {
            if (data.message) {
                userModalInstance.hide();
                
                // Set the user type label (Tourist or Admin)
                document.getElementById('modalUserTypeLabel').textContent = selectedUserType.charAt(0).toUpperCase() + selectedUserType.slice(1);
                
                // Update modal title based on user type
                document.getElementById('userInfoModalTitle').textContent = 
                    selectedUserType === 'tourist' ? 'Tourist Information' : 'Admin Information';
                
                // Set user ID in the highlighted area
                document.getElementById('modalUserId').textContent = selectedUser.user_id;
                
                // Fill in the user details
                document.getElementById('modalFullName').textContent = selectedUser.full_name;
                document.getElementById('modalContact').textContent = selectedUser.contact_details;
                document.getElementById('modalAddress').textContent = selectedUser.address;
                document.getElementById('modalCreatedAt').textContent = new Date(selectedUser.created_at).toLocaleString();
                
                // Show the user info modal
                userInfoModalInstance.show();
            } else {
                document.getElementById('pinError').textContent = 'Incorrect PIN.';
                document.getElementById('pinError').style.display = 'block';
            }
        })
        .catch(() => {
            document.getElementById('pinError').textContent = 'Error validating PIN.';
            document.getElementById('pinError').style.display = 'block';
        });
    });

    // Verify PIN before showing Change PIN section
    document.getElementById('changePinBtn').addEventListener('click', function () {
        const pin = document.getElementById('pinInput').value;

        fetch(`/admin/pin/verify`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ pin })
        })
        .then(response => response.json())
        .then(data => {
            if (data.message) {
                // Unlock PIN change section
                document.getElementById('pinChangeSection').style.display = 'block';
                document.getElementById('unlockBtn').style.display = 'none';
                document.getElementById('pinInput').style.display = 'none';
                document.getElementById('changePinBtn').style.display = 'none';
                document.getElementById('userModalLabel').textContent = 'Change PIN';
                document.getElementById('pinError').style.display = 'none';
            } else {
                document.getElementById('pinError').textContent = 'Incorrect PIN to authorize PIN change.';
                document.getElementById('pinError').style.display = 'block';
            }
        })
        .catch(() => {
            document.getElementById('pinError').textContent = 'Error verifying current PIN.';
            document.getElementById('pinError').style.display = 'block';
        });
    });

    document.getElementById('cancelPinChange').addEventListener('click', function () {
        document.getElementById('pinChangeSection').style.display = 'none';
        document.getElementById('unlockBtn').style.display = 'inline-block';
        document.getElementById('pinInput').style.display = 'inline-block';
        document.getElementById('changePinBtn').style.display = 'inline-block';
        document.getElementById('userModalLabel').textContent = 'Enter PIN to View Details';

        // Clear both password fields
        document.getElementById('pinInput').value = '';
        document.getElementById('newPinInput').value = '';
        document.getElementById('pinError').style.display = 'none';
        document.getElementById('pinChangeError').style.display = 'none';
    });

    document.getElementById('saveNewPinBtn').addEventListener('click', function () {
        const newPin = document.getElementById('newPinInput').value;
        const currentPin = document.getElementById('pinInput').value;

        if (newPin.length !== 6) {
            document.getElementById('pinChangeError').textContent = 'PIN must be exactly 6 digits.';
            document.getElementById('pinChangeError').style.display = 'block';
            return;
        }

        fetch(`/admin/pin/update`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                current_pin: currentPin,
                new_pin: newPin
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.message) {
                alert('PIN updated successfully');

                // Reset modal state
                document.getElementById('pinChangeSection').style.display = 'none';
                document.getElementById('unlockBtn').style.display = 'inline-block';
                document.getElementById('pinInput').style.display = 'inline-block';
                document.getElementById('changePinBtn').style.display = 'inline-block';
                document.getElementById('userModalLabel').textContent = 'Enter PIN to View Details';
                document.getElementById('pinChangeError').style.display = 'none';

                // Clear both password fields
                document.getElementById('pinInput').value = '';
                document.getElementById('newPinInput').value = '';
            } else {
                document.getElementById('pinChangeError').textContent = 'Failed to update PIN.';
                document.getElementById('pinChangeError').style.display = 'block';
            }
        })
        .catch(() => {
            document.getElementById('pinChangeError').textContent = 'Something went wrong. Please try again.';
            document.getElementById('pinChangeError').style.display = 'block';
        });
    });

    // Close modals and blur effects
    document.getElementById('closeUserInfoModal').addEventListener('click', () => mainContent.classList.remove('blurred'));
    userModalEl.addEventListener('shown.bs.modal', () => mainContent.classList.add('blurred'));
    userInfoModalEl.addEventListener('shown.bs.modal', () => mainContent.classList.add('blurred'));
    userModalEl.addEventListener('hidden.bs.modal', () => {
        if (!userInfoModalEl.classList.contains('show')) {
            mainContent.classList.remove('blurred');
        }
    });
    userInfoModalEl.addEventListener('hidden.bs.modal', () => mainContent.classList.remove('blurred'));

    // Search by Tourist ID
    document.getElementById('searchTouristInput').addEventListener('keyup', function () {
        const searchQuery = this.value.toLowerCase();
        const rows = document.querySelectorAll('.tourist-row');
        const noResultMessage = document.getElementById('noTouristResultMessage');
        let found = false;

        rows.forEach(row => {
            const touristId = row.querySelector('td:first-child').textContent.toLowerCase();
            if (touristId.includes(searchQuery)) {
                row.style.display = '';
                found = true;
            } else {
                row.style.display = 'none';
            }
        });

        noResultMessage.style.display = found ? 'none' : 'block';
    });
    
    // Search by Admin ID
    document.getElementById('searchAdminInput').addEventListener('keyup', function () {
        const searchQuery = this.value.toLowerCase();
        const rows = document.querySelectorAll('.admin-row');
        const noResultMessage = document.getElementById('noAdminResultMessage');
        let found = false;

        rows.forEach(row => {
            const adminId = row.querySelector('td:first-child').textContent.toLowerCase();
            if (adminId.includes(searchQuery)) {
                row.style.display = '';
                found = true;
            } else {
                row.style.display = 'none';
            }
        });

        noResultMessage.style.display = found ? 'none' : 'block';
    });

    document.getElementById('saveAccountBtn').addEventListener('click', function() {
    const fullName = document.getElementById('fullName').value;
    const contactDetails = document.getElementById('contactDetails').value;
    const address = document.getElementById('address').value;

    // Validate form
    if (!fullName || !contactDetails || !address) {
        alert('Please fill in all required fields');
        return;
    }

    // Show loading indicator
    this.disabled = true;
    this.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Creating...';

    fetch('/admin/create-admin-account', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            full_name: fullName,
            contact_details: contactDetails,
            address: address
        })
    })
    .then(response => response.json())
    .then(data => {
        // Reset button
        this.disabled = false;
        this.innerHTML = 'Save Account';

        if (data.success) {
            const newUser = data.user;

            // Add to admin table
            const tableBody = document.getElementById('adminTableBody');
            const newRow = document.createElement('tr');
            newRow.className = 'userRow admin-row';
            newRow.innerHTML = `
                <td>${newUser.user_id}</td>
                <td>
                    <button 
                        type="button"
                        class="btn btn-sm btn-primary view-details-btn"
                        data-user='${JSON.stringify(newUser)}' 
                        data-user-type="admin"
                        data-bs-toggle="modal" 
                        data-bs-target="#userModal">
                        View Details
                    </button>
                </td>
            `;

            tableBody.prepend(newRow);

            // Update counters
            const totalCounter = document.querySelector('.total-accounts-card .account-card-number');
            const onsiteCounter = document.querySelector('.pending-accounts-card .account-card-number');
            totalCounter.textContent = parseInt(totalCounter.textContent) + 1;
            onsiteCounter.textContent = parseInt(onsiteCounter.textContent) + 1;

            // Close modal and reset form
            const createModal = bootstrap.Modal.getInstance(document.getElementById('createAccountModal'));
            createModal.hide();
            document.getElementById('createAccountForm').reset();

            // Show success message
            alert('Admin account created successfully with default PIN: 1234');
        } else {
            alert('Failed to create account: ' + data.message);
        }
    })
    .catch(error => {
        // Reset button
        this.disabled = false;
        this.innerHTML = 'Save Account';

        console.error('Error:', error);
        alert('Error: ' + error.message || 'Something went wrong. Please try again.');
    });
});


    // Prevent content shift when modals are displayed
    document.querySelectorAll('[data-bs-toggle="modal"]').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const targetModal = document.querySelector(this.getAttribute('data-bs-target'));
            if (targetModal) {
                document.body.style.paddingRight = '0';
                document.body.style.overflow = 'auto';
            }
        });
    });

    // For modal close events
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('hidden.bs.modal', function() {
            document.body.style.paddingRight = '0';
            document.body.style.overflow = 'auto';
        });
    });
    
</script>
@endpush