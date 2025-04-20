@extends(backpack_view('blank'))

@php
    $user = isset($user) ? $user : auth()->user();
@endphp

@section('header')
    <!-- Include the custom CSS for this page -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link href="{{ asset('css/my-account.css') }}" rel="stylesheet">
    
    <section class="header-operation container-fluid animated fadeIn d-flex mb-2 align-items-center d-print-none" bp-section="page-header">
        <!-- Profile Avatar -->
        <div class="profile-avatar mr-3">
            @if ($user->profile_image)
                <img src="{{ asset('storage/' . $user->profile_image) }}" alt="Profile Image" class="rounded-circle" width="80" height="80">
            @else
                <img src="{{ asset('img/default-avatar.png') }}" alt="Default Avatar" class="rounded-circle" width="80" height="80">
            @endif
        </div>
        <!-- Title -->
        <div>
            <h1 class="text-capitalize mb-0" bp-section="page-heading">{{ trans('backpack::base.my_account') }}</h1>
        </div>
    </section>
@endsection

@section('content')
    <div class="row">
        <!-- Account Info Section -->
        <div class="col-md-12 col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h4>{{ trans('backpack::base.update_account_info') }}</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('backpack.account.info.store') }}" enctype="multipart/form-data">
                        @csrf
                        @method('POST')

                        <!-- Profile Image -->
                        <div class="form-group">
                            <label for="profile_image" class="font-weight-bold"> Update Profile Image</label>
                            <input type="file" name="profile_image" id="profile_image" class="form-control mb-2">
                            @if ($user->profile_image)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $user->profile_image) }}" alt="Profile Image" width="100" height="100">
                                </div>
                            @endif
                        </div>

                        <!-- Name -->
                        <div class="form-group">
                            <label for="name">{{ trans('backpack::base.name') }}</label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ $user->name }}" required>
                        </div>

                        <!-- Email -->
                        <div class="form-group">
                            <label for="email">{{ trans('backpack::base.email') }}</label>
                            <input type="email" name="email" id="email" class="form-control" value="{{ $user->email }}" required>
                        </div>

                        <!-- Save Button -->
                        <button type="submit" class="btn btn-success">{{ trans('backpack::base.save') }}</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Password Change Section -->
        <div class="col-md-12 col-lg-6">
            <div class="card mt-4 mt-lg-0">
                <div class="card-header">
                    <h4>{{ trans('backpack::base.change_password') }}</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('backpack.account.password') }}">
                        @csrf
                        @method('POST')

                        <!-- Old Password -->
                        <div class="form-group">
                            <label for="old_password">{{ trans('backpack::base.old_password') }}</label>
                            <input type="password" name="old_password" id="old_password" class="form-control" required>
                        </div>

                        <!-- New Password -->
                        <div class="form-group">
                            <label for="new_password">{{ trans('backpack::base.new_password') }}</label>
                            <input type="password" name="new_password" id="new_password" class="form-control" required>
                        </div>

                        <!-- Confirm Password -->
                        <div class="form-group">
                            <label for="confirm_password">{{ trans('backpack::base.confirm_password') }}</label>
                            <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                        </div>

                        <!-- Change Password Button -->
                        <button type="submit" class="btn btn-warning">{{ trans('backpack::base.change_password') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
