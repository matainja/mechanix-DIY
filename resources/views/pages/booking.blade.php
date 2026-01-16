@extends('layouts.main')

@section('title', 'Booking – Mechanix D.I.Y.')

@section('content')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="{{ asset('assets/css/booking.css') }}">

@endpush
<section class="mx-body">
    <div>
        <!-- Workstation titles -->
        <div class="mx-workstations" id="mxWorkstations">
            <div class="mx-w-title active" data-ws="1">Workstation</div>
            <!-- <div class="mx-w-title" data-ws="2">Workstation II</div> -->
        </div>

        <!-- Mobile Lift Selector -->
        <div class="mx-lift-dropdown d-md-none mb-3">
            <div class="dropdown">
                <button class="mx-liftbtn active dropdown-toggle w-100 d-flex align-items-center justify-content-between"
                    type="button"
                    id="mxLiftDropdownBtn"
                    data-bs-toggle="dropdown"
                    aria-expanded="false">
                    Select Lift Type
                </button>

                <ul class="dropdown-menu w-100" id="mxLiftDropdownMenu">
                    <li>
                        <a class="dropdown-item d-flex align-items-center gap-2" data-lift="four" data-icon="{{ asset('assets/images/icons/four-post.png') }}" href="#">
                            Four-Post Lift
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center gap-2" data-lift="two" data-icon="{{ asset('assets/images/icons/two-post.png') }}" href="#">
                            Two-Post Lift
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center gap-2" data-lift="scissor" data-icon="{{ asset('assets/images/icons/scissor.png') }}" href="#">
                            Scissor Lift
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center gap-2" data-lift="flat" data-icon="{{ asset('assets/images/icons/moto-lift.png') }}" href="#">
                            Motorcycle Lift
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center gap-2" data-lift="flat2" data-icon="{{ asset('assets/images/icons/alignment-rack.png') }}" href="#">
                            Alignment Rack
                        </a>
                    </li>
                </ul>

            </div>
        </div>


        <!-- Lift type buttons row -->
        <div class="mx-liftbar">
            <button class="mx-liftbtn active " data-lift="four">
                <img src="{{ asset('assets/images/icons/four-post.png') }}" class="mx-ic" alt="">
                <span>Four-Post Lift</span>
            </button>

            <button class="mx-liftbtn mx-redmark" data-lift="two">
                <img src="{{ asset('assets/images/icons/two-post.png') }}" class="mx-ic" alt="">
                <span>Two-Post Lift</span>
            </button>

            <button class="mx-liftbtn" data-lift="scissor">
                <img src="{{ asset('assets/images/icons/scissor.png')}}" class="mx-ic" alt="">
                <span>Scissor Lift</span>
            </button>

            <button class="mx-liftbtn" data-lift="flat">
                <img src="{{ asset('assets/images/icons/moto-lift.png')}}" class="mx-ic" alt="">
                <span>Motorcycle Lift</span>
            </button>

            <button class="mx-liftbtn" data-lift="flat2">
                <img src="{{ asset('assets/images/icons/alignment-rack.png')}}" class="mx-ic" alt="">
                <span>Alignment rack</span>
            </button>
        </div>
    </div>
    <div class="mx-wrap container-fluid ">

        <!-- Main content -->
        <div class="mx-main">

            <!-- LEFT: pricing + lift preview -->
            <div class="mx-left" id="liftSection">
                <div class="mx-pricecard mx-selected" id="hoursSection" data-hours="1">
                    <span class="mx-hours">1 Hour</span>
                    <span class="mx-price">$45</span>
                </div>

                <div class="mx-pricecard" data-hours="9">
                    <span class="mx-hours">9 Hours</span>
                    <span class="mx-price">$40 / hour</span>
                </div>

                <div class="mx-pricecard" data-hours="18">
                    <span class="mx-hours">18 Hours</span>
                    <span class="mx-price">$35 / hour</span>
                </div>


                <div class="mx-liftpreview">

                    <div class="mx-liftimg">
                        <img id="mxLiftPreviewImg" src="{{ asset('assets/images/icons/lift-red.png') }}" alt="Lift preview">
                    </div>

                    <ul class="mx-liftpoints" id="mxLiftPoints">
                        <li>Heavy-duty four-post support</li>
                        <li>Ideal for storage & repairs</li>
                        <li>Stable and safe platform</li>
                    </ul>

                </div>

                <div class="mx-leftbottom" id="leftupButton">
                    <button class="mx-bookbig" id="openDayCalendar">Book Now</button>

                </div>
            </div>

            <!-- RIGHT: calendar -->
            <div class="mx-right" id="calendarSection">

                <!--  Flatpickr input (hidden by CSS) -->
                <div class="calendar-box">
                    <input id="bookingDate" type="text" placeholder="Select date" readonly />
                </div>

                <!--  Fixed 800×400 calendar -->
                <div class="calendar-wrap" id="calendarWrap"></div>
                <!--  Time slots view (hidden until user clicks Book) -->

                <div class="mx-timeView" id="mxTimeView" style="display:none;">
                    <div class="mx-timeTop">
                        <button type="button" class="mx-backBtn" id="mxBackToDate">
                            <i class="fa-solid fa-arrow-left"></i> Change date
                        </button>

                        <div class="mx-timeTitle">
                            Select a start time for <span id="mxSelectedDateText">----</span>
                        </div>
                    </div>

                    <div class="mx-timeGrid" id="mxTimeGrid"></div>

                    <div class="mx-timeBottom">
                        <div class="mx-pickedInfo">
                            Start: <b id="mxPickedTimeText">None</b>
                        </div>

                        <button type="button" class="mx-confirmBtn" id="mxContinueBtn" disabled>
                            Continue
                        </button>
                    </div>
                </div>

                <!--  Modal for hours/package -->
                <div id="mxSlotModal" class="mx-modal-overlay" aria-hidden="true">
                    <div class="mx-modal-card" role="dialog" aria-modal="true" aria-labelledby="mxModalTitle">
                        <div class="mx-modal-head">
                            <div>
                                <div id="mxModalTitle" class="mx-modal-title">Confirm booking</div>
                                <div class="mx-modal-sub">Choose package & hours (must be continuous).</div>
                            </div>
                            <button type="button" class="mx-modal-x" id="mxModalClose" aria-label="Close">×</button>
                        </div>

                        <div class="mx-modal-body">
                            <div class="mx-info-row">
                                <div class="mx-info-label">Selected</div>
                                <div class="mx-info-value" id="mxSlotText">—</div>
                            </div>

                            <!-- <div class="mx-info-row">
                                    <div class="mx-info-label">Package</div>
                                    <div class="mx-info-value mx-packages">
                                        <label class="mx-pack">
                                            <input type="radio" name="mxPack" value="1" checked>
                                            <span>1 Hour • $45</span>
                                        </label>

                                        <label class="mx-pack">
                                            <input type="radio" name="mxPack" value="9">
                                            <span>9 Hours • $40/hr</span>
                                        </label>

                                        <label class="mx-pack">
                                            <input type="radio" name="mxPack" value="18">
                                            <span>18 Hours • $35/hr</span>
                                        </label>
                                    </div>
                                </div> -->

                            <div class="mx-info-row">
                                <div class="mx-info-label">Hours</div>
                                <div class="mx-info-value">
                                    <button type="button" class="mx-gbtn" id="mxHMinus">−</button>
                                    <span id="mxSelectedHours" class="mx-hours-pill">1</span>
                                    <button type="button" class="mx-gbtn" id="mxHPlus">+</button>
                                </div>
                            </div>

                            <div class="mx-hint" id="mxHintText">Continuous booking required.</div>

                            <div class="mx-total">
                                Total: <b id="mxTotalText">$0</b>
                            </div>
                        </div>

                        <div class="mx-modal-actions">
                            <button type="button" class="mx-btn-outline" id="mxModalCancel">Cancel</button>
                            <button type="button" class="mx-btn-solid" id="mxModalConfirm">Confirm</button>
                        </div>
                    </div>
                </div>



                <div class="mx-gridWrap">
                    <div class="mx-gridHead" id="mxGridHead"></div>
                    <div class="mx-gridBody" id="mxGridBody"></div>
                </div>



                <div class="mx-legendMini">
                    <span><i class="mx-box green"></i> Available</span>
                    <span><i class="mx-box red"></i> Booked</span>
                    <span><i class="mx-box grey"></i> Unavailable</span>
                </div>
                <!-- for mobile view -->
                <div class="mx-leftbottom cal-sub-btn" id="bookclose">
                    <button class="mx-bookbig" id="openDayCalendarMb">Book Now</button>
                </div>
            </div>


        </div>
        <!-- </div> -->
</section>
<div class="mx-demo-ribbon" aria-label="Demo Mode">
    DEMO
    <small>Work in progress</small>
</div>

<div id="mx-auth-state"
    data-logged-in="{{ auth()->check() ? '1' : '0' }}">
</div>
<div id="mx-routes"
    data-login-url="{{ route('popup.login') }}"
    data-register-url="{{ route('popup.register') }}">
</div>
{{-- Auth Modal --}}
<div class="modal fade" id="mxAuthModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Continue to Book</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                {{-- Tabs --}}
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#mxTabLogin" type="button" role="tab">
                            Login
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#mxTabRegister" type="button" role="tab">
                            Register
                        </button>
                    </li>
                </ul>

                <div class="tab-content pt-3">

                    {{-- LOGIN --}}
                    <div class="tab-pane fade show active" id="mxTabLogin" role="tabpanel">
                        <div id="mxLoginErr" class="alert alert-danger d-none"></div>

                        <form id="mxLoginForm">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" class="form-control" name="password" required>
                            </div>

                            <button class="btn btn-primary w-100" type="submit">Login</button>
                        </form>
                    </div>

                    {{-- REGISTER --}}
                    <div class="tab-pane fade" id="mxTabRegister" role="tabpanel">
                        <div id="mxRegErr" class="alert alert-danger d-none"></div>

                        <form id="mxRegisterForm">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Mobile No</label>
                                <input type="text" class="form-control" name="mobile_no" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Address</label>
                                <textarea class="form-control" name="address" rows="2" required></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" class="form-control" name="password" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" name="password_confirmation" required>
                            </div>

                            <button class="btn btn-success w-100" type="submit">Create account</button>
                        </form>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="{{ asset('assets/js/booking.js') }}"></script>


@endpush