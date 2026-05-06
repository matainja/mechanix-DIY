/**
 * booking.js  —  Mechanix D.I.Y.  (v5 – dual mode)
 *
 * TWO MODES (detected from DOM on init):
 *
 * ── PRODUCT MODE  (came via Rentals → product detail → Book Now) ──────
 *   • Lift is already known from the product (#mxProductMeta[data-lift-key])
 *   • selectedLift is set automatically on page load
 *   • Liftbar & prompt are hidden by the blade; JS never touches them
 *   • Book Now only needs a DATE — lift gate is skipped
 *   • Slot availability is checked per-lift as usual
 *
 * ── DIRECT MODE  (user navigates straight to /booking) ───────────────
 *   • User must choose LIFT then DATE before Book Now enables
 *   • Lift prompt banner shown; hides on first lift click
 *   • Calendar repaints on every lift switch
 *
 * SHARED FEATURES:
 *   ③ Calendar: green / yellow / orange / red / grey (BookMyShow scale)
 *   ④ Slot cards: Available (green) | Booked (hatched grey) | Selected (red)
 *   🔑 Per-lift slot isolation: "YYYY-MM-DD__liftKey" keys in bookedSlots
 */

$(function () {

    /* ================================================================
       SCROLL HELPER  (defined first — used by lift-click handler)
    ================================================================ */
    function scrollToEl(el, offset) {
        offset = (offset === undefined) ? 70 : offset;
        if (!el) return;
        $('html, body').animate({ scrollTop: $(el).offset().top - offset }, 400);
    }

    /* ================================================================
       DETECT MODE
    ================================================================ */
    var $meta       = $('#mxProductMeta');
    var PRODUCT_MODE = $meta.length > 0 && $meta.data('product-mode') == 1;
    /*
     * In product mode: lift key comes from the blade data attribute.
     * In direct mode:  starts null, set when user clicks a lift button.
     */
    var AUTO_LIFT_KEY  = PRODUCT_MODE ? ($meta.data('lift-key')  || 'all') : null;
    var AUTO_LIFT_NAME = PRODUCT_MODE ? ($meta.data('lift-name') || '')     : null;

    /* ================================================================
       STATE
    ================================================================ */
    const TODAY     = new Date();
    const MIN_MONTH = new Date();

    var dayData     = {};
    /*
     * bookedSlots keyed by "YYYY-MM-DD__liftKey"
     * Legacy flat server format { "YYYY-MM-DD": [...] } is normalised
     * to "YYYY-MM-DD__all" so the fallback in isSlotBooked() works.
     */
    var bookedSlots = {};

    var selectedDate      = null;
    var selectedStartTime = null;
    var selectedHours     = 1;
    var selectedPackHours = 1;
    var selectedLift      = PRODUCT_MODE ? AUTO_LIFT_KEY : null;
    var fpInstance        = null;

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

    /* Sun–Thu 09:00–18:00 | Fri 09:00–12:00 | Sat closed */
    function getWorkingHours(dateStr) {
        var day = new Date(dateStr + 'T00:00:00').getDay();
        if (day === 6) return null;
        if (day === 5) return { start: 9, end: 12 };
        return { start: 9, end: 18 };
    }

    function getWorkingSlots(dateStr) {
        var wh = getWorkingHours(dateStr);
        if (!wh) return [];
        var s = [];
        for (var h = wh.start; h < wh.end; h++) s.push(pad2(h) + ':00');
        return s;
    }

    /* ----------------------------------------------------------------
       🔑 PER-LIFT SLOT ISOLATION
    ---------------------------------------------------------------- */
    function slotKey(dateStr, liftKey) {
        return dateStr + '__' + (liftKey || selectedLift || 'all');
    }

    function isSlotBooked(dateStr, timeStr, liftKey) {
        liftKey = liftKey || selectedLift || 'all';
        var specific = bookedSlots[slotKey(dateStr, liftKey)] || [];
        var generic  = bookedSlots[slotKey(dateStr, 'all')]   || [];
        return specific.indexOf(timeStr) !== -1 || generic.indexOf(timeStr) !== -1;
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
        if (packHours === 1)  return isDayHasAnyFreeHour(dateStr, liftKey);
        if (packHours === 9)  return getWorkingSlots(dateStr).length >= 9 && isDayFullyFree(dateStr, liftKey);
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
            if (h >= wh.end)  { cur = addDaysStr(cur, 1); h = 0; continue; }
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
        return r.endDate !== dateStr ? fmt(r.endDate) + ' ' + formatTimePoint(r.endHour) : formatTimePoint(r.endHour);
    }

    function prettyRange(dateStr, startHour, hoursNeeded) {
        var r = addWorkingHours(dateStr, startHour, hoursNeeded);
        var fmt = function (d) {
            return new Date(d + 'T00:00:00').toLocaleDateString([], { year: 'numeric', month: 'short', day: '2-digit' });
        };
        var d1 = fmt(dateStr), d2 = fmt(r.endDate);
        var t1 = formatTimePoint(startHour), t2 = formatTimePoint(r.endHour);
        return d1 !== d2 ? d1 + ' \u2022 ' + t1 + ' \u2192 ' + d2 + ' \u2022 ' + t2
                         : d1 + ' \u2022 ' + t1 + ' \u2013 ' + t2;
    }

    function validateConsecutiveCrossDay(startDateStr, startTimeStr, hoursNeeded, liftKey) {
        liftKey = liftKey || selectedLift || 'all';
        var startH = parseInt(startTimeStr.slice(0, 2), 10);
        var wh0    = getWorkingHours(startDateStr);
        if (!wh0) return { ok: false, message: 'Closed on selected day.' };
        if (startH < wh0.start || startH >= wh0.end)
            return { ok: false, message: 'Start time outside working hours.' };

        var rem = hoursNeeded, cur = startDateStr, h = startH;
        while (rem > 0) {
            var wh = getWorkingHours(cur);
            if (!wh) return { ok: false, message: 'Closed on ' + cur + '.' };
            if (h < wh.start) h = wh.start;
            if (h >= wh.end)  { cur = addDaysStr(cur, 1); h = 0; continue; }
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
    function getSelectedCard()      { return $('.mx-pricecard.mx-selected').first(); }
    function getRatePerHour()       { return Number(getSelectedCard().data('price')) || 0; }
    function getPackageTotal(hours) { return getRatePerHour() * hours; }

    /* ================================================================
       LIFT LABELS
    ================================================================ */
    var LIFT_LABELS = {
        four:    'Four-Post Lift',
        two:     'Two-Post Lift',
        scissor: 'Scissor Lift',
        flat:    'Motorcycle Lift',
        flat2:   'Alignment Rack'
    };
    function getActiveLiftLabel() {
        if (PRODUCT_MODE && AUTO_LIFT_NAME) return AUTO_LIFT_NAME;
        return LIFT_LABELS[selectedLift] || selectedLift || '\u2014';
    }

    /* ================================================================
       ① BOOK-NOW GATE
       Product mode → only date required.
       Direct mode  → lift + date required.
    ================================================================ */
    function updateBookBtnState() {
        var hasLift = selectedLift !== null;
        var hasDate = selectedDate !== null;
        var ready;
        var hint = '';

        if (PRODUCT_MODE) {
            // Lift is pre-set — only need a date
            ready = hasDate;
            if (!hasDate) hint = 'Please pick an available date on the calendar.';
        } else {
            ready = hasLift && hasDate;
            if (!hasLift && !hasDate) hint = 'Select a lift type and a date to continue.';
            else if (!hasLift)        hint = 'Select a lift type above to enable booking.';
            else if (!hasDate)        hint = 'Pick an available date on the calendar.';
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
    function openModal(id)  { $(id).addClass('show').attr('aria-hidden', 'false'); }
    function closeModal(id) { $(id).removeClass('show').attr('aria-hidden', 'true'); }

    function openSlotModal()     { openModal('#mxSlotModal');    }
    function closeSlotModal()    { closeModal('#mxSlotModal');   }
    function openSummaryModal()  { openModal('#mxSummaryModal'); }
    function closeSummaryModal() { closeModal('#mxSummaryModal');}
    function openPayModal()      { openModal('#mxPayModal');     }
    function closePayModal()     { closeModal('#mxPayModal');    }
    function openSuccessModal()  { openModal('#mxSuccessModal'); }

    $(document).on('keydown', function (e) {
        if (e.key !== 'Escape') return;
        closeSlotModal(); closeSummaryModal(); closePayModal();
    });

    /* ================================================================
       HOUR CONTROLS
    ================================================================ */
    function toggleHourControls(lock) {
        $('#mxHMinus, #mxHPlus').prop('disabled', lock).toggleClass('mx-disabled', lock);
    }

    function setHours(val) {
        selectedHours = Math.max(selectedPackHours, Math.min(val, 48));
        var startH = parseInt(selectedStartTime.slice(0, 2), 10);
        var check  = validateConsecutiveCrossDay(selectedDate, selectedStartTime, selectedHours);

        $('#mxSelectedHours').text(selectedHours);
        $('#mxTotalText').text(formatMoney(getPackageTotal(selectedHours)));
        $('#mxSlotText').text(prettyRange(selectedDate, startH, selectedHours));
        $('#mxHintText').text(
            check.ok
                ? 'Continuous booking for ' + selectedHours + ' hour' + (selectedHours > 1 ? 's' : '') + '.'
                : check.message
        );
        $('#mxModalConfirm').prop('disabled', !check.ok).css('opacity', check.ok ? '1' : '.5');
    }

    $('#mxHMinus').on('click', function () { setHours(selectedHours - 1); });
    $('#mxHPlus').on('click',  function () { setHours(selectedHours + 1); });

    /* ================================================================
       SUMMARY MODAL
    ================================================================ */
    function populateAndOpenSummary() {
        var rate    = getRatePerHour();
        var total   = getPackageTotal(selectedHours);
        var startH  = parseInt(selectedStartTime.slice(0, 2), 10);
        var dateFmt = new Date(selectedDate + 'T00:00:00').toLocaleDateString([], {
            weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
        });
        $('#mxsWorkstation').text('Workstation 1');
        $('#mxsLift').text(getActiveLiftLabel());
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
       ④ TIME-SLOT GRID  (per-lift aware)
    ================================================================ */
    function renderTimeSlots(dateStr) {
        var $grid = $('#mxTimeGrid').empty();

        $('#mxSelectedDateText').text(
            new Date(dateStr + 'T00:00:00').toLocaleDateString([], {
                weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
            })
        );
        selectedStartTime = null;
        $('#mxPickedTimeText').text('None');
        $('#mxContinueBtn').prop('disabled', true);

        var wh = getWorkingHours(dateStr);
        if (!wh) {
            $grid.html('<div class="mx-slot-closed">This day is closed. No slots available.</div>');
            return;
        }

        /* 9h / 18h → single fixed-start card */
        if (selectedPackHours === 9 || selectedPackHours === 18) {
            var startVal = pad2(wh.start) + ':00';
            var chk = validateConsecutiveCrossDay(dateStr, startVal, selectedPackHours);
            if (!chk.ok) {
                $grid.html(
                    '<div class="mx-slot-unavail">Not available for <strong>' + selectedPackHours +
                    'h</strong> from 9:00 AM.<br><small>' + chk.message + '</small></div>'
                );
                return;
            }
            $('<button>', {
                type: 'button', class: 'mx-slot available', 'data-value': startVal,
                html: '<span class="mx-slot-time">Start at ' + formatTimePoint(wh.start) + '</span>' +
                      '<span class="mx-slot-badge free">Full ' + selectedPackHours + 'h block</span>'
            }).on('click', function () {
                $('.mx-slot').removeClass('selected'); $(this).addClass('selected');
                selectedStartTime = startVal;
                $('#mxPickedTimeText').text(formatTimePoint(wh.start));
                $('#mxContinueBtn').prop('disabled', false);
            }).appendTo($grid);
            return;
        }

        /* 1h → each hourly slot; booked state is per-lift */
        var slots = getWorkingSlots(dateStr);
        if (!slots.length) {
            $grid.html('<div class="mx-slot-closed">No working slots on this day.</div>');
            return;
        }

        slots.forEach(function (value) {
            var h      = parseInt(value.slice(0, 2), 10);
            var label  = formatTimePoint(h) + ' \u2013 ' + formatTimePoint(h + 1);
            var booked = isSlotBooked(dateStr, value, selectedLift); /* 🔑 per-lift */

            var $btn = $('<button>', {
                type: 'button',
                class: 'mx-slot ' + (booked ? 'booked' : 'available'),
                disabled: booked,
                'data-value': value,
                html: '<span class="mx-slot-time">' + label + '</span>' +
                      '<span class="mx-slot-badge ' + (booked ? 'taken' : 'free') + '">' +
                      (booked ? 'Booked' : 'Available') + '</span>'
            });

            if (!booked) {
                $btn.on('click', function () {
                    $('.mx-slot').removeClass('selected'); $(this).addClass('selected');
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
       CALENDAR DATA  (server → normalise bookedSlots format)
    ================================================================ */
    async function loadCalendarData(monthStr, workstation) {
        monthStr    = monthStr    || null;
        workstation = workstation || 1;
        try {
            var params = new URLSearchParams();
            if (monthStr) params.append('month', monthStr);
            params.append('workstation', workstation);
            var res  = await fetch('/booking/calendar-data?' + params.toString(), {
                method: 'GET', credentials: 'same-origin', headers: { Accept: 'application/json' }
            });
            var data = await res.json();
            dayData = data.dayData || {};

            /* Normalise */
            var raw = data.bookedSlots || {};
            bookedSlots = {};
            Object.keys(raw).forEach(function (k) {
                /* New keyed format "YYYY-MM-DD__liftKey" — keep as-is */
                /* Legacy flat "YYYY-MM-DD" — file under "__all" */
                bookedSlots[k.indexOf('__') !== -1 ? k : k + '__all'] = raw[k];
            });
        } catch (_) { /* demo / offline — silent */ }
        if (fpInstance) fpInstance.redraw();
    }

    loadCalendarData(null, 1);

    /* ================================================================
       ③ CALENDAR COLOUR SCALE
    ================================================================ */
    function dayAvailClass(dateStr) {
        if (!getWorkingHours(dateStr)) return 'day-unavailable';
        var info = dayData[dateStr];
        if (info && info.status === 'booked')      return 'day-booked';
        if (info && info.status === 'unavailable') return 'day-unavailable';
        if (!isDateAvailableByPackage(dateStr, selectedPackHours, selectedLift)) return 'day-unavailable';
        var r = dayFreeRatio(dateStr, selectedLift);
        if (r === 0)   return 'day-unavailable';
        if (r >= 0.70) return 'day-available';
        if (r >= 0.30) return 'day-partial';
        return 'day-scarce';
    }

    /* ================================================================
       FLATPICKR
    ================================================================ */
    function updateMonthNav(fp) {
        var cur     = new Date(fp.currentYear, fp.currentMonth, 1);
        var min     = new Date(MIN_MONTH.getFullYear(), MIN_MONTH.getMonth(), 1);
        var prevBtn = fp.calendarContainer.querySelector('.flatpickr-prev-month');
        if (!prevBtn) return;
        var atMin = cur <= min;
        prevBtn.style.pointerEvents = atMin ? 'none' : 'auto';
        prevBtn.style.opacity       = atMin ? '0.3'  : '1';
    }

    fpInstance = flatpickr('#bookingDate', {
        inline: true, dateFormat: 'Y-m-d', disableMobile: true,
        defaultDate: TODAY,
        appendTo: document.getElementById('calendarWrap'),
        minDate:  new Date(),

        onReady:       function (s, d, fp) { fpInstance = fp; updateMonthNav(fp); },
        onMonthChange: function (s, d, fp) {
            updateMonthNav(fp); fp.redraw();
            loadCalendarData(fp.currentYear + '-' + pad2(fp.currentMonth + 1), 1);
        },
        onYearChange:  function (s, d, fp) { updateMonthNav(fp); fp.redraw(); },

        disable: [function (date) {
            if (date.getDay() === 6) return true;
            var info = dayData[flatpickr.formatDate(date, 'Y-m-d')];
            return info && (info.status === 'unavailable' || info.status === 'booked');
        }],

        onDayCreate: function (dObj, dStr, fp, dayElem) {
            dayElem.classList.remove('day-available','day-partial','day-scarce',
                                     'day-booked','day-unavailable','day-nextmonth','day-prevmonth');
            if (dayElem.classList.contains('nextMonthDay')) { dayElem.classList.add('day-nextmonth'); return; }
            if (dayElem.classList.contains('prevMonthDay')) { dayElem.classList.add('day-prevmonth'); return; }

            var key = fp.formatDate(dayElem.dateObj, 'Y-m-d');
            if (dayElem.classList.contains('flatpickr-disabled')) {
                var info = dayData[key];
                dayElem.classList.add((info && info.status === 'booked') ? 'day-booked' : 'day-unavailable');
                return;
            }

            dayElem.classList.add(dayAvailClass(key));

            /* Occupancy dot + tooltip */
            var slots = getWorkingSlots(key);
            var free  = slots.filter(function (t) { return !isSlotBooked(key, t, selectedLift); }).length;
            if (slots.length > 0 && free > 0 && free < slots.length) {
                dayElem.setAttribute('title', free + ' of ' + slots.length + ' slots available');
                var dot = document.createElement('span');
                dot.className = 'mx-day-dot';
                dayElem.appendChild(dot);
            }
        },

        onChange: function (selectedDates, dateStr) {
            if (!dateStr) { selectedDate = null; updateBookBtnState(); return; }
            selectedDate = isDateAvailableByPackage(dateStr, selectedPackHours, selectedLift) ? dateStr : null;
            updateBookBtnState();
        }
    });

    /* ================================================================
       PRICE CARDS
    ================================================================ */
    function selectPackage(hours) {
        selectedPackHours = hours;
        selectedHours     = hours;
        $('.mx-pricecard').each(function () {
            var h = parseInt($(this).data('hours'), 10);
            $(this).toggleClass('mx-selected', h === hours).toggleClass('mx-dimmed', h !== hours);
        });
        toggleHourControls(hours === 9 || hours === 18);
        if (fpInstance) fpInstance.redraw();
        if (selectedDate) renderTimeSlots(selectedDate);
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
       ② LIFT BUTTONS  (direct-booking mode only)
       In product mode this block still runs but the liftbar is hidden
       by the blade, so users never see or interact with it.
    ================================================================ */
    var LIFT_DATA = {
        four:    { img: 'assets/images/rentals/fourpost.jpg',       points: ['Heavy-duty four-post support','Perfect for long-hour jobs','Maximum stability & safety'] },
        two:     { img: 'assets/images/rentals/Media (6).jpg',      points: ['Quick vehicle access','Ideal for mechanical repairs','Compact and space efficient'] },
        scissor: { img: 'assets/images/rentals/scissor.jpg',        points: ['Low profile design','Fast lifting operation','Great for tire & brake work'] },
        flat:    { img: 'assets/images/rentals/motocycle.jpg',       points: ['Designed for motorcycles','Easy loading & unloading','Stable flat platform'] },
        flat2:   { img: 'assets/images/rentals/allignmentrack.jpg', points: ['Precision wheel alignment','Extended ramp length','Perfect for alignment jobs'] }
    };

    if (!PRODUCT_MODE) {
        /* Clear any accidentally baked-in active class */
        $('.mx-liftbtn').removeClass('active');

        $(document).on('click', '.mx-liftbtn', function () {
            $('.mx-liftbtn').removeClass('active');
            $(this).addClass('active');
            selectedLift = $(this).data('lift');

            /* Show lift image + bullet points */
            var lift = LIFT_DATA[selectedLift];
            if (lift) {
                $('#mxLiftPlaceholder').hide();
                $('#mxLiftPreviewImg').attr('src', lift.img).show();
                $('#mxLiftPoints').html(lift.points.map(function (p) { return '<li>' + p + '</li>'; }).join(''));
            }
            /* Hide the prompt banner */
            $('#mxLiftPrompt').addClass('hidden');

            /* Repaint calendar with this lift's slot data */
            if (fpInstance) fpInstance.redraw();

            /* Re-render time slots if already viewing the time grid */
            if (selectedDate && $('#mxTimeView').is(':visible')) renderTimeSlots(selectedDate);

            /* Invalidate selected date if it's no longer available for this lift */
            if (selectedDate && !isDateAvailableByPackage(selectedDate, selectedPackHours, selectedLift)) {
                selectedDate = null;
            }

            updateBookBtnState();
            setTimeout(function () { scrollToEl($('#hoursSection')[0]); }, 200);
        });

        $(document).on('click', '#mxLiftDropdownMenu .dropdown-item', function (e) {
            e.preventDefault();
            $('#mxLiftDropdownBtn').text($(this).text().trim());
            $('.mx-liftbtn[data-lift="' + $(this).data('lift') + '"]').trigger('click');
        });
    }
    /* In PRODUCT MODE: selectedLift is already set to AUTO_LIFT_KEY above — done. */

    /* ================================================================
       BOOK NOW → time-slot view
    ================================================================ */
    $('#openDayCalendar').on('click', function () {
        if (!selectedDate) return;
        if (!PRODUCT_MODE && !selectedLift) return;  // direct mode gate
         $(this).prop('disabled', true).removeClass('enabled');
        $('.mx-gridWrap, .mx-legendMini').hide();
        showTimeView();
        renderTimeSlots(selectedDate);
    });

    $('#openDayCalendarMb').on('click', function () {
        if (!selectedDate) return;
        if (!PRODUCT_MODE && !selectedLift) return;
        $('.mx-gridWrap, .mx-legendMini').hide();
        $(this).hide();
        showTimeView();
        renderTimeSlots(selectedDate);
    });

    $('#mxBackToDate').on('click', function () { showDateView(); $('#openDayCalendarMb').show(); });

    /* ================================================================
       CONTINUE → hours/confirmation modal
    ================================================================ */
    $('#mxContinueBtn').on('click', function () {
        if (!selectedDate || !selectedStartTime) return;
        selectedHours = selectedPackHours;
        var startH = parseInt(selectedStartTime.slice(0, 2), 10);
        var check  = validateConsecutiveCrossDay(selectedDate, selectedStartTime, selectedHours);

        $('#mxSlotText').text(prettyRange(selectedDate, startH, selectedHours));
        $('#mxSelectedHours').text(selectedHours);
        $('#mxTotalText').text(formatMoney(getPackageTotal(selectedHours)));
        $('#mxHintText').text(
            check.ok
                ? 'Continuous booking for ' + selectedHours + ' hour' + (selectedHours > 1 ? 's' : '') + '.'
                : check.message
        );
        $('#mxModalConfirm').prop('disabled', !check.ok).css('opacity', check.ok ? '1' : '.5');
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
            date: selectedDate, start: selectedStartTime,
            hours: selectedHours, total: getPackageTotal(selectedHours),
            lift: selectedLift, package: selectedPackHours, workstation: 1,
            product_id: PRODUCT_MODE ? ($meta.data('product-id') || null) : null
        };
    }

    /* ================================================================
       SLOT MODAL CONFIRM → auth → summary
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
        closeSlotModal();
        populateAndOpenSummary();
    });

    function mxContinueAfterAuth() {
        var raw = sessionStorage.getItem('mx_booking_payload');
        if (!raw) return;
        var p = JSON.parse(raw);
        selectedDate = p.date; selectedStartTime = p.start;
        selectedHours = p.hours; selectedPackHours = p.package; selectedLift = p.lift;

        /* Restore lift UI in direct mode */
        if (!PRODUCT_MODE) {
            $('.mx-liftbtn').removeClass('active');
            $('.mx-liftbtn[data-lift="' + selectedLift + '"]').addClass('active');
            var lift = LIFT_DATA[selectedLift];
            if (lift) {
                $('#mxLiftPlaceholder').hide();
                $('#mxLiftPreviewImg').attr('src', lift.img).show();
                $('#mxLiftPoints').html(lift.points.map(function (pt) { return '<li>' + pt + '</li>'; }).join(''));
            }
            $('#mxLiftPrompt').addClass('hidden');
        }
        updateBookBtnState();
        var inst = bootstrap.Modal.getInstance(document.getElementById('mxAuthModal'));
        if (inst) inst.hide();
        populateAndOpenSummary();
    }

    /* ================================================================
       SUMMARY
    ================================================================ */
    $('#mxSummaryClose').on('click', closeSummaryModal);
    $('#mxSummaryModal').on('click', function (e) { if ($(e.target).is('#mxSummaryModal')) closeSummaryModal(); });
    $('#mxSummaryBack').on('click', function () { closeSummaryModal(); openSlotModal(); });
    $('#mxSummaryPay').on('click', function () {
        closeSummaryModal();
        $('#mxCardNum, #mxCardExp, #mxCardCvv, #mxCardName').val('');
        $('#mxPayError').addClass('d-none').text('');
        $('#mxPayNowBtn').prop('disabled', false);
        $('#mxPaySpinner').addClass('d-none');
        $('#mxPayBtnText').html('Pay <span id="mxPayBtnAmt">' + $('#mxPayAmount').text() + '</span>');
        $('.mxs-pay-tab').removeClass('active').filter('[data-tab="card"]').addClass('active');
        $('.mxs-pay-panel').removeClass('active'); $('#mxPayPanel-card').addClass('active');
        openPayModal();
    });

    /* ================================================================
       PAY MODAL
    ================================================================ */
    $('#mxPayClose').on('click', closePayModal);
    $('#mxPayModal').on('click', function (e) { if ($(e.target).is('#mxPayModal')) closePayModal(); });

    $(document).on('click', '.mxs-pay-tab', function () {
        var tab = $(this).data('tab');
        $('.mxs-pay-tab').removeClass('active'); $(this).addClass('active');
        $('.mxs-pay-panel').removeClass('active'); $('#mxPayPanel-' + tab).addClass('active');
    });

    $('#mxCardNum').on('input', function () {
        var r = $(this).val().replace(/\D/g,'').slice(0,16);
        $(this).val(r.match(/.{1,4}/g) ? r.match(/.{1,4}/g).join(' ') : r);
    });
    $('#mxCardExp').on('input', function () {
        var v = $(this).val().replace(/\D/g,'').slice(0,4);
        if (v.length >= 3) v = v.slice(0,2) + ' / ' + v.slice(2);
        $(this).val(v);
    });

    /* ================================================================
       DEMO PAYMENT
    ================================================================ */
    function simulateDemoPayment(onSuccess) {
        var $btn = $('#mxPayNowBtn'), $sp = $('#mxPaySpinner'), $err = $('#mxPayError');
        if ($('.mxs-pay-tab.active').data('tab') === 'card') {
            if (!$('#mxCardNum').val().replace(/\s/g,'').match(/^\d{16}$/) ||
                !$('#mxCardExp').val().match(/\d{2}\s*\/\s*\d{2}/) ||
                !$('#mxCardCvv').val().match(/^\d{3}$/) ||
                !$('#mxCardName').val().trim()) {
                $err.text('Please fill in all card details correctly.').removeClass('d-none');
                return;
            }
        }
        $err.addClass('d-none');
        $btn.prop('disabled', true);
        $('#mxPayBtnText').text('Processing\u2026');
        $sp.removeClass('d-none');
        setTimeout(function () {
            $sp.addClass('d-none');
            $('#mxPayBtnText').text('\u2713 Payment Successful!');
            setTimeout(function () { closePayModal(); onSuccess(); }, 700);
        }, 1800);
    }

    $('#mxPayNowBtn').on('click', function () {
        simulateDemoPayment(function () {
            submitBooking(JSON.parse(sessionStorage.getItem('mx_booking_payload') || '{}'));
        });
    });

    /* ================================================================
       SUBMIT BOOKING
    ================================================================ */
    async function submitBooking(payload) {
        try {
            var res  = await fetch('/booking/confirm', {
                method: 'POST', credentials: 'same-origin',
                headers: { 'Content-Type':'application/json','X-CSRF-TOKEN':window.MX_CSRF,'Accept':'application/json' },
                body: JSON.stringify(payload)
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
       SUCCESS RECEIPT
    ================================================================ */
    function openSuccessReceipt(bookingId, payload) {
        var rate    = getRatePerHour();
        var total   = getPackageTotal(payload.hours);
        var startH  = parseInt(payload.start.slice(0, 2), 10);
        var dateFmt = new Date(payload.date + 'T00:00:00').toLocaleDateString([], {
            weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
        });
        $('#mxSuccessBookingId').text(bookingId);
        $('#mxrWorkstation').text('Workstation 1');
        $('#mxrLift').text(LIFT_LABELS[payload.lift] || getActiveLiftLabel());
        $('#mxrDate').text(dateFmt);
        $('#mxrStart').text(formatTimePoint(startH));
        $('#mxrDuration').text(payload.hours + ' hour' + (payload.hours > 1 ? 's' : ''));
        $('#mxrEnd').text(buildEndLabel(payload.date, payload.start, payload.hours));
        $('#mxrRate').text(formatMoney(rate) + ' / hr');
        $('#mxrTotal').text(formatMoney(total));
        openSuccessModal();
    }

    /* ================================================================
       PRINT
    ================================================================ */
    $('#mxPrintBtn').on('click', function () { window.print(); });

    /* ================================================================
       AUTH FORMS
    ================================================================ */
    $('#mxLoginForm').on('submit', async function (e) {
        e.preventDefault();
        var $err = $('#mxLoginErr').addClass('d-none').text('');
        try {
            var res  = await fetch('/login', {
                method:'POST', credentials:'same-origin',
                headers:{'Content-Type':'application/json','X-CSRF-TOKEN':window.MX_CSRF,'Accept':'application/json'},
                body: JSON.stringify({ email:$(this).find('[name=email]').val(), password:$(this).find('[name=password]').val() })
            });
            var data = await res.json().catch(function(){ return {}; });
            if (!res.ok) { $err.text(data.message||'Login failed.').removeClass('d-none'); return; }
            window.MX_IS_LOGGED_IN = true; mxContinueAfterAuth();
        } catch (_) { $err.text('Network error.').removeClass('d-none'); }
    });

    $('#mxRegisterForm').on('submit', async function (e) {
        e.preventDefault();
        var $err = $('#mxRegErr').addClass('d-none').text('');
        try {
            var res  = await fetch('/register', {
                method:'POST', credentials:'same-origin',
                headers:{'Content-Type':'application/json','X-CSRF-TOKEN':window.MX_CSRF,'Accept':'application/json'},
                body: JSON.stringify({
                    email:$(this).find('[name=email]').val(), mobile_no:$(this).find('[name=mobile_no]').val(),
                    password:$(this).find('[name=password]').val(), password_confirmation:$(this).find('[name=password_confirmation]').val()
                })
            });
            var data = await res.json().catch(function(){ return {}; });
            if (!res.ok) {
                $err.text(data.errors ? Object.values(data.errors).flat().join(' ') : (data.message||'Registration failed.')).removeClass('d-none');
                return;
            }
            window.MX_IS_LOGGED_IN = true; mxContinueAfterAuth();
        } catch (_) { $err.text('Network error.').removeClass('d-none'); }
    });

    /* ================================================================
       WORKSTATION TABS
    ================================================================ */
    $(document).on('click', '.mx-w-title', function () {
        $('.mx-w-title').removeClass('active'); $(this).addClass('active');
        loadCalendarData(null, parseInt($(this).data('ws'),10)||1);
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
    updateBookBtnState();  // Correct initial disabled/hint state for both modes

});