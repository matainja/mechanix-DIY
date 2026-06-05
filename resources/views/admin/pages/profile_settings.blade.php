@extends('admin.layouts.admin')

@section('title', 'Profile Settings')

@section('content')
    <div class="pc-container">
        <div class="pc-content">

            {{-- Breadcrumb --}}
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h5 class="m-b-10">Profile Settings</h5>
                            </div>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('user.profile.settings') }}">Profile</a></li>
                                <li class="breadcrumb-item" aria-current="page">Settings</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Session Messages --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row g-4">

                {{-- LEFT: Profile Info Card --}}
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center py-4"><br>

                            <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center
                                    justify-content-center mx-auto mb-3"
                                style="width:88px;height:88px;">
                                <span class="fw-bold text-primary" style="font-size:2rem;">
                                    {{ strtoupper(substr(auth()->user()->email, 0, 1)) }}
                                </span>
                            </div>

                            <h6 class="mb-1 fw-semibold">{{ auth()->user()->email }}</h6>
                            <p class="text-muted small mb-3">
                                {{ auth()->user()->mobile_no ?? 'No phone set' }}
                            </p>

                            <div class="d-flex justify-content-center gap-2 flex-wrap">
                                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2">
                                    <i class="ti ti-user me-1"></i> Registered User
                                </span>
                                <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2">
                                    <i class="ti ti-calendar me-1"></i>
                                    Since {{ auth()->user()->created_at->format('M Y') }}
                                </span>
                            </div>

                        </div>

                        <div class="card-footer bg-transparent px-4 pb-4 border-top-0">
                            <ul class="list-unstyled mb-0">
                                <li class="d-flex align-items-center gap-2 py-2 border-bottom">
                                    <i class="ti ti-mail text-muted"></i>
                                    <span class="text-muted small">Email</span>
                                    <span class="ms-auto small fw-medium text-truncate" style="max-width:160px;">
                                        {{ auth()->user()->email }}
                                    </span>
                                </li>
                                <li class="d-flex align-items-center gap-2 py-2 border-bottom">
                                    <i class="ti ti-phone text-muted"></i>
                                    <span class="text-muted small">Mobile</span>
                                    <span class="ms-auto small fw-medium">
                                        {{ auth()->user()->mobile_no ?? '—' }}
                                    </span>
                                </li>
                                <li class="d-flex align-items-center gap-2 py-2">
                                    <i class="ti ti-calendar-check text-muted"></i>
                                    <span class="text-muted small">Total Bookings</span>
                                    <span class="ms-auto small fw-medium">
                                        {{ number_format($confirmedCount + $pendingCount + $cancelledCount) }}
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- RIGHT: Edit Profile Card --}}
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent border-bottom py-3">
                            <h6 class="mb-0 fw-semibold">
                                <i class="ti ti-edit me-2 text-primary"></i>Edit Profile
                            </h6>
                        </div>
                        <div class="card-body">

                            <form method="POST" action="{{ route('user.profile.update') }}">
                                @csrf
                                @method('PUT')

                                <div class="row g-3">

                                    {{-- Email --}}
                                    <div class="col-md-6">
                                        <label class="form-label small fw-medium">Email Address</label>
                                        <input type="email" name="email"
                                            class="form-control @error('email') is-invalid @enderror"
                                            value="{{ old('email', auth()->user()->email) }}" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Mobile --}}
                                    <div class="col-md-6">
                                        <label class="form-label small fw-medium">Mobile Number</label>
                                        <input type="text" name="mobile_no"
                                            class="form-control @error('mobile_no') is-invalid @enderror"
                                            value="{{ old('mobile_no', auth()->user()->mobile_no) }}"
                                            placeholder="+1 234 567 890">
                                        @error('mobile_no')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                </div>

                                <hr class="my-4">

                                <h6 class="fw-semibold mb-1">Change Password</h6>
                                <p class="text-muted small mb-3">Leave blank to keep your current password.</p>

                                <div class="row g-3">

                                    <div class="col-md-4">
                                        <label class="form-label small fw-medium">Current Password</label>
                                        <div class="position-relative">
                                            <input type="password" id="current_password" name="current_password"
                                                class="form-control pe-5 @error('current_password') is-invalid @enderror"
                                                placeholder="Enter current password">
                                            <i class="ti ti-eye position-absolute top-50 end-0 translate-middle-y me-3"
                                                style="cursor:pointer;z-index:5;color:#94a3b8;"
                                                onclick="togglePassword('current_password', this)"></i>
                                        </div>
                                        @error('current_password')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label small fw-medium">New Password</label>
                                        <div class="position-relative">
                                            <input type="password" id="password" name="password"
                                                class="form-control pe-5 @error('password') is-invalid @enderror"
                                                placeholder="Enter new password">
                                            <i class="ti ti-eye position-absolute top-50 end-0 translate-middle-y me-3"
                                                style="cursor:pointer;z-index:5;color:#94a3b8;"
                                                onclick="togglePassword('password', this)"></i>
                                        </div>
                                        @error('password')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label small fw-medium">Confirm New Password</label>
                                        <div class="position-relative">
                                            <input type="password" id="password_confirmation"
                                                name="password_confirmation" class="form-control pe-5"
                                                placeholder="Re-enter new password">
                                            <i class="ti ti-eye position-absolute top-50 end-0 translate-middle-y me-3"
                                                style="cursor:pointer;z-index:5;color:#94a3b8;"
                                                onclick="togglePassword('password_confirmation', this)"></i>
                                        </div>
                                    </div>

                                </div>

                                <div class="d-flex align-items-center gap-3 mt-4 pt-2 border-top">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ti ti-device-floppy me-1"></i> Save Changes
                                    </button>
                                    <a href="{{ route('user.dashboard') }}" class="btn btn-outline-secondary">
                                        Cancel
                                    </a>
                                </div>

                            </form>

                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
    <script>
        function togglePassword(id, icon) {
            const input = document.getElementById(id);
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('ti-eye');
                icon.classList.add('ti-eye-off');
            } else {
                input.type = 'password';
                icon.classList.remove('ti-eye-off');
                icon.classList.add('ti-eye');
            }
        }
    </script>
@endsection
