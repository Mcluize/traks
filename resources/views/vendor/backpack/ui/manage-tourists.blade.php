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
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&display=swap" rel="stylesheet">
<link href="{{ asset('css/manage_tourist.css') }}" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<div class="dashboard-container" id="mainContent">
    <!-- Account Summary Section -->
    <div class="account-summary-row">
        <div class="account-card total-accounts-card">
            <div class="account-card-title">Total Accounts</div>
            <div class="account-card-number" id="totalAccounts">{{ $totalAccounts }}</div>
        </div>
        <div class="account-card destination-accounts-card">
            <div class="account-card-title">Total Tourist Accounts</div>
            <div class="account-card-number" id="touristAccounts">{{ $touristAccounts }}</div>
        </div>
        <div class="account-card pending-accounts-card">
            <div class="account-card-title">Total Admin Accounts</div>
            <div class="account-card-number" id="adminAccounts">{{ $adminAccounts }}</div>
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
                                        data-user='@json($user)' 
                                        data-user-type="tourist" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#userModal">
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
                <div class="pagination-container" id="touristPagination">
                    <div class="pagination">
                        <button class="pagination-btn prev-btn" id="touristPrevBtn" disabled>< Previous</button>
                        <div class="page-numbers" id="touristPageNumbers">
                        </div>
                        <button class="pagination-btn next-btn" id="touristNextBtn">Next ></button>
                    </div>
                </div>
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
                            @if($user['user_type'] == 'admin' && $user['status'] !== 'locked')
                            <tr class="userRow admin-row" data-user-id="{{ $user['user_id'] }}">
                                <td>{{ $user['user_id'] }}</td>
                                <td>
                                    <button 
                                        type="button"
                                        class="btn btn-sm btn-primary view-details-btn"
                                        data-user='@json($user)' 
                                        data-user-type="admin" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#userModal">
                                        View Details
                                    </button>
                                    <button 
                                        type="button"
                                        class="btn btn-sm btn-danger lock-btn"
                                        data-user-id="{{ $user['user_id'] }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#lockConfirmModal">
                                        Lock
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
                <div class="pagination-container" id="adminPagination">
                    <div class="pagination">
                        <button class="pagination-btn prev-btn" id="adminPrevBtn" disabled>< Previous</button>
                        <div class="page-numbers" id="adminPageNumbers">
                        </div>
                        <button class="pagination-btn next-btn" id="adminNextBtn">Next ></button>
                    </div>
                </div>
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
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="unlockBtn" style="background-color: #FF7E3F;">Unlock</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal 2: Change PIN Modal -->
<div class="modal fade" id="changePinModal" tabindex="-1" aria-labelledby="changePinModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #FF7E3F; color: #fff;">
                <h5 class="modal-title" id="changePinModalLabel">Change Your PIN</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="password" id="newPinInput" class="form-control mb-3" placeholder="Enter New PIN" maxlength="6">
                <div id="pinChangeError" class="text-danger" style="display:none;">Failed to update PIN.</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="cancelPinChange">Cancel</button>
                <button type="button" class="btn btn-success" id="saveNewPinBtn">Save New PIN</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal 3: User Info (after PIN unlocked) -->
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

<!-- Lock Confirmation Modal -->
<div class="modal fade" id="lockConfirmModal" tabindex="-1" aria-labelledby="lockConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="lockConfirmModalLabel">Confirm Lock Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to lock this account? This will prevent the user from accessing the system.</p>
            </div>
            <div class="modal-footer modal-footer-side-by-side">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger">Lock</button>
            </div>
        </div>
    </div>
</div>

<!-- Create Account Modal -->
<div class="modal fade" id="createAccountModal" tabindex="-1" aria-labelledby="createAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #0BC8CA; color: #fff;">
                <h5 class="modal-title" id="createAccountModalLabel">Create New Admin Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="fullName" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="fullName" required>
                </div>
                <div class="mb-3">
                    <label for="contactDetails" class="form-label">Contact Details</label>
                    <div class="input-group">
                        <span class="input-group-text">+63</span>
                        <input type="text" class="form-control" id="contactDetails" required pattern="9\d{9}" maxlength="10" placeholder="9XXXXXXXXX">
                    </div>
                    <div class="invalid-feedback" id="contactDetailsError">
                        Please enter a valid 10-digit mobile number starting with 9.
                    </div>
                </div>
                <div id="createAccountError" class="text-danger" style="display:none;"></div>
            </div>
            <div class="modal-footer justify-content-between">
                <div class="d-flex w-100 gap-2">
                    <button type="button" class="btn btn-secondary w-50" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn w-50" id="saveAccountBtn" style="background-color: #FF7E3F; color: white;">Save Account</button>
                </div>
            </div>            
        </div>
    </div>
</div>
@endsection

@push('after_scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    let selectedUser = null;
    let selectedUserType = 'tourist';
    const userModalEl = document.getElementById('userModal');
    const changePinModalEl = document.getElementById('changePinModal');
    const userInfoModalEl = document.getElementById('userInfoModal');
    const createAccountModalEl = document.getElementById('createAccountModal');
    const userModalInstance = new bootstrap.Modal(userModalEl);
    const changePinModalInstance = new bootstrap.Modal(changePinModalEl);
    const userInfoModalInstance = new bootstrap.Modal(userInfoModalEl);
    const createAccountModalInstance = new bootstrap.Modal(createAccountModalEl);
    const mainContent = document.getElementById('mainContent');

    const ITEMS_PER_PAGE = 5;
    
    let touristCurrentPage = 1;
    const touristRows = document.querySelectorAll('.tourist-row');
    const touristTotalItems = touristRows.length;
    const touristTotalPages = Math.ceil(touristTotalItems / ITEMS_PER_PAGE);
    
    let adminCurrentPage = 1;
    const adminRows = document.querySelectorAll('.admin-row');
    const adminTotalItems = adminRows.length;
    const adminTotalPages = Math.ceil(adminTotalItems / ITEMS_PER_PAGE);
    
    initPagination('tourist', touristRows, touristTotalPages);
    initPagination('admin', adminRows, adminTotalPages);

    userModalEl.addEventListener('show.bs.modal', function () {
        resetPinModal();
    });

    changePinModalEl.addEventListener('show.bs.modal', function () {
        document.getElementById('newPinInput').value = '';
        document.getElementById('pinChangeError').style.display = 'none';
    });

    createAccountModalEl.addEventListener('show.bs.modal', function () {
        const fullName = document.getElementById('fullName');
        const contactDetails = document.getElementById('contactDetails');
        const createAccountError = document.getElementById('createAccountError');
        if (fullName) fullName.value = '';
        if (contactDetails) contactDetails.value = '';
        if (createAccountError) {
            createAccountError.textContent = '';
            createAccountError.style.display = 'none';
        }
        fullName.classList.remove('is-invalid');
        contactDetails.classList.remove('is-invalid');
    });
    
    const contactDetails = document.getElementById('contactDetails');
    if (contactDetails) {
        contactDetails.addEventListener('input', function(e) {
            this.value = this.value.replace(/\D/g, '');
            if (/^9\d{9}$/.test(this.value)) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            } else {
                this.classList.remove('is-valid');
                if (this.value.length > 0) {
                    this.classList.add('is-invalid');
                }
            }
        });
    }

    document.addEventListener('click', function (event) {
        if (event.target.classList.contains('view-details-btn')) {
            selectedUser = JSON.parse(event.target.dataset.user);
            selectedUserType = event.target.dataset.userType;
            userModalInstance.show();
            mainContent.classList.add('blurred');
        }
    });

    document.getElementById('changePinBtn').addEventListener('click', function () {
        const pinInput = document.getElementById('pinInput').value;
        if (!pinInput) {
            document.getElementById('pinError').textContent = 'Please enter your current PIN';
            document.getElementById('pinError').style.display = 'block';
            return;
        }

        fetch('/admin/pin/verify', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ pin: pinInput })
        })
        .then(response => response.json())
        .then(data => {
            if (data.message) {
                userModalInstance.hide();
                changePinModalInstance.show();
                document.getElementById('pinError').style.display = 'none';
            } else {
                document.getElementById('pinError').textContent = 'Wrong PIN, not authorized to change PIN';
                document.getElementById('pinError').style.display = 'block';
            }
        })
        .catch(() => {
            document.getElementById('pinError').textContent = 'Error verifying PIN';
            document.getElementById('pinError').style.display = 'block';
        });
    });

    document.getElementById('saveNewPinBtn').addEventListener('click', function () {
        const currentPin = document.getElementById('pinInput').value;
        const newPin = document.getElementById('newPinInput').value;

        if (!newPin) {
            document.getElementById('pinChangeError').textContent = 'Please enter a new PIN';
            document.getElementById('pinChangeError').style.display = 'block';
            return;
        }

        fetch('/admin/pin/update', {
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
                toastr.success('PIN updated successfully');
                changePinModalInstance.hide();
                userModalInstance.show();
                resetPinModal();
            } else {
                document.getElementById('pinChangeError').textContent = data.error || 'Failed to update PIN';
                document.getElementById('pinChangeError').style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('pinChangeError').textContent = 'Error updating PIN';
            document.getElementById('pinChangeError').style.display = 'block';
        });
    });

    document.getElementById('cancelPinChange').addEventListener('click', function () {
        changePinModalInstance.hide();
        userModalInstance.show();
        resetPinModal();
    });

    function resetPinModal() {
        document.getElementById('pinInput').value = '';
        document.getElementById('pinError').style.display = 'none';
    }

    $(document).on('click', '.lock-btn', function () {
        const userId = $(this).data('user-id');
        $('#lockConfirmModal').modal('show');
        $('.modal-footer button:contains("Lock")').data('user-id', userId);
    });

    $('.modal-footer button:contains("Lock")').click(function () {
        const userId = $(this).data('user-id');
        const lockButton = $(this);
        console.log('Attempting to lock admin account ID:', userId);
        lockButton.prop('disabled', true).text('Locking...');

        $.ajax({
            url: `/admin/admin/lock/${userId}`,
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                if (response.success) {
                    toastr.success('Account locked successfully');
                    $(`tr[data-user-id="${userId}"]`).find('.lock-btn').remove();
                    $('#lockConfirmModal').modal('hide');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    toastr.error('Failed to lock account');
                }
            },
            error: function (xhr) {
                console.error('Error details:', xhr.responseText);
                toastr.error('An error occurred while locking the account');
            },
            complete: function () {
                lockButton.prop('disabled', false).text('Lock');
            }
        });
    });

    const searchTouristInput = document.getElementById('searchTouristInput');
    if (searchTouristInput) {
        searchTouristInput.addEventListener('keyup', function () {
            const searchQuery = this.value.toLowerCase();
            const rows = document.querySelectorAll('.tourist-row');
            const noResultMessage = document.getElementById('noTouristResultMessage');
            let found = false;

            rows.forEach(row => {
                const touristId = row.querySelector('td:first-child').textContent.toLowerCase();
                if (touristId.includes(searchQuery)) {
                    row.classList.add('searchable');
                    row.style.display = '';
                    found = true;
                } else {
                    row.classList.remove('searchable');
                    row.style.display = 'none';
                }
            });

            if (searchQuery) {
                document.getElementById('touristPagination').style.display = 'none';
            } else {
                document.getElementById('touristPagination').style.display = 'flex';
                touristCurrentPage = 1;
                updatePageDisplay('tourist', touristRows, touristTotalPages, touristCurrentPage);
            }

            if (noResultMessage) {
                noResultMessage.style.display = found ? 'none' : 'block';
            }
        });
    }

    const searchAdminInput = document.getElementById('searchAdminInput');
    if (searchAdminInput) {
        searchAdminInput.addEventListener('keyup', function () {
            const searchQuery = this.value.toLowerCase();
            const rows = document.querySelectorAll('.admin-row');
            const noResultMessage = document.getElementById('noAdminResultMessage');
            let found = false;

            rows.forEach(row => {
                const adminId = row.querySelector('td:first-child').textContent.toLowerCase();
                if (adminId.includes(searchQuery)) {
                    row.classList.add('searchable');
                    row.style.display = '';
                    found = true;
                } else {
                    row.classList.remove('searchable');
                    row.style.display = 'none';
                }
            });

            if (searchQuery) {
                document.getElementById('adminPagination').style.display = 'none';
            } else {
                document.getElementById('adminPagination').style.display = 'flex';
                adminCurrentPage = 1;
                updatePageDisplay('admin', adminRows, adminTotalPages, adminCurrentPage);
            }

            if (noResultMessage) {
                noResultMessage.style.display = found ? 'none' : 'block';
            }
        });
    }

    function sortAdminTable() {
        const tableBody = document.getElementById('adminTableBody');
        const rows = Array.from(tableBody.querySelectorAll('tr'));
        rows.sort((a, b) => {
            const idA = parseInt(a.querySelector('td:first-child').textContent);
            const idB = parseInt(b.querySelector('td:first-child').textContent);
            return idB - idA;
        });
        rows.forEach(row => tableBody.appendChild(row));
    }

    const saveAccountBtn = document.getElementById('saveAccountBtn');
    if (saveAccountBtn) {
        saveAccountBtn.addEventListener('click', function () {
            const fullName = document.getElementById('fullName');
            const contactDetails = document.getElementById('contactDetails');
            const contactDetailsError = document.getElementById('contactDetailsError');
            const createAccountError = document.getElementById('createAccountError');

            if (!fullName || !contactDetails) {
                if (createAccountError) {
                    createAccountError.textContent = 'Required input fields are missing in the DOM';
                    createAccountError.style.display = 'block';
                }
                return;
            }

            const fullNameValue = fullName.value.trim();
            const contactValue = contactDetails.value.trim();

            let isValid = true;
            fullName.classList.remove('is-invalid');
            contactDetails.classList.remove('is-invalid');
            if (createAccountError) {
                createAccountError.textContent = '';
                createAccountError.style.display = 'none';
            }
            
            if (!fullNameValue) {
                fullName.classList.add('is-invalid');
                isValid = false;
            }
            
            const phoneRegex = /^9\d{9}$/;
            if (!phoneRegex.test(contactValue)) {
                contactDetails.classList.add('is-invalid');
                if (contactDetailsError) {
                    contactDetailsError.textContent = 'Please enter a valid 10-digit mobile number starting with 9.';
                }
                isValid = false;
            }

            if (!isValid) {
                return;
            }

            const formattedContact = `+63${contactValue}`;

            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Creating...';

            fetch('/admin/create-admin-account', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    full_name: fullNameValue,
                    contact_details: formattedContact
                })
            })
            .then(response => {
                return response.json().then(data => ({ status: response.status, data }));
            })
            .then(({ status, data }) => {
                if (status >= 200 && status < 300) {
                    const newUser = data.user;
                    const tableBody = document.getElementById('adminTableBody');
                    const newRow = document.createElement('tr');
                    newRow.className = 'userRow admin-row';
                    newRow.setAttribute('data-user-id', newUser.user_id);
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
                            <button 
                                type="button"
                                class="btn btn-sm btn-danger lock-btn"
                                data-user-id="${newUser.user_id}"
                                data-bs-toggle="modal"
                                data-bs-target="#lockConfirmModal">
                                Lock
                            </button>
                        </td>
                    `;

                    tableBody.appendChild(newRow);
                    sortAdminTable();
                    const adminRows = document.querySelectorAll('.admin-row');
                    const adminTotalItems = adminRows.length;
                    const adminTotalPages = Math.ceil(adminTotalItems / ITEMS_PER_PAGE);
                    initPagination('admin', adminRows, adminTotalPages);
                    
                    createAccountModalInstance.hide();
                    toastr.success('Admin account created successfully with default PIN: 1234');
                    fullName.value = '';
                    contactDetails.value = '';
                } else {
                    console.error('Server error:', data);
                    createAccountError.textContent = data.message || 'Failed to create account';
                    createAccountError.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Network error:', error);
                createAccountError.textContent = 'A network error occurred. Please try again.';
                createAccountError.style.display = 'block';
            })
            .finally(() => {
                this.disabled = false;
                this.innerHTML = 'Save Account';
            });
        });
    }

    const closeUserInfoModal = document.getElementById('closeUserInfoModal');
    if (closeUserInfoModal) {
        closeUserInfoModal.addEventListener('click', () => {
            mainContent.classList.remove('blurred');
        });
    }

    userModalEl.addEventListener('hidden.bs.modal', () => {
        if (!userInfoModalEl.classList.contains('show') && !changePinModalEl.classList.contains('show')) {
            mainContent.classList.remove('blurred');
        }
    });
    userInfoModalEl.addEventListener('hidden.bs.modal', () => {
        mainContent.classList.remove('blurred');
    });
    changePinModalEl.addEventListener('hidden.bs.modal', () => {
        if (!userModalEl.classList.contains('show')) {
            mainContent.classList.remove('blurred');
        }
    });

    
const unlockBtn = document.getElementById('unlockBtn');
if (unlockBtn) {
    unlockBtn.addEventListener('click', function () {
        const pinInput = document.getElementById('pinInput');
        if (!pinInput) return;
        const pin = pinInput.value;

        this.disabled = true;
        this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Unlocking...';

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
                // PIN correct, now fetch user details BEFORE hiding PIN modal or removing blur
                fetch(`/admin/api/user-details/${selectedUser.user_id}`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => { throw new Error(err.error || 'Failed to fetch user details'); });
                    }
                    return response.json();
                })
                .then(userData => {
                    if (userData.error) {
                        throw new Error(userData.error);
                    }

                    // Populate user info modal fields
                    document.getElementById('modalUserTypeLabel').textContent = selectedUserType.charAt(0).toUpperCase() + selectedUserType.slice(1);
                    document.getElementById('userInfoModalTitle').textContent = selectedUserType === 'tourist' ? 'Tourist Information' : 'Admin Information';
                    document.getElementById('modalUserId').textContent = userData.user_id || 'N/A';
                    document.getElementById('modalFullName').textContent = userData.full_name || 'N/A';
                    document.getElementById('modalContact').textContent = userData.contact_details || 'N/A';
                    document.getElementById('modalAddress').textContent = userData.address || 'N/A';
                    const createdAt = userData.created_at ? new Date(userData.created_at) : null;
                    document.getElementById('modalCreatedAt').textContent = createdAt && !isNaN(createdAt.getTime()) ? createdAt.toLocaleString() : 'N/A';

                    // Now hide PIN modal and show user info modal
                    userModalInstance.hide();
                    userInfoModalInstance.show();

                    unlockBtn.disabled = false;
                    unlockBtn.innerHTML = 'Unlock';
                })
                .catch(error => {
                    console.error('Error fetching user details:', error);
                    toastr.error(error.message || 'Failed to fetch user details');
                    unlockBtn.disabled = false;
                    unlockBtn.innerHTML = 'Unlock';
                });
            } else {
                const pinError = document.getElementById('pinError');
                if (pinError) {
                    pinError.textContent = 'Incorrect PIN.';
                    pinError.style.display = 'block';
                }
                unlockBtn.disabled = false;
                unlockBtn.innerHTML = 'Unlock';
            }
        })
        .catch(() => {
            const pinError = document.getElementById('pinError');
            if (pinError) {
                pinError.textContent = 'Error validating PIN.';
                pinError.style.display = 'block';
            }
            unlockBtn.disabled = false;
            unlockBtn.innerHTML = 'Unlock';
        });
    });
}
    sortAdminTable();
    
    function initPagination(tableType, rows, totalPages) {
        createPaginationButtons(tableType, totalPages);
        updatePageDisplay(tableType, rows, totalPages, 1);
        
        document.getElementById(`${tableType}PrevBtn`).addEventListener('click', function() {
            if (tableType === 'tourist') {
                if (touristCurrentPage > 1) {
                    touristCurrentPage--;
                    updatePageDisplay(tableType, rows, totalPages, touristCurrentPage);
                }
            } else {
                if (adminCurrentPage > 1) {
                    adminCurrentPage--;
                    updatePageDisplay(tableType, rows, totalPages, adminCurrentPage);
                }
            }
        });
        
        document.getElementById(`${tableType}NextBtn`).addEventListener('click', function() {
            if (tableType === 'tourist') {
                if (touristCurrentPage < totalPages) {
                    touristCurrentPage++;
                    updatePageDisplay(tableType, rows, totalPages, touristCurrentPage);
                }
            } else {
                if (adminCurrentPage < totalPages) {
                    adminCurrentPage++;
                    updatePageDisplay(tableType, rows, totalPages, adminCurrentPage);
                }
            }
        });
    }
    
    function createPaginationButtons(tableType, totalPages) {
        const pageNumbersContainer = document.getElementById(`${tableType}PageNumbers`);
        if (!pageNumbersContainer) return;
        
        pageNumbersContainer.innerHTML = '';
        
        if (totalPages <= 7) {
            for (let i = 1; i <= totalPages; i++) {
                const pageButton = document.createElement('button');
                pageButton.className = 'page-number';
                pageButton.textContent = i;
                pageButton.dataset.page = i;
                
                pageButton.addEventListener('click', function() {
                    const page = parseInt(this.dataset.page);
                    if (tableType === 'tourist') {
                        touristCurrentPage = page;
                        updatePageDisplay(tableType, touristRows, totalPages, page);
                    } else {
                        adminCurrentPage = page;
                        updatePageDisplay(tableType, adminRows, totalPages, page);
                    }
                });
                
                pageNumbersContainer.appendChild(pageButton);
            }
        } else {
            createComplexPagination(tableType, totalPages, 1);
        }
    }
    
    function createComplexPagination(tableType, totalPages, currentPage) {
        const pageNumbersContainer = document.getElementById(`${tableType}PageNumbers`);
        if (!pageNumbersContainer) return;
        
        pageNumbersContainer.innerHTML = '';
        
        const createPageButton = (number) => {
            const button = document.createElement('button');
            button.className = 'page-number';
            button.textContent = number;
            button.dataset.page = number;
            
            if (number === currentPage) {
                button.classList.add('active');
            }
            
            button.addEventListener('click', function() {
                const page = parseInt(this.dataset.page);
                if (tableType === 'tourist') {
                    touristCurrentPage = page;
                    updatePageDisplay(tableType, touristRows, totalPages, page);
                } else {
                    adminCurrentPage = page;
                    updatePageDisplay(tableType, adminRows, totalPages, page);
                }
            });
            
            return button;
        };
        
        const addEllipsis = () => {
            const span = document.createElement('span');
            span.className = 'ellipsis';
            span.textContent = '...';
            pageNumbersContainer.appendChild(span);
        };
        
        pageNumbersContainer.appendChild(createPageButton(1));
        
        if (currentPage > 3) {
            addEllipsis();
        }
        
        for (let i = Math.max(2, currentPage - 1); i <= Math.min(totalPages - 1, currentPage + 1); i++) {
            pageNumbersContainer.appendChild(createPageButton(i));
        }
        
        if (currentPage < totalPages - 2) {
            addEllipsis();
        }
        
        if (totalPages > 1) {
            pageNumbersContainer.appendChild(createPageButton(totalPages));
        }
    }
    
    function updatePageDisplay(tableType, rows, totalPages, currentPage) {
        const prevBtn = document.getElementById(`${tableType}PrevBtn`);
        const nextBtn = document.getElementById(`${tableType}NextBtn`);
        
        if (prevBtn) prevBtn.disabled = currentPage === 1;
        if (nextBtn) nextBtn.disabled = currentPage === totalPages;
        
        rows.forEach(row => {
            row.style.display = 'none';
        });
        
        const startIndex = (currentPage - 1) * ITEMS_PER_PAGE;
        const endIndex = Math.min(startIndex + ITEMS_PER_PAGE, rows.length);
        
        for (let i = startIndex; i < endIndex; i++) {
            if (rows[i]) {
                rows[i].style.display = '';
            }
        }
        
        createComplexPagination(tableType, totalPages, currentPage);
    }

    function updateAccountCounts() {
        $.ajax({
            url: '/admin/api/account-counts',
            method: 'GET',
            success: function(data) {
                $('#totalAccounts').text(data.totalAccounts);
                $('#touristAccounts').text(data.touristAccounts);
                $('#adminAccounts').text(data.adminAccounts);
            },
            error: function(xhr) {
                console.error('Error fetching account counts:', xhr);
            }
        });
    }

    setInterval(updateAccountCounts, 5000);
});
</script>
@endpush