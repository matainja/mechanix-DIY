// /**
//  * booking.js  —  Mechanix D.I.Y.  (v6 – bug-fixed)
//  *
//  * FIXES IN THIS VERSION:
//  *  1. Login form: listens on #mxLoginForm (was #mxLoginForm ✓ but error div was
//  *     #mxLoginErr — blade now has #loginErrorMsg, so JS updated to match).
//  *  2. Register form: same mismatch fixed (#registerErrorMsg).
//  *  3. Guest booking: payload now always includes product_id correctly.
//  *  4. Auth modal forms: selectors aligned with blade IDs throughout.
//  */

// $(function () {

//     /* ================================================================
//        SCROLL HELPER
//     ================================================================ */
//     function scrollToEl(el, offset) {
//         offset = (offset === undefined) ? 70 : offset;
//         if (!el) return;
//         $('html, body').animate({ scrollTop: $(el).offset().top - offset }, 400);
//     }

//     /* ================================================================
//        DETECT MODE
//     ================================================================ */
//     var $meta        = $('#mxProductMeta');
//     var PRODUCT_MODE = $meta.length > 0 && $meta.data('product-mode') == 1;

//     var AUTO_LIFT_KEY  = PRODUCT_MODE ? ($meta.data('lift-key')  || 'all') : null;
//     var AUTO_LIFT_NAME = PRODUCT_MODE ? ($meta.data('lift-name') || '')    : null;

//     /* ================================================================
//        STATE
//     ================================================================ */
//     const TODAY     = new Date();
//     const MIN_MONTH = new Date();

//     var dayData     = {};
//     var bookedSlots = {};

//     var selectedDate      = null;
//     var selectedStartTime = null;
//     var selectedHours     = 1;
//     var selectedPackHours = 1;
//     var selectedLift      = PRODUCT_MODE ? AUTO_LIFT_KEY : null;
//     var fpInstance        = null;

//     /* ================================================================
//        HELPERS
//     ================================================================ */
//     function pad2(n) { return String(n).padStart(2, '0'); }

//     function formatTimePoint(h24) {
//         return (h24 % 12 || 12) + ':00 ' + (h24 >= 12 ? 'PM' : 'AM');
//     }

//     function formatMoney(n) { return '$' + Number(n).toFixed(0); }

//     function addDaysStr(dateStr, days) {
//         var d = new Date(dateStr + 'T00:00:00');
//         d.setDate(d.getDate() + days);
//         return d.getFullYear() + '-' + pad2(d.getMonth() + 1) + '-' + pad2(d.getDate());
//     }

//     function nextDate(dateStr, days) {
//         return addDaysStr(dateStr, days === undefined ? 1 : days);
//     }

//     function getWorkingHours(dateStr) {
//         var day = new Date(dateStr + 'T00:00:00').getDay();
//         if (day === 6) return null;
//         if (day === 5) return { start: 9, end: 12 };
//         return { start: 9, end: 18 };
//     }

//     function getWorkingSlots(dateStr) {
//         var wh = getWorkingHours(dateStr);
//         if (!wh) return [];
//         var s = [];
//         for (var h = wh.start; h < wh.end; h++) s.push(pad2(h) + ':00');
//         return s;
//     }

//     /* ================================================================
//        GUEST BOOKING FORM
//        FIX: payload uses mxGetBookingPayload() which already has product_id
//     ================================================================ */
//    $('#guestBookingForm').on('submit', async function (e) {
//     e.preventDefault();
//     var $err = $('#guestErrorMsg').addClass('d-none').text('');

//     var rawPhone = $('#guestPhone').val().replace(/\D/g, '');
//     if (rawPhone.length !== 10) {
//         $err.text('Please enter a valid 10-digit US phone number.').removeClass('d-none');
//         return;
//     }

//     var payload          = mxGetBookingPayload();
//     payload.guest_name   = $('#guestName').val().trim();
//     payload.guest_phone  = '+1' + rawPhone;

//     if (!payload.date || !payload.start) {
//         $err.text('Booking details are missing. Please go back and select a date and time.').removeClass('d-none');
//         return;
//     }

//     // Store payload for payment later
//     sessionStorage.setItem('mx_guest_booking_payload', JSON.stringify(payload));

//     try {
//         var res  = await fetch('/booking/guest', {
//             method: 'POST',
//             credentials: 'same-origin',
//             headers: {
//                 'Content-Type': 'application/json',
//                 'X-CSRF-TOKEN': window.MX_CSRF,
//                 'Accept':       'application/json',
//             },
//             body: JSON.stringify(payload),
//         });

//         var data = await res.json().catch(function () { return {}; });

//         if (!res.ok || !data.status) {
//             $err.text(data.message || 'Booking failed. Please try again.').removeClass('d-none');
//             return;
//         }

//         var inst = bootstrap.Modal.getInstance(document.getElementById('mxAuthModal'));
//         if (inst) inst.hide();

//         // Store booking ID for later use
//         sessionStorage.setItem('mx_guest_booking_id', data.booking_id);

//         showGuestSuccessModal(data.booking_id, payload, data.expires_at);

//     } catch (err) {
//         $err.text('Network error. Please try again.').removeClass('d-none');
//     }
// });
//     /* ================================================================
//        GUEST SUCCESS MODAL WITH COUNTDOWN
//     ================================================================ */
//     function showGuestSuccessModal(bookingId, payload, expiresAt) {
//         var startH  = parseInt(payload.start.slice(0, 2), 10);
//         var dateFmt = new Date(payload.date + 'T00:00:00').toLocaleDateString([], {
//             weekday: 'long', year: 'numeric', month: 'long', day: 'numeric',
//         });

//         $('#mxGuestBookingId').text(bookingId);
//         $('#mxgName').text(payload.guest_name);
//         $('#mxgPhone').text(payload.guest_phone);
//         $('#mxgLift').text(LIFT_LABELS[payload.lift] || getActiveLiftLabel());
//         $('#mxgDate').text(dateFmt);
//         $('#mxgTime').text(formatTimePoint(startH));
//         $('#mxgDuration').text(payload.hours + ' hour' + (payload.hours > 1 ? 's' : ''));
// var slotText = prettyRange(payload.date, startH, payload.hours);

// $('#mxgSlotTiming').text(slotText);
// $('#mxgTotal').text(formatMoney(getPackageTotal(payload.hours)));
//         startGuestTimer(expiresAt);
//         openModal('#mxGuestSuccessModal');
//     }

//     /* ================================================================
//        COUNTDOWN TIMER
//     ================================================================ */
//     var guestTimerInterval = null;

//     function startGuestTimer(expiresAt) {
//         if (guestTimerInterval) clearInterval(guestTimerInterval);
//         var expiryTime = new Date(expiresAt).getTime();

//         guestTimerInterval = setInterval(function () {
//             var distance = expiryTime - Date.now();
//             if (distance < 0) {
//                 clearInterval(guestTimerInterval);
//                 $('#mxGuestTimer').text('EXPIRED').css('color', '#ef4444');
//                 return;
//             }
//             var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
//             var seconds = Math.floor((distance % (1000 * 60)) / 1000);
//             $('#mxGuestTimer').text(pad2(minutes) + ':' + pad2(seconds));
//             if (minutes < 5) $('#mxGuestTimer').css('color', '#f59e0b');
//         }, 1000);
//     }

//     /* ================================================================
//        PHONE FORMATTING
//     ================================================================ */
//     $('#guestPhone').on('input', function () {
//         var v = $(this).val().replace(/\D/g, '');
//         var f = '';
//         if (v.length > 0) f = '(' + v.substring(0, 3);
//         if (v.length >= 4) f += ') ' + v.substring(3, 6);
//         if (v.length >= 7) f += '-' + v.substring(6, 10);
//         $(this).val(f);
//     });

//     /* ================================================================
//        CLOSE GUEST MODAL
//     ================================================================ */
//     $('#mxGuestSuccessModal').on('click', function (e) {
//         if ($(e.target).is('#mxGuestSuccessModal')) {
//             if (confirm('Are you sure? Your slot reservation will be lost if you haven\'t called to confirm.')) {
//                 closeModal('#mxGuestSuccessModal');
//                 if (guestTimerInterval) clearInterval(guestTimerInterval);
//                 location.reload();
//             }
//         }
//     });

//     /* ================================================================
//    GUEST SUCCESS MODAL - PAY NOW BUTTON
// ================================================================ */
// $(document).on('click', '#mxGuestSuccessModal #mxSummaryPay', function(e) {
//     e.preventDefault();

//     // Get the stored booking payload
//     var guestPayload = sessionStorage.getItem('mx_guest_booking_payload');
//     if (!guestPayload) {
//         alert('Booking data not found. Please try again.');
//         return;
//     }

//     // Close guest success modal
//     closeModal('#mxGuestSuccessModal');

//     // Clear the guest timer
//     if (guestTimerInterval) {
//         clearInterval(guestTimerInterval);
//         guestTimerInterval = null;
//     }

//     // Prepare payment modal
//     var payload = JSON.parse(guestPayload);
//     var total = getPackageTotal(payload.hours);

//     $('#mxPayAmount').text(formatMoney(total));
//     $('#mxPayBtnAmt').text(formatMoney(total));

//     // Reset payment form
//     $('#mxCardNum, #mxCardExp, #mxCardCvv, #mxCardName').val('');
//     $('#mxUpiId').val('');
//     $('input[name="mxBank"]').prop('checked', false);
//     $('#mxPayError').addClass('d-none').text('');
//     $('#mxPayNowBtn').prop('disabled', false);
//     $('#mxPaySpinner').addClass('d-none');
//     $('#mxPayBtnText').html('Pay <span id="mxPayBtnAmt">' + formatMoney(total) + '</span>');

//     // Set card tab as active
//     $('.mxs-pay-tab').removeClass('active').filter('[data-tab="card"]').addClass('active');
//     $('.mxs-pay-panel').removeClass('active');
//     $('#mxPayPanel-card').addClass('active');

//     // Open payment modal
//     openPayModal();
// });


// /* ================================================================
//    UPDATE GUEST BOOKING SUBMISSION
// ================================================================ */
// // Update the existing guest booking form submission
// var originalGuestSubmit = $('#guestBookingForm').data('events')?.submit?.[0]?.handler;
// $('#guestBookingForm').off('submit').on('submit', async function (e) {
//     e.preventDefault();
//     var $err = $('#guestErrorMsg').addClass('d-none').text('');

//     var rawPhone = $('#guestPhone').val().replace(/\D/g, '');
//     if (rawPhone.length !== 10) {
//         $err.text('Please enter a valid 10-digit US phone number.').removeClass('d-none');
//         return;
//     }

//     var payload          = mxGetBookingPayload();
//     payload.guest_name   = $('#guestName').val().trim();
//     payload.guest_phone  = '+1' + rawPhone;

//     if (!payload.date || !payload.start) {
//         $err.text('Booking details are missing. Please go back and select a date and time.').removeClass('d-none');
//         return;
//     }

//     // Store payload for payment later
//     sessionStorage.setItem('mx_guest_booking_payload', JSON.stringify(payload));

//     try {
//         var res  = await fetch('/booking/guest', {
//             method: 'POST',
//             credentials: 'same-origin',
//             headers: {
//                 'Content-Type': 'application/json',
//                 'X-CSRF-TOKEN': window.MX_CSRF,
//                 'Accept':       'application/json',
//             },
//             body: JSON.stringify(payload),
//         });

//         var data = await res.json().catch(function () { return {}; });

//         if (!res.ok || !data.status) {
//             $err.text(data.message || 'Booking failed. Please try again.').removeClass('d-none');
//             return;
//         }

//         var inst = bootstrap.Modal.getInstance(document.getElementById('mxAuthModal'));
//         if (inst) inst.hide();

//         // Store booking ID for later use
//         sessionStorage.setItem('mx_guest_booking_id', data.booking_id);

//         showGuestSuccessModal(data.booking_id, payload, data.expires_at);

//     } catch (err) {
//         $err.text('Network error. Please try again.').removeClass('d-none');
//     }
// });

// /* ================================================================
//    UPDATE PAYMENT SUBMISSION FOR GUEST BOOKINGS
// ================================================================ */
// // Modify the existing payment button click handler
// $('#mxPayNowBtn').off('click').on('click', function() {
//     var guestPayload = sessionStorage.getItem('mx_guest_booking_payload');

//     if (guestPayload) {
//         // This is a guest booking payment
//         simulateDemoPayment(function() {
//             confirmGuestBookingWithPayment();
//         });
//     } else {
//         // Regular logged-in user payment
//         simulateDemoPayment(function() {
//             submitBooking(JSON.parse(sessionStorage.getItem('mx_booking_payload') || '{}'));
//         });
//     }
// });


// /* ================================================================
//    CONFIRM GUEST BOOKING AFTER PAYMENT
// ================================================================ */
// async function confirmGuestBookingWithPayment() {
//     var payload = JSON.parse(sessionStorage.getItem('mx_guest_booking_payload'));
//     var bookingId = sessionStorage.getItem('mx_guest_booking_id');

//     if (!payload || !bookingId) {
//         alert('Booking information not found.');
//         return;
//     }

//     try {
//         var res = await fetch('/booking/guest/confirm-payment', {
//             method: 'POST',
//             credentials: 'same-origin',
//             headers: {
//                 'Content-Type': 'application/json',
//                 'X-CSRF-TOKEN': window.MX_CSRF,
//                 'Accept': 'application/json',
//             },
//             body: JSON.stringify({
//                 booking_id: bookingId,
//                 payment_method: $('.mxs-pay-tab.active').data('tab'),
//                 amount: getPackageTotal(payload.hours)
//             }),
//         });

//         var data = await res.json().catch(function () { return {}; });

//         if (!res.ok || !data.status) {
//             alert(data.message || 'Payment confirmation failed. Please contact us.');
//             return;
//         }

//         // Clear session storage
//         sessionStorage.removeItem('mx_guest_booking_payload');
//         sessionStorage.removeItem('mx_guest_booking_id');

//         // Show success receipt
//         openSuccessReceipt(bookingId, payload);

//     } catch (err) {
//         console.error('Payment confirmation error:', err);
//         alert('Network error. Your booking is reserved. Please contact us to complete payment.');
//     }
// }
//     /* ----------------------------------------------------------------
//        PER-LIFT SLOT ISOLATION
//     ---------------------------------------------------------------- */
//     function slotKey(dateStr, liftKey) {
//         return dateStr + '__' + (liftKey || selectedLift || 'all');
//     }

//     function isSlotBooked(dateStr, timeStr, liftKey) {
//         liftKey = liftKey || selectedLift || 'all';
//         var specific = bookedSlots[slotKey(dateStr, liftKey)] || [];
//         var generic  = bookedSlots[slotKey(dateStr, 'all')]   || [];
//         return specific.indexOf(timeStr) !== -1 || generic.indexOf(timeStr) !== -1;
//     }

//     function isHourFree(dateStr, hour, liftKey) {
//         var wh = getWorkingHours(dateStr);
//         if (!wh || hour < wh.start || hour >= wh.end) return false;
//         return !isSlotBooked(dateStr, pad2(hour) + ':00', liftKey);
//     }

//     function isDayFullyFree(dateStr, liftKey) {
//         var slots = getWorkingSlots(dateStr);
//         return slots.length > 0 && slots.every(function (t) { return !isSlotBooked(dateStr, t, liftKey); });
//     }

//     function isDayHasAnyFreeHour(dateStr, liftKey) {
//         return getWorkingSlots(dateStr).some(function (t) { return !isSlotBooked(dateStr, t, liftKey); });
//     }

//     function dayFreeRatio(dateStr, liftKey) {
//         var slots = getWorkingSlots(dateStr);
//         if (!slots.length) return 0;
//         var free = slots.filter(function (t) { return !isSlotBooked(dateStr, t, liftKey); }).length;
//         return free / slots.length;
//     }

//     function isDateAvailableByPackage(dateStr, packHours, liftKey) {
//         liftKey = liftKey || selectedLift || 'all';
//         if (!getWorkingHours(dateStr)) return false;
//         if (packHours === 1)  return isDayHasAnyFreeHour(dateStr, liftKey);
//         if (packHours === 9)  return getWorkingSlots(dateStr).length >= 9 && isDayFullyFree(dateStr, liftKey);
//         if (packHours === 18) {
//             if (getWorkingSlots(dateStr).length < 9) return false;
//             var nxt = nextDate(dateStr);
//             return getWorkingSlots(nxt).length >= 9 && isDayFullyFree(dateStr, liftKey) && isDayFullyFree(nxt, liftKey);
//         }
//         return isDayHasAnyFreeHour(dateStr, liftKey);
//     }

//     function addWorkingHours(startDateStr, startHour, hoursNeeded) {
//         var rem = hoursNeeded, cur = startDateStr, h = startHour;
//         while (rem > 0) {
//             var wh = getWorkingHours(cur);
//             if (!wh) { cur = addDaysStr(cur, 1); h = 0; continue; }
//             if (h < wh.start) h = wh.start;
//             if (h >= wh.end)  { cur = addDaysStr(cur, 1); h = 0; continue; }
//             var av = wh.end - h;
//             if (rem <= av) { h += rem; rem = 0; }
//             else { rem -= av; cur = addDaysStr(cur, 1); h = 0; }
//         }
//         return { endDate: cur, endHour: h };
//     }

//     function buildEndLabel(dateStr, startTimeStr, hours) {
//         var startH = parseInt(startTimeStr.slice(0, 2), 10);
//         var r      = addWorkingHours(dateStr, startH, hours);
//         var fmt    = function (d) {
//             return new Date(d + 'T00:00:00').toLocaleDateString([], { month: 'short', day: '2-digit', year: 'numeric' });
//         };
//         return r.endDate !== dateStr
//             ? fmt(r.endDate) + ' ' + formatTimePoint(r.endHour)
//             : formatTimePoint(r.endHour);
//     }

//     function prettyRange(dateStr, startHour, hoursNeeded) {
//         var r   = addWorkingHours(dateStr, startHour, hoursNeeded);
//         var fmt = function (d) {
//             return new Date(d + 'T00:00:00').toLocaleDateString([], { year: 'numeric', month: 'short', day: '2-digit' });
//         };
//         var d1 = fmt(dateStr), d2 = fmt(r.endDate);
//         var t1 = formatTimePoint(startHour), t2 = formatTimePoint(r.endHour);
//         return d1 !== d2
//             ? d1 + ' \u2022 ' + t1 + ' \u2192 ' + d2 + ' \u2022 ' + t2
//             : d1 + ' \u2022 ' + t1 + ' \u2013 ' + t2;
//     }

//     function validateConsecutiveCrossDay(startDateStr, startTimeStr, hoursNeeded, liftKey) {
//         liftKey    = liftKey || selectedLift || 'all';
//         var startH = parseInt(startTimeStr.slice(0, 2), 10);
//         var wh0    = getWorkingHours(startDateStr);
//         if (!wh0) return { ok: false, message: 'Closed on selected day.' };
//         if (startH < wh0.start || startH >= wh0.end)
//             return { ok: false, message: 'Start time outside working hours.' };

//         var rem = hoursNeeded, cur = startDateStr, h = startH;
//         while (rem > 0) {
//             var wh = getWorkingHours(cur);
//             if (!wh) return { ok: false, message: 'Closed on ' + cur + '.' };
//             if (h < wh.start) h = wh.start;
//             if (h >= wh.end)  { cur = addDaysStr(cur, 1); h = 0; continue; }
//             if (!isHourFree(cur, h, liftKey))
//                 return { ok: false, message: pad2(h) + ':00 on ' + cur + ' is already booked.' };
//             rem--; h++;
//             if (h >= wh.end && rem > 0) { cur = addDaysStr(cur, 1); h = 0; }
//         }
//         return { ok: true };
//     }

//     /* ================================================================
//        PRICING
//     ================================================================ */
//     function getSelectedCard()      { return $('.mx-pricecard.mx-selected').first(); }
//     function getRatePerHour()       { return Number(getSelectedCard().data('price')) || 0; }
//     function getPackageTotal(hours) { return getRatePerHour() * hours; }

//     /* ================================================================
//        LIFT LABELS
//     ================================================================ */
//     var LIFT_LABELS = {
//         four:    'Four-Post Style Lift',
//         two:     'Two-Post Lift',
//         scissor: 'Scissor Lift',
//         flat:    'Motorcycle Lift',
//         flat2:   'Alignment Rack',
//     };

//     function getActiveLiftLabel() {
//         if (PRODUCT_MODE && AUTO_LIFT_NAME) return AUTO_LIFT_NAME;
//         return LIFT_LABELS[selectedLift] || selectedLift || '\u2014';
//     }

//     /* ================================================================
//        BOOK-NOW GATE
//     ================================================================ */
//     function updateBookBtnState() {
//         var hasLift = selectedLift !== null;
//         var hasDate = selectedDate !== null;
//         var ready, hint = '';

//         if (PRODUCT_MODE) {
//             ready = hasDate;
//             if (!hasDate) hint = 'Please pick an available date on the calendar.';
//         } else {
//             ready = hasLift && hasDate;
//             if (!hasLift && !hasDate) hint = 'Select a lift type and a date to continue.';
//             else if (!hasLift)        hint = 'Select a lift type above to enable booking.';
//             else if (!hasDate)        hint = 'Pick an available date on the calendar.';
//         }

//         $('#openDayCalendar, #openDayCalendarMb')
//             .prop('disabled', !ready)
//             .toggleClass('enabled', ready)
//             .text(ready ? 'Book for ' + selectedDate : 'Book Now');

//         $('.mx-book-hint').text(hint);
//     }

//     /* ================================================================
//        MODALS
//     ================================================================ */
//     function openModal(id)  { $(id).addClass('show').attr('aria-hidden', 'false'); }
//     function closeModal(id) { $(id).removeClass('show').attr('aria-hidden', 'true'); }

//     function openSlotModal()     { openModal('#mxSlotModal');    }
//     function closeSlotModal()    { closeModal('#mxSlotModal');   }
//     function openSummaryModal()  { openModal('#mxSummaryModal'); }
//     function closeSummaryModal() { closeModal('#mxSummaryModal'); }
//     function openPayModal()      { openModal('#mxPayModal');     }
//     function closePayModal()     { closeModal('#mxPayModal');    }
//     function openSuccessModal()  { openModal('#mxSuccessModal'); }

//     $(document).on('keydown', function (e) {
//         if (e.key !== 'Escape') return;
//         closeSlotModal(); closeSummaryModal(); closePayModal();
//     });

//     /* ================================================================
//        HOUR CONTROLS
//     ================================================================ */
//     function toggleHourControls(lock) {
//         $('#mxHMinus, #mxHPlus').prop('disabled', lock).toggleClass('mx-disabled', lock);
//     }

//     function setHours(val) {
//         // selectedHours = Math.max(selectedPackHours, Math.min(val, 48));

//           var maxHours = selectedPackHours === 1 ? 8 : selectedPackHours;
//     selectedHours = Math.max(selectedPackHours, Math.min(val, maxHours));
//         var startH = parseInt(selectedStartTime.slice(0, 2), 10);
//         var check  = validateConsecutiveCrossDay(selectedDate, selectedStartTime, selectedHours);

//         $('#mxSelectedHours').text(selectedHours);
//         $('#mxTotalText').text(formatMoney(getPackageTotal(selectedHours)));
//         $('#mxSlotText').text(prettyRange(selectedDate, startH, selectedHours));
//         $('#mxHintText').text(
//             check.ok
//                 ? 'Continuous booking for ' + selectedHours + ' hour' + (selectedHours > 1 ? 's' : '') + '.'
//                 : check.message
//         );
//         $('#mxModalConfirm').prop('disabled', !check.ok).css('opacity', check.ok ? '1' : '.5');
//     }

//     $('#mxHMinus').on('click', function () { setHours(selectedHours - 1); });
//     $('#mxHPlus').on('click',  function () { setHours(selectedHours + 1); });

//     /* ================================================================
//        SUMMARY MODAL
//     ================================================================ */
//     function populateAndOpenSummary() {
//         var rate    = getRatePerHour();
//         var total   = getPackageTotal(selectedHours);
//         var startH  = parseInt(selectedStartTime.slice(0, 2), 10);
//         var dateFmt = new Date(selectedDate + 'T00:00:00').toLocaleDateString([], {
//             weekday: 'long', year: 'numeric', month: 'long', day: 'numeric',
//         });
//         $('#mxsWorkstation').text('Workstation 1');
//         $('#mxsLift').text(getActiveLiftLabel());
//         $('#mxsDate').text(dateFmt);
//         $('#mxsStart').text(formatTimePoint(startH));
//         $('#mxsDuration').text(selectedHours + ' hour' + (selectedHours > 1 ? 's' : ''));
//         $('#mxsEnd').text(buildEndLabel(selectedDate, selectedStartTime, selectedHours));
//         $('#mxsRate').text(formatMoney(rate) + ' / hr');
//         $('#mxsHours').text(selectedHours);
//         $('#mxsTotal').text(formatMoney(total));
//         $('#mxPayAmount').text(formatMoney(total));
//         $('#mxPayBtnAmt').text(formatMoney(total));
//         openSummaryModal();
//     }

//     /* ================================================================
//        TIME-SLOT GRID (per-lift aware)
//     ================================================================ */
//     function renderTimeSlots(dateStr) {
//         var $grid = $('#mxTimeGrid').empty();

//         $('#mxSelectedDateText').text(
//             new Date(dateStr + 'T00:00:00').toLocaleDateString([], {
//                 weekday: 'long', year: 'numeric', month: 'long', day: 'numeric',
//             })
//         );
//         selectedStartTime = null;
//         $('#mxPickedTimeText').text('None');
//         $('#mxContinueBtn').prop('disabled', true);

//         var wh = getWorkingHours(dateStr);
//         if (!wh) {
//             $grid.html('<div class="mx-slot-closed">This day is closed. No slots available.</div>');
//             return;
//         }

//         if (selectedPackHours === 9 || selectedPackHours === 18) {
//             var startVal = pad2(wh.start) + ':00';
//             var chk      = validateConsecutiveCrossDay(dateStr, startVal, selectedPackHours);
//             if (!chk.ok) {
//                 $grid.html(
//                     '<div class="mx-slot-unavail">Not available for <strong>' + selectedPackHours +
//                     'h</strong> from 9:00 AM.<br><small>' + chk.message + '</small></div>'
//                 );
//                 return;
//             }
//             $('<button>', {
//                 type: 'button', class: 'mx-slot available', 'data-value': startVal,
//                 html: '<span class="mx-slot-time">Start at ' + formatTimePoint(wh.start) + '</span>' +
//                       '<span class="mx-slot-badge free">Full ' + selectedPackHours + 'h block</span>',
//             }).on('click', function () {
//                 $('.mx-slot').removeClass('selected'); $(this).addClass('selected');
//                 selectedStartTime = startVal;
//                 $('#mxPickedTimeText').text(formatTimePoint(wh.start));
//                 $('#mxContinueBtn').prop('disabled', false);
//             }).appendTo($grid);
//             return;
//         }

//         var slots = getWorkingSlots(dateStr);
//         if (!slots.length) {
//             $grid.html('<div class="mx-slot-closed">No working slots on this day.</div>');
//             return;
//         }

//         slots.forEach(function (value) {
//             var h      = parseInt(value.slice(0, 2), 10);
//             var label  = formatTimePoint(h) + ' \u2013 ' + formatTimePoint(h + 1);
//             var booked = isSlotBooked(dateStr, value, selectedLift);

//             var $btn = $('<button>', {
//                 type: 'button',
//                 class: 'mx-slot ' + (booked ? 'booked' : 'available'),
//                 disabled: booked,
//                 'data-value': value,
//                 html: '<span class="mx-slot-time">' + label + '</span>' +
//                       '<span class="mx-slot-badge ' + (booked ? 'taken' : 'free') + '">' +
//                       (booked ? 'Booked' : 'Available') + '</span>',
//             });

//             if (!booked) {
//                 $btn.on('click', function () {
//                     $('.mx-slot').removeClass('selected'); $(this).addClass('selected');
//                     selectedStartTime = value;
//                     $('#mxPickedTimeText').text(label);
//                     $('#mxContinueBtn').prop('disabled', false);
//                 });
//             }
//             $grid.append($btn);
//         });
//     }

//     /* ================================================================
//        VIEW SWITCH
//     ================================================================ */
//     function showTimeView() { $('#calendarWrap').hide(); $('#mxTimeView').show(); }
//     function showDateView() {
//         $('#mxTimeView').hide(); $('#calendarWrap').show();
//         $('.mx-gridWrap, .mx-legendMini').show();
//         selectedStartTime = null;
//         $('#mxPickedTimeText').text('None');
//         $('#mxContinueBtn').prop('disabled', true);
//     }

//     /* ================================================================
//        CALENDAR DATA
//     ================================================================ */
//     async function loadCalendarData(monthStr, workstation) {
//         monthStr    = monthStr    || null;
//         workstation = workstation || 1;
//         try {
//             var params = new URLSearchParams();
//             if (monthStr) params.append('month', monthStr);
//             params.append('workstation', workstation);
//             var res  = await fetch('/booking/calendar-data?' + params.toString(), {
//                 method: 'GET', credentials: 'same-origin', headers: { Accept: 'application/json' },
//             });
//             var data = await res.json();
//             dayData = data.dayData || {};

//             var raw = data.bookedSlots || {};
//             bookedSlots = {};
//             Object.keys(raw).forEach(function (k) {
//                 bookedSlots[k.indexOf('__') !== -1 ? k : k + '__all'] = raw[k];
//             });
//         } catch (_) { /* offline / demo */ }
//         if (fpInstance) fpInstance.redraw();
//     }

//     loadCalendarData(null, 1);

//     /* ================================================================
//        CALENDAR COLOUR SCALE
//     ================================================================ */
//     function dayAvailClass(dateStr) {
//         if (!getWorkingHours(dateStr)) return 'day-unavailable';
//         var info = dayData[dateStr];
//         if (info && info.status === 'booked')      return 'day-booked';
//         if (info && info.status === 'unavailable') return 'day-unavailable';
//         if (!isDateAvailableByPackage(dateStr, selectedPackHours, selectedLift)) return 'day-unavailable';
//         var r = dayFreeRatio(dateStr, selectedLift);
//         if (r === 0)   return 'day-unavailable';
//         if (r >= 0.70) return 'day-available';
//         if (r >= 0.30) return 'day-partial';
//         return 'day-scarce';
//     }

//     /* ================================================================
//        FLATPICKR
//     ================================================================ */
//     function updateMonthNav(fp) {
//         var cur     = new Date(fp.currentYear, fp.currentMonth, 1);
//         var min     = new Date(MIN_MONTH.getFullYear(), MIN_MONTH.getMonth(), 1);
//         var prevBtn = fp.calendarContainer.querySelector('.flatpickr-prev-month');
//         if (!prevBtn) return;
//         var atMin = cur <= min;
//         prevBtn.style.pointerEvents = atMin ? 'none'  : 'auto';
//         prevBtn.style.opacity       = atMin ? '0.3'   : '1';
//     }

//     fpInstance = flatpickr('#bookingDate', {
//         inline: true, dateFormat: 'Y-m-d', disableMobile: true,
//         defaultDate: TODAY,
//         appendTo: document.getElementById('calendarWrap'),
//         minDate:  new Date(),

//         onReady:       function (s, d, fp) { fpInstance = fp; updateMonthNav(fp); },
//         onMonthChange: function (s, d, fp) {
//             updateMonthNav(fp); fp.redraw();
//             loadCalendarData(fp.currentYear + '-' + pad2(fp.currentMonth + 1), 1);
//         },
//         onYearChange:  function (s, d, fp) { updateMonthNav(fp); fp.redraw(); },

//         disable: [function (date) {
//             if (date.getDay() === 6) return true;
//             var info = dayData[flatpickr.formatDate(date, 'Y-m-d')];
//             return info && (info.status === 'unavailable' || info.status === 'booked');
//         }],

//         onDayCreate: function (dObj, dStr, fp, dayElem) {
//             dayElem.classList.remove('day-available','day-partial','day-scarce',
//                                      'day-booked','day-unavailable','day-nextmonth','day-prevmonth');
//             if (dayElem.classList.contains('nextMonthDay')) { dayElem.classList.add('day-nextmonth'); return; }
//             if (dayElem.classList.contains('prevMonthDay')) { dayElem.classList.add('day-prevmonth'); return; }

//             var key = fp.formatDate(dayElem.dateObj, 'Y-m-d');
//             if (dayElem.classList.contains('flatpickr-disabled')) {
//                 var info = dayData[key];
//                 dayElem.classList.add((info && info.status === 'booked') ? 'day-booked' : 'day-unavailable');
//                 return;
//             }
//             dayElem.classList.add(dayAvailClass(key));

//             var slots = getWorkingSlots(key);
//             var free  = slots.filter(function (t) { return !isSlotBooked(key, t, selectedLift); }).length;
//             if (slots.length > 0 && free > 0 && free < slots.length) {
//                 dayElem.setAttribute('title', free + ' of ' + slots.length + ' slots available');
//                 var dot = document.createElement('span');
//                 dot.className = 'mx-day-dot';
//                 dayElem.appendChild(dot);
//             }
//         },

//         onChange: function (selectedDates, dateStr) {
//             if (!dateStr) { selectedDate = null; updateBookBtnState(); return; }
//             selectedDate = isDateAvailableByPackage(dateStr, selectedPackHours, selectedLift) ? dateStr : null;
//             updateBookBtnState();
//         },
//     });

//     /* ================================================================
//        PRICE CARDS
//     ================================================================ */
//     function selectPackage(hours) {
//         selectedPackHours = hours;
//         selectedHours     = hours;
//         $('.mx-pricecard').each(function () {
//             var h = parseInt($(this).data('hours'), 10);
//             $(this).toggleClass('mx-selected', h === hours).toggleClass('mx-dimmed', h !== hours);
//         });
//         toggleHourControls(hours === 9 || hours === 18);
//         if (fpInstance) fpInstance.redraw();
//         if (selectedDate) renderTimeSlots(selectedDate);
//     }

//     (function initPackage() {
//         var $c = $('.mx-pricecard.mx-selected').first();
//         if ($c.length) { selectedPackHours = parseInt($c.data('hours'), 10) || 1; selectedHours = selectedPackHours; }
//         selectPackage(selectedPackHours);
//     }());

//     $(document).on('click', '.mx-pricecard', function () {
//         if ($(this).closest('a').length) return;
//         selectPackage(parseInt($(this).data('hours'), 10) || 1);
//         setTimeout(function () { scrollToEl($('#calendarSection')[0]); }, 200);
//     });

//     /* ================================================================
//        LIFT BUTTONS (direct-booking mode only)
//     ================================================================ */
//     var LIFT_DATA = {
//         four:    { img: 'assets/images/rentals/Media (8).jpg',      points: ['Heavy-duty support', 'Perfect for long-hour jobs', 'Maximum stability & safety'] },
//         two:     { img: 'assets/images/rentals/Media (6).jpg',      points: ['Quick vehicle access', 'Ideal for mechanical repairs', 'Compact and space efficient'] },
//         scissor: { img: 'assets/images/rentals/scissor.jpg',        points: ['Low profile design', 'Fast lifting operation', 'Great for tire & brake work'] },
//         flat:    { img: 'assets/images/rentals/motocycle.jpg',       points: ['Designed for motorcycles', 'Easy loading & unloading', 'Stable flat platform'] },
//         flat2:   { img: 'assets/images/rentals/allignmentrack.jpg', points: ['Precision wheel alignment', 'Extended ramp length', 'Perfect for alignment jobs'] },
//     };

//     /* ----------------------------------------------------------------
//        renderLiftPriceCards
//        FIX: reads the json_encoded blob (no trailing comma possible now).
//        Also adds the membership card at the bottom every time.
//     ---------------------------------------------------------------- */
//     function renderLiftPriceCards(liftKey) {
//         var wrap = document.getElementById('mxPriceCardsWrap');
//         if (!wrap) return;

//         var raw = document.getElementById('mxAllLiftPrices');
//         if (!raw) return;

//         var allPrices = {};
//         try {
//             allPrices = JSON.parse(raw.textContent || raw.innerText);
//         } catch (e) {
//             console.error('Price JSON parse error:', e, raw.textContent);
//             wrap.innerHTML = '<p style="color:red;font-size:13px;">Price data failed to load.</p>';
//             return;
//         }

//         var liftData = allPrices[liftKey];
//         if (!liftData || !liftData.prices || !liftData.prices.length) {
//             wrap.innerHTML = '<p style="color:var(--color-text-secondary);font-size:13px;padding:8px 0;">No pricing available for this lift.</p>';
//             return;
//         }

//         var html = liftData.prices.map(function (p, i) {
//             var label    = p.hours === 1 ? '1 Hour' : p.hours + ' Hours';
//             var priceStr = p.is_membership
//                 ? 'Members Only'
//                 : (p.hours > 1 ? '$' + p.price + ' / hour' : '$' + p.price);

//             var card = '<div class="mx-pricecard ' + (i === 0 && !p.is_membership ? 'mx-selected' : '') + '"' +
//                        ' data-hours="' + p.hours + '"' +
//                        ' data-price="' + p.price + '"' +
//                        ' data-total="' + (p.price * p.hours) + '">' +
//                        '<span class="mx-hours">' + label + '</span>' +
//                        '<span class="mx-price">' + priceStr + '</span>' +
//                        '</div>';

//             return p.is_membership
//                 ? '<a href="/membership" class="mx-pricecard-link">' + card + '</a>'
//                 : card;
//         }).join('');

//         // Always append membership card at bottom
//         html += '<a href="/membership" class="mx-pricecard-link">' +
//                 '<div class="mx-pricecard mx-membership">' +
//                 '<span class="mx-hours">18 Hours</span>' +
//                 '<span class="mx-price">Members Only</span>' +
//                 '</div></a>';

//         wrap.innerHTML = html;

//         var $first = $(wrap).find('.mx-pricecard.mx-selected').first();
//         if ($first.length) {
//             selectedPackHours = parseInt($first.data('hours'), 10) || 1;
//             selectedHours     = selectedPackHours;
//             toggleHourControls(selectedPackHours === 9 || selectedPackHours === 18);
//             if (fpInstance) fpInstance.redraw();
//         }

//         $(wrap).find('.mx-pricecard').not($(wrap).find('a .mx-pricecard')).on('click', function () {
//             selectPackage(parseInt($(this).data('hours'), 10) || 1);
//             setTimeout(function () { scrollToEl($('#calendarSection')[0]); }, 200);
//         });
//     }

//     if (!PRODUCT_MODE) {
//         $('.mx-liftbtn').removeClass('active');

//         $(document).on('click', '.mx-liftbtn', function () {
//             $('.mx-liftbtn').removeClass('active');
//             $(this).addClass('active');
//             selectedLift = $(this).data('lift');

//             renderLiftPriceCards(selectedLift);

//             var lift = LIFT_DATA[selectedLift];
//             if (lift) {
//                 $('#mxLiftPlaceholder').hide();
//                 $('#mxLiftPreviewImg').attr('src', lift.img).show();
//                 $('#mxLiftPoints').html(lift.points.map(function (p) { return '<li>' + p + '</li>'; }).join(''));
//             }

//             $('#mxLiftPrompt').addClass('hidden');

//             if (fpInstance) fpInstance.redraw();
//             if (selectedDate && $('#mxTimeView').is(':visible')) renderTimeSlots(selectedDate);

//             if (selectedDate && !isDateAvailableByPackage(selectedDate, selectedPackHours, selectedLift)) {
//                 selectedDate = null;
//             }

//             updateBookBtnState();
//             setTimeout(function () { scrollToEl($('#liftSection')[0]); }, 200);
//         });

//         $(document).on('click', '#mxLiftDropdownMenu .dropdown-item', function (e) {
//             e.preventDefault();
//             $('#mxLiftDropdownBtn').text($(this).text().trim());
//             $('.mx-liftbtn[data-lift="' + $(this).data('lift') + '"]').trigger('click');
//         });
//     }

//     /* ================================================================
//        BOOK NOW → time-slot view
//     ================================================================ */
//     $('#openDayCalendar').on('click', function () {
//         if (!selectedDate) return;
//         if (!PRODUCT_MODE && !selectedLift) return;
//         $(this).prop('disabled', true).removeClass('enabled');
//         $('.mx-gridWrap, .mx-legendMini').hide();
//         showTimeView();
//         renderTimeSlots(selectedDate);
//     });

//     $('#openDayCalendarMb').on('click', function () {
//         if (!selectedDate) return;
//         if (!PRODUCT_MODE && !selectedLift) return;
//         $('.mx-gridWrap, .mx-legendMini').hide();
//         $(this).hide();
//         showTimeView();
//         renderTimeSlots(selectedDate);
//     });

//     $('#mxBackToDate').on('click', function () {
//         showDateView();
//         $('#openDayCalendar').prop('disabled', false).addClass('enabled');
//         $('#openDayCalendarMb').show();
//     });

//     /* ================================================================
//        CONTINUE → slot modal
//     ================================================================ */
//     $('#mxContinueBtn').on('click', function () {
//         if (!selectedDate || !selectedStartTime) return;
//         selectedHours = selectedPackHours;
//         var startH = parseInt(selectedStartTime.slice(0, 2), 10);
//         var check  = validateConsecutiveCrossDay(selectedDate, selectedStartTime, selectedHours);

//         $('#mxSlotText').text(prettyRange(selectedDate, startH, selectedHours));
//         $('#mxSelectedHours').text(selectedHours);
//         $('#mxTotalText').text(formatMoney(getPackageTotal(selectedHours)));
//         $('#mxHintText').text(
//             check.ok
//                 ? 'Continuous booking for ' + selectedHours + ' hour' + (selectedHours > 1 ? 's' : '') + '.'
//                 : check.message
//         );
//         $('#mxModalConfirm').prop('disabled', !check.ok).css('opacity', check.ok ? '1' : '.5');
//         openSlotModal();
//     });

//     /* ================================================================
//        SLOT MODAL CLOSE
//     ================================================================ */
//     $('#mxModalClose, #mxModalCancel').on('click', closeSlotModal);
//     $('#mxSlotModal').on('click', function (e) { if ($(e.target).is('#mxSlotModal')) closeSlotModal(); });

//     /* ================================================================
//        AUTH
//     ================================================================ */
//     window.MX_CSRF = $('meta[name="csrf-token"]').attr('content') || '';
//     window.MX_IS_LOGGED_IN = (function () {
//         var v = $('#mx-auth-state').data('logged-in');
//         return (v === 1 || v === '1' || v === true);
//     }());

//     function mxGetBookingPayload() {
//         return {
//             date:       selectedDate,
//             start:      selectedStartTime,
//             hours:      selectedHours,
//             total:      getPackageTotal(selectedHours),
//             lift:       selectedLift,
//             package:    selectedPackHours,
//             workstation: 1,
//             product_id: PRODUCT_MODE ? ($meta.data('product-id') || null) : null,
//         };
//     }

//     /* ================================================================
//        SLOT MODAL CONFIRM → auth gate → summary
//     ================================================================ */
//     $('#mxModalConfirm').on('click', function () {
//         var check = validateConsecutiveCrossDay(selectedDate, selectedStartTime, selectedHours);
//         if (!check.ok) { alert(check.message); return; }

//         sessionStorage.setItem('mx_booking_payload', JSON.stringify(mxGetBookingPayload()));

//         if (!window.MX_IS_LOGGED_IN) {
//             closeSlotModal();
//             new bootstrap.Modal(document.getElementById('mxAuthModal')).show();
//             return;
//         }
//         closeSlotModal();
//         populateAndOpenSummary();
//     });

//     function mxContinueAfterAuth() {
//         var raw = sessionStorage.getItem('mx_booking_payload');
//         if (!raw) return;
//         var p = JSON.parse(raw);
//         selectedDate      = p.date;
//         selectedStartTime = p.start;
//         selectedHours     = p.hours;
//         selectedPackHours = p.package;
//         selectedLift      = p.lift;

//         if (!PRODUCT_MODE) {
//             $('.mx-liftbtn').removeClass('active');
//             $('.mx-liftbtn[data-lift="' + selectedLift + '"]').addClass('active');
//             var lift = LIFT_DATA[selectedLift];
//             if (lift) {
//                 $('#mxLiftPlaceholder').hide();
//                 $('#mxLiftPreviewImg').attr('src', lift.img).show();
//                 $('#mxLiftPoints').html(lift.points.map(function (pt) { return '<li>' + pt + '</li>'; }).join(''));
//             }
//             $('#mxLiftPrompt').addClass('hidden');
//         }
//         updateBookBtnState();
//         var inst = bootstrap.Modal.getInstance(document.getElementById('mxAuthModal'));
//         if (inst) inst.hide();
//         populateAndOpenSummary();
//     }

//     /* ================================================================
//        SUMMARY
//     ================================================================ */
//     $('#mxSummaryClose').on('click', closeSummaryModal);
//     $('#mxSummaryModal').on('click', function (e) { if ($(e.target).is('#mxSummaryModal')) closeSummaryModal(); });
//     $('#mxSummaryBack').on('click', function () { closeSummaryModal(); openSlotModal(); });
//     $('#mxSummaryPay').on('click', function () {
//         closeSummaryModal();
//         $('#mxCardNum, #mxCardExp, #mxCardCvv, #mxCardName').val('');
//         $('#mxPayError').addClass('d-none').text('');
//         $('#mxPayNowBtn').prop('disabled', false);
//         $('#mxPaySpinner').addClass('d-none');
//         $('#mxPayBtnText').html('Pay <span id="mxPayBtnAmt">' + $('#mxPayAmount').text() + '</span>');
//         $('.mxs-pay-tab').removeClass('active').filter('[data-tab="card"]').addClass('active');
//         $('.mxs-pay-panel').removeClass('active'); $('#mxPayPanel-card').addClass('active');
//         openPayModal();
//     });

//     /* ================================================================
//        PAY MODAL
//     ================================================================ */
//     $('#mxPayClose').on('click', closePayModal);
//     $('#mxPayModal').on('click', function (e) { if ($(e.target).is('#mxPayModal')) closePayModal(); });

//     $(document).on('click', '.mxs-pay-tab', function () {
//         var tab = $(this).data('tab');
//         $('.mxs-pay-tab').removeClass('active'); $(this).addClass('active');
//         $('.mxs-pay-panel').removeClass('active'); $('#mxPayPanel-' + tab).addClass('active');
//     });

//     $('#mxCardNum').on('input', function () {
//         var r = $(this).val().replace(/\D/g, '').slice(0, 16);
//         $(this).val(r.match(/.{1,4}/g) ? r.match(/.{1,4}/g).join(' ') : r);
//     });
//     $('#mxCardExp').on('input', function () {
//         var v = $(this).val().replace(/\D/g, '').slice(0, 4);
//         if (v.length >= 3) v = v.slice(0, 2) + ' / ' + v.slice(2);
//         $(this).val(v);
//     });

//     /* ================================================================
//        DEMO PAYMENT
//     ================================================================ */
//     function simulateDemoPayment(onSuccess) {
//         var $btn = $('#mxPayNowBtn'), $sp = $('#mxPaySpinner'), $err = $('#mxPayError');
//         if ($('.mxs-pay-tab.active').data('tab') === 'card') {
//             if (!$('#mxCardNum').val().replace(/\s/g, '').match(/^\d{16}$/) ||
//                 !$('#mxCardExp').val().match(/\d{2}\s*\/\s*\d{2}/) ||
//                 !$('#mxCardCvv').val().match(/^\d{3}$/) ||
//                 !$('#mxCardName').val().trim()) {
//                 $err.text('Please fill in all card details correctly.').removeClass('d-none');
//                 return;
//             }
//         }
//         $err.addClass('d-none');
//         $btn.prop('disabled', true);
//         $('#mxPayBtnText').text('Processing\u2026');
//         $sp.removeClass('d-none');
//         setTimeout(function () {
//             $sp.addClass('d-none');
//             $('#mxPayBtnText').text('\u2713 Payment Successful!');
//             setTimeout(function () { closePayModal(); onSuccess(); }, 700);
//         }, 1800);
//     }

//     $('#mxPayNowBtn').on('click', function () {
//         simulateDemoPayment(function () {
//             submitBooking(JSON.parse(sessionStorage.getItem('mx_booking_payload') || '{}'));
//         });
//     });

//     /* ================================================================
//        SUBMIT BOOKING
//     ================================================================ */
//     async function submitBooking(payload) {
//         try {
//             var res  = await fetch('/booking/confirm', {
//                 method: 'POST', credentials: 'same-origin',
//                 headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.MX_CSRF, 'Accept': 'application/json' },
//                 body: JSON.stringify(payload),
//             });
//             var data = await res.json().catch(function () { return {}; });
//             sessionStorage.removeItem('mx_booking_payload');
//             if (!res.ok || !data.status) { alert(data.message || 'Booking failed. Please try again.'); return; }
//             openSuccessReceipt(data.booking_id || ('MX-' + Date.now()), payload);
//         } catch (_) {
//             sessionStorage.removeItem('mx_booking_payload');
//             openSuccessReceipt('MX-DEMO-' + Date.now(), payload);
//         }
//     }

//     /* ================================================================
//        SUCCESS RECEIPT
//     ================================================================ */
//     function openSuccessReceipt(bookingId, payload) {
//         var rate    = getRatePerHour();
//         var total   = getPackageTotal(payload.hours);
//         var startH  = parseInt(payload.start.slice(0, 2), 10);
//         var dateFmt = new Date(payload.date + 'T00:00:00').toLocaleDateString([], {
//             weekday: 'long', year: 'numeric', month: 'long', day: 'numeric',
//         });
//         $('#mxSuccessBookingId').text(bookingId);
//         $('#mxrWorkstation').text('Workstation 1');
//         $('#mxrLift').text(LIFT_LABELS[payload.lift] || getActiveLiftLabel());
//         $('#mxrDate').text(dateFmt);
//         $('#mxrStart').text(formatTimePoint(startH));
//         $('#mxrDuration').text(payload.hours + ' hour' + (payload.hours > 1 ? 's' : ''));
//         $('#mxrEnd').text(buildEndLabel(payload.date, payload.start, payload.hours));
//         $('#mxrRate').text(formatMoney(rate) + ' / hr');
//         $('#mxrTotal').text(formatMoney(total));
//         openSuccessModal();
//     }

//     /* ================================================================
//        PRINT
//     ================================================================ */
//     $('#mxPrintBtn').on('click', function () { window.print(); });

// /* ================================================================
//    AUTH FORMS - CLEAN WORKING VERSION
// ================================================================ */

// /* ================= LOGIN ================= */
// $('#mxLoginForm').on('submit', async function (e) {

//     e.preventDefault();

//     let $err = $('#loginErrorMsg');
//     $err.addClass('d-none').text('');

//     let loginUrl = $('#mx-routes').data('login-url') || '/popup-login';

//     try {

//         let res = await fetch(loginUrl, {
//             method: 'POST',
//             credentials: 'same-origin', // IMPORTANT FOR SESSION LOGIN
//             headers: {
//                 'Content-Type': 'application/json',
//                 'Accept': 'application/json',
//                 'X-CSRF-TOKEN':
//                     window.MX_CSRF ||
//                     $('meta[name="csrf-token"]').attr('content') ||
//                     ''
//             },

//             body: JSON.stringify({
//                 email: $(this).find('[name=email]').val().trim(),
//                 password: $(this).find('[name=password]').val(),
//             })
//         });

//         let data = await res.json().catch(() => ({}));

//         /* ---------- ERROR ---------- */
//         if (!res.ok) {

//             $err
//                 .text(data.message || 'Login failed. Please check your credentials.')
//                 .removeClass('d-none');

//             return;
//         }

//         /* ---------- SUCCESS ---------- */

//         // update frontend auth state
//         window.MX_IS_LOGGED_IN = true;

//         $('#mx-auth-state').attr('data-logged-in', '1');

//         // close modal
//         const modalEl = document.getElementById('mxAuthModal');

//         if (modalEl) {

//             const modalInstance = bootstrap.Modal.getInstance(modalEl);

//             if (modalInstance) {
//                 modalInstance.hide();
//             } else {
//                 $(modalEl).modal('hide');
//             }
//         }

//         // wait for modal animation then reload
//         setTimeout(() => {

//             // HARD RELOAD
//             window.location.href = window.location.href;

//         }, 500);

//     } catch (err) {

//         console.error(err);

//         $err
//             .text('Network error. Please try again.')
//             .removeClass('d-none');
//     }
// });


// /* ================= REGISTER ================= */
// $('#mxRegisterForm').on('submit', async function (e) {

//     e.preventDefault();

//     let $err = $('#registerErrorMsg');
//     $err.addClass('d-none').text('');

//     let registerUrl =
//         $('#mx-routes').data('register-url') || '/popup-register';

//     try {

//         let res = await fetch(registerUrl, {
//             method: 'POST',
//             credentials: 'same-origin', // IMPORTANT
//             headers: {
//                 'Content-Type': 'application/json',
//                 'Accept': 'application/json',
//                 'X-CSRF-TOKEN':
//                     window.MX_CSRF ||
//                     $('meta[name="csrf-token"]').attr('content') ||
//                     ''
//             },

//             body: JSON.stringify({

//                 email:
//                     $(this).find('[name=email]').val().trim(),

//                 mobile_no:
//                     $(this).find('[name=mobile_no]').val().trim(),

//                 password:
//                     $(this).find('[name=password]').val(),

//                 password_confirmation:
//                     $(this).find('[name=password_confirmation]').val(),
//             })
//         });

//         let data = await res.json().catch(() => ({}));

//         /* ---------- ERROR ---------- */
//         if (!res.ok) {

//             let errorText = data.errors
//                 ? Object.values(data.errors).flat().join(' • ')
//                 : (data.message || 'Registration failed.');

//             $err
//                 .text(errorText)
//                 .removeClass('d-none');

//             return;
//         }

//         /* ---------- SUCCESS ---------- */

//         window.MX_IS_LOGGED_IN = true;

//         $('#mx-auth-state').attr('data-logged-in', '1');

//         // close modal
//         const modalEl = document.getElementById('mxAuthModal');

//         if (modalEl) {

//             const modalInstance = bootstrap.Modal.getInstance(modalEl);

//             if (modalInstance) {
//                 modalInstance.hide();
//             } else {
//                 $(modalEl).modal('hide');
//             }
//         }

//         // reload after modal closes
//         setTimeout(() => {

//             window.location.href = window.location.href;

//         }, 500);

//     } catch (err) {

//         console.error(err);

//         $err
//             .text('Network error. Please try again.')
//             .removeClass('d-none');
//     }
// });
//     /* ================================================================
//        WORKSTATION TABS
//     ================================================================ */
//     $(document).on('click', '.mx-w-title', function () {
//         $('.mx-w-title').removeClass('active'); $(this).addClass('active');
//         loadCalendarData(null, parseInt($(this).data('ws'), 10) || 1);
//     });

//     /* ================================================================
//        RESPONSIVE
//     ================================================================ */
//     function toggleBookClose() { window.innerWidth < 768 ? $('#bookclose').show() : $('#bookclose').hide(); }
//     toggleBookClose();
//     $(window).on('resize', toggleBookClose);

//     function syncBtnPointers() {
//         $('#leftupButton button').css('pointer-events', $('#leftupButton').is(':visible') ? 'auto' : 'none');
//         $('.cal-sub-btn button').css('pointer-events', $('.cal-sub-btn').is(':visible') ? 'auto' : 'none');
//     }
//     syncBtnPointers();
//     $(window).on('resize', syncBtnPointers);

//     /* ================================================================
//        INIT
//     ================================================================ */
//     updateBookBtnState();
// });
// function closeGuestMemberSuccessModal() {

//     const modal = document.getElementById("mxGuestSuccessModal");

//     if (!modal) return;

//     modal.classList.remove("show");
//     modal.setAttribute("aria-hidden", "true");
// }


/**
 * booking.js  —  Mechanix D.I.Y.  (v7 – fixed)
 *
 * FIXES IN THIS VERSION:
 *  1. Removed duplicate #guestBookingForm submit handler (was causing double
 *     POST → SQL unique-conflict on the second identical request).
 *  2. Payment modal flow removed for now (commented). After slot confirm,
 *     guest booking goes straight to the "call to confirm" success modal.
 *  3. Logged-in user flow: after slot confirm, goes straight to submitBooking
 *     (no pay modal). Pay modal re-enable later by uncommenting marked sections.
 *  4. mxModalConfirm for logged-in users skips pay modal → submitBooking directly.
 */

$(function () {

    // Add at the top of the $(function(){...}) block:
    // $('<style>')
    //     .text(
    //         '.mx-liftbtn--unavailable{opacity:.4;cursor:not-allowed;position:relative;}' +
    //         '.mx-lift-unavail-badge{position:absolute;top:4px;right:4px;background:#ef4444;color:#fff;font-size:9px;padding:1px 5px;border-radius:4px;font-weight:700;letter-spacing:.5px;}'
    //     )
    //     .appendTo('head');

    $('<style>')
        .text(
            '.mx-liftbtn--unavailable{opacity:.4;cursor:not-allowed;position:relative;}' +
            '.mx-lift-unavail-badge{position:absolute;top:4px;right:4px;background:#ef4444;color:#fff;font-size:9px;padding:1px 5px;border-radius:4px;font-weight:700;letter-spacing:.5px;}' +
            '.flatpickr-day.day-available{background:#16a34a !important;color:#fff !important;border-color:#16a34a !important;}' +
            '.flatpickr-day.day-partial{background:#eab308 !important;color:#111 !important;border-color:#eab308 !important;}' +
            '.flatpickr-day.day-scarce{background:#f97316 !important;color:#fff !important;border-color:#f97316 !important;}' +
            '.flatpickr-day.day-booked{background:#ef4444 !important;color:#fff !important;border-color:#ef4444 !important;}' +
            '.flatpickr-day.day-unavailable{background:#cbd5e1 !important;color:#64748b !important;border-color:#cbd5e1 !important;cursor:not-allowed !important;}' +
            '.mx-day-tooltip{position:fixed;z-index:99999;background:#0f172a;color:#f1f5f9;border:1px solid #334155;' +
            'padding:6px 10px;border-radius:6px;font-size:12px;font-weight:600;pointer-events:none;white-space:nowrap;' +
            'box-shadow:0 4px 14px rgba(0,0,0,.4);display:none;}'
        )
        .appendTo('head');

    if (!$('#mxDayTooltip').length) {
        $('<div id="mxDayTooltip" class="mx-day-tooltip"></div>').appendTo('body');
    }

    $(document)
        .off('mouseenter.mxDayTip mousemove.mxDayTip mouseleave.mxDayTip')
        .on('mouseenter.mxDayTip', '.flatpickr-day', function () {
            var txt = this.getAttribute('data-tooltip');
            if (!txt) return;
            $('#mxDayTooltip').text(txt).css('display', 'block');
        })
        .on('mousemove.mxDayTip', '.flatpickr-day', function (e) {
            $('#mxDayTooltip').css({ left: e.clientX + 14 + 'px', top: e.clientY + 14 + 'px' });
        })
        .on('mouseleave.mxDayTip', '.flatpickr-day', function () {
            $('#mxDayTooltip').css('display', 'none');
        });

    /* ================================================================
       SCROLL HELPER
    ================================================================ */
    function scrollToEl(el, offset) {
        offset = (offset === undefined) ? 70 : offset;
        if (!el) return;
        $('html, body').animate({ scrollTop: $(el).offset().top - offset }, 400);
    }

    /* ================================================================
       DETECT MODE
    ================================================================ */
    var $meta = $('#mxProductMeta');
    var PRODUCT_MODE = $meta.length > 0 && $meta.data('product-mode') == 1;

    var AUTO_LIFT_KEY = PRODUCT_MODE ? ($meta.data('lift-key') || 'all') : null;
    var AUTO_LIFT_NAME = PRODUCT_MODE ? ($meta.data('lift-name') || '') : null;

    /* ================================================================
       STATE
    ================================================================ */
    const TODAY = new Date();
    const MIN_MONTH = new Date();
    var liftStatuses = {}; // populated from DB
    var addonSelected = false;
    var addonPrice = 0;
    var ADDON_LIFT_KEY = 'flat2'; // alignment rack
    var dayData = {};
    var bookedSlots = {};

    var selectedDate = null;
    var selectedStartTime = null;
    var selectedHours = 1;
    var selectedPackHours = 1;
    var selectedLift = PRODUCT_MODE ? AUTO_LIFT_KEY : null;
    var fpInstance = null;

    // Add this here
var selectedAddon = null;

    /* ================================================================
       HELPERS
    ================================================================ */
    function pad2(n) { return String(n).padStart(2, '0'); }

    function formatTimePoint(h24) {
        return (h24 % 12 || 12) + ':00 ' + (h24 >= 12 ? 'PM' : 'AM');
    }

    function formatMoney(n) { return '$' + Number(n).toFixed(0); }

    function addDaysStr(dateStr, days) {
        var d = new Date(dateStr + 'T00:00:00');
        d.setDate(d.getDate() + days);
        return d.getFullYear() + '-' + pad2(d.getMonth() + 1) + '-' + pad2(d.getDate());
    }

    function nextDate(dateStr, days) {
        return addDaysStr(dateStr, days === undefined ? 1 : days);
    }
function getNJDateString() {
    return new Intl.DateTimeFormat('en-CA', {
        timeZone: 'America/New_York'
    }).format(new Date());
}

function getWorkingHours(dateStr) {

    const nowNJ = new Date(
        new Date().toLocaleString('en-US', {
            timeZone: 'America/New_York'
        })
    );

    const selectedDate = new Date(dateStr + 'T00:00:00');
    const day = selectedDate.getDay();

    if (day === 6) return null; // Sunday closed

    let startHour = 9;

    if (dateStr === getNJDateString()) {
        startHour = Math.max(9, nowNJ.getHours() + 1);
    }

    return {
        start: startHour,
        end: day === 5 ? 12 : 18
    };
}
    // function getWorkingHours(dateStr) {
    //     var day = new Date(dateStr + 'T00:00:00').getDay();
    //     if (day === 6) return null;
    //     if (day === 5) return { start: 9, end: 12 };
    //     return { start: 9, end: 18 };
    // }

    function getWorkingSlots(dateStr) {
        var wh = getWorkingHours(dateStr);
        if (!wh) return [];
        var s = [];
        for (var h = wh.start; h < wh.end; h++) s.push(pad2(h) + ':00');
        return s;
    }

    /* ================================================================
       PER-LIFT SLOT ISOLATION
    ================================================================ */
    function slotKey(dateStr, liftKey) {
        return dateStr + '__' + (liftKey || selectedLift || 'all');
    }

    // function isSlotBooked(dateStr, timeStr, liftKey) {
    //     liftKey = liftKey || selectedLift || 'all';
    //     var specific = bookedSlots[slotKey(dateStr, liftKey)] || [];
    //     var generic = bookedSlots[slotKey(dateStr, 'all')] || [];
    //     return specific.indexOf(timeStr) !== -1 || generic.indexOf(timeStr) !== -1;
    // }


    function getNJNow() {
        var parts = new Intl.DateTimeFormat('en-US', {
            timeZone: 'America/New_York',
            year: 'numeric', month: '2-digit', day: '2-digit',
            hour: '2-digit', minute: '2-digit', hour12: false,
        }).formatToParts(new Date());

        var map = {};
        parts.forEach(function (p) { map[p.type] = p.value; });

        var hour = parseInt(map.hour, 10);
        if (hour === 24) hour = 0;

        return { dateStr: map.year + '-' + map.month + '-' + map.day, hour: hour };
    }

    function isPastSlot(dateStr, hour) {
        var nj = getNJNow();
        if (dateStr !== nj.dateStr) return false; // only matters for "today" in NJ
        return hour <= nj.hour;
    }

    function isSlotReserved(dateStr, timeStr, liftKey) {
        liftKey = liftKey || selectedLift || 'all';
        var specific = bookedSlots[slotKey(dateStr, liftKey)] || [];
        var generic = bookedSlots[slotKey(dateStr, 'all')] || [];
        return specific.indexOf(timeStr) !== -1 || generic.indexOf(timeStr) !== -1;
    }

    function isSlotBooked(dateStr, timeStr, liftKey) {
        var hour = parseInt(timeStr.slice(0, 2), 10);
        if (isPastSlot(dateStr, hour)) return true;
        return isSlotReserved(dateStr, timeStr, liftKey);
    }

    function isHourFree(dateStr, hour, liftKey) {
        var wh = getWorkingHours(dateStr);
        if (!wh || hour < wh.start || hour >= wh.end) return false;
        return !isSlotBooked(dateStr, pad2(hour) + ':00', liftKey);
    }

    function isDayFullyFree(dateStr, liftKey) {
        var slots = getWorkingSlots(dateStr);
        return slots.length > 0 && slots.every(function (t) { return !isSlotBooked(dateStr, t, liftKey); });
    }

    function isDayHasAnyFreeHour(dateStr, liftKey) {
        return getWorkingSlots(dateStr).some(function (t) { return !isSlotBooked(dateStr, t, liftKey); });
    }

    function dayFreeRatio(dateStr, liftKey) {
        var slots = getWorkingSlots(dateStr);
        if (!slots.length) return 0;
        var free = slots.filter(function (t) { return !isSlotBooked(dateStr, t, liftKey); }).length;
        return free / slots.length;
    }

    function isDateAvailableByPackage(dateStr, packHours, liftKey) {
        liftKey = liftKey || selectedLift || 'all';
        if (!getWorkingHours(dateStr)) return false;
        if (packHours === 1) return isDayHasAnyFreeHour(dateStr, liftKey);
        if (packHours === 9) return getWorkingSlots(dateStr).length >= 9 && isDayFullyFree(dateStr, liftKey);
        if (packHours === 18) {
            if (getWorkingSlots(dateStr).length < 9) return false;
            var nxt = nextDate(dateStr);
            return getWorkingSlots(nxt).length >= 9 && isDayFullyFree(dateStr, liftKey) && isDayFullyFree(nxt, liftKey);
        }
        return isDayHasAnyFreeHour(dateStr, liftKey);
    }

    function addWorkingHours(startDateStr, startHour, hoursNeeded) {
        var rem = hoursNeeded, cur = startDateStr, h = startHour;
        while (rem > 0) {
            var wh = getWorkingHours(cur);
            if (!wh) { cur = addDaysStr(cur, 1); h = 0; continue; }
            if (h < wh.start) h = wh.start;
            if (h >= wh.end) { cur = addDaysStr(cur, 1); h = 0; continue; }
            var av = wh.end - h;
            if (rem <= av) { h += rem; rem = 0; }
            else { rem -= av; cur = addDaysStr(cur, 1); h = 0; }
        }
        return { endDate: cur, endHour: h };
    }

    function buildEndLabel(dateStr, startTimeStr, hours) {
        var startH = parseInt(startTimeStr.slice(0, 2), 10);
        var r = addWorkingHours(dateStr, startH, hours);
        var fmt = function (d) {
            return new Date(d + 'T00:00:00').toLocaleDateString([], { month: 'short', day: '2-digit', year: 'numeric' });
        };
        return r.endDate !== dateStr
            ? fmt(r.endDate) + ' ' + formatTimePoint(r.endHour)
            : formatTimePoint(r.endHour);
    }

    function prettyRange(dateStr, startHour, hoursNeeded) {
        var r = addWorkingHours(dateStr, startHour, hoursNeeded);
        var fmt = function (d) {
            return new Date(d + 'T00:00:00').toLocaleDateString([], { year: 'numeric', month: 'short', day: '2-digit' });
        };
        var d1 = fmt(dateStr), d2 = fmt(r.endDate);
        var t1 = formatTimePoint(startHour), t2 = formatTimePoint(r.endHour);
        return d1 !== d2
            ? d1 + ' \u2022 ' + t1 + ' \u2192 ' + d2 + ' \u2022 ' + t2
            : d1 + ' \u2022 ' + t1 + ' \u2013 ' + t2;
    }

    function validateConsecutiveCrossDay(startDateStr, startTimeStr, hoursNeeded, liftKey) {
        liftKey = liftKey || selectedLift || 'all';
        var startH = parseInt(startTimeStr.slice(0, 2), 10);
        var wh0 = getWorkingHours(startDateStr);
        if (!wh0) return { ok: false, message: 'Closed on selected day.' };
        if (startH < wh0.start || startH >= wh0.end)
            return { ok: false, message: 'Start time outside working hours.' };

        var rem = hoursNeeded, cur = startDateStr, h = startH;
        while (rem > 0) {
            var wh = getWorkingHours(cur);
            if (!wh) return { ok: false, message: 'Closed on ' + cur + '.' };
            if (h < wh.start) h = wh.start;
            if (h >= wh.end) { cur = addDaysStr(cur, 1); h = 0; continue; }
            if (!isHourFree(cur, h, liftKey))
                return { ok: false, message: pad2(h) + ':00 on ' + cur + ' is already booked.' };
            rem--; h++;
            if (h >= wh.end && rem > 0) { cur = addDaysStr(cur, 1); h = 0; }
        }
        return { ok: true };
    }

    /* ================================================================
       PRICING
    ================================================================ */
    function getSelectedCard() { return $('.mx-pricecard.mx-selected').first(); }
    function getRatePerHour() { return Number(getSelectedCard().data('price')) || 0; }
    function getPackageTotal(hours) { return getRatePerHour() * hours; }

    // ADD HERE ↓
    function getEffectiveTotal(hours) {
        var base = getPackageTotal(hours);
        // var addon = addonSelected ? (addonPrice * hours) : 0;
        var addon = addonSelected ? addonPrice : 0;
        return base + addon;
    }
    /* ================================================================
       LIFT LABELS
    ================================================================ */
    var LIFT_LABELS = {
        four: 'Four-Post Style Lift',
        two: 'Two-Post Lift',
        scissor: 'Scissor Lift',
        flat: 'Motorcycle Lift',
        flat2: 'Alignment Rack',
    };

    function getActiveLiftLabel() {
        if (PRODUCT_MODE && AUTO_LIFT_NAME) return AUTO_LIFT_NAME;
        return LIFT_LABELS[selectedLift] || selectedLift || '\u2014';
    }

    /* ================================================================
       BOOK-NOW GATE
    ================================================================ */
    function updateBookBtnState() {
        var hasLift = selectedLift !== null;
        var hasDate = selectedDate !== null;
        var ready, hint = '';

        if (PRODUCT_MODE) {
            ready = hasDate;
            if (!hasDate) hint = 'Please pick an available date on the calendar.';
        } else {
            ready = hasLift && hasDate;
            if (!hasLift && !hasDate) hint = 'Select a lift type and a date to continue.';
            else if (!hasLift) hint = 'Select a lift type above to enable booking.';
            else if (!hasDate) hint = 'Pick an available date on the calendar.';
        }

        $('#openDayCalendar, #openDayCalendarMb')
            .prop('disabled', !ready)
            .toggleClass('enabled', ready)
            .text(ready ? 'Book for ' + selectedDate : 'Book Now');

        $('.mx-book-hint').text(hint);
    }

    /* ================================================================
       MODALS
    ================================================================ */
    function openModal(id) { $(id).addClass('show').attr('aria-hidden', 'false'); }
    function closeModal(id) { $(id).removeClass('show').attr('aria-hidden', 'true'); }

    function openSlotModal() { openModal('#mxSlotModal'); }
    function closeSlotModal() { closeModal('#mxSlotModal'); }
    function openSummaryModal() { openModal('#mxSummaryModal'); }
    function closeSummaryModal() { closeModal('#mxSummaryModal'); }
    // function openPayModal()      { openModal('#mxPayModal');     }  // PAYMENT DISABLED FOR NOW
    // function closePayModal()     { closeModal('#mxPayModal');    }  // PAYMENT DISABLED FOR NOW
    function openSuccessModal() { openModal('#mxSuccessModal'); }

    $(document).on('keydown', function (e) {
        if (e.key !== 'Escape') return;
        closeSlotModal();
        closeSummaryModal();
        // closePayModal();  // PAYMENT DISABLED FOR NOW
    });

    /* ================================================================
       HOUR CONTROLS
    ================================================================ */
    function toggleHourControls(lock) {
        $('#mxHMinus, #mxHPlus').prop('disabled', lock).toggleClass('mx-disabled', lock);
    }

    // function setHours(val) {
    //     var maxHours = selectedPackHours === 1 ? 8 : selectedPackHours;
    //     selectedHours = Math.max(selectedPackHours, Math.min(val, maxHours));
    //     var startH = parseInt(selectedStartTime.slice(0, 2), 10);
    //     var check  = validateConsecutiveCrossDay(selectedDate, selectedStartTime, selectedHours);

    //     $('#mxSelectedHours').text(selectedHours);
    //     $('#mxTotalText').text(formatMoney(getPackageTotal(selectedHours)));
    //     $('#mxSlotText').text(prettyRange(selectedDate, startH, selectedHours));
    //     $('#mxHintText').text(
    //         check.ok
    //             ? 'Continuous booking for ' + selectedHours + ' hour' + (selectedHours > 1 ? 's' : '') + '.'
    //             : check.message
    //     );
    //     $('#mxModalConfirm').prop('disabled', !check.ok).css('opacity', check.ok ? '1' : '.5');
    // }

    function setHours(val) {

        var wh = getWorkingHours(selectedDate);

        var maxHours = selectedPackHours === 1 ? 9 : selectedPackHours;

        selectedHours = Math.max(
            selectedPackHours,
            Math.min(val, maxHours)
        );

        var startH = parseInt(selectedStartTime.slice(0, 2), 10);

        var check = validateConsecutiveCrossDay(
            selectedDate,
            selectedStartTime,
            selectedHours
        );

        var endHour = startH + selectedHours;

        $('#mxSelectedHours').text(selectedHours);
        // $('#mxTotalText').text(formatMoney(getPackageTotal(selectedHours)));
        $('#mxTotalText').text(formatMoney(getEffectiveTotal(selectedHours)));
$('#mxsLift').text(getActiveLiftLabel() + ' ($' + getRatePerHour() + '/hr)');
        $('#mxsAddon').text(
    addonSelected
        ? 'Alignment Rack ($' + addonPrice + ')'
        : 'None'
);
        $('#mxSlotText').text(
            prettyRange(selectedDate, startH, selectedHours)
        );
        

        $('#mxHintText').text(
            check.ok
                ? 'Continuous booking for ' +
                selectedHours +
                ' hour' +
                (selectedHours > 1 ? 's' : '') +
                '.'
                : check.message
        );

        $('#mxModalConfirm')
            .prop('disabled', !check.ok)
            .css('opacity', check.ok ? '1' : '.5');

        // dynamic close hour check
        $('#mxHPlus').prop(
            'disabled',
            !wh || endHour >= wh.end
        );
    }

    $('#mxHMinus').on('click', function () { setHours(selectedHours - 1); });
    // $('#mxHPlus').on('click',  function () { setHours(selectedHours + 1); });
    $('#mxHPlus').on('click', async function () {

        let nextHours = selectedHours + 1;

        let result = await checkBookingAvailability(
            selectedDate,
            selectedLift,
            selectedStartTime,
            nextHours
        );

        if (!result.ok) {

            $('#mxHintText')
                .removeClass('text-success text-muted')
                .addClass('text-danger fw-bold')
                .html('⚠️ Cannot extend booking. Time slot already occupied.');

            return;
        }

        setHours(nextHours);

        

    });

    /* ================================================================
       SUMMARY MODAL
       NOTE: Summary modal "Pay Now" button now submits booking directly
             (no payment modal). Re-enable payment later by uncommenting.
    ================================================================ */
    function populateAndOpenSummary() {
        var rate = getRatePerHour();
        // var total = getPackageTotal(selectedHours);
        var total = getEffectiveTotal(selectedHours);

        var startH = parseInt(selectedStartTime.slice(0, 2), 10);
        var dateFmt = new Date(selectedDate + 'T00:00:00').toLocaleDateString([], {
            weekday: 'long', year: 'numeric', month: 'long', day: 'numeric',
        });
        $('#mxsWorkstation').text('Workstation 1');
        $('#mxsLift').text(getActiveLiftLabel());
         // Add-On
 $('#mxsAddon').text(
    addonSelected
        ? 'Alignment Rack ($' + addonPrice + ')'
        : 'None'
);
        $('#mxsDate').text(dateFmt);
        $('#mxsStart').text(formatTimePoint(startH));
        $('#mxsDuration').text(selectedHours + ' hour' + (selectedHours > 1 ? 's' : ''));
        $('#mxsEnd').text(buildEndLabel(selectedDate, selectedStartTime, selectedHours));
        $('#mxsRate').text(formatMoney(rate) + ' / hr');
        $('#mxsHours').text(selectedHours);
        $('#mxsTotal').text(formatMoney(total));
        $('#mxPayAmount').text(formatMoney(total));
        $('#mxPayBtnAmt').text(formatMoney(total));
        openSummaryModal();
    }

    /* ================================================================
       TIME-SLOT GRID (per-lift aware)
    ================================================================ */
    function renderTimeSlots(dateStr, timeArray) {
        console.log("timearray", timeArray);

        var $grid = $('#mxTimeGrid').empty();

        $('#mxSelectedDateText').text(
            new Date(dateStr + 'T00:00:00').toLocaleDateString([], {
                weekday: 'long', year: 'numeric', month: 'long', day: 'numeric',
            })
        );
        selectedStartTime = null;
        $('#mxPickedTimeText').text('None');
        $('#mxContinueBtn').prop('disabled', true);

        var wh = getWorkingHours(dateStr);
        console.log("date check", wh);

        if (!wh) {
            $grid.html('<div class="mx-slot-closed">This day is closed. No slots available.</div>');
            return;
        }

        // if (selectedPackHours === 9 || selectedPackHours === 18) {
        //     var startVal = pad2(wh.start) + ':00';
        //     var chk      = validateConsecutiveCrossDay(dateStr, startVal, selectedPackHours);
        //     if (!chk.ok) {
        //         $grid.html(
        //             '<div class="mx-slot-unavail">Not available for <strong>' + selectedPackHours +
        //             'h</strong> from 9:00 AM.<br><small>' + chk.message + '</small></div>'
        //         );
        //         return;
        //     }
        //     $('<button>', {
        //         type: 'button', class: 'mx-slot available', 'data-value': startVal,
        //         html: '<span class="mx-slot-time">Start at ' + formatTimePoint(wh.start) + '</span>' +
        //               '<span class="mx-slot-badge free">Full ' + selectedPackHours + 'h block</span>',
        //     }).on('click', function () {
        //         $('.mx-slot').removeClass('selected'); $(this).addClass('selected');
        //         selectedStartTime = startVal;
        //         $('#mxPickedTimeText').text(formatTimePoint(wh.start));
        //         $('#mxContinueBtn').prop('disabled', false);
        //     }).appendTo($grid);
        //     return;
        // }
        if (selectedPackHours === 9 || selectedPackHours === 18) {

            var startVal = pad2(wh.start) + ':00';

            var blockedTimes = new Set(
                timeArray.map(function (t) {
                    return t.slice(0, 5);
                })
            );

            var blocked = blockedTimes.has(startVal);

            var chk = validateConsecutiveCrossDay(
                dateStr,
                startVal,
                selectedPackHours
            );

            var isDisabled = blocked || !chk.ok;

            var $btn = $('<button>', {
                type: 'button',
                class: 'mx-slot ' + (
                    blocked ? 'blocked' :
                        !chk.ok ? 'booked' :
                            'available'
                ),
                disabled: isDisabled,
                'data-value': startVal,
                html:
                    '<span class="mx-slot-time">Start at ' +
                    formatTimePoint(wh.start) +
                    '</span>' +
                    '<span class="mx-slot-badge ' +
                    (blocked ? 'blocked-badge' :
                        !chk.ok ? 'taken' :
                            'free') +
                    '">' +
                    (blocked ? 'Blocked' :
                        !chk.ok ? 'Unavailable' :
                            'Full ' + selectedPackHours + 'h block') +
                    '</span>'
            });

            if (!isDisabled) {
                $btn.on('click', function () {
                    $('.mx-slot').removeClass('selected');
                    $(this).addClass('selected');

                    selectedStartTime = startVal;
                    $('#mxPickedTimeText').text(formatTimePoint(wh.start));
                    $('#mxContinueBtn').prop('disabled', false);
                });
            }

            $grid.append($btn);
            return;
        }

        var slots = getWorkingSlots(dateStr);
        if (!slots.length) {
            $grid.html('<div class="mx-slot-closed">No working slots on this day.</div>');
            return;
        }

        // slots.forEach(function (value) {
        //     var h      = parseInt(value.slice(0, 2), 10);
        //     var label  = formatTimePoint(h) + ' \u2013 ' + formatTimePoint(h + 1);
        //     var booked = isSlotBooked(dateStr, value, selectedLift);

        //     var $btn = $('<button>', {
        //         type: 'button',
        //         class: 'mx-slot ' + (booked ? 'booked' : 'available'),
        //         disabled: booked,
        //         'data-value': value,
        //         html: '<span class="mx-slot-time">' + label + '</span>' +
        //               '<span class="mx-slot-badge ' + (booked ? 'taken' : 'free') + '">' +
        //               (booked ? 'Booked' : 'Available') + '</span>',
        //     });

        //     if (!booked) {
        //         $btn.on('click', function () {
        //             $('.mx-slot').removeClass('selected'); $(this).addClass('selected');
        //             selectedStartTime = value;
        //             $('#mxPickedTimeText').text(label);
        //             $('#mxContinueBtn').prop('disabled', false);
        //         });
        //     }
        //     $grid.append($btn);
        // });
        // Create blocked time lookup
        var blockedTimes = new Set(
            timeArray.map(function (t) {
                return t.slice(0, 5);
            })
        );

        slots.forEach(function (value) {

            console.log("slot:", value);

            var h = parseInt(value.slice(0, 2), 10);
            // var label = formatTimePoint(h) + ' – ' + formatTimePoint(h + 1);
            var label = formatTimePoint(h);

            // var booked = isSlotBooked(dateStr, value, selectedLift);
            // var blocked = blockedTimes.has(value);

            // console.log(value, "blocked?", blocked);

            // var isDisabled = booked || blocked;

            // var $btn = $('<button>', {
            //     type: 'button',
            //     class: 'mx-slot ' + (
            //         booked ? 'booked' :
            //             blocked ? 'blocked' :
            //                 'available'
            //     ),
            //     disabled: isDisabled,
            //     'data-value': value,
            //     html:
            //         '<span class="mx-slot-time">' + label + '</span>' +
            //         '<span class="mx-slot-badge ' +
            //         (booked ? 'taken' :
            //             blocked ? 'blocked-badge' :
            //                 'free') + '">' +
            //         (booked ? 'Booked' :
            //             blocked ? 'Blocked' :
            //                 'Available') +
            //         '</span>'
            // });


            var past     = isPastSlot(dateStr, h);
            var reserved = !past && isSlotReserved(dateStr, value, selectedLift);
            var blocked  = !past && !reserved && blockedTimes.has(value);

            var isDisabled = past || reserved || blocked;

            var stateClass = past ? 'blocked' : reserved ? 'booked' : blocked ? 'blocked' : 'available';
            var badgeClass = past ? 'blocked-badge' : reserved ? 'taken' : blocked ? 'blocked-badge' : 'free';
            var badgeText  = past ? 'Past' : reserved ? 'Booked' : blocked ? 'Blocked' : 'Available';

            var $btn = $('<button>', {
                type: 'button',
                class: 'mx-slot ' + stateClass,
                disabled: isDisabled,
                'data-value': value,
                html:
                    '<span class="mx-slot-time">' + label + '</span>' +
                    '<span class="mx-slot-badge ' + badgeClass + '">' + badgeText + '</span>'
            });

            if (!isDisabled) {
                $btn.on('click', function () {
                    $('.mx-slot').removeClass('selected');
                    $(this).addClass('selected');

                    selectedStartTime = value;
                    $('#mxPickedTimeText').text(label);
                    $('#mxContinueBtn').prop('disabled', false);
                });
            }

            $grid.append($btn);
        });
    }

    /* ================================================================
       VIEW SWITCH
    ================================================================ */
    function showTimeView() { $('#calendarWrap').hide(); $('#mxTimeView').show(); }
    function showDateView() {
        $('#mxTimeView').hide(); $('#calendarWrap').show();
        $('.mx-gridWrap, .mx-legendMini').show();
        selectedStartTime = null;
        $('#mxPickedTimeText').text('None');
        $('#mxContinueBtn').prop('disabled', true);
    }

    /* ================================================================
       CALENDAR DATA
    ================================================================ */
    async function loadCalendarData(monthStr, workstation) {
        monthStr = monthStr || null;
        workstation = workstation || 1;
        try {
            var params = new URLSearchParams();
            if (monthStr) params.append('month', monthStr);
            params.append('workstation', workstation);
            var res = await fetch('/booking/calendar-data?' + params.toString(), {
                method: 'GET', credentials: 'same-origin', headers: { Accept: 'application/json' },
            });
            var data = await res.json();
            dayData = data.dayData || {};

            // var raw = data.bookedSlots || {};
            // bookedSlots = {};
            // Object.keys(raw).forEach(function (k) {
            //     bookedSlots[k.indexOf('__') !== -1 ? k : k + '__all'] = raw[k];
            // });

            var raw = data.bookedSlots || {};
            bookedSlots = {};
            Object.keys(raw).forEach(function (k) {
                var key = k.indexOf('__') !== -1 ? k : k + '__all';
                // backend sends "09:00:00"; slot keys are "09:00" — strip seconds so they actually match
                bookedSlots[key] = (raw[k] || []).map(function (t) { return t.slice(0, 5); });
            });
        } catch (_) { /* offline / demo */ }
        if (fpInstance) fpInstance.redraw();
    }

    loadCalendarData(null, 1);

    /* ================================================================
       CALENDAR COLOUR SCALE
    ================================================================ */
    // function dayAvailClass(dateStr) {
    //     if (!getWorkingHours(dateStr)) return 'day-unavailable';
    //     var info = dayData[dateStr];
    //     // if (info && info.status === 'booked')      return 'day-booked';
    //     if (info && info.status === 'booked') return 'day-available';
    //     if (info && info.status === 'unavailable') return 'day-unavailable';
    //     if (!isDateAvailableByPackage(dateStr, selectedPackHours, selectedLift)) return 'day-unavailable';
    //     var r = dayFreeRatio(dateStr, selectedLift);
    //     if (r === 0) return 'day-unavailable';
    //     if (r >= 0.70) return 'day-available';
    //     if (r >= 0.30) return 'day-partial';
    //     return 'day-scarce';
    // }

    function dayAvailClass(dateStr) {
        if (!getWorkingHours(dateStr)) return 'day-unavailable'; // Saturday / closed

        var info = dayData[dateStr];
        if (info && info.status === 'unavailable') return 'day-unavailable'; // holiday

        // Color reflects real per-lift occupancy, not the workstation-wide flag —
        // a "booked" workstation status doesn't mean THIS lift is full.
        var r = dayFreeRatio(dateStr, selectedLift);
        if (r === 0)   return 'day-booked';    // red  — fully booked
        if (r >= 0.70) return 'day-available'; // green
        if (r >= 0.30) return 'day-partial';   // yellow — filling fast
        return 'day-scarce';                   // orange — almost full
    }

    /* ================================================================
       FLATPICKR
    ================================================================ */
    function updateMonthNav(fp) {
        var cur = new Date(fp.currentYear, fp.currentMonth, 1);
        var min = new Date(MIN_MONTH.getFullYear(), MIN_MONTH.getMonth(), 1);
        var prevBtn = fp.calendarContainer.querySelector('.flatpickr-prev-month');
        if (!prevBtn) return;
        var atMin = cur <= min;
        prevBtn.style.pointerEvents = atMin ? 'none' : 'auto';
        prevBtn.style.opacity = atMin ? '0.3' : '1';
    }

    fpInstance = flatpickr('#bookingDate', {
        inline: true, dateFormat: 'Y-m-d', disableMobile: true,
        defaultDate: TODAY,
        appendTo: document.getElementById('calendarWrap'),
        minDate: new Date(),

        onReady: function (s, d, fp) { fpInstance = fp; updateMonthNav(fp); },
        onMonthChange: function (s, d, fp) {
            updateMonthNav(fp); fp.redraw();
            loadCalendarData(fp.currentYear + '-' + pad2(fp.currentMonth + 1), 1);
        },
        onYearChange: function (s, d, fp) { updateMonthNav(fp); fp.redraw(); },

        // disable: [function (date) {
        //     if (date.getDay() === 6) return true;
        //     var info = dayData[flatpickr.formatDate(date, 'Y-m-d')];
        //     // return info && (info.status === 'unavailable' || info.status === 'booked');
        //     return info && info.status === 'unavailable';
        // }],

        disable: [function (date) {
            if (date.getDay() === 6) return true;
            var key  = flatpickr.formatDate(date, 'Y-m-d');
            var info = dayData[key];
            if (info && info.status === 'unavailable') return true; // holiday
            return !isDateAvailableByPackage(key, selectedPackHours, selectedLift); // fully booked for this lift/package
        }],

        // onDayCreate: function (dObj, dStr, fp, dayElem) {
        //     dayElem.classList.remove('day-available', 'day-partial', 'day-scarce',
        //         'day-booked', 'day-unavailable', 'day-nextmonth', 'day-prevmonth');
        //     if (dayElem.classList.contains('nextMonthDay')) { dayElem.classList.add('day-nextmonth'); return; }
        //     if (dayElem.classList.contains('prevMonthDay')) { dayElem.classList.add('day-prevmonth'); return; }

        //     var key = fp.formatDate(dayElem.dateObj, 'Y-m-d');
        //     // if (dayElem.classList.contains('flatpickr-disabled')) {
        //     //     var info = dayData[key];
        //     //     dayElem.classList.add((info && info.status === 'booked') ? 'day-booked' : 'day-unavailable');
        //     //     return;
        //     // }
        //     if (dayElem.classList.contains('flatpickr-disabled')) {
        //         var info = dayData[key];

        //         if (info && info.status === 'booked') {
        //             dayElem.classList.add('day-unavailable'); // booked looks available
        //         } else {
        //             dayElem.classList.add('day-unavailable');
        //         }

        //         return;
        //     }
        //     dayElem.classList.add(dayAvailClass(key));

        //     var slots = getWorkingSlots(key);
        //     var free = slots.filter(function (t) { return !isSlotBooked(key, t, selectedLift); }).length;
        //     if (slots.length > 0 && free > 0 && free < slots.length) {
        //         dayElem.setAttribute('title', free + ' of ' + slots.length + ' slots available');
        //         var dot = document.createElement('span');
        //         dot.className = 'mx-day-dot';
        //         dayElem.appendChild(dot);
        //     }

        // },


        onDayCreate: function (dObj, dStr, fp, dayElem) {
            dayElem.classList.remove('day-available', 'day-partial', 'day-scarce',
                'day-booked', 'day-unavailable', 'day-nextmonth', 'day-prevmonth');
            if (dayElem.classList.contains('nextMonthDay')) { dayElem.classList.add('day-nextmonth'); return; }
            if (dayElem.classList.contains('prevMonthDay')) { dayElem.classList.add('day-prevmonth'); return; }

            var key = fp.formatDate(dayElem.dateObj, 'Y-m-d');
            var nj  = getNJNow();

            if (dayElem.classList.contains('flatpickr-disabled')) {
                var info = dayData[key];

                if (key < nj.dateStr) {
                    dayElem.classList.add('day-unavailable');
                    dayElem.setAttribute('data-tooltip', 'Past date');
                } else if (info && info.status === 'unavailable') {
                    dayElem.classList.add('day-unavailable');
                    dayElem.setAttribute('data-tooltip', 'Holiday — Closed');
                } else if (!getWorkingHours(key)) {
                    dayElem.classList.add('day-unavailable');
                    dayElem.setAttribute('data-tooltip', 'Closed');
                } else {
                    dayElem.classList.add('day-booked');
                    var s0 = getWorkingSlots(key);
                    var b0 = s0.filter(function (t) { return isSlotReserved(key, t, selectedLift); }).length;
                    dayElem.setAttribute('data-tooltip', b0 + ' of ' + s0.length + ' slots booked');
                }
                return;
            }

            dayElem.classList.add(dayAvailClass(key));

            var slots  = getWorkingSlots(key);
            var booked = slots.filter(function (t) { return isSlotReserved(key, t, selectedLift); }).length;
            var free   = slots.filter(function (t) { return !isSlotBooked(key, t, selectedLift); }).length;

            dayElem.setAttribute('data-tooltip', booked + ' of ' + slots.length + ' slots booked');

            if (slots.length > 0 && free > 0 && free < slots.length) {
                var dot = document.createElement('span');
                dot.className = 'mx-day-dot';
                dayElem.appendChild(dot);
            }
        },

        onChange: function (selectedDates, dateStr) {
            if (!dateStr) { selectedDate = null; updateBookBtnState(); return; }
            selectedDate = isDateAvailableByPackage(dateStr, selectedPackHours, selectedLift) ? dateStr : null;
            updateBookBtnState();
        },
    });

    /* ================================================================
       PRICE CARDS
    ================================================================ */
   function selectPackage(hours) {
    selectedPackHours = hours;
    selectedHours = hours;
    $('.mx-pricecard').each(function () {
        if ($(this).hasClass('mx-addon-card')) return;  // skip addon
        var h = parseInt($(this).data('hours'), 10);
        $(this).toggleClass('mx-selected', h === hours).toggleClass('mx-dimmed', h !== hours);
    });
        toggleHourControls(hours === 9 || hours === 18);
        if (fpInstance) fpInstance.redraw();
        if (selectedDate) getBlockedTimes(selectedLift, selectedDate, function (timeArray) {
            renderTimeSlots(selectedDate, timeArray);
        });
    }

    (function initPackage() {
        var $c = $('.mx-pricecard.mx-selected').first();
        if ($c.length) { selectedPackHours = parseInt($c.data('hours'), 10) || 1; selectedHours = selectedPackHours; }
        selectPackage(selectedPackHours);
    }());

    $(document).on('click', '.mx-pricecard', function () {
        if ($(this).closest('a').length) return;
        selectPackage(parseInt($(this).data('hours'), 10) || 1);
        setTimeout(function () { scrollToEl($('#calendarSection')[0]); }, 200);
    });

    /* ================================================================
       LIFT BUTTONS (direct-booking mode only)
    ================================================================ */
    var LIFT_DATA = {
        four: { img: 'assets/images/rentals/Media (8).jpg', points: ['Heavy-duty support', 'Perfect for long-hour jobs', 'Maximum stability & safety'] },
        two: { img: 'assets/images/rentals/Media (6).jpg', points: ['Quick vehicle access', 'Ideal for mechanical repairs', 'Compact and space efficient'] },
        scissor: { img: 'assets/images/rentals/scissor.jpg', points: ['Low profile design', 'Fast lifting operation', 'Great for tire & brake work'] },
        flat: { img: 'assets/images/rentals/motocycle.jpg', points: ['Designed for motorcycles', 'Easy loading & unloading', 'Stable flat platform'] },
        flat2: { img: 'assets/images/rentals/allignmentrack.jpg', points: ['Precision wheel alignment', 'Extended ramp length', 'Perfect for alignment jobs'] },
    };

    function renderLiftPriceCards(liftKey) {
        var wrap = document.getElementById('mxPriceCardsWrap');
        if (!wrap) return;

        var raw = document.getElementById('mxAllLiftPrices');
        if (!raw) return;

        var allPrices = {};
        try {
            allPrices = JSON.parse(raw.textContent || raw.innerText);
        } catch (e) {
            console.error('Price JSON parse error:', e, raw.textContent);
            wrap.innerHTML = '<p style="color:red;font-size:13px;">Price data failed to load.</p>';
            return;
        }

        var liftData = allPrices[liftKey];
        if (!liftData || !liftData.prices || !liftData.prices.length) {
            wrap.innerHTML = '<p style="color:var(--color-text-secondary);font-size:13px;padding:8px 0;">No pricing available for this lift.</p>';
            return;
        }

        var html = liftData.prices.map(function (p, i) {
            var label = liftKey === 'flat2'
                ? ''
                : (p.hours === 1
                    ? 'Hourly Rental'
                    : p.hours + ' Hour Package');

            var priceStr = p.is_membership
                ? 'Members Only'
                : '$' + p.price;

            var card = '<div class="mx-pricecard ' + (i === 0 && !p.is_membership ? 'mx-selected' : '') + '"' +
                ' data-hours="' + p.hours + '"' +
                ' data-price="' + p.price + '"' +
                ' data-total="' + (p.price * p.hours) + '">' +
                (label ? '<span class="mx-hours">' + label + '</span>' : '') +
                '<span class="mx-price">' + priceStr + '</span>' +
                '</div>';

            return p.is_membership
                ? '<a href="/membership" class="mx-pricecard-link">' + card + '</a>'
                : card;
        }).join('');

        html += '<a href="/membership" class="mx-pricecard-link">' +
            '<div class="mx-pricecard mx-membership">' +
            '<span class="mx-hours">Membership Plan</span>' +
            '<span class="mx-price"></span>' +
            '</div></a>';

        wrap.innerHTML = html;

        var $first = $(wrap).find('.mx-pricecard.mx-selected').first();
        if ($first.length) {
            selectedPackHours = parseInt($first.data('hours'), 10) || 1;
            selectedHours = selectedPackHours;
            toggleHourControls(selectedPackHours === 9 || selectedPackHours === 18);
            if (fpInstance) fpInstance.redraw();
        }

        $(wrap).find('.mx-pricecard').not($(wrap).find('a .mx-pricecard')).on('click', function () {
            selectPackage(parseInt($(this).data('hours'), 10) || 1);
            setTimeout(function () { scrollToEl($('#calendarSection')[0]); }, 200);
        });

        // ADD THESE 6 LINES:
        if (liftKey === 'four') {
            renderAddonSection();
        } else {
            $('#mxAddonSection').hide();
            addonSelected = false;
            addonPrice = 0;
        }
    }


    function renderAddonSection() {
        var wasSelected = addonSelected;
        if (liftStatuses['flat2'] && liftStatuses['flat2'].status === 0) {
            $('#mxAddonSection').hide();
            return;
        }

        var raw = document.getElementById('mxAllLiftPrices');
        if (!raw) {
            console.warn('mxAllLiftPrices not found');
            return;
        }

        var allPrices = {};
        try {
            allPrices = JSON.parse(raw.textContent || raw.innerText);
        } catch (e) {
            console.error('Price JSON parse error', e);
            return;
        }

        var flat2Data = allPrices['flat2'];
        if (!flat2Data || !flat2Data.prices || !flat2Data.prices.length) {
            console.warn('flat2 pricing not found:', allPrices);
            return;
        }

        var hourlyPrice = flat2Data.prices.find(function (p) { return p.hours === 1; });
        if (!hourlyPrice) hourlyPrice = flat2Data.prices[0];
        addonPrice = hourlyPrice ? parseFloat(hourlyPrice.price) : 0;

        // ── Read product meta (image, name, description) ──
        var productMeta = { name: 'Alignment Rack', description: '', image: 'assets/images/rentals/allignmentrack.jpg' };
        var metaRaw = document.getElementById('mxAddonProductData');
        if (metaRaw) {
            try {
                productMeta = JSON.parse(metaRaw.textContent || metaRaw.innerText);
            } catch (e) { }
        }

        // ── Build description lines (split by newline, take first 3) ──
        var descLines = (productMeta.description || '')
            .split('\n')
            .map(function (l) { return l.trim(); })
            .filter(function (l) { return l.length > 0; })
            .slice(0, 3);

        var descHtml = descLines.length
            ? '<ul style="margin:4px 0 0 0;padding-left:14px;list-style:disc;">' +
            descLines.map(function (l) {
                return '<li style="font-size:11px;color:#94a3b8;margin-bottom:2px;">' + l + '</li>';
            }).join('') +
            '</ul>'
            : '';

        // ── Build all price pills ──
        var pricePillsHtml = flat2Data.prices
            .filter(function (p) { return !p.is_membership; })
            .map(function (p) {
                return '<span style="' +
                    'display:inline-block;' +
                    'background:#1e293b;' +
                    'border:1px solid #334155;' +
                    'border-radius:4px;' +
                    'padding:2px 7px;' +
                    'font-size:11px;' +
                    'color:#e2e8f0;' +
                    'margin-right:4px;' +
                    'margin-top:4px;">' +
                    (p.hours === 1 ? '1 hr' : p.hours + ' hrs') +
                    ' — <strong style="color:#e74c3c;">$' + p.price + '</strong>' +
                    '</span>';
            }).join('');

        var $section = $('#mxAddonSection');

      $section.html(
    '<div class="mx-addon-wrapper">' +

        

        '<div class="mx-pricecard mx-addon-card">' +
        '<div class="mx-addon-badge">ADD-ON</div>' +

            '<label class="mx-addon-label">' +

                '<input type="checkbox" id="mxAddonAlignmentRack" hidden>' +

                '<img class="mx-addon-image" src="' + productMeta.image + '"' +
                ' alt="' + productMeta.name + '"' +
                ' onerror="this.src=\'assets/images/rentals/allignmentrack.jpg\'">' +

                '<div class="mx-addon-info">' +
                    '<span class="mx-hours">' + productMeta.name + '</span>' +
                    '<span class="mx-price">$' + addonPrice + '</span>' +
                '</div>' +

            '</label>' +

        '</div>' +

    '</div>'
).show();

        // Restore addon selection state after HTML rebuild
        if (wasSelected) {
            $('#mxAddonAlignmentRack').prop('checked', true);
            $('.mx-addon-card').addClass('mx-selected');
        }

        // Bind checkbox
        $('#mxAddonAlignmentRack').off('change').on('change', function () {
    addonSelected = $(this).is(':checked');

    $('.mx-addon-card').toggleClass('mx-selected', addonSelected);  // ADD THIS
    // $('.mx-addon-card').toggleClass('active', addonSelected);

    if ($('#mxSlotModal').hasClass('show')) {
        $('#mxTotalText').text(
            formatMoney(getEffectiveTotal(selectedHours))
        );
    }
});
    }
    // Replace the entire "if (!PRODUCT_MODE)" block
    if (!PRODUCT_MODE) {
        $('.mx-liftbtn').removeClass('active');

        // Load lift statuses from DB first
        $.getJSON('/booking/lift-statuses', function (statuses) {
            liftStatuses = statuses;

            // Mark unavailable lift buttons
            Object.keys(statuses).forEach(function (liftKey) {
                if (statuses[liftKey].status === 0) {
                    var $btn = $('.mx-liftbtn[data-lift="' + liftKey + '"]');
                    $btn.addClass('mx-liftbtn--unavailable')
                        .prop('disabled', true)
                        .attr('title', 'Currently unavailable');
                    // Add unavailable badge
                    if (!$btn.find('.mx-lift-unavail-badge').length) {
                        $btn.append('<span class="mx-lift-unavail-badge">Unavailable</span>');
                    }
                }
            });
        });

        $(document).on('click', '.mx-liftbtn', function () {
            var liftKey = $(this).data('lift');

            // Block if unavailable
            if (liftStatuses[liftKey] && liftStatuses[liftKey].status === 0) {
                return;
            }

            $('.mx-liftbtn').removeClass('active');
            $(this).addClass('active');
            selectedLift = liftKey;

            renderLiftPriceCards(selectedLift);

            var lift = LIFT_DATA[selectedLift];
            if (lift) {
                $('#mxLiftPlaceholder').hide();
                $('#mxLiftPreviewImg').attr('src', lift.img).show();
            }

            // Show/hide add-on section
            if (selectedLift === 'four') {
                renderAddonSection();
            } else {
                $('#mxAddonSection').hide();
                addonSelected = false;
                addonPrice = 0;
            }

            $('#mxLiftPrompt').addClass('hidden');

            if (fpInstance) fpInstance.redraw();
            if (selectedDate && $('#mxTimeView').is(':visible')) {
                getBlockedTimes(selectedLift, selectedDate, function (timeArray) {
                    renderTimeSlots(selectedDate, timeArray);
                });
            }

            if (selectedDate && !isDateAvailableByPackage(selectedDate, selectedPackHours, selectedLift)) {
                selectedDate = null;
            }

            updateBookBtnState();
            setTimeout(function () { scrollToEl($('#liftSection')[0]); }, 200);
        });

        $(document).on('click', '#mxLiftDropdownMenu .dropdown-item', function (e) {
            e.preventDefault();
            var liftKey = $(this).data('lift');
            if (liftStatuses[liftKey] && liftStatuses[liftKey].status === 0) return;
            $('#mxLiftDropdownBtn').text($(this).text().trim());
            $('.mx-liftbtn[data-lift="' + liftKey + '"]').trigger('click');
        });
    }

    /* ================================================================
       BOOK NOW → time-slot view
    ================================================================ */
    $('#openDayCalendar').on('click', function () {
        if (!selectedDate) return;
        if (!PRODUCT_MODE && !selectedLift) return;
        $(this).prop('disabled', true).removeClass('enabled');
        $('.mx-gridWrap, .mx-legendMini').hide();
        showTimeView();
        getBlockedTimes(selectedLift, selectedDate, function (timeArray) {
            renderTimeSlots(selectedDate, timeArray);
        });

    });

    $('#openDayCalendarMb').on('click', function () {
        if (!selectedDate) return;
        if (!PRODUCT_MODE && !selectedLift) return;
        $('.mx-gridWrap, .mx-legendMini').hide();
        $(this).hide();
        showTimeView();

        getBlockedTimes(selectedLift, selectedDate, function (timeArray) {
            renderTimeSlots(selectedDate, timeArray);
        });
        console.log(selectedLift);

    });

    $('#mxBackToDate').on('click', function () {
        showDateView();
        $('#openDayCalendar').prop('disabled', false).addClass('enabled');
        $('#openDayCalendarMb').show();
    });

    /* ================================================================
       CONTINUE → slot modal
    ================================================================ */
    // $('#mxContinueBtn').on('click', function () {
    //     if (!selectedDate || !selectedStartTime) return;
    //     selectedHours = selectedPackHours;
    //     var startH = parseInt(selectedStartTime.slice(0, 2), 10);
    //     var check  = validateConsecutiveCrossDay(selectedDate, selectedStartTime, selectedHours);

    //     $('#mxSlotText').text(prettyRange(selectedDate, startH, selectedHours));
    //     $('#mxSelectedHours').text(selectedHours);
    //     $('#mxTotalText').text(formatMoney(getPackageTotal(selectedHours)));
    //     $('#mxHintText').text(
    //         check.ok
    //             ? 'Continuous booking for ' + selectedHours + ' hour' + (selectedHours > 1 ? 's' : '') + '.'
    //             : check.message
    //     );
    //     $('#mxModalConfirm').prop('disabled', !check.ok).css('opacity', check.ok ? '1' : '.5');
    //     openSlotModal();
    // });

    $('#mxContinueBtn').on('click', function () {

        if (!selectedDate || !selectedStartTime) return;

        selectedHours = selectedPackHours;

        var startH = parseInt(selectedStartTime.slice(0, 2), 10);

        var check = validateConsecutiveCrossDay(
            selectedDate,
            selectedStartTime,
            selectedHours
        );

        $('#mxSlotText').text(
            prettyRange(selectedDate, startH, selectedHours)
        );

        $('#mxSelectedHours').text(selectedHours);
        // $('#mxTotalText').text(formatMoney(getPackageTotal(selectedHours)));
        $('#mxTotalText').text(formatMoney(getEffectiveTotal(selectedHours)));

        $('#mxHintText').text(
            check.ok
                ? 'Continuous booking for ' + selectedHours + ' hour' +
                (selectedHours > 1 ? 's' : '') + '.'
                : check.message
        );

        $('#mxModalConfirm')
            .prop('disabled', !check.ok)
            .css('opacity', check.ok ? '1' : '.5');

            //   $('#mxsLift').text(getActiveLiftLabel());
            $('#mxsLift').text(getActiveLiftLabel() + ' ($' + getRatePerHour() + '/hr)');
    $('#mxsAddon').text(
        addonSelected
            ? 'Alignment Rack ($' + addonPrice + ')'
            : 'None'
    );

        /* ---------- IMPORTANT ---------- */

        var wh = getWorkingHours(selectedDate);

        // actual ending hour
        var endHour = startH + selectedHours;

        // disable + if next hour exceeds day's closing time
        $('#mxHPlus').prop(
            'disabled',
            !wh || endHour >= wh.end
        );

        openSlotModal();
    });

    /* ================================================================
       SLOT MODAL CLOSE
    ================================================================ */
    $('#mxModalClose, #mxModalCancel').on('click', closeSlotModal);
    $('#mxSlotModal').on('click', function (e) { if ($(e.target).is('#mxSlotModal')) closeSlotModal(); });

    /* ================================================================
       AUTH
    ================================================================ */
    window.MX_CSRF = $('meta[name="csrf-token"]').attr('content') || '';
    window.MX_IS_LOGGED_IN = (function () {
        var v = $('#mx-auth-state').data('logged-in');
        return (v === 1 || v === '1' || v === true);
    }());

    function mxGetBookingPayload() {
        return {
            date: selectedDate,
            start: selectedStartTime,
            hours: selectedHours,
            total: getEffectiveTotal(selectedHours),   // changed
            lift: selectedLift,
            package: selectedPackHours,
            workstation: 1,
            product_id: PRODUCT_MODE ? ($meta.data('product-id') || null) : null,
            addon_lift: addonSelected ? ADDON_LIFT_KEY : null,   // new
            addon_price: addonSelected ? addonPrice : 0,          // new
        };
    }

    /* ================================================================
       SLOT MODAL CONFIRM → auth gate
       - Guest  → auth modal → guest form → storeGuestBooking → success modal
       - Logged in → submitBooking directly (no pay modal for now)
    ================================================================ */
    $('#mxModalConfirm').on('click', function () {
        var check = validateConsecutiveCrossDay(selectedDate, selectedStartTime, selectedHours);
        if (!check.ok) { alert(check.message); return; }

        sessionStorage.setItem('mx_booking_payload', JSON.stringify(mxGetBookingPayload()));

        if (!window.MX_IS_LOGGED_IN) {
            closeSlotModal();
            new bootstrap.Modal(document.getElementById('mxAuthModal')).show();
            return;
        }

        // Logged-in user: skip summary + pay modal for now, go straight to booking
        closeSlotModal();

        /* ── PAYMENT DISABLED: submit directly ── */
        submitBooking(mxGetBookingPayload());

        /* ── TO RE-ENABLE SUMMARY+PAYMENT: comment the line above,
               uncomment the line below ──
        populateAndOpenSummary();
        */
    });

    function mxContinueAfterAuth() {
        var raw = sessionStorage.getItem('mx_booking_payload');
        if (!raw) return;
        var p = JSON.parse(raw);
        selectedDate = p.date;
        selectedStartTime = p.start;
        selectedHours = p.hours;
        selectedPackHours = p.package;
        selectedLift = p.lift;

        if (!PRODUCT_MODE) {
            $('.mx-liftbtn').removeClass('active');
            $('.mx-liftbtn[data-lift="' + selectedLift + '"]').addClass('active');
            var lift = LIFT_DATA[selectedLift];
            if (lift) {
                $('#mxLiftPlaceholder').hide();
                $('#mxLiftPreviewImg').attr('src', lift.img).show();
                // $('#mxLiftPoints').html(lift.points.map(function (pt) { return '<li>' + pt + '</li>'; }).join(''));
            }
            $('#mxLiftPrompt').addClass('hidden');
        }
        updateBookBtnState();
        var inst = bootstrap.Modal.getInstance(document.getElementById('mxAuthModal'));
        if (inst) inst.hide();

        /* ── PAYMENT DISABLED: submit directly ── */
        submitBooking(p);

        /* ── TO RE-ENABLE SUMMARY+PAYMENT: comment the line above,
               uncomment the line below ──
        populateAndOpenSummary();
        */
    }

    /* ================================================================
       SUMMARY MODAL (kept in DOM but not shown in current flow)
       "Pay Now" button wired up for when payment is re-enabled.
    ================================================================ */
    $('#mxSummaryClose').on('click', closeSummaryModal);
    $('#mxSummaryModal').on('click', function (e) { if ($(e.target).is('#mxSummaryModal')) closeSummaryModal(); });
    $('#mxSummaryBack').on('click', function () { closeSummaryModal(); openSlotModal(); });

    /* PAYMENT DISABLED — #mxSummaryPay now does nothing visible */
    $('#mxSummaryPay').on('click', function () {
        /* ── TO RE-ENABLE PAYMENT: uncomment the block below ──
        closeSummaryModal();
        $('#mxCardNum, #mxCardExp, #mxCardCvv, #mxCardName').val('');
        $('#mxPayError').addClass('d-none').text('');
        $('#mxPayNowBtn').prop('disabled', false);
        $('#mxPaySpinner').addClass('d-none');
        $('#mxPayBtnText').html('Pay <span id="mxPayBtnAmt">' + $('#mxPayAmount').text() + '</span>');
        $('.mxs-pay-tab').removeClass('active').filter('[data-tab="card"]').addClass('active');
        $('.mxs-pay-panel').removeClass('active'); $('#mxPayPanel-card').addClass('active');
        openPayModal();
        */
    });

    /* ================================================================
       PAY MODAL (disabled — kept for future use)
    ================================================================ */
    /* PAYMENT DISABLED
    $('#mxPayClose').on('click', closePayModal);
    $('#mxPayModal').on('click', function (e) { if ($(e.target).is('#mxPayModal')) closePayModal(); });

    $(document).on('click', '.mxs-pay-tab', function () {
        var tab = $(this).data('tab');
        $('.mxs-pay-tab').removeClass('active'); $(this).addClass('active');
        $('.mxs-pay-panel').removeClass('active'); $('#mxPayPanel-' + tab).addClass('active');
    });

    $('#mxCardNum').on('input', function () {
        var r = $(this).val().replace(/\D/g, '').slice(0, 16);
        $(this).val(r.match(/.{1,4}/g) ? r.match(/.{1,4}/g).join(' ') : r);
    });
    $('#mxCardExp').on('input', function () {
        var v = $(this).val().replace(/\D/g, '').slice(0, 4);
        if (v.length >= 3) v = v.slice(0, 2) + ' / ' + v.slice(2);
        $(this).val(v);
    });

    $('#mxPayNowBtn').on('click', function () {
        simulateDemoPayment(function () {
            submitBooking(JSON.parse(sessionStorage.getItem('mx_booking_payload') || '{}'));
        });
    });
    */

    /* ================================================================
       GUEST BOOKING FORM
       Single handler only — fixes the double-submit SQL conflict bug.
    ================================================================ */
    $('#guestBookingForm').on('submit', async function (e) {
        e.preventDefault();
        var $err = $('#guestErrorMsg').addClass('d-none').text('');

        var rawPhone = $('#guestPhone').val().replace(/\D/g, '');
        if (rawPhone.length !== 10) {
            $err.text('Please enter a valid 10-digit US phone number.').removeClass('d-none');
            return;
        }

        var payload = mxGetBookingPayload();
        payload.guest_name = $('#guestName').val().trim();
        payload.guest_phone = '+1' + rawPhone;

        if (!payload.date || !payload.start) {
            $err.text('Booking details are missing. Please go back and select a date and time.').removeClass('d-none');
            return;
        }

        // Disable submit button to prevent double-click re-submit
        var $btn = $(this).find('button[type=submit]').prop('disabled', true);

        try {
            var res = await fetch('/booking/guest', {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.MX_CSRF,
                    'Accept': 'application/json',
                },
                body: JSON.stringify(payload),
            });

            var data = await res.json().catch(function () { return {}; });

            if (!res.ok || !data.status) {
                $err.text(data.message || 'Booking failed. Please try again.').removeClass('d-none');
                $btn.prop('disabled', false);
                return;
            }

            // Close auth modal
            var inst = bootstrap.Modal.getInstance(document.getElementById('mxAuthModal'));
            if (inst) inst.hide();

            // Show guest success modal (call to confirm)
            showGuestSuccessModal(data.booking_id, payload, data.expires_at);

        } catch (err) {
            $err.text('Network error. Please try again.').removeClass('d-none');
            $btn.prop('disabled', false);
        }
    });

    /* ================================================================
       GUEST SUCCESS MODAL — "Call to Confirm"
    ================================================================ */
    function showGuestSuccessModal(bookingId, payload, expiresAt) {
        var startH = parseInt(payload.start.slice(0, 2), 10);
        var dateFmt = new Date(payload.date + 'T00:00:00').toLocaleDateString([], {
            weekday: 'long', year: 'numeric', month: 'long', day: 'numeric',
        });

        $('#mxGuestBookingId').text(bookingId);
        $('#mxgName').text(payload.guest_name);
        $('#mxgPhone').text(payload.guest_phone);
        $('#mxgLift').text(LIFT_LABELS[payload.lift] || getActiveLiftLabel());
        $('#mxgAddon').text(
    addonSelected
        ? 'Alignment Rack ($' + addonPrice + '/hr)'
        : 'None'
);
        $('#mxgDate').text(dateFmt);
        $('#mxgTime').text(formatTimePoint(startH));
        $('#mxgDuration').text(payload.hours + ' hour' + (payload.hours > 1 ? 's' : ''));
        $('#mxgSlotTiming').text(prettyRange(payload.date, startH, payload.hours));
        // $('#mxgTotal').text(formatMoney(getPackageTotal(payload.hours)));
        $('#mxgTotal').text(formatMoney(getEffectiveTotal(payload.hours)));

        startGuestTimer(expiresAt);
        openModal('#mxGuestSuccessModal');
    }

    /* ================================================================
       COUNTDOWN TIMER
    ================================================================ */
    var guestTimerInterval = null;

    function startGuestTimer(expiresAt) {
        if (guestTimerInterval) clearInterval(guestTimerInterval);
        var expiryTime = new Date(expiresAt).getTime();

        guestTimerInterval = setInterval(function () {
            var distance = expiryTime - Date.now();
            if (distance < 0) {
                clearInterval(guestTimerInterval);
                $('#mxGuestTimer').text('EXPIRED').css('color', '#ef4444');
                return;
            }
            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((distance % (1000 * 60)) / 1000);
            $('#mxGuestTimer').text(pad2(minutes) + ':' + pad2(seconds));
            if (minutes < 5) $('#mxGuestTimer').css('color', '#f59e0b');
        }, 1000);
    }

    /* ================================================================
       PHONE FORMATTING
    ================================================================ */
    $('#guestPhone').on('input', function () {
        var v = $(this).val().replace(/\D/g, '');
        var f = '';
        if (v.length > 0) f = '(' + v.substring(0, 3);
        if (v.length >= 4) f += ') ' + v.substring(3, 6);
        if (v.length >= 7) f += '-' + v.substring(6, 10);
        $(this).val(f);
    });

    /* ================================================================
       CLOSE GUEST MODAL (backdrop click)
    ================================================================ */
    $('#mxGuestSuccessModal').on('click', function (e) {
        if ($(e.target).is('#mxGuestSuccessModal')) {
            if (confirm('Are you sure? Your slot reservation will be lost if you haven\'t called to confirm.')) {
                closeModal('#mxGuestSuccessModal');
                if (guestTimerInterval) clearInterval(guestTimerInterval);
                location.reload();
            }
        }
    });

    /* ================================================================
       SUBMIT BOOKING (logged-in users)
    ================================================================ */
    async function submitBooking(payload) {
        try {
            var res = await fetch('/booking/confirm', {
                method: 'POST', credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.MX_CSRF,
                    'Accept': 'application/json',
                },
                body: JSON.stringify(payload),
            });
            var data = await res.json().catch(function () { return {}; });
            sessionStorage.removeItem('mx_booking_payload');
            if (!res.ok || !data.status) { alert(data.message || 'Booking failed. Please try again.'); return; }
            openSuccessReceipt(data.booking_id || ('MX-' + Date.now()), payload);
        } catch (_) {
            sessionStorage.removeItem('mx_booking_payload');
            openSuccessReceipt('MX-DEMO-' + Date.now(), payload);
        }
    }

    /* ================================================================
       SUCCESS RECEIPT (logged-in users)
    ================================================================ */
    function openSuccessReceipt(bookingId, payload) {
        var rate = getRatePerHour();
        var total = getEffectiveTotal(payload.hours);
        var startH = parseInt(payload.start.slice(0, 2), 10);
        var dateFmt = new Date(payload.date + 'T00:00:00').toLocaleDateString([], {
            weekday: 'long', year: 'numeric', month: 'long', day: 'numeric',
        });
        $('#mxSuccessBookingId').text(bookingId);
        $('#mxrWorkstation').text('100 Midstreams Rd, Brick, NJ');
        $('#mxrLift').text(LIFT_LABELS[payload.lift] || getActiveLiftLabel());
   $('#mxrAddon').text(
    addonSelected
        ? 'Alignment Rack ($' + addonPrice + '/hr)'
        : 'None'
);
        $('#mxrDate').text(dateFmt);
        $('#mxrStart').text(formatTimePoint(startH));
        $('#mxrDuration').text(payload.hours + ' hour' + (payload.hours > 1 ? 's' : ''));
        $('#mxrEnd').text(buildEndLabel(payload.date, payload.start, payload.hours));
        $('#mxrRate').text(formatMoney(rate) + ' / hr');
        $('#mxrTotal').text(formatMoney(total));
        openSuccessModal();
    }

 /* ================================================================
   PRINT / PDF RECEIPT
================================================================ */
$('#mxPrintBtn').on('click', function () {
    printBookingReceipt();
});

function printBookingReceipt() {

    var rows = [
        ['Workstation', $('#mxrWorkstation').text()],
        ['Lift Type', $('#mxrLift').text()],
        ['Booking Date', $('#mxrDate').text()],
        ['Start Time', $('#mxrStart').text()],
        ['Duration', $('#mxrDuration').text()],
        ['End Time', $('#mxrEnd').text()],
        ['Rate', $('#mxrRate').text()],
        ['Add-On', $('#mxrAddon').text()]
    ];

    var total   = $('#mxrTotal').text();
    var today   = new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
    var refNo   = 'MX-' + new Date().toISOString().slice(0, 10).replace(/-/g, '') + '-' +
                  Math.floor(100 + Math.random() * 900);

    var rowsHtml = rows.map(function (r) {
        return `
            <div class="detail-row">
                <span class="detail-label">${r[0]}</span>
                <span class="detail-value">${r[1]}</span>
            </div>
        `;
    }).join('');

    var html = `
    <html>
    <head>
        <title>Booking Receipt — ${refNo}</title>
        <style>

            @page { size: A4; margin: 14mm; }

            * { box-sizing: border-box; -webkit-print-color-adjust: exact; print-color-adjust: exact; }

            body{
                font-family:'Segoe UI', Helvetica, Arial, sans-serif;
                margin:0; padding:0;
                background:#fff;
                color:#1f2937;
            }

            .accent-bar{
                height:6px;
                background:linear-gradient(90deg,#dc2626,#7f1d1d);
            }

            .receipt{
                max-width:760px;
                margin:0 auto;
                padding:36px 40px 40px;
            }

            /* ── HEADER ── */
            .header{
                display:flex;
                justify-content:space-between;
                align-items:flex-start;
                padding-bottom:22px;
                border-bottom:1px solid #e5e7eb;
            }

            .brand img{ height:42px; margin-bottom:10px; display:block; }
            .brand-meta{
                font-size:11.5px;
                line-height:1.6;
                color:#6b7280;
            }
            .brand-meta b{ color:#374151; }

            .header-right{ text-align:right; }
            .receipt-title{
                font-size:22px;
                font-weight:800;
                letter-spacing:.5px;
                color:#111827;
                margin-bottom:6px;
            }
            .receipt-meta{
                font-size:11.5px;
                color:#6b7280;
                line-height:1.6;
            }
            .receipt-meta b{ color:#374151; }

            .status-pill{
                display:inline-block;
                margin-top:8px;
                padding:4px 12px;
                border-radius:20px;
                background:#fef3c7;
                color:#92400e;
                font-size:10.5px;
                font-weight:700;
                letter-spacing:.6px;
                text-transform:uppercase;
            }

            /* ── DETAILS ── */
            .section-label{
                font-size:11px;
                font-weight:700;
                letter-spacing:.8px;
                text-transform:uppercase;
                color:#9ca3af;
                margin:28px 0 10px;
            }

            .details-card{
                border:1px solid #e5e7eb;
                border-radius:10px;
                overflow:hidden;
            }

            .detail-row{
                display:flex;
                justify-content:space-between;
                padding:11px 18px;
                border-bottom:1px solid #f1f1f2;
                font-size:13.5px;
            }
            .detail-row:nth-child(even){ background:#fafafa; }
            .detail-row:last-child{ border-bottom:none; }

            .detail-label{ color:#6b7280; }
            .detail-value{ font-weight:600; color:#111827; }

            /* ── TOTAL ── */
            .total-box{
                margin-top:22px;
                background:linear-gradient(135deg,#111827,#1f2937);
                border-radius:10px;
                padding:18px 22px;
                display:flex;
                justify-content:space-between;
                align-items:center;
            }
            .total-box .label{
                color:#9ca3af;
                font-size:11px;
                letter-spacing:.6px;
                text-transform:uppercase;
                margin-bottom:4px;
            }
            .total-box .amount{
                color:#fff;
                font-size:26px;
                font-weight:800;
            }

            /* ── NOTICE ── */
            .notice-box{
                margin-top:26px;
                padding:14px 18px;
                background:#fffbeb;
                border-left:4px solid #f59e0b;
                border-radius:6px;
                font-size:13px;
                color:#78350f;
            }
            .notice-box h3{
                margin:0 0 4px;
                font-size:13.5px;
                font-weight:700;
                color:#92400e;
            }

            /* ── CONTACT ── */
            .contact-box{
                margin-top:20px;
                padding:16px 18px;
                background:#fef2f2;
                border-radius:8px;
                font-size:13px;
                color:#374151;
                display:flex;
                justify-content:space-between;
                align-items:center;
            }
            .contact-box .phone{
                color:#dc2626;
                font-weight:800;
                font-size:16px;
            }
            .contact-box .hours{
                text-align:right;
                font-size:11.5px;
                color:#6b7280;
                line-height:1.5;
            }

            /* ── FOOTER ── */
            .footer{
                margin-top:36px;
                padding-top:16px;
                border-top:1px solid #e5e7eb;
                text-align:center;
                font-size:11px;
                color:#9ca3af;
                line-height:1.6;
            }

        </style>
    </head>

    <body>

        <div class="accent-bar"></div>

        <div class="receipt">

            <div class="header">
                <div class="brand">
                    <img src="/assets/images/logomain.png" onerror="this.style.display='none'">
                    <div class="brand-meta">
                        <b>Mechanix D.I.Y.</b><br>
                        100 Midstreams Rd, Brick, NJ <br>
                        732-730-7712 Ext. 3 &nbsp;•&nbsp; www.mechanixdiy.com
                    </div>
                </div>

                <div class="header-right">
                    <div class="receipt-title">BOOKING RECEIPT</div>
                    <div class="receipt-meta">
                        <b>Reference:</b> ${refNo}<br>
                        <b>Date Issued:</b> ${today}
                    </div>
                    <div class="status-pill">Pending Confirmation</div>
                </div>
            </div>

            <div class="section-label">Booking Details</div>
            <div class="details-card">
                ${rowsHtml}
            </div>

            <div class="total-box">
                <div>
                    <div class="label">Total Amount To Be Paid</div>
                </div>
                <div class="amount">${total}</div>
            </div>

            <div class="notice-box">
                <h3>Booking Request Submitted</h3>
                This slot is reserved pending confirmation. Please call us to finalize your booking — it is not guaranteed until confirmed by our team.
            </div>

            <div class="contact-box">
                <div>
                    Call to confirm:<br>
                    <span class="phone">732-730-7712 Ext. 3</span>
                </div>
                <div class="hours">
                    Mon–Fri: 9:00 AM – 6:00 PM<br>
                    Saturday: 9:00 AM – 12:00 PM
                </div>
            </div>

            <div class="footer">
                Thank you for choosing Mechanix D.I.Y.<br>
                Please retain this receipt for your records.
            </div>

        </div>

    </body>
    </html>
    `;

    var w = window.open('', '_blank');

    w.document.open();
    w.document.write(html);
    w.document.close();

    setTimeout(function () {
        w.focus();
        w.print();
    }, 500);
}


/* ================================================================
   PRINT guest  / PDF RECEIPT guest
================================================================ */
$(document).on('click', '#mxGuestPrintBtn', function () {
    printGuestReceipt();
});

function printGuestReceipt() {

    var rows = [
        ['Guest Name', $('#mxgName').text()],
        ['Phone Number', $('#mxgPhone').text()],
        ['Lift Type', $('#mxgLift').text()],
        ['Booking Date', $('#mxgDate').text()],
        ['Time', $('#mxgTime').text()],
        ['Duration', $('#mxgDuration').text()],
        ['Slot Timing', $('#mxgSlotTiming').text()],
        ['Add-On', $('#mxgAddon').text()]
    ];

    var total = $('#mxgTotal').text();

    var today = new Date().toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });

    var refNo =
        'MXG-' +
        new Date().toISOString().slice(0, 10).replace(/-/g, '') +
        '-' +
        Math.floor(100 + Math.random() * 900);

    var rowsHtml = rows.map(function (r) {
        return `
            <div class="detail-row">
                <span class="detail-label">${r[0]}</span>
                <span class="detail-value">${r[1]}</span>
            </div>
        `;
    }).join('');

    var html = `
    <html>
    <head>
        <title>Guest Booking Receipt</title>

        <style>

            @page { size:A4; margin:14mm; }

            *{
                box-sizing:border-box;
                -webkit-print-color-adjust:exact;
                print-color-adjust:exact;
            }

            body{
                font-family:'Segoe UI',Arial,sans-serif;
                margin:0;
                color:#222;
            }

            .receipt{
                max-width:800px;
                margin:auto;
                padding:30px;
            }

            .header{
                display:flex;
                justify-content:space-between;
                align-items:flex-start;
                border-bottom:2px solid #ddd;
                padding-bottom:20px;
                margin-bottom:25px;
            }

            .logo{
                max-width:180px;
            }

            .company-info{
                font-size:12px;
                color:#666;
                margin-top:10px;
                line-height:1.6;
            }

            .right{
                text-align:right;
            }

            .receipt-title{
                font-size:24px;
                font-weight:700;
            }

            .status{
                margin-top:10px;
                display:inline-block;
                background:#fff3cd;
                color:#856404;
                padding:6px 12px;
                border-radius:20px;
                font-size:12px;
                font-weight:700;
            }

            .details-card{
                border:1px solid #ddd;
                border-radius:8px;
                overflow:hidden;
            }

            .detail-row{
                display:flex;
                justify-content:space-between;
                padding:12px 16px;
                border-bottom:1px solid #eee;
            }

            .detail-row:last-child{
                border-bottom:none;
            }

            .detail-label{
                color:#666;
            }

            .detail-value{
                font-weight:600;
            }

            .total-box{
                margin-top:20px;
                background:#111827;
                color:#fff;
                border-radius:8px;
                padding:18px;
                display:flex;
                justify-content:space-between;
                align-items:center;
            }

            .total-box .amount{
                font-size:24px;
                font-weight:800;
            }

            .notice{
                margin-top:20px;
                padding:15px;
                background:#fffbeb;
                border-left:4px solid #f59e0b;
                border-radius:6px;
            }

            .contact{
                margin-top:20px;
                background:#f8f9fa;
                padding:18px;
                border-radius:8px;
                text-align:center;
            }

            .phone{
                font-size:20px;
                font-weight:bold;
                color:#198754;
            }

            .signatures{
                margin-top:60px;
                display:flex;
                justify-content:space-between;
            }

            .sign-box{
                width:250px;
                text-align:center;
            }

            .sign-line{
                border-top:1px solid #000;
                margin-top:60px;
                padding-top:8px;
                font-size:13px;
            }

            .footer{
                margin-top:40px;
                text-align:center;
                font-size:11px;
                color:#777;
            }

        </style>

    </head>

    <body>

        <div class="receipt">

            <div class="header">

                <div>

                    <img class="logo"
                         src="/assets/images/logomain.png"
                         onerror="this.style.display='none'">

                    <div class="company-info">
                        YOUR CAR. YOUR RULES. YOUR SKILLS.<br>
                        100 Midstreams Rd, Brick, NJ<br>
                        732-730-7712 Ext. 3
                    </div>

                </div>

                <div class="right">

                    <div class="receipt-title">
                        GUEST BOOKING RECEIPT
                    </div>

                    <div>
                        Ref: ${refNo}<br>
                        Date: ${today}
                    </div>

                    <div class="status">
                        Pending Confirmation
                    </div>

                </div>

            </div>

            <div class="details-card">
                ${rowsHtml}
            </div>

            <div class="total-box">
                <div>Total Amount To Be Paid</div>
                <div class="amount">${total}</div>
            </div>

            <div class="notice">
                <strong>Booking Request Submitted Successfully</strong><br>
                Your booking request has been received and is awaiting confirmation from our team.
            </div>

            <div class="contact">
                Call To Confirm<br>
                <div class="phone">732-730-7712 EXT. 3</div>
            </div>

            <div class="signatures">

                <div class="sign-box">
                    <div class="sign-line">
                        Customer Signature
                    </div>
                </div>

                <div class="sign-box">
                    <div class="sign-line">
                        Authorized Signature
                    </div>
                </div>

            </div>

            <div class="footer">
                Thank you for choosing Mechanix D.I.Y.
            </div>

        </div>

    </body>
    </html>
    `;

    var w = window.open('', '_blank');

    w.document.open();
    w.document.write(html);
    w.document.close();

    setTimeout(function () {
        w.focus();
        w.print();
    }, 500);
}
    /* ================================================================
       AUTH FORMS
    ================================================================ */

    /* ── LOGIN ── */
    $('#mxLoginForm').on('submit', async function (e) {
        e.preventDefault();
        var $err = $('#loginErrorMsg').addClass('d-none').text('');
        var loginUrl = $('#mx-routes').data('login-url') || '/popup-login';
        var email = $(this).find('[name=email]').val().trim();
        var password = $(this).find('[name=password]').val();

        if (!email) {
            $err.text('Email is required.').removeClass('d-none');
            return;
        }

        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!emailRegex.test(email)) {
            $err.text('Please enter a valid email address.').removeClass('d-none');
            return;
        }

        if (!password) {
            $err.text('Password is required.').removeClass('d-none');
            return;
        }

        if (password.length < 6) {
            $err.text('Password must be at least 6 characters.').removeClass('d-none');
            return;
        }
        try {
            var res = await fetch(loginUrl, {
                method: 'POST', credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': window.MX_CSRF || $('meta[name="csrf-token"]').attr('content') || '',
                },
                body: JSON.stringify({
                    email: $(this).find('[name=email]').val().trim(),
                    password: $(this).find('[name=password]').val(),
                }),
            });

            var data = await res.json().catch(function () { return {}; });

            if (!res.ok) {
                $err.text(data.message || 'Login failed. Please check your credentials.').removeClass('d-none');
                return;
            }

            window.MX_IS_LOGGED_IN = true;
            $('#mx-auth-state').attr('data-logged-in', '1');

            var modalEl = document.getElementById('mxAuthModal');
            if (modalEl) {
                var mi = bootstrap.Modal.getInstance(modalEl);
                if (mi) mi.hide(); else $(modalEl).modal('hide');
            }

            // After login, continue the booking that was in progress
            setTimeout(function () { mxContinueAfterAuth(); }, 400);

            // Reload page
            // window.location.reload();
            //redirect to admin dashboard after login
            window.location.href = '/admin';

        } catch (err) {
            $err.text('Network error. Please try again.').removeClass('d-none');
        }
    });

    /* ── REGISTER ── */
    $('#mxRegisterForm').on('submit', async function (e) {
        e.preventDefault();
        var $err = $('#registerErrorMsg').addClass('d-none').text('');
        var registerUrl = $('#mx-routes').data('register-url') || '/popup-register';
        var email = $(this).find('[name=email]').val().trim();
        var mobile = $(this).find('[name=mobile_no]').val().trim();
        var password = $(this).find('[name=password]').val();
        var confirmPassword = $(this).find('[name=password_confirmation]').val();

        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        var phoneRegex = /^[0-9]{10}$/;

        if (!email) {
            $err.text('Email is required.').removeClass('d-none');
            return;
        }

        if (!emailRegex.test(email)) {
            $err.text('Please enter a valid email address.').removeClass('d-none');
            return;
        }

        if (!mobile) {
            $err.text('Mobile number is required.').removeClass('d-none');
            return;
        }

        if (!phoneRegex.test(mobile)) {
            $err.text('Mobile number must be exactly 10 digits.').removeClass('d-none');
            return;
        }

        if (!password) {
            $err.text('Password is required.').removeClass('d-none');
            return;
        }

        if (password.length < 8) {
            $err.text('Password must be at least 8 characters long.').removeClass('d-none');
            return;
        }

        if (!/[A-Z]/.test(password)) {
            $err.text('Password must contain at least one uppercase letter.').removeClass('d-none');
            return;
        }

        if (!/[a-z]/.test(password)) {
            $err.text('Password must contain at least one lowercase letter.').removeClass('d-none');
            return;
        }

        if (!/[0-9]/.test(password)) {
            $err.text('Password must contain at least one number.').removeClass('d-none');
            return;
        }

        if (password !== confirmPassword) {
            $err.text('Password confirmation does not match.').removeClass('d-none');
            return;
        }
        try {
            var res = await fetch(registerUrl, {
                method: 'POST', credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': window.MX_CSRF || $('meta[name="csrf-token"]').attr('content') || '',
                },
                body: JSON.stringify({
                    email: $(this).find('[name=email]').val().trim(),
                    mobile_no: $(this).find('[name=mobile_no]').val().trim(),
                    password: $(this).find('[name=password]').val(),
                    password_confirmation: $(this).find('[name=password_confirmation]').val(),
                }),
            });

            var data = await res.json().catch(function () { return {}; });

            if (!res.ok) {
                var errorText = data.errors
                    ? Object.values(data.errors).flat().join(' • ')
                    : (data.message || 'Registration failed.');
                $err.text(errorText).removeClass('d-none');
                return;
            }

            window.MX_IS_LOGGED_IN = true;
            $('#mx-auth-state').attr('data-logged-in', '1');

            var modalEl = document.getElementById('mxAuthModal');
            if (modalEl) {
                var mi = bootstrap.Modal.getInstance(modalEl);
                if (mi) mi.hide(); else $(modalEl).modal('hide');
            }

            // After register, continue the booking that was in progress
            setTimeout(function () { mxContinueAfterAuth(); }, 400);

            // Reload page
            // window.location.reload();
            //redirect to admin dashboard after registration
            window.location.href = '/admin';
        } catch (err) {
            $err.text('Network error. Please try again.').removeClass('d-none');
        }
    });

    /* ================================================================
       WORKSTATION TABS
    ================================================================ */
    $(document).on('click', '.mx-w-title', function () {
        $('.mx-w-title').removeClass('active'); $(this).addClass('active');
        loadCalendarData(null, parseInt($(this).data('ws'), 10) || 1);
    });

    /* ================================================================
       RESPONSIVE
    ================================================================ */
    function toggleBookClose() { window.innerWidth < 768 ? $('#bookclose').show() : $('#bookclose').hide(); }
    toggleBookClose();
    $(window).on('resize', toggleBookClose);

    function syncBtnPointers() {
        $('#leftupButton button').css('pointer-events', $('#leftupButton').is(':visible') ? 'auto' : 'none');
        $('.cal-sub-btn button').css('pointer-events', $('.cal-sub-btn').is(':visible') ? 'auto' : 'none');
    }
    syncBtnPointers();
    $(window).on('resize', syncBtnPointers);

    /* ================================================================
       INIT
    ================================================================ */
    updateBookBtnState();
});

/* ================================================================
   GLOBAL: close guest/member success modal (called from blade onclick)
================================================================ */

// function closeGuestMemberSuccessModal() {
//     var modal = document.getElementById('mxGuestSuccessModal');
//     console.log("age",modal);


//     if (!modal) return;
//     console.log("pore",modal);


//     modal.classList.remove('show');
//     modal.setAttribute('aria-hidden', 'true');
// }
document
    .getElementById('guestSuccessCloseBtn')
    .addEventListener('click', function () {

        const modal = document.getElementById('mxGuestSuccessModal');

        modal.classList.remove('show');
        modal.setAttribute('aria-hidden', 'true');

        window.location.reload();
    });

document.getElementById('mxSuccessCloseBtn')?.addEventListener('click', function () {
    document.getElementById('mxSuccessModal').classList.remove('show');
    // Reload page
    window.location.reload();
});

function getBlockedTimes(selectedLift, selectedDate, callback) {

    $.ajax({
        url: '/get-blocked-times',
        type: 'POST',
        data: {
            lift_type: selectedLift,
            date: selectedDate,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {

            // response.start_times = ["10:00:00","09:00:00",...]
            callback(response.start_times);

        },
        error: function (xhr) {
            console.error('Error fetching blocked times', xhr);

            // fallback empty array
            callback([]);
        }
    });
}
async function checkBookingAvailability(date, lift, startTime, hours) {

    return $.ajax({

        url: '/check-booking-hours',
        type: 'POST',

        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            date: date,
            lift: lift,
            start_time: startTime,
            hours: hours
        }

    });
}