@extends('layouts.main')

@section('title', 'Booking – Mechanix D.I.Y.')

@section('content')

    @push('styles')
        {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css"> --}}
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
                    <button
                        class="mx-liftbtn active dropdown-toggle w-100 d-flex align-items-center justify-content-between"
                        type="button" id="mxLiftDropdownBtn" data-bs-toggle="dropdown" aria-expanded="false">
                        Select Lift Type
                    </button>

                    <ul class="dropdown-menu w-100" id="mxLiftDropdownMenu">
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2" data-lift="four"
                                data-icon="{{ asset('assets/images/icons/four-post.png') }}" href="#">
                                Four-Post Lift
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2" data-lift="two"
                                data-icon="{{ asset('assets/images/icons/two-post.png') }}" href="#">
                                Two-Post Lift
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2" data-lift="scissor"
                                data-icon="{{ asset('assets/images/icons/scissor.png') }}" href="#">
                                Scissor Lift
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2" data-lift="flat"
                                data-icon="{{ asset('assets/images/icons/moto-lift.png') }}" href="#">
                                Motorcycle Lift
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2" data-lift="flat2"
                                data-icon="{{ asset('assets/images/icons/alignment-rack.png') }}" href="#">
                                Alignment Rack
                            </a>
                        </li>
                    </ul>

                </div>
            </div>


            <!-- Lift type buttons row -->
            <div class="mx-liftbar">
                <button class="mx-liftbtn active " data-lift="four">
                    <img src="{{ asset('assets/images/icons/fourpost.jpg') }}" class="mx-ic" alt="">
                    <span>Four-Post Lift</span>
                </button>

                <button class="mx-liftbtn mx-redmark" data-lift="two">
                    <img src="{{ asset('assets/images/icons/twopost.jpg') }}" class="mx-ic" alt="">
                    <span>Two-Post Lift</span>
                </button>

                <button class="mx-liftbtn" data-lift="scissor">
                    <img src="{{ asset('assets/images/icons/scissor.jpg') }}" class="mx-ic" alt="">
                    <span>Scissor Lift</span>
                </button>

                <button class="mx-liftbtn" data-lift="flat">
                    <img src="{{ asset('assets/images/icons/motocycle.jpg') }}" class="mx-ic" alt="">
                    <span>Motorcycle Lift</span>
                </button>

                <button class="mx-liftbtn" data-lift="flat2">
                    <img src="{{ asset('assets/images/icons/alignmentrack.jpg') }}" class="mx-ic" alt="">
                    <span>Alignment rack</span>
                </button>
            </div>
        </div>
        <div class="mx-wrap container-fluid ">

            <!-- Main content -->
            <div class="mx-main">

           {{-- LEFT: pricing + lift preview --}}
<div class="mx-left" id="liftSection">

    {{-- ============================= --}}
    {{-- IF PRODUCT SELECTED → DB DATA --}}
    {{-- ============================= --}}
    @if($product)

        @foreach($product->prices as $price)
            <div
                class="mx-pricecard {{ $loop->first ? 'mx-selected' : '' }}"
                data-hours="{{ $price->hours }}"
                data-price="{{ $price->price }}"
                data-total="{{ $price->price * $price->hours }}"
            >
                <span class="mx-hours">
                    {{ $price->hours }} Hour{{ $price->hours > 1 ? 's' : '' }}
                </span>

                <span class="mx-price">
                    $ {{ $price->price }}
                    @if($price->hours > 1)
                        / hour
                    @endif
                </span>
            </div>
        @endforeach


        @php
            $defaultImage =
                $product->images->firstWhere('is_default',1)
                ?? $product->images->first();
        @endphp

        <div class="mx-liftpreview">
            <div class="mx-liftimg">
                <img
                    id="mxLiftPreviewImg"
                    src="{{ $defaultImage ? asset('storage/'.$defaultImage->image_path) : asset('assets/images/no-image.png') }}"
                    alt="{{ $product->name }}"
                >
            </div>

            <ul class="mx-liftpoints" id="mxLiftPoints">
                @foreach(explode("\n", $product->description ?? '') as $line)
                    @if(trim($line))
                        <li>{{ $line }}</li>
                    @endif
                @endforeach
            </ul>
        </div>


    {{-- ================================= --}}
    {{-- NO PRODUCT → SHOW OLD STATIC UI --}}
    {{-- ================================= --}}
    @else

        <div class="mx-pricecard mx-selected" data-hours="1" data-price="45">
            <span class="mx-hours">1 Hour</span>
            <span class="mx-price">$45</span>
        </div>

        <div class="mx-pricecard" data-hours="9" data-price="40">
            <span class="mx-hours">9 Hours</span>
            <span class="mx-price">$35 / hour</span>
        </div>

        {{-- <div class="mx-pricecard" data-hours="18" data-price="35">
            <span class="mx-hours">18 Hours</span>
            <span class="mx-price">$35 / hour</span>
        </div> --}}

        <a href="{{ route('membership') }}" class="mx-pricecard-link">
  <div class="mx-pricecard" data-hours="18" data-price="35">
      <span class="mx-hours">18 Hours</span>
      <span class="mx-price"></span>
  </div>
</a>

        <div class="mx-liftpreview">
            <div class="mx-liftimg">
                <img
                    id="mxLiftPreviewImg"
                    src="{{ asset('assets/images/icons/lift-red.png') }}"
                    alt="Lift preview"
                >
            </div>

            <ul class="mx-liftpoints" id="mxLiftPoints">
                <li>Heavy-duty four-post support</li>
                <li>Ideal for storage & repairs</li>
                <li>Stable and safe platform</li>
            </ul>
        </div>

    @endif


    {{-- ================= BOOK BUTTON ================= --}}
    <div class="mx-leftbottom" id="leftupButton">
        <button class="mx-bookbig" id="openDayCalendar">
            Book Now
        </button>
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
                                <button type="button" class="mx-modal-x" id="mxModalClose"
                                    aria-label="Close">×</button>
                            </div>

                            <div class="mx-modal-body">
                                <div class="mx-info-row">
                                    <div class="mx-info-label">Selected</div>
                                    <div class="mx-info-value" id="mxSlotText">—</div>
                                </div>

                                {{-- <div class="mx-info-row">
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
                                    </div> --}}

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

            {{--
    ═══════════════════════════════════════════════════════════
    PASTE THESE THREE MODALS anywhere inside @section('content')
    just before the closing </section> or after the slot modal.
    They are full-screen overlays so placement in DOM is fine.
    ═══════════════════════════════════════════════════════════
--}}


{{-- ╔══════════════════════════════════════════╗
     ║  1. SUMMARY MODAL  (#mxSummaryModal)     ║
     ╚══════════════════════════════════════════╝ --}}
<div id="mxSummaryModal" class="mx-modal-overlay" aria-hidden="true">
    <div class="mx-modal-card mx-summary-card" role="dialog" aria-modal="true" aria-labelledby="mxSummaryTitle">

        {{-- Header --}}
        <div class="mx-modal-head">
            <div>
                <div id="mxSummaryTitle" class="mx-modal-title">Booking Summary</div>
                <div class="mx-modal-sub">Review your details before payment.</div>
            </div>
            <button type="button" class="mx-modal-x" id="mxSummaryClose" aria-label="Close">×</button>
        </div>

        {{-- Body --}}
        <div class="mx-modal-body">

            {{-- Receipt-style rows --}}
            <div class="mx-receipt">

                <div class="mx-receipt-row">
                    <span class="mx-receipt-label">Workstation</span>
                    <span class="mx-receipt-value" id="mxsWorkstation">—</span>
                </div>

                <div class="mx-receipt-row">
                    <span class="mx-receipt-label">Lift Type</span>
                    <span class="mx-receipt-value" id="mxsLift">—</span>
                </div>

                <div class="mx-receipt-divider"></div>

                <div class="mx-receipt-row">
                    <span class="mx-receipt-label">Date</span>
                    <span class="mx-receipt-value" id="mxsDate">—</span>
                </div>

                <div class="mx-receipt-row">
                    <span class="mx-receipt-label">Start Time</span>
                    <span class="mx-receipt-value" id="mxsStart">—</span>
                </div>

                <div class="mx-receipt-row">
                    <span class="mx-receipt-label">Duration</span>
                    <span class="mx-receipt-value" id="mxsDuration">—</span>
                </div>

                <div class="mx-receipt-row">
                    <span class="mx-receipt-label">End Time</span>
                    <span class="mx-receipt-value" id="mxsEnd">—</span>
                </div>

                <div class="mx-receipt-divider"></div>

                <div class="mx-receipt-row">
                    <span class="mx-receipt-label">Rate</span>
                    <span class="mx-receipt-value" id="mxsRate">—</span>
                </div>

                <div class="mx-receipt-row">
                    <span class="mx-receipt-label">Hours</span>
                    <span class="mx-receipt-value" id="mxsHours">—</span>
                </div>

                <div class="mx-receipt-row mx-receipt-total">
                    <span class="mx-receipt-label">Total</span>
                    <span class="mx-receipt-value" id="mxsTotal">—</span>
                </div>

            </div>

            {{-- Info note --}}
            <p class="mx-summary-note">
                <i class="fa-solid fa-circle-info"></i>
                Payment is collected on the next screen. Booking is confirmed only after successful payment.
            </p>

        </div>

        {{-- Footer Actions --}}
        <div class="mx-modal-actions">
            <button type="button" class="mx-btn-outline" id="mxSummaryBack">
                <i class="fa-solid fa-arrow-left"></i> Edit
            </button>
            <button type="button" class="mx-btn-solid" id="mxSummaryPay">
                Pay Now &nbsp;<i class="fa-solid fa-lock"></i>
            </button>
        </div>

    </div>
</div>


{{-- ╔══════════════════════════════════════════╗
     ║  2. PAY MODAL  (#mxPayModal)             ║
     ╚══════════════════════════════════════════╝ --}}
<div id="mxPayModal" class="mx-modal-overlay" aria-hidden="true">
    <div class="mx-modal-card mx-pay-card" role="dialog" aria-modal="true" aria-labelledby="mxPayTitle">

        {{-- Header --}}
        <div class="mx-modal-head">
            <div>
                <div id="mxPayTitle" class="mx-modal-title">Secure Payment</div>
                <div class="mx-modal-sub">
                    Amount due: <strong id="mxPayAmount">$0</strong>
                </div>
            </div>
            <button type="button" class="mx-modal-x" id="mxPayClose" aria-label="Close">×</button>
        </div>

        {{-- Body --}}
        <div class="mx-modal-body">

            {{-- Payment method tabs --}}
            <div class="mxs-pay-tabs">
                <button type="button" class="mxs-pay-tab active" data-tab="card">
                    <i class="fa-regular fa-credit-card"></i> Card
                </button>
                <button type="button" class="mxs-pay-tab" data-tab="upi">
                    <i class="fa-solid fa-mobile-screen-button"></i> UPI
                </button>
                <button type="button" class="mxs-pay-tab" data-tab="netbanking">
                    <i class="fa-solid fa-building-columns"></i> Net Banking
                </button>
            </div>

            {{-- Error --}}
            <div id="mxPayError" class="mx-pay-error d-none"></div>

            {{-- ── Card Panel ── --}}
            <div class="mxs-pay-panel active" id="mxPayPanel-card">
                <div class="mx-card-preview">
                    <div class="mx-card-chip"></div>
                    <div class="mx-card-number-display" id="mxCardDisplay">•••• •••• •••• ••••</div>
                    <div class="mx-card-bottom">
                        <div>
                            <div class="mx-card-meta-label">Card Holder</div>
                            <div class="mx-card-meta-value" id="mxCardNameDisplay">YOUR NAME</div>
                        </div>
                        <div>
                            <div class="mx-card-meta-label">Expires</div>
                            <div class="mx-card-meta-value" id="mxCardExpDisplay">MM / YY</div>
                        </div>
                    </div>
                </div>

                <div class="mx-pay-fields">
                    <div class="mx-field-wrap full">
                        <label>Card Number</label>
                        <input type="text" id="mxCardNum" class="mx-pay-input" placeholder="1234 5678 9012 3456" maxlength="19" inputmode="numeric">
                    </div>
                    <div class="mx-field-wrap full">
                        <label>Cardholder Name</label>
                        <input type="text" id="mxCardName" class="mx-pay-input" placeholder="Name on card">
                    </div>
                    <div class="mx-field-wrap half">
                        <label>Expiry</label>
                        <input type="text" id="mxCardExp" class="mx-pay-input" placeholder="MM / YY" maxlength="7" inputmode="numeric">
                    </div>
                    <div class="mx-field-wrap half">
                        <label>CVV</label>
                        <input type="password" id="mxCardCvv" class="mx-pay-input" placeholder="•••" maxlength="3" inputmode="numeric">
                    </div>
                </div>
            </div>

            {{-- ── UPI Panel ── --}}
            <div class="mxs-pay-panel" id="mxPayPanel-upi">
                <div class="mx-upi-wrap">
                    <i class="fa-solid fa-mobile-screen-button mx-upi-icon"></i>
                    <p class="mx-upi-label">Enter your UPI ID</p>
                    <input type="text" class="mx-pay-input" id="mxUpiId" placeholder="yourname@upi" style="max-width:280px;margin:0 auto;display:block;">
                    <p class="mx-upi-hint">e.g. name@okaxis, name@ybl, name@paytm</p>
                </div>
            </div>

            {{-- ── Net Banking Panel ── --}}
            <div class="mxs-pay-panel" id="mxPayPanel-netbanking">
                <div class="mx-nb-grid">
                    @foreach(['SBI','HDFC','ICICI','Axis','Kotak','Yes Bank','PNB','BOB'] as $bank)
                    <label class="mx-nb-option">
                        <input type="radio" name="mxBank" value="{{ strtolower(str_replace(' ','',$bank)) }}">
                        <span>{{ $bank }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

        </div>

        {{-- Footer --}}
        <div class="mx-modal-actions">
            <div class="mx-secure-badge">
                <i class="fa-solid fa-shield-halved"></i> 256-bit SSL secured
            </div>
            <button type="button" class="mx-btn-solid mx-pay-btn" id="mxPayNowBtn">
                <span id="mxPayBtnText">Pay <span id="mxPayBtnAmt">$0</span></span>
                <span id="mxPaySpinner" class="mx-spinner d-none"></span>
            </button>
        </div>

    </div>
</div>


{{-- ╔══════════════════════════════════════════╗
     ║  3. SUCCESS MODAL  (#mxSuccessModal)     ║
     ╚══════════════════════════════════════════╝ --}}
<div id="mxSuccessModal" class="mx-modal-overlay" aria-hidden="true">
    <div class="mx-modal-card mx-success-card" role="dialog" aria-modal="true" aria-labelledby="mxSuccessTitle">

        {{-- Success animation --}}
        <div class="mx-success-anim">
            <svg class="mx-checkmark" viewBox="0 0 52 52" xmlns="http://www.w3.org/2000/svg">
                <circle class="mx-checkmark-circle" cx="26" cy="26" r="25" fill="none"/>
                <path class="mx-checkmark-check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
            </svg>
        </div>

        <div id="mxSuccessTitle" class="mx-success-title">Booking Confirmed!</div>
        <div class="mx-success-sub">
            Your booking ID is <strong id="mxSuccessBookingId">—</strong>
        </div>

        {{-- Receipt --}}
        <div class="mx-receipt mx-success-receipt">

            <div class="mx-receipt-row">
                <span class="mx-receipt-label">Workstation</span>
                <span class="mx-receipt-value" id="mxrWorkstation">—</span>
            </div>

            <div class="mx-receipt-row">
                <span class="mx-receipt-label">Lift</span>
                <span class="mx-receipt-value" id="mxrLift">—</span>
            </div>

            <div class="mx-receipt-divider"></div>

            <div class="mx-receipt-row">
                <span class="mx-receipt-label">Date</span>
                <span class="mx-receipt-value" id="mxrDate">—</span>
            </div>

            <div class="mx-receipt-row">
                <span class="mx-receipt-label">Start</span>
                <span class="mx-receipt-value" id="mxrStart">—</span>
            </div>

            <div class="mx-receipt-row">
                <span class="mx-receipt-label">Duration</span>
                <span class="mx-receipt-value" id="mxrDuration">—</span>
            </div>

            <div class="mx-receipt-row">
                <span class="mx-receipt-label">End</span>
                <span class="mx-receipt-value" id="mxrEnd">—</span>
            </div>

            <div class="mx-receipt-divider"></div>

            <div class="mx-receipt-row">
                <span class="mx-receipt-label">Rate</span>
                <span class="mx-receipt-value" id="mxrRate">—</span>
            </div>

            <div class="mx-receipt-row mx-receipt-total">
                <span class="mx-receipt-label">Total Paid</span>
                <span class="mx-receipt-value" id="mxrTotal">—</span>
            </div>

        </div>

        {{-- Actions --}}
        <div class="mx-modal-actions mx-success-actions">
            <button type="button" class="mx-btn-outline" id="mxPrintBtn">
                <i class="fa-solid fa-print"></i> Print Receipt
            </button>
            <button type="button" class="mx-btn-solid" onclick="location.reload()">
                Done
            </button>
        </div>

    </div>
</div>


{{-- ═══════════════════════════════════════════════════════════
     CSS — paste into booking.css  (or a <style> block in blade)
     ═══════════════════════════════════════════════════════════ --}}
<style>
/* ── Modal shared overlay ─────────────────────────────── */
.mx-modal-overlay {
    display: none;
    position: fixed; inset: 0; z-index: 9999;
    background: rgba(0,0,0,.55);
    backdrop-filter: blur(4px);
    align-items: center; justify-content: center;
    padding: 16px;
}
.mx-modal-overlay.show { display: flex; }

.mx-modal-card {
    background: #fff;
    border-radius: 18px;
    width: 100%; max-width: 480px;
    box-shadow: 0 24px 64px rgba(0,0,0,.22);
    animation: mxSlideUp .28s cubic-bezier(.34,1.56,.64,1);
    overflow: hidden;
    display: flex; flex-direction: column;
    max-height: 92vh;
}
@keyframes mxSlideUp {
    from { opacity:0; transform: translateY(32px) scale(.97); }
    to   { opacity:1; transform: translateY(0)     scale(1);  }
}

.mx-modal-head {
    display: flex; justify-content: space-between; align-items: flex-start;
    padding: 22px 24px 16px;
    border-bottom: 1px solid #f0f0f0;
    flex-shrink: 0;
}
.mx-modal-title { font-size: 1.15rem; font-weight: 700; color: #111; }
.mx-modal-sub   { font-size: .83rem; color: #777; margin-top: 2px; }
.mx-modal-x {
    background: none; border: none; font-size: 1.5rem; line-height: 1;
    color: #999; cursor: pointer; padding: 0 0 0 12px;
    transition: color .15s;
}
.mx-modal-x:hover { color: #e8282b; }

.mx-modal-body {
    padding: 20px 24px;
    overflow-y: auto;
    flex: 1;
}

.mx-modal-actions {
    display: flex; align-items: center; gap: 10px;
    padding: 16px 24px 20px;
    border-top: 1px solid #f0f0f0;
    flex-shrink: 0;
}
.mx-btn-outline {
    flex: 1; padding: 10px 18px; border-radius: 10px;
    border: 1.5px solid #ddd; background: #fff;
    font-size: .9rem; font-weight: 600; color: #444;
    cursor: pointer; transition: border-color .15s, color .15s;
    display: flex; align-items: center; justify-content: center; gap: 6px;
}
.mx-btn-outline:hover { border-color: #e8282b; color: #e8282b; }
.mx-btn-solid {
    flex: 1; padding: 10px 18px; border-radius: 10px;
    background: #e8282b; border: none; color: #fff;
    font-size: .9rem; font-weight: 700; cursor: pointer;
    transition: background .15s, opacity .15s;
    display: flex; align-items: center; justify-content: center; gap: 6px;
}
.mx-btn-solid:hover   { background: #c9191c; }
.mx-btn-solid:disabled{ opacity: .55; cursor: not-allowed; }

/* ── Receipt rows ─────────────────────────────────────── */
.mx-receipt { display: flex; flex-direction: column; gap: 0; }
.mx-receipt-row {
    display: flex; justify-content: space-between; align-items: center;
    padding: 8px 0; border-bottom: 1px dashed #f0f0f0;
}
.mx-receipt-row:last-child { border-bottom: none; }
.mx-receipt-label { font-size: .82rem; color: #888; font-weight: 500; }
.mx-receipt-value { font-size: .88rem; color: #222; font-weight: 600; text-align: right; }
.mx-receipt-divider { height: 8px; }
.mx-receipt-total .mx-receipt-label,
.mx-receipt-total .mx-receipt-value {
    font-size: 1rem; color: #111; font-weight: 800;
}
.mx-receipt-total .mx-receipt-value { color: #e8282b; }

/* ── Summary note ─────────────────────────────────────── */
.mx-summary-note {
    margin: 14px 0 0; font-size: .78rem; color: #888;
    background: #fafafa; border: 1px solid #eee;
    border-radius: 8px; padding: 10px 12px;
    display: flex; gap: 8px; align-items: flex-start; line-height: 1.45;
}
.mx-summary-note i { color: #e8282b; margin-top: 2px; flex-shrink: 0; }

/* ── Pay Modal – tabs ─────────────────────────────────── */
.mxs-pay-tabs {
    display: flex; gap: 6px;
    background: #f5f5f5; border-radius: 10px; padding: 4px;
    margin-bottom: 16px;
}
.mxs-pay-tab {
    flex: 1; padding: 7px 4px; border-radius: 7px;
    border: none; background: transparent;
    font-size: .8rem; font-weight: 600; color: #888;
    cursor: pointer; transition: background .18s, color .18s;
    display: flex; align-items: center; justify-content: center; gap: 5px;
}
.mxs-pay-tab.active { background: #fff; color: #e8282b; box-shadow: 0 1px 4px rgba(0,0,0,.1); }

.mxs-pay-panel { display: none; }
.mxs-pay-panel.active { display: block; }

/* ── Card preview ─────────────────────────────────────── */
.mx-card-preview {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
    border-radius: 14px; padding: 20px 22px 18px;
    color: #fff; margin-bottom: 18px; position: relative; overflow: hidden;
}
.mx-card-preview::before {
    content: ''; position: absolute; top: -30px; right: -30px;
    width: 120px; height: 120px; border-radius: 50%;
    background: rgba(255,255,255,.05);
}
.mx-card-chip {
    width: 36px; height: 26px; border-radius: 5px;
    background: linear-gradient(135deg, #e8c170, #c89b3c);
    margin-bottom: 18px;
}
.mx-card-number-display {
    font-size: 1.12rem; letter-spacing: .15em; font-family: 'Courier New', monospace;
    margin-bottom: 16px; opacity: .9;
}
.mx-card-bottom { display: flex; justify-content: space-between; }
.mx-card-meta-label { font-size: .62rem; opacity: .6; text-transform: uppercase; letter-spacing: .08em; }
.mx-card-meta-value { font-size: .85rem; font-weight: 600; letter-spacing: .05em; margin-top: 2px; }

/* ── Pay fields ───────────────────────────────────────── */
.mx-pay-fields { display: flex; flex-wrap: wrap; gap: 10px; }
.mx-field-wrap { display: flex; flex-direction: column; gap: 4px; }
.mx-field-wrap.full  { width: 100%; }
.mx-field-wrap.half  { width: calc(50% - 5px); }
.mx-field-wrap label { font-size: .75rem; font-weight: 600; color: #555; }
.mx-pay-input {
    padding: 9px 12px; border: 1.5px solid #e0e0e0; border-radius: 8px;
    font-size: .9rem; transition: border-color .15s;
    outline: none; width: 100%;
}
.mx-pay-input:focus { border-color: #e8282b; }

/* ── UPI panel ────────────────────────────────────────── */
.mx-upi-wrap { text-align: center; padding: 10px 0 4px; }
.mx-upi-icon { font-size: 2.4rem; color: #e8282b; margin-bottom: 10px; }
.mx-upi-label { font-size: .95rem; font-weight: 600; color: #333; margin-bottom: 12px; }
.mx-upi-hint  { font-size: .75rem; color: #aaa; margin-top: 8px; }

/* ── Net Banking ──────────────────────────────────────── */
.mx-nb-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
.mx-nb-option {
    display: flex; align-items: center; gap: 8px;
    padding: 9px 12px; border: 1.5px solid #eee; border-radius: 8px;
    cursor: pointer; font-size: .85rem; font-weight: 600; color: #333;
    transition: border-color .15s, background .15s;
}
.mx-nb-option:has(input:checked) { border-color: #e8282b; background: #fff5f5; color: #e8282b; }
.mx-nb-option input { accent-color: #e8282b; }

/* ── Pay error ────────────────────────────────────────── */
.mx-pay-error {
    background: #fff0f0; border: 1px solid #ffd0d0; border-radius: 8px;
    padding: 9px 12px; font-size: .83rem; color: #c9191c;
    margin-bottom: 12px;
}

/* ── Secure badge ─────────────────────────────────────── */
.mx-secure-badge {
    font-size: .73rem; color: #aaa; display: flex; align-items: center; gap: 4px;
    flex: 1;
}
.mx-secure-badge i { color: #27ae60; }

/* ── Pay button spinner ───────────────────────────────── */
.mx-spinner {
    width: 16px; height: 16px; border: 2px solid rgba(255,255,255,.4);
    border-top-color: #fff; border-radius: 50%;
    animation: mxSpin .7s linear infinite; display: inline-block;
}
@keyframes mxSpin { to { transform: rotate(360deg); } }

/* ── Pay button wider ─────────────────────────────────── */
.mx-pay-btn { flex: 0 0 auto; min-width: 130px; }

/* ── Success modal ────────────────────────────────────── */
.mx-success-card { max-width: 440px; text-align: center; }
.mx-success-anim { padding: 28px 0 12px; }
.mx-checkmark {
    width: 64px; height: 64px;
    animation: mxScaleIn .4s ease .1s both;
}
@keyframes mxScaleIn { from { opacity:0; transform:scale(.5); } to { opacity:1; transform:scale(1); } }
.mx-checkmark-circle {
    stroke: #27ae60; stroke-width: 2;
    stroke-dasharray: 166; stroke-dashoffset: 166;
    animation: mxStroke .6s cubic-bezier(.65,0,.45,1) .3s forwards;
}
.mx-checkmark-check {
    stroke: #27ae60; stroke-width: 2.5; stroke-linecap: round; stroke-linejoin: round;
    stroke-dasharray: 48; stroke-dashoffset: 48;
    animation: mxStroke .4s ease .8s forwards;
}
@keyframes mxStroke { to { stroke-dashoffset: 0; } }
.mx-success-title {
    font-size: 1.3rem; font-weight: 800; color: #111; margin-bottom: 4px;
}
.mx-success-sub {
    font-size: .84rem; color: #888; margin-bottom: 18px;
}
.mx-success-receipt { text-align: left; }
.mx-success-actions { justify-content: center; }

/* ── Card live preview JS ─────────────────────────────── */
/* (handled by the inline script below) */

/* ── Print ────────────────────────────────────────────── */
@media print {
    body > *:not(#mxSuccessModal) { display: none !important; }
    #mxSuccessModal {
        display: block !important; position: static !important;
        background: none !important; padding: 0 !important;
    }
    #mxSuccessModal .mx-modal-card { box-shadow: none !important; max-height: none !important; }
    .mx-success-actions { display: none !important; }
}
</style>

{{-- Live card preview script (tiny, no deps) --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const cardNum  = document.getElementById('mxCardNum');
    const cardName = document.getElementById('mxCardName');
    const cardExp  = document.getElementById('mxCardExp');

    if (!cardNum) return;

    cardNum.addEventListener('input', function () {
        const v = this.value || '•••• •••• •••• ••••';
        document.getElementById('mxCardDisplay').textContent =
            v.length > 0 ? v.padEnd(19, '•').slice(0, 19).replace(/(.{4})/g, '$1 ').trim() : '•••• •••• •••• ••••';
    });
    cardName.addEventListener('input', function () {
        document.getElementById('mxCardNameDisplay').textContent = this.value.toUpperCase() || 'YOUR NAME';
    });
    cardExp.addEventListener('input', function () {
        document.getElementById('mxCardExpDisplay').textContent = this.value || 'MM / YY';
    });
});
</script>
    </section>
    <div class="mx-demo-ribbon" aria-label="Demo Mode">
        DEMO
        <small>Work in progress</small>
    </div>

    <div id="mx-auth-state" data-logged-in="{{ auth()->check() ? '1' : '0' }}">
    </div>
    <div id="mx-routes" data-login-url="{{ route('popup.login') }}" data-register-url="{{ route('popup.register') }}">
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
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#mxTabLogin"
                                type="button" role="tab">
                                Login
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#mxTabRegister" type="button"
                                role="tab">
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

    <div class="row g-2">

        <div class="col-6">
            <label class="form-label small">Email</label>
            <input type="email" class="form-control form-control-sm" name="email" required>
        </div>

        <div class="col-6">
            <label class="form-label small">Mobile</label>
            <input type="text" class="form-control form-control-sm" name="mobile_no" required>
        </div>

        <div class="col-6">
            <label class="form-label small">Password</label>
            <input type="password" class="form-control form-control-sm" name="password" required>
        </div>

        <div class="col-6">
            <label class="form-label small">Confirm</label>
            <input type="password" class="form-control form-control-sm" name="password_confirmation" required>
        </div>

    </div>

    <button class="btn btn-success w-100 mt-3">
        Create account
    </button>
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

