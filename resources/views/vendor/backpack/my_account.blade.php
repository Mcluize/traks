@extends(backpack_view('blank'))

@php
    $user = isset($user) ? $user : auth()->user();
    $superAdminController = app(\App\Http\Controllers\Admin\SuperAdminContactController::class);
    $super_admin_contact = $superAdminController->getContactDetails();
@endphp

@section('header')
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link href="{{ asset('css/my-account.css') }}" rel="stylesheet">
    <div class="container-fluid">
        <div class="justify-content-between align-items-left">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb bg-transparent p-0">
                        <li class="breadcrumb-item"><a href="#">Pages</a></li>
                        <li class="breadcrumb-item active" aria-current="page">My Account</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <section class="header-operation container-fluid animated fadeIn d-flex mb-4 align-items-center d-print-none" bp-section="page-header">
        <div class="profile-avatar mr-3">
            @if ($user->profile_image)
                <img src="{{ asset('storage/' . $user->profile_image) }}" alt="Profile Image" class="rounded-circle profile-header-img">
            @else
                <img src="{{ asset('img/default-avatar.png') }}" alt="Default Avatar" class="rounded-circle profile-header-img">
            @endif
        </div>
        <div>
            <h1 class="text-capitalize mb-0 header-title" bp-section="page-heading">My Account</h1>
            <p class="text-muted header-subtitle">Manage your profile information</p>
        </div>
    </section>
@endsection

@section('content')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="container">
        <div class="row">
            <!-- Profile Info Card -->
            <div class="col-lg-8 col-md-7 mb-4">
                <div class="card profile-card h-100">
                    <div class="card-header">
                        <h4 class="card-title">Profile Information</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 text-center mb-4 mb-md-0">
                                <div class="profile-avatar-large mb-3">
                                    @if ($user->profile_image)
                                        <img src="{{ asset('storage/' . $user->profile_image) }}" alt="Profile Image" class="rounded-circle profile-img">
                                    @else
                                        <img src="{{ asset('img/default-avatar.png') }}" alt="Default Avatar" class="rounded-circle profile-img">
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="profile-details">
                                    <div class="info-group">
                                        <label>Name</label>
                                        <h5>{{ $user->name }}</h5>
                                    </div>
                                    <div class="info-group">
                                        <label>Email</label>
                                        <h5>{{ $user->email }}</h5>
                                    </div>
                                    <div class="mt-4">
                                        <button class="btn edit-btn" data-toggle="modal" data-target="#editModal">
                                            <i class="la la-edit"></i> Edit Profile
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Super Admin Contact Card -->
            <div class="col-lg-4 col-md-5 mb-4">
                <div class="card stats-card h-100">
                    <div class="card-header">
                        <h4 class="card-title">Emergency Contact Number</h4>
                    </div>
                    <div class="card-body">
                        <div class="stat-item">
                            <div class="stat-icon">
                                <i class="la la-phone"></i>
                            </div>
                            <div class="stat-info">
                                <label>Contact Number</label>
                                <h5>{{ $super_admin_contact ?? 'Not set' }}</h5>
                            </div>
                        </div>
                        <div class="mt-3">
                            <button class="btn edit-btn" data-toggle="modal" data-target="#editSuperAdminContactModal">
                                <i class="la la-edit"></i> Edit Contact
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Password Management Card -->
            <div class="col-12 mb-4">
                <div class="card security-card">
                    <div class="card-header">
                        <h4 class="card-title">Security Settings</h4>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h5 class="mb-2">Password Management</h5>
                                <p class="text-muted">It's a good idea to use a strong password that you don't use elsewhere</p>
                            </div>
                            <div class="col-md-4 text-md-right">
                                <button class="btn password-btn" data-toggle="modal" data-target="#passwordModal">
                                    <i class="la la-lock"></i> Change Password
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Profile Modal -->
        <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true" data-backdrop="false">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit Profile</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <form method="POST" action="{{ route('backpack.account.info.store') }}" enctype="multipart/form-data" id="profile-form">
                        @csrf
                        @method('POST')
                        <div class="modal-body">
                            <div class="text-center mb-4">
                                <div class="profile-avatar-preview">
                                    @if ($user->profile_image)
                                        <img src="{{ asset('storage/' . $user->profile_image) }}" id="preview-image" alt="Profile Preview" class="rounded-circle">
                                    @else
                                        <img src="{{ asset('img/default-avatar.png') }}" id="preview-image" alt="Default Avatar" class="rounded-circle">
                                    @endif
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="profile_image" class="font-weight-bold">Profile Image</label>
                                <div class="custom-file">
                                    <input type="file" name="profile_image" id="profile_image" class="custom-file-input" onchange="previewImage(this)">
                                </div>
                                <small class="form-text text-muted">Recommended: Square image, at least 300x300px</small>
                            </div>

                            <div class="form-group">
                                <label for="name" class="font-weight-bold">Name</label>
                                <input type="text" name="name" id="name" class="form-control" value="{{ $user->name }}" required>
                            </div>

                            <div class="form-group">
                                <label for="email" class="font-weight-bold">Email</label>
                                <input type="email" name="email" id="email" class="form-control" value="{{ $user->email }}" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn submit-btn">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Super Admin Contact Modal -->
        <div class="modal fade" id="editSuperAdminContactModal" tabindex="-1" role="dialog" aria-labelledby="editSuperAdminContactModalLabel" aria-hidden="true" data-backdrop="false">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editSuperAdminContactModalLabel">Edit Super Admin Contact</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <form method="POST" action="{{ route('superadmin.contact.update') }}" id="super-admin-contact-form">
                        @csrf
                        @method('POST')
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="super_admin_contact" class="font-weight-bold">Contact Number</label>
                                <input type="text" name="super_admin_contact" id="super_admin_contact" class="form-control" value="{{ $super_admin_contact ?? '' }}" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn submit-btn">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Password Change Modal -->
        <div class="modal fade" id="passwordModal" tabindex="-1" role="dialog" aria-labelledby="passwordModalLabel" aria-hidden="true" data-backdrop="false">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="passwordModalLabel">Change Password</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <form method="POST" action="{{ route('backpack.account.password') }}" id="password-form">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="old_password" class="font-weight-bold">Current Password</label>
                                <input type="password" name="old_password" id="old_password" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="new_password" class="font-weight-bold">New Password</label>
                                <input type="password" name="new_password" id="new_password" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="confirm_password" class="font-weight-bold">Confirm New Password</label>
                                <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn password-submit-btn">Update Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                
                reader.onload = function(e) {
                    $('#preview-image').attr('src', e.target.result);
                }
                
                reader.readAsDataURL(input.files[0]);
            }
        }

        $(document).ready(function() {
            // Edit Profile Form Submission with Loading State
            $('#profile-form').on('submit', function(e) {
                e.preventDefault();
                
                var form = $(this);
                var formData = new FormData(form[0]);
                var submitBtn = form.find('.submit-btn');
                
                submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');
                
                $.ajax({
                    url: form.attr('action'),
                    type: form.attr('method'),
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('#editModal').modal('hide');
                        Swal.fire({
                            title: 'Success!',
                            text: 'Your profile has been updated successfully.',
                            icon: 'success',
                            confirmButtonColor: '#0BC8CA'
                        }).then((result) => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            var errorMessage = '';
                            for (var field in errors) {
                                errorMessage += errors[field][0] + '\n';
                            }
                            Swal.fire({
                                title: 'Validation Error',
                                text: errorMessage,
                                icon: 'error',
                                confirmButtonColor: '#FF7E3F'
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Something went wrong. Please try again.',
                                icon: 'error',
                                confirmButtonColor: '#FF7E3F'
                            });
                        }
                    },
                    complete: function() {
                        submitBtn.prop('disabled', false).html('Save Changes');
                    }
                });
            });

            // Edit Super Admin Contact Form Submission with Validation and Loading State
            $('#super-admin-contact-form').on('submit', function(e) {
                var contactNumber = $('#super_admin_contact').val();
                var regex = /^(?:\+639|09)\d{9}$/;

                if (!regex.test(contactNumber)) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Invalid Contact Number',
                        text: 'Please enter valid number starting with +639 or 09 and followed by exactly 9 digits.',
                        icon: 'error',
                        confirmButtonColor: '#FF7E3F'
                    });
                    return;
                }

                
                e.preventDefault();
                
                var form = $(this);
                var formData = new FormData(form[0]);
                var submitBtn = form.find('.submit-btn');
                
                submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');
                
                $.ajax({
                    url: form.attr('action'),
                    type: form.attr('method'),
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('#editSuperAdminContactModal').modal('hide');
                        Swal.fire({
                            title: 'Success!',
                            text: 'Super admin contact has been updated successfully.',
                            icon: 'success',
                            confirmButtonColor: '#0BC8CA'
                        }).then((result) => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Failed to update super admin contact.',
                            icon: 'error',
                            confirmButtonColor: '#FF7E3F'
                        });
                    },
                    complete: function() {
                        submitBtn.prop('disabled', false).html('Save Changes');
                    }
                });
            });

            // Change Password Form Submission with Loading State
            $('#password-form').on('submit', function(e) {
                e.preventDefault();
                
                var form = $(this);
                var formData = new FormData(form[0]);
                var submitBtn = form.find('.password-submit-btn');
                
                submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...');
                
                $.ajax({
                    url: form.attr('action'),
                    type: form.attr('method'),
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $('#passwordModal').modal('hide');
                        Swal.fire({
                            title: 'Success!',
                            text: 'Your password has been updated successfully.',
                            icon: 'success',
                            confirmButtonColor: '#0BC8CA'
                        }).then((result) => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            var errorMessage = '';
                            for (var field in errors) {
                                errorMessage += errors[field][0] + '\n';
                            }
                            Swal.fire({
                                title: 'Validation Error',
                                text: errorMessage,
                                icon: 'error',
                                confirmButtonColor: '#FF7E3F'
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Something went wrong. Please try again.',
                                icon: 'error',
                                confirmButtonColor: '#FF7E3F'
                            });
                        }
                    },
                    complete: function() {
                        submitBtn.prop('disabled', false).html('Update Password');
                    }
                });
            });
        });
    </script>
@endsection