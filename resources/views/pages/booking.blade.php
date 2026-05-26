@extends('layouts.main')

@section('title', 'Booking – Mechanix D.I.Y.')

@section('content')

    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/css/booking.css') }}">
    @endpush

    @php
        $liftKeyMap = [
            'four' => 'four',
            'four-post' => 'four',
            'four-post style lift' => 'four',
            'fourpost' => 'four',
            'two' => 'two',
            'two-post' => 'two',
            'twopost' => 'two',
            'scissor' => 'scissor',
            'flat' => 'flat',
            'motorcycle' => 'flat',
            'moto' => 'flat',
            'flat2' => 'flat2',
            'alignment' => 'flat2',
        ];

        $productLiftKey = null;
        $productLiftName = null;

        if ($product ?? null) {
            $rawLift = strtolower(trim($product->lift_type ?? ($product->name ?? '')));
            foreach ($liftKeyMap as $needle => $key) {
                if (str_contains($rawLift, $needle)) {
                    $productLiftKey = $key;
                    break;
                }
            }
            $liftNames = [
                'four' => 'Four-Post Lift',
                'two' => 'Two-Post Lift',
                'scissor' => 'Scissor Lift',
                'flat' => 'Motorcycle Lift',
                'flat2' => 'Alignment Rack',
            ];
            $productLiftName = $liftNames[$productLiftKey] ?? ($product->name ?? 'Lift');
        }

        $isProductMode = ($product ?? null) && $productLiftKey;

        $defaultImage =
            $product ?? null ? $product->images->firstWhere('is_default', 1) ?? $product->images->first() : null;

        /*
         * ─── BUILD LIFT PRICES JSON SAFELY IN PHP ───────────────────────
         * Using json_encode() instead of inline Blade loops avoids ALL
         * trailing-comma / broken-JSON bugs that broke the price cards.
         */
        $liftKeyMapForPrices = [
            'four-post lift' => 'four',
            'four post lift' => 'four',
             'four-post style lift' => 'four',
            'two-post lift' => 'two',
            'two post lift' => 'two',
            'scissor lift' => 'scissor',
            'motorcycle lift' => 'flat',
            'alignment' => 'flat2',
        ];

        $allLiftPricesJson = [];
        foreach ($allLiftProducts as $lp) {
            $lpKey = null;
            $lpNameLow = strtolower(trim($lp->name));
            foreach ($liftKeyMapForPrices as $needle => $key) {
                if (str_contains($lpNameLow, $needle)) {
                    $lpKey = $key;
                    break;
                }
            }
            if (!$lpKey) {
                continue;
            }

            $prices = [];
            foreach ($lp->prices as $price) {
                $prices[] = [
                    'hours' => (int) $price->hours,
                    'price' => (float) $price->price,
                    'total' => (float) ($price->price * $price->hours),
                    'is_membership' => (int) ($price->is_membership ?? 0),
                ];
            }
            $allLiftPricesJson[$lpKey] = [
                'name' => $lp->name,
                'prices' => $prices,
            ];
        }
    @endphp

    <section class="mx-body">

        <div>
            <div class="mx-workstations" id="mxWorkstations">
                <div class="mx-w-title active" data-ws="1">Workstation</div>
            </div>

            @if (!$isProductMode)
                {{-- Mobile dropdown --}}
                <div class="mx-lift-dropdown d-md-none mb-3">
                    <div class="dropdown">
                        <button class="mx-liftbtn dropdown-toggle w-100 d-flex align-items-center justify-content-between"
                            type="button" id="mxLiftDropdownBtn" data-bs-toggle="dropdown" aria-expanded="false">
                            Select Lift Type
                        </button>
                        <ul class="dropdown-menu w-100" id="mxLiftDropdownMenu">
                            <li><a class="dropdown-item" data-lift="four" href="#">Four-Post Lift</a></li>
                            <li><a class="dropdown-item" data-lift="two" href="#">Two-Post Lift</a></li>
                            <li><a class="dropdown-item" data-lift="scissor" href="#">Scissor Lift</a></li>
                            <li><a class="dropdown-item" data-lift="flat" href="#">Motorcycle Lift</a></li>
                            <li><a class="dropdown-item" data-lift="flat2" href="#">Alignment Rack</a></li>
                        </ul>
                    </div>
                </div>

                <div class="mx-liftbar">
                    <button class="mx-liftbtn" data-lift="four">
                        <img src="{{ asset('assets/images/icons/four-post.png') }}" class="mx-ic" alt="">
                        <span>Four-Post Style Lift</span>
                    </button>
                    <button class="mx-liftbtn mx-redmark" data-lift="two">
                        <img src="{{ asset('assets/images/icons/two-post.png') }}" class="mx-ic" alt="">
                        <span>Two-Post Lift</span>
                    </button>
                    <button class="mx-liftbtn" data-lift="scissor">
                        <img src="{{ asset('assets/images/icons/scissor.png') }}" class="mx-ic" alt="">
                        <span>Scissor Lift</span>
                    </button>
                    <button class="mx-liftbtn" data-lift="flat">
                        <img src="{{ asset('assets/images/icons/moto-lift.png') }}" class="mx-ic" alt="">
                        <span>Motorcycle Lift</span>
                    </button>
                    <button class="mx-liftbtn" data-lift="flat2">
                        <img src="{{ asset('assets/images/icons/alignment-rack.png') }}" class="mx-ic" alt="">
                        <span>Alignment Rack</span>
                    </button>
                </div>

                <div class="mx-lift-prompt" id="mxLiftPrompt">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10" />
                        <line x1="12" y1="8" x2="12" y2="12" />
                        <line x1="12" y1="16" x2="12.01" y2="16" />
                    </svg>
                    Please select a lift type above to begin your booking
                </div>
            @else
                <div class="mx-product-liftbadge" id="mxLiftPrompt">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12" />
                    </svg>
                    Booking: <strong>{{ $productLiftName }}</strong>
                    &nbsp;—&nbsp;
                    <a href="{{ route('rentals') }}" class="mx-product-change">Change product</a>
                </div>

                <div id="mxProductMeta" data-product-mode="1" data-lift-key="{{ $productLiftKey }}"
                    data-lift-name="{{ $productLiftName }}" data-product-id="{{ $product->id }}">
                </div>
            @endif
        </div>

        <div class="mx-wrap container-fluid">
            <div class="mx-main">

                <div class="mx-left" id="liftSection">

                    @if ($isProductMode)
                        @foreach ($product->prices as $price)
                            <div class="mx-pricecard {{ $loop->first ? 'mx-selected' : '' }}"
                                data-hours="{{ $price->hours }}" data-price="{{ $price->price }}"
                                data-total="{{ $price->price * $price->hours }}">
                                <span class="mx-hours">
                                    {{ $price->hours }} Hour{{ $price->hours > 1 ? 's' : '' }}
                                </span>
                                <span class="mx-price">
                                    ${{ $price->price }}
                                    @if ($price->hours > 1)
                                        / hour
                                    @endif
                                </span>
                            </div>
                        @endforeach

                        <div class="mx-liftpreview">
                            <div class="mx-liftimg" id="mxLiftImgWrap">
                                <img id="mxLiftPreviewImg"
                                    src="{{ $defaultImage ? asset('storage/' . $defaultImage->image_path) : asset('assets/images/no-image.png') }}"
                                    alt="{{ $product->name }}">
                            </div>
                            <ul class="mx-liftpoints" id="mxLiftPoints">
                                @foreach (explode("\n", $product->description ?? '') as $line)
                                    @if (trim($line))
                                        <li>{{ trim($line) }}</li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    @elseif($product ?? null)
                        @foreach ($product->prices as $price)
                            <div class="mx-pricecard {{ $loop->first ? 'mx-selected' : '' }}"
                                data-hours="{{ $price->hours }}" data-price="{{ $price->price }}"
                                data-total="{{ $price->price * $price->hours }}">
                                <span class="mx-hours">{{ $price->hours }} Hour{{ $price->hours > 1 ? 's' : '' }}</span>
                                <span class="mx-price">${{ $price->price }}@if ($price->hours > 1)
                                        / hour
                                    @endif
                                </span>
                            </div>
                        @endforeach
                        <div class="mx-liftpreview">
                            <div class="mx-liftimg" id="mxLiftImgWrap">
                                <img id="mxLiftPreviewImg"
                                    src="{{ $defaultImage ? asset('storage/' . $defaultImage->image_path) : asset('assets/images/no-image.png') }}"
                                    alt="{{ $product->name }}">
                            </div>
                            <ul class="mx-liftpoints" id="mxLiftPoints">
                                @foreach (explode("\n", $product->description ?? '') as $line)
                                    @if (trim($line))
                                        <li>{{ trim($line) }}</li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    @else
                        {{-- ── DIRECT BOOKING MODE ── --}}

                        {{--
                            FIX: Use json_encode() built in PHP above.
                            No more inline Blade loops = no trailing comma bug.
                        --}}
                        <script id="mxAllLiftPrices" type="application/json">
                            {!! json_encode($allLiftPricesJson, JSON_UNESCAPED_UNICODE) !!}
                        </script>

                        <div id="mxPriceCardsWrap">
                            <div class="mx-lift-price-placeholder text-muted small py-3">
                                Select a lift type above to see pricing.
                            </div>
                        </div>

                        <div class="mx-liftpreview">
                            <div class="mx-liftimg mx-liftimg--placeholder" id="mxLiftImgWrap">
                                <div class="mx-liftimg-placeholder" id="mxLiftPlaceholder">
                                    <svg width="56" height="56" viewBox="0 0 24 24" fill="none"
                                        stroke="#cbd5e1" stroke-width="1.5">
                                        <rect x="3" y="3" width="18" height="18" rx="3" />
                                        <path d="M3 9h18M9 21V9" />
                                    </svg>
                                    <p>Select a lift type to preview</p>
                                </div>
                                <img id="mxLiftPreviewImg" src="{{ asset('assets/images/icons/lift-red.png') }}"
                                    alt="Lift preview" style="display:none;">
                            </div>
                            <ul class="mx-liftpoints" id="mxLiftPoints">
                                <li>Select a lift type above to see details</li>
                            </ul>
                        </div>
                    @endif

                    <div class="mx-leftbottom" id="leftupButton">
                        <button class="mx-bookbig" id="openDayCalendar" disabled>Book Now</button>
                        <p class="mx-book-hint" id="mxBookHintTop">
                            @if ($isProductMode)
                                Please pick an available date on the calendar.
                            @else
                                Please select a lift type and a date to continue.
                            @endif
                        </p>
                    </div>
                </div>

                <div class="mx-right" id="calendarSection">

                    <div class="calendar-box">
                        <input id="bookingDate" type="text" placeholder="Select date" readonly />
                    </div>

                    <div class="calendar-wrap" id="calendarWrap"></div>

                    <div class="mx-legendMini">
                        <span><i class="mx-box green"></i> Available</span>
                        <span><i class="mx-box yellow"></i> Filling Fast</span>
                        <span><i class="mx-box orange"></i> Almost Full</span>
                        <span><i class="mx-box red"></i> Booked</span>
                        <span><i class="mx-box grey"></i> Unavailable</span>
                    </div>

                    <div class="mx-timeView" id="mxTimeView" style="display:none;">
                        <div class="mx-timeTop">
                            <button type="button" class="mx-backBtn" id="mxBackToDate">
                                <i class="fa-solid fa-arrow-left"></i> Change date
                            </button>
                            <div class="mx-timeTitle">
                                Select a start time for <span id="mxSelectedDateText">----</span>
                            </div>
                        </div>

                        <div class="mx-slot-legend">
                            <span class="mx-slot-leg-item">
                                <span class="mx-slot-leg-dot available"></span>Available
                            </span>
                            <span class="mx-slot-leg-item">
                                <span class="mx-slot-leg-dot booked"></span>Booked
                            </span>
                        </div>

                        <div class="mx-timeGrid" id="mxTimeGrid"></div>

                        <div class="mx-timeBottom">
                            <div class="mx-pickedInfo">
                                Start: <b id="mxPickedTimeText">None</b>
                            </div>
                            <button type="button" class="mx-confirmBtn" id="mxContinueBtn" disabled>
                                Continue To Booking
                            </button>
                        </div>
                    </div>

                    <div id="mxSlotModal" class="mx-modal-overlay" aria-hidden="true">
                        <div class="mx-modal-card" role="dialog" aria-modal="true" aria-labelledby="mxModalTitle">
                            <div class="mx-modal-head">
                                <div>
                                    <div id="mxModalTitle" class="mx-modal-title">Confirm Booking</div>
                                    <div class="mx-modal-sub">Adjust hours if needed — must be consecutive.</div>
                                </div>
                                <button type="button" class="mx-modal-x" id="mxModalClose"
                                    aria-label="Close">×</button>
                            </div>
                            <div class="mx-modal-body">
                                <div class="mx-info-row">
                                    <div class="mx-info-label">Selected</div>
                                    <div class="mx-info-value" id="mxSlotText">—</div>
                                </div>
                                <div class="mx-info-row">
                                    <div class="mx-info-label">Hours</div>
                                    <div class="mx-info-value">
                                        <button type="button" class="mx-gbtn" id="mxHMinus">−</button>
                                        <span id="mxSelectedHours" class="mx-hours-pill">1</span>
                                        <button type="button" class="mx-gbtn" id="mxHPlus">+</button>
                                    </div>
                                </div>
                                <div class="mx-hint" id="mxHintText">Continuous booking required.</div>
                                <div class="mx-total">Total: <b id="mxTotalText">$0</b></div>
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

                    <div class="mx-leftbottom cal-sub-btn" id="bookclose">
                        <button class="mx-bookbig" id="openDayCalendarMb" disabled>Book Now</button>
                        <p class="mx-book-hint" id="mxBookHintBottom">
                            @if ($isProductMode)
                                Please pick an available date on the calendar.
                            @else
                                Please select a lift type and a date to continue.
                            @endif
                        </p>
                    </div>
                </div>

            </div>
        </div>

        {{-- ── SUMMARY MODAL ── --}}
        <div id="mxSummaryModal" class="mx-modal-overlay" aria-hidden="true">
            <div class="mx-modal-card mx-summary-card" role="dialog" aria-modal="true"
                aria-labelledby="mxSummaryTitle">
                <div class="mx-modal-head">
                    <div>
                        <div id="mxSummaryTitle" class="mx-modal-title">Booking Summary</div>
                        <div class="mx-modal-sub">Review your details before payment.</div>
                    </div>
                    <button type="button" class="mx-modal-x" id="mxSummaryClose" aria-label="Close">×</button>
                </div>
                <div class="mx-modal-body">
                    <div class="mx-receipt">
                        <div class="mx-receipt-row"><span class="mx-receipt-label">Workstation</span><span
                                class="mx-receipt-value" id="mxsWorkstation">—</span></div>
                        <div class="mx-receipt-row"><span class="mx-receipt-label">Lift Type</span><span
                                class="mx-receipt-value" id="mxsLift">—</span></div>
                        <div class="mx-receipt-divider"></div>
                        <div class="mx-receipt-row"><span class="mx-receipt-label">Date</span><span
                                class="mx-receipt-value" id="mxsDate">—</span></div>
                        <div class="mx-receipt-row"><span class="mx-receipt-label">Start Time</span><span
                                class="mx-receipt-value" id="mxsStart">—</span></div>
                        <div class="mx-receipt-row"><span class="mx-receipt-label">Duration</span><span
                                class="mx-receipt-value" id="mxsDuration">—</span></div>
                        <div class="mx-receipt-row"><span class="mx-receipt-label">End Time</span><span
                                class="mx-receipt-value" id="mxsEnd">—</span></div>
                        <div class="mx-receipt-divider"></div>
                        <div class="mx-receipt-row"><span class="mx-receipt-label">Rate</span><span
                                class="mx-receipt-value" id="mxsRate">—</span></div>
                        <div class="mx-receipt-row"><span class="mx-receipt-label">Hours</span><span
                                class="mx-receipt-value" id="mxsHours">—</span></div>
                        <div class="mx-receipt-row mx-receipt-total"><span class="mx-receipt-label">Total</span><span
                                class="mx-receipt-value" id="mxsTotal">—</span></div>
                    </div>
                    <p class="mx-summary-note">
                        <i class="fa-solid fa-circle-info"></i>
                        Payment is collected on the next screen. Booking is confirmed only after successful payment.
                    </p>
                </div>
                <div class="mx-modal-actions">
                    <button type="button" class="mx-btn-outline" id="mxSummaryBack"><i
                            class="fa-solid fa-arrow-left"></i> Edit</button>
                    <button type="button" class="mx-btn-solid" id="mxSummaryPay">Pay Now &nbsp;<i
                            class="fa-solid fa-lock"></i></button>
                </div>
            </div>
        </div>

        {{-- ── PAY MODAL ── --}}
        <div id="mxPayModal" class="mx-modal-overlay" aria-hidden="true">
            <div class="mx-modal-card mx-pay-card" role="dialog" aria-modal="true" aria-labelledby="mxPayTitle">
                <div class="mx-modal-head">
                    <div>
                        <div id="mxPayTitle" class="mx-modal-title">Secure Payment</div>
                        <div class="mx-modal-sub">Amount due: <strong id="mxPayAmount">$0</strong></div>
                    </div>
                    <button type="button" class="mx-modal-x" id="mxPayClose" aria-label="Close">×</button>
                </div>
                <div class="mx-modal-body">
                    <div class="mxs-pay-tabs">
                        <button type="button" class="mxs-pay-tab active" data-tab="card"><i
                                class="fa-regular fa-credit-card"></i> Card</button>
                        <button type="button" class="mxs-pay-tab" data-tab="upi"><i
                                class="fa-solid fa-mobile-screen-button"></i> UPI</button>
                        <button type="button" class="mxs-pay-tab" data-tab="netbanking"><i
                                class="fa-solid fa-building-columns"></i> Net Banking</button>
                    </div>
                    <div id="mxPayError" class="mx-pay-error d-none"></div>
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
                            <div class="mx-field-wrap full"><label>Card Number</label><input type="text"
                                    id="mxCardNum" class="mx-pay-input" placeholder="1234 5678 9012 3456" maxlength="19"
                                    inputmode="numeric"></div>
                            <div class="mx-field-wrap full"><label>Cardholder Name</label><input type="text"
                                    id="mxCardName" class="mx-pay-input" placeholder="Name on card"></div>
                            <div class="mx-field-wrap half"><label>Expiry</label><input type="text" id="mxCardExp"
                                    class="mx-pay-input" placeholder="MM / YY" maxlength="7" inputmode="numeric"></div>
                            <div class="mx-field-wrap half"><label>CVV</label><input type="password" id="mxCardCvv"
                                    class="mx-pay-input" placeholder="•••" maxlength="3" inputmode="numeric"></div>
                        </div>
                    </div>
                    <div class="mxs-pay-panel" id="mxPayPanel-upi">
                        <div class="mx-upi-wrap">
                            <i class="fa-solid fa-mobile-screen-button mx-upi-icon"></i>
                            <p class="mx-upi-label">Enter your UPI ID</p>
                            <input type="text" class="mx-pay-input" id="mxUpiId" placeholder="yourname@upi"
                                style="max-width:280px;margin:0 auto;display:block;">
                            <p class="mx-upi-hint">e.g. name@okaxis, name@ybl, name@paytm</p>
                        </div>
                    </div>
                    <div class="mxs-pay-panel" id="mxPayPanel-netbanking">
                        <div class="mx-nb-grid">
                            @foreach (['SBI', 'HDFC', 'ICICI', 'Axis', 'Kotak', 'Yes Bank', 'PNB', 'BOB'] as $bank)
                                <label class="mx-nb-option">
                                    <input type="radio" name="mxBank"
                                        value="{{ strtolower(str_replace(' ', '', $bank)) }}">
                                    <span>{{ $bank }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="mx-modal-actions">
                    <div class="mx-secure-badge"><i class="fa-solid fa-shield-halved"></i> 256-bit SSL secured</div>
                    <button type="button" class="mx-btn-solid mx-pay-btn" id="mxPayNowBtn">
                        <span id="mxPayBtnText">Pay <span id="mxPayBtnAmt">$0</span></span>
                        <span id="mxPaySpinner" class="mx-spinner d-none"></span>
                    </button>
                </div>
            </div>
        </div>

        {{-- ── SUCCESS MODAL ── --}}
        <div id="mxSuccessModal" class="mx-modal-overlay" aria-hidden="true">
            <div class="mx-modal-card mx-success-card" role="dialog" aria-modal="true"
                aria-labelledby="mxSuccessTitle">
                <div class="mx-success-anim"
     style="
        width:58px;
        height:58px;
        margin:0 auto 8px;
     ">

    <svg class="mx-checkmark"
         viewBox="0 0 52 52"
         xmlns="http://www.w3.org/2000/svg"
         style="
            width:40px;
            height:40px;
         ">

        <circle class="mx-checkmark-circle"
                cx="26"
                cy="26"
                r="25"
                fill="none" />

        <path class="mx-checkmark-check"
              fill="none"
              d="M14.1 27.2l7.1 7.2 16.7-16.8" />
    </svg>

</div>
                <div id="mxSuccessTitle" class="mx-success-title">Booking Confirmed!</div>
                        <div class="mx-garage-contact"
     style="
        background:#1e293b;
        padding:8px 10px;
        border-radius:8px;
        margin:8px 0;
        text-align:center;
        line-height:1.2;
     ">

    <p style="
        color:#94a3b8;
        margin:0 0 4px;
        font-size:10px;
        font-weight:600;
        letter-spacing:.4px;
    ">
        CALL TO CONFIRM
    </p>

    <a href="tel:+17327307712"
       class="mx-phone-link"
       style="
          color:#22c55e;
          font-size:14px;
          font-weight:700;
          text-decoration:none;
          display:inline-flex;
          align-items:center;
          gap:5px;
          margin:0;
       ">

        <i class="fa-solid fa-phone" style="font-size:11px;"></i>

        732-730-7712 EXT. 3
    </a>

    <p style="
        color:#94a3b8;
        margin:4px 0 0;
        font-size:9px;
    ">
        Mon–Fri 9AM–6PM • Sat 9AM–12PM
    </p>

</div>
                {{-- <div class="mx-success-sub">Your booking ID is <strong id="mxSuccessBookingId">—</strong></div> --}}
                <div class="mx-receipt mx-success-receipt">
                    <div class="mx-receipt-row"><span class="mx-receipt-label">Workstation</span><span
                            class="mx-receipt-value" id="mxrWorkstation">—</span></div>
                    <div class="mx-receipt-row"><span class="mx-receipt-label">Lift</span><span class="mx-receipt-value"
                            id="mxrLift">—</span></div>
                    <div class="mx-receipt-divider"></div>
                    <div class="mx-receipt-row"><span class="mx-receipt-label">Date</span><span class="mx-receipt-value"
                            id="mxrDate">—</span></div>
                    <div class="mx-receipt-row"><span class="mx-receipt-label">Start</span><span class="mx-receipt-value"
                            id="mxrStart">—</span></div>
                    <div class="mx-receipt-row"><span class="mx-receipt-label">Duration</span><span
                            class="mx-receipt-value" id="mxrDuration">—</span></div>
                    <div class="mx-receipt-row"><span class="mx-receipt-label">End</span><span class="mx-receipt-value"
                            id="mxrEnd">—</span></div>
                    <div class="mx-receipt-divider"></div>
                    <div class="mx-receipt-row"><span class="mx-receipt-label">Rate</span><span class="mx-receipt-value"
                            id="mxrRate">—</span></div>
                    <div class="mx-receipt-row mx-receipt-total"><span class="mx-receipt-label">Total Paid</span><span
                            class="mx-receipt-value" id="mxrTotal">—</span></div>
                </div>
                <div class="mx-modal-actions mx-success-actions">
                    <button type="button" class="mx-btn-outline" id="mxPrintBtn"><i class="fa-solid fa-print"></i>
                        Print Receipt</button>
                    <button type="button" class="mx-btn-solid" onclick="location.reload()">Done</button>
                </div>
            </div>
        </div>

    </section>

    <div class="mx-demo-ribbon" aria-label="Demo Mode">DEMO<small>Work in progress</small></div>

    <div id="mx-auth-state" data-logged-in="{{ auth()->check() ? '1' : '0' }}"></div>
    <div id="mx-routes" data-login-url="{{ route('popup.login') }}" data-register-url="{{ route('popup.register') }}">
    </div>

    {{-- ── AUTH MODAL ── --}}
    <div class="modal fade" id="mxAuthModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background:#1f1f1f; color:#fff; border-radius:10px;">
                <div class="modal-header border-0">
                    <h5 class="modal-title text-white">Continue to Book</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs" id="authTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link  text-white bg-dark" id="loginTab" data-bs-toggle="tab"
                                data-bs-target="#loginTabPane" type="button" role="tab">Login</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link text-white bg-dark" id="registerTab" data-bs-toggle="tab"
                                data-bs-target="#registerTabPane" type="button" role="tab">Register</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active text-white bg-dark" id="guestTab" data-bs-toggle="tab"
                                data-bs-target="#guestTabPane" type="button" role="tab">Guest Booking</button>
                        </li>
                    </ul>

                    <div class="tab-content pt-3" id="authTabsContent">

                        {{-- LOGIN TAB --}}
                        <div class="tab-pane fade " id="loginTabPane" role="tabpanel">
                            {{-- FIX: id changed from loginFormMain → mxLoginForm to match JS listener --}}
                            <div id="loginErrorMsg" class="alert alert-danger d-none"></div>
                            <form id="mxLoginForm">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label small text-white">Email</label>
                                    <input type="email" class="form-control border-0" name="email"
                                        style="background:#2d2d2d;color:#fff;box-shadow:none;outline:none;border:none;"
                                        required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small text-white">Password</label>
                                    <input type="password" class="form-control border-0" name="password"
                                        style="background:#2d2d2d;color:#fff;box-shadow:none;outline:none;border:none;"
                                        required>
                                </div>
                                <div class="text-end mt-2">
                                    <a href="#" id="forgotPasswordBtn" class="small text-danger btn">Forgot
                                        password?</a>
                                </div>
                                <button type="submit" class="btn w-100 text-white fw-semibold mt-3"
                                    style="background:linear-gradient(180deg,rgba(221,43,49,1) 0%,rgb(119,17,23) 100%);border:2px solid #791218;height:46px;letter-spacing:1px;">
                                    Login
                                </button>
                            </form>
                        </div>

                        {{-- REGISTER TAB --}}
                        <div class="tab-pane fade" id="registerTabPane" role="tabpanel">
                            {{-- FIX: id changed from registerFormMain → mxRegisterForm to match JS listener --}}
                            <div id="registerErrorMsg" class="alert alert-danger d-none"></div>
                            <form id="mxRegisterForm">
                                @csrf
                                <div class="row g-2">
                                    <div class="col-6">
                                        <label class="form-label small text-white">Email</label>
                                        <input type="email" class="form-control border-0 form-control-sm"
                                            name="email"
                                            style="background:#2d2d2d;color:#fff;box-shadow:none;outline:none;border:none;"
                                            required>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small text-white">Mobile</label>
                                        <input type="text" class="form-control border-0 form-control-sm"
                                            name="mobile_no"
                                            style="background:#2d2d2d;color:#fff;box-shadow:none;outline:none;border:none;"
                                            required>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small text-white">Password</label>
                                        <input type="password" class="form-control border-0 form-control-sm"
                                            name="password"
                                            style="background:#2d2d2d;color:#fff;box-shadow:none;outline:none;border:none;"
                                            required>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small text-white">Confirm</label>
                                        <input type="password" class="form-control border-0 form-control-sm"
                                            name="password_confirmation"
                                            style="background:#2d2d2d;color:#fff;box-shadow:none;outline:none;border:none;"
                                            required>
                                    </div>
                                </div>
                                <button type="submit" class="btn w-100 text-white fw-semibold mt-3"
                                    style="background:linear-gradient(180deg,rgba(221,43,49,1) 0%,rgb(119,17,23) 100%);border:2px solid #791218;height:46px;letter-spacing:1px;">
                                    Create Account
                                </button>
                            </form>
                        </div>

                        {{-- GUEST BOOKING TAB --}}
                        <div class="tab-pane fade show active" id="guestTabPane" role="tabpanel">
                            <div id="guestErrorMsg" class="alert alert-danger d-none"></div>
                            <div class="alert alert-info"
                                style="background:#2d3748;border:1px solid #4a5568;color:#e2e8f0;font-size:13px;">
                                <i class="fa-solid fa-info-circle"></i>
                                Your slot will be held for <strong>30 minutes</strong>. Please call us to confirm your
                                booking.
                            </div>
                            {{-- FIX: id kept as guestBookingForm — matches JS listener --}}
                            <form id="guestBookingForm">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label small text-white">Full Name</label>
                                    <input type="text" class="form-control border-0" name="guest_name" id="guestName"
                                        placeholder="Enter your full name"
                                        style="background:#2d2d2d;color:#fff;box-shadow:none;outline:none;border:none;-webkit-text-fill-color:#fff;"
                                        required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small text-white">US Phone Number</label>
                                    <input type="tel" class="form-control border-0" name="guest_phone"
                                        id="guestPhone" placeholder="(XXX) XXX-XXXX"
                                        style="background:#2d2d2d;color:#fff;box-shadow:none;outline:none;border:none;-webkit-text-fill-color:#fff;"
                                        required>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="guestAgree" required>
                                    <label class="form-check-label small text-white" for="guestAgree">
                                        I understand I must call to confirm this booking within 30 minutes
                                    </label>
                                </div>
                                <button type="submit" class="btn w-100 text-white fw-semibold"
                                    style="background:linear-gradient(180deg,rgba(221,43,49,1) 0%,rgb(119,17,23) 100%);border:2px solid #791218;height:46px;letter-spacing:1px;">
                                    Reserve Slot (Call to Confirm)
                                </button>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── GUEST SUCCESS MODAL ── --}}
    <div id="mxGuestSuccessModal" class="mx-modal-overlay" aria-hidden="true">
        <div class="mx-modal-card mx-success-card" role="dialog" aria-modal="true">
           <button type="button"
        class="mx-modal-close"
        onclick="closeGuestMemberSuccessModal()">

    &times;

</button>
           <div class="mx-success-anim"
     style="
        width:58px;
        height:58px;
        margin:0 auto 8px;
     ">

    <svg class="mx-checkmark"
         viewBox="0 0 52 52"
         xmlns="http://www.w3.org/2000/svg"
         style="
            width:40px;
            height:40px;
         ">

        <circle class="mx-checkmark-circle"
                cx="26"
                cy="26"
                r="25"
                fill="none" />

        <path class="mx-checkmark-check"
              fill="none"
              d="M14.1 27.2l7.1 7.2 16.7-16.8" />
    </svg>

</div>
            <div class="mx-success-title">Slot Reserved!</div>
            {{-- <div class="mx-success-sub">Booking ID: <strong id="mxGuestBookingId">—</strong></div> --}}
           <div class="mx-mini-timer"
     style="
        background:#1e293b;
        border:1px solid #334155;
        border-radius:8px;
        padding:6px 10px;
        margin:8px 0;
        display:flex;
        align-items:center;
        justify-content:center;
        gap:8px;
        font-size:12px;
        line-height:1;
     ">

    <i class="fa-solid fa-clock"
       style="
          color:#f59e0b;
          font-size:12px;
       "></i>

    <span style="color:#cbd5e1;">
        Time Left:
    </span>

    <strong id="mxGuestTimer"
            style="
                color:#fbbf24;
                font-size:13px;
                margin-right:4px;
            ">
        30:00
    </strong>

    <span style="
        color:#64748b;
        font-size:11px;
    ">
        Slot auto releases
    </span>

</div>
            <div class="mx-garage-contact"
     style="
        background:#1e293b;
        padding:10px 14px;
        border-radius:10px;
        margin:10px 0;
        display:flex;
        align-items:center;
        justify-content:center;
        gap:14px;
        flex-wrap:wrap;
     ">

    <span style="
        color:#94a3b8;
        font-size:11px;
        font-weight:700;
        letter-spacing:.4px;
        line-height:1;
    ">
        CALL TO CONFIRM
    </span>

    <a href="tel:+11234567890"
       class="mx-phone-link"
       style="
          color:#22c55e;
          font-size:15px;
          font-weight:700;
          text-decoration:none;
          display:flex;
          align-items:center;
          gap:6px;
          line-height:1;
       ">

        <i class="fa-solid fa-phone" style="font-size:12px;"></i>

        732-730-7712 EXT 3
    </a>

    <span style="
        color:#94a3b8;
        font-size:10px;
        line-height:1;
        white-space:nowrap;
    ">
        Mon–Fri 9AM–6PM
    </span>

</div>
            <div class="mx-receipt mx-success-receipt">
                <div class="mx-receipt-row"><span class="mx-receipt-label">Name</span><span class="mx-receipt-value"
                        id="mxgName">—</span></div>
                <div class="mx-receipt-row"><span class="mx-receipt-label">Phone</span><span class="mx-receipt-value"
                        id="mxgPhone">—</span></div>
                <div class="mx-receipt-divider"></div>
                <div class="mx-receipt-row"><span class="mx-receipt-label">Lift</span><span class="mx-receipt-value"
                        id="mxgLift">—</span></div>
                <div class="mx-receipt-row"><span class="mx-receipt-label">Date</span><span class="mx-receipt-value"
                        id="mxgDate">—</span></div>
                <div class="mx-receipt-row"><span class="mx-receipt-label">Booking Time</span><span
                        class="mx-receipt-value" id="mxgTime">—</span></div>
                <div class="mx-receipt-row">
                    <span class="mx-receipt-label">Slot Timing</span>
                    <span class="mx-receipt-value" id="mxgSlotTiming">—</span>
                </div>
                <div class="mx-receipt-row"><span class="mx-receipt-label">Duration</span><span class="mx-receipt-value"
                        id="mxgDuration">—</span></div>


                <div class="mx-receipt-row">
                    <span class="mx-receipt-label">Total Amount</span>
                    <span class="mx-receipt-value" id="mxgTotal">$0</span>
                </div>
            </div>
            <div class="mx-modal-actions mx-success-actions">
                <a href="tel:+732-730-7712" class="mx-btn-solid" style="text-decoration:none;">
                    <i class="fa-solid fa-phone"></i> Call Now to Confirm
                </a>
            </div>
             {{-- <div class="mx-modal-actions">
                   
                    <button type="button" class="mx-btn-solid" id="mxSummaryPay">Pay Now &nbsp;<i
                            class="fa-solid fa-lock"></i></button>
                </div> --}}
        </div>
    </div>

    {{-- ── FORGOT PASSWORD MODAL ── --}}
    <div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm" style="background:linear-gradient(180deg,#1f1f1f,#2a2a2a);border-radius:10px;">
            <div class="modal-content mt-4 p-4" style="background:transparent;border:none;">
                <div id="fpStepEmail">
                    <h6 class="mb-3 text-white">Reset Password</h6>
                    <input type="email" id="fpEmailInput" class="form-control form-control-sm mb-2"
                        placeholder="Enter email" style="background:#2d2d2d;color:#fff;border:none;">
                    <button class="btn btn-primary w-100 btn-sm d-flex align-items-center justify-content-center gap-2"
                        id="fpSendOtpBtn">
                        <span class="btn-text">Send OTP</span>
                        <span class="spinner-border spinner-border-sm d-none" id="fpOtpLoader"></span>
                    </button>
                </div>
                <div id="fpStepOtp" class="d-none">
                    <label class="form-label small text-white">Please enter the OTP sent to your email</label>
                    <input type="text" id="fpOtpInput" class="form-control form-control-sm mb-2 text-center"
                        placeholder="Enter OTP" style="background:#2d2d2d;color:#fff;border:none;">
                    <button class="btn btn-success w-100 btn-sm" id="fpVerifyOtpBtn">Verify OTP</button>
                    <button class="btn btn-link btn-sm w-100 text-white" id="fpResendOtpBtn">Resend OTP</button>
                </div>
                <div id="fpStepReset" class="d-none text-white w-100"
                    style="background:#1f1f1f;padding:16px;border-radius:10px;">
                    <label class="form-label small">New Password</label>
                    <input type="password" id="fpNewPassword" class="form-control border-0 mb-3"
                        style="background:#2d2d2d;color:#fff;box-shadow:none;outline:none;border:none;">
                    <label class="form-label small">Confirm Password</label>
                    <input type="password" id="fpConfirmPassword" class="form-control border-0 mb-3"
                        style="background:#2d2d2d;color:#fff;box-shadow:none;outline:none;border:none;">
                    <button id="fpResetPasswordBtn" class="btn w-100 text-white fw-semibold shadow"
                        style="background:linear-gradient(180deg,rgba(221,43,49,1) 0%,rgb(119,17,23) 100%);border:2px solid #791218;height:46px;letter-spacing:1px;">
                        Reset Password
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var cn = document.getElementById('mxCardNum');
            var nm = document.getElementById('mxCardName');
            var ex = document.getElementById('mxCardExp');
            if (!cn) return;
            cn.addEventListener('input', function() {
                var v = this.value.replace(/\D/g, '').padEnd(16, '•').slice(0, 16).match(/.{1,4}/g);
                document.getElementById('mxCardDisplay').textContent = v ? v.join(' ') :
                    '•••• •••• •••• ••••';
            });
            nm.addEventListener('input', function() {
                document.getElementById('mxCardNameDisplay').textContent = this.value.toUpperCase() ||
                    'YOUR NAME';
            });
            ex.addEventListener('input', function() {
                document.getElementById('mxCardExpDisplay').textContent = this.value || 'MM / YY';
            });
        });
    </script>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="{{ asset('assets/js/booking.js') }}"></script>
@endpush
