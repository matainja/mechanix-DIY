const TODAY = new Date();

let dayData = {};
let bookedSlots = {};

async function loadCalendarData(monthStr = null, workstation = 1) {
    const params = new URLSearchParams();
    if (monthStr) params.append("month", monthStr);
    params.append("workstation", workstation);

    const res = await fetch(`/booking/calendar-data?${params.toString()}`, {
        method: "GET",
        credentials: "same-origin",
        headers: { Accept: "application/json" },
    });

    const data = await res.json();
    dayData = data.dayData || {};
    bookedSlots = data.bookedSlots || {};

    // repaint calendar with updated dayData
    if (fpInstance) fpInstance.redraw();
}

// call once on page load
document.addEventListener("DOMContentLoaded", () => {
    loadCalendarData(null, 1);
});

// ===== Elements =====
const openBtn = document.getElementById("openDayCalendar");
const calendarWrap = document.getElementById("calendarWrap");
const gridWrap = document.querySelector(".mx-gridWrap");
const gridMini = document.querySelector(".mx-legendMini");

const timeView = document.getElementById("mxTimeView");
const timeGrid = document.getElementById("mxTimeGrid");
const backBtn = document.getElementById("mxBackToDate");
const selectedDateText = document.getElementById("mxSelectedDateText");

const pickedTimeText = document.getElementById("mxPickedTimeText");
const continueBtn = document.getElementById("mxContinueBtn");

// Modal
const modal = document.getElementById("mxSlotModal");
const modalClose = document.getElementById("mxModalClose");
const modalCancel = document.getElementById("mxModalCancel");
const modalConfirm = document.getElementById("mxModalConfirm");
const slotText = document.getElementById("mxSlotText");
const hintText = document.getElementById("mxHintText");

const hMinus = document.getElementById("mxHMinus");
const hPlus = document.getElementById("mxHPlus");
const selectedHoursEl = document.getElementById("mxSelectedHours");
const totalText = document.getElementById("mxTotalText");

const packRadios = () =>
    Array.from(document.querySelectorAll('input[name="mxPack"]'));

let selectedDate = null;
let selectedStartTime = null; // "HH:00"
let selectedHours = 1;

//Routes for login & R
const routeEl = document.getElementById("mx-routes");

const LOGIN_URL = routeEl?.dataset?.loginUrl;
const REGISTER_URL = routeEl?.dataset?.registerUrl;

// ===== Pricing =====
function getRatePerHour(hours) {
    if (hours >= 18) return 35;
    if (hours >= 9) return 40;
    return 45;
}

function formatMoney(n) {
    return `$${n.toFixed(0)}`;
}

// ===== Helpers =====
function pad2(n) {
    return String(n).padStart(2, "0");
}

function toggleHourControls(isLocked) {
    hMinus.disabled = isLocked;
    hPlus.disabled = isLocked;

    hMinus.classList.toggle("mx-disabled", isLocked);
    hPlus.classList.toggle("mx-disabled", isLocked);
}

function formatTimePoint(hour24) {
    const ampm = hour24 >= 12 ? "PM" : "AM";
    let h = hour24 % 12;
    if (h === 0) h = 12;
    return `${h}:00 ${ampm}`;
}

function addWorkingHours(startDateStr, startHour, hoursNeeded) {
    let remaining = hoursNeeded;
    let curDate = startDateStr;
    let curHour = startHour;

    while (remaining > 0) {
        const wh = getWorkingHours(curDate);
        if (!wh) {
            curDate = addDaysStr(curDate, 1);
            curHour = 0;
            continue;
        }

        if (curHour < wh.start) curHour = wh.start;
        if (curHour >= wh.end) {
            curDate = addDaysStr(curDate, 1);
            curHour = 0;
            continue;
        }

        const available = wh.end - curHour;

        if (remaining <= available) {
            curHour += remaining;
            remaining = 0;
        } else {
            remaining -= available;
            curDate = addDaysStr(curDate, 1);
            curHour = 0;
        }
    }

    return { endDate: curDate, endHour: curHour };
}

function prettyRange(dateStr, startHour, hoursNeeded) {
    const { endDate, endHour } = addWorkingHours(
        dateStr,
        startHour,
        hoursNeeded
    );

    const d1 = new Date(dateStr + "T00:00:00").toLocaleDateString([], {
        year: "numeric",
        month: "short",
        day: "2-digit",
    });

    const d2 = new Date(endDate + "T00:00:00").toLocaleDateString([], {
        year: "numeric",
        month: "short",
        day: "2-digit",
    });

    const t1 = formatTimePoint(startHour);
    const t2 = formatTimePoint(endHour);

    return d1 !== d2
        ? `${d1} • ${t1} → ${d2} • ${t2}`
        : `${d1} • ${t1} - ${t2}`;
}

function formatTimeLabel(hour24) {
    const ampm = hour24 >= 12 ? "PM" : "AM";
    let h = hour24 % 12;
    if (h === 0) h = 12;
    return `${h}:00 ${ampm}`;
}

function getWorkingHours(dateStr) {
    const d = new Date(dateStr + "T00:00:00");
    const day = d.getDay(); // 0 Sun ... 5 Fri ... 6 Sat
    if (day === 6) return null; // Saturday closed
    if (day === 5) return { start: 9, end: 12 }; // Friday
    return { start: 9, end: 18 }; // Sun-Thu
}

function openModal() {
    modal.classList.add("show");
    modal.setAttribute("aria-hidden", "false");
}
function closeModal() {
    modal.classList.remove("show");
    modal.setAttribute("aria-hidden", "true");
}

// ===== Consecutive validation =====
function isSlotBooked(dateStr, timeStr) {
    return (bookedSlots[dateStr] || []).includes(timeStr);
}

function getWorkingEndHour(dateStr) {
    const wh = getWorkingHours(dateStr);
    return wh ? wh.end : null;
}

function addDaysStr(dateStr, days) {
    const d = new Date(dateStr + "T00:00:00");
    d.setDate(d.getDate() + days);
    return `${d.getFullYear()}-${pad2(d.getMonth() + 1)}-${pad2(d.getDate())}`;
}

// checks if a single hour slot exists and is free on that date (09:00,10:00,...)
function isHourFree(dateStr, hour) {
    const wh = getWorkingHours(dateStr);
    if (!wh) return false;

    if (hour < wh.start || hour >= wh.end) return false; // hour slot must exist
    const t = `${pad2(hour)}:00`;
    return !isSlotBooked(dateStr, t);
}

/**
 *  Cross-day consecutive check:
 * Example:
 *  - 9h  : must fit in same day (Sun-Thu) from selected start
 *  - 18h : can spill to next day (and next month) automatically
 */
function validateConsecutiveCrossDay(startDateStr, startTimeStr, hoursNeeded) {
    const startH = parseInt(startTimeStr.slice(0, 2), 10);

    let remaining = hoursNeeded;
    let curDate = startDateStr;
    let curHour = startH;

    // first day: must start within working hours
    const wh0 = getWorkingHours(curDate);
    if (!wh0) return { ok: false, message: "Closed this day." };
    if (curHour < wh0.start || curHour >= wh0.end) {
        return { ok: false, message: "Start time not in working hours." };
    }

    while (remaining > 0) {
        const wh = getWorkingHours(curDate);
        if (!wh) return { ok: false, message: `Closed on ${curDate}.` };

        // if curHour is before start, snap to start (for next days)
        if (curHour < wh.start) curHour = wh.start;

        // if curHour reached end, jump to next day
        if (curHour >= wh.end) {
            curDate = addDaysStr(curDate, 1);
            curHour = 0;
            continue;
        }

        // hour must be free
        if (!isHourFree(curDate, curHour)) {
            return {
                ok: false,
                message: `Blocked at ${curDate} ${pad2(curHour)}:00`,
            };
        }

        // consume 1 hour
        remaining--;
        curHour++;

        // if day ended, move to next day
        if (curHour >= wh.end && remaining > 0) {
            curDate = addDaysStr(curDate, 1);
            curHour = 0;
        }
    }

    return { ok: true };
}

// ===== Render time slots =====
function renderTimeSlots(dateStr) {
    timeGrid.innerHTML = "";
    selectedDateText.textContent = dateStr;

    selectedStartTime = null;
    pickedTimeText.textContent = "None";
    continueBtn.disabled = true;

    const wh = getWorkingHours(dateStr);
    if (!wh) {
        timeGrid.innerHTML = `<div style="grid-column:1/-1;padding:14px;border:1px solid #eee;border-radius:12px;">
      Closed.
    </div>`;
        return;
    }

    //  Decide hours needed from selected pack
    const hoursNeeded = selectedPackHours; // 1 / 9 / 18

    //  If pack is 9 or 18 => show ONLY "9:00 AM" start
    if (hoursNeeded === 9 || hoursNeeded === 18) {
        const startValue = `${pad2(wh.start)}:00`; // always 09:00
        const check = validateConsecutiveCrossDay(
            dateStr,
            startValue,
            hoursNeeded
        );

        if (!check.ok) {
            timeGrid.innerHTML = `<div style="grid-column:1/-1;padding:14px;border:1px solid #eee;border-radius:12px;">
        Not available for <b>${hoursNeeded} hours</b> starting 9:00 AM.<br>
        <small>${check.message}</small>
      </div>`;
            continueBtn.disabled = true;
            return;
        }

        const btn = document.createElement("button");
        btn.type = "button";
        btn.className = "mx-slot available";
        btn.textContent = `Start at ${formatTimeLabel(
            wh.start
        )} (Full ${hoursNeeded}h)`;
        btn.dataset.value = startValue;

        btn.addEventListener("click", () => {
            document
                .querySelectorAll(".mx-slot")
                .forEach((b) => b.classList.remove("selected"));
            btn.classList.add("selected");

            selectedStartTime = startValue;
            pickedTimeText.textContent = formatTimeLabel(wh.start);
            continueBtn.disabled = false;
        });

        timeGrid.appendChild(btn);
        return;
    }

    //  Pack = 1h => show all hourly slots
    for (let h = wh.start; h < wh.end; h++) {
        const value = `${pad2(h)}:00`;
        const label = `${formatTimeLabel(h)} - ${formatTimeLabel(h + 1)}`;

        const btn = document.createElement("button");
        btn.type = "button";
        btn.className = "mx-slot available";
        btn.textContent = label;
        btn.dataset.value = value;

        if (isSlotBooked(dateStr, value)) {
            btn.className = "mx-slot disabled";
            btn.disabled = true;
            btn.textContent = `${label} (Booked)`;
        }

        btn.addEventListener("click", () => {
            document
                .querySelectorAll(".mx-slot")
                .forEach((b) => b.classList.remove("selected"));
            btn.classList.add("selected");

            selectedStartTime = value;
            pickedTimeText.textContent = label;
            continueBtn.disabled = false;
        });

        timeGrid.appendChild(btn);
    }
}

// ===== View switch =====
function showTimeView() {
    calendarWrap.style.display = "none";
    timeView.style.display = "block";
}
function showDateView() {
    timeView.style.display = "none";
    calendarWrap.style.display = "block";

    selectedStartTime = null;
    pickedTimeText.textContent = "None";
    continueBtn.disabled = true;
}

// ===== Modal update =====
function setHours(val) {
    selectedHours = Math.max(1, Math.min(val, 48));
    selectedHoursEl.textContent = selectedHours;

    const startH = parseInt(selectedStartTime.slice(0, 2), 10);

    //  Update modal "Selected" with full range (date + time)
    slotText.textContent = prettyRange(selectedDate, startH, selectedHours);

    console.log(selectedDate);

    const rate = getRatePerHour(selectedHours);
    totalText.textContent = formatMoney(rate * selectedHours);

    const check = validateConsecutiveCrossDay(
        selectedDate,
        selectedStartTime,
        selectedHours
    );

    hintText.textContent = check.ok
        ? `Continuous booking  for ${selectedHours} hour(s).`
        : check.message;

    modalConfirm.disabled = !check.ok;
    modalConfirm.style.opacity = check.ok ? "1" : ".6";
}

function applyPackage(packHours) {
    // pick package minimum hours
    setHours(packHours);
}

// radio change => sets base hours
packRadios().forEach((r) => {
    r.addEventListener("change", () => applyPackage(parseInt(r.value, 10)));
});

// plus/minus
hMinus.addEventListener("click", () => setHours(selectedHours - 1));
hPlus.addEventListener("click", () => setHours(selectedHours + 1));

let selectedPackHours = 1; // 1, 9, 18
let fpInstance = null; // flatpickr instance reference
const MIN_MONTH = new Date();

// ===== Flatpickr init =====
fpInstance = flatpickr("#bookingDate", {
    inline: true,
    dateFormat: "Y-m-d",
    disableMobile: true,
    defaultDate: TODAY,
    appendTo: calendarWrap,
    minDate: new Date(), //  user can’t navigate before this date

    onReady: function (selectedDates, dateStr, fp) {
        fpInstance = fp;

        //  disable/hide prev button if on current month
        updateMonthNav(fp);
    },

    onMonthChange: function (selectedDates, dateStr, fp) {
        updateMonthNav(fp);
        fp.redraw(); // repaint day colors

        const monthStr = `${fp.currentYear}-${String(
            fp.currentMonth + 1
        ).padStart(2, "0")}`;
        loadCalendarData(monthStr, 1); // workstation 1 for now
    },

    onYearChange: function (selectedDates, dateStr, fp) {
        updateMonthNav(fp);
        fp.redraw();
    },

    disable: [
        function (date) {
            // 1) Saturday disabled
            if (date.getDay() === 6) return true;

            // 2) Disable booked/unavailable from dayData
            const key = flatpickr.formatDate(date, "Y-m-d");
            const info = dayData[key];

            if (!info) return false;

            return info.status === "unavailable" || info.status === "booked";
        },
    ],

    onDayCreate: function (dObj, dStr, fp, dayElem) {
        // reset styles
        dayElem.classList.remove(
            "day-available",
            "day-unavailable",
            "day-booked"
        );

        if (dayElem.classList.contains("flatpickr-disabled")) {
            // If it’s disabled by your disable() logic, mark it visually
            const key = fp.formatDate(dayElem.dateObj, "Y-m-d");
            const info = dayData[key];

            if (info?.status === "booked") dayElem.classList.add("day-booked");
            else dayElem.classList.add("day-unavailable");

            return;
        }

        if (dayElem.classList.contains("nextMonthDay")) {
            dayElem.classList.add("day-nextmonth"); //  blue
            return; // optional: stop further status coloring
        }

        if (dayElem.classList.contains("prevMonthDay")) {
            dayElem.classList.add("day-prevmonth");
            return;
        }
        // If flatpickr disabled (Saturday)
        if (dayElem.classList.contains("flatpickr-disabled")) {
            dayElem.classList.add("day-unavailable");
            return;
        }

        const key = fp.formatDate(dayElem.dateObj, "Y-m-d");

        // If you still want your hard statuses to override:
        const info = dayData[key];
        if (info) {
            if (info.status === "booked") {
                dayElem.classList.add("day-booked");
                return;
            }
            if (info.status === "unavailable") {
                dayElem.classList.add("day-unavailable");
                return;
            }
        }

        //  MAIN: package-based availability
        const ok = isDateAvailableByPackage(key, selectedPackHours);
        dayElem.classList.add(ok ? "day-available" : "day-unavailable");
    },

    onChange: function (selectedDates, dateStr, fp) {
        // Block selecting unavailable day based on current package
        const ok = isDateAvailableByPackage(dateStr, selectedPackHours);

        if (!ok) {
            selectedDate = null;
            openBtn.textContent = "Pick a valid date";
            openBtn.disabled = true;
            openBtn.classList.remove("enabled");
            return;
        }

        selectedDate = dateStr || null;

        if (selectedDate) {
            openBtn.textContent = `Book for ${selectedDate}`;
            openBtn.disabled = false;
            openBtn.classList.add("enabled");
        } else {
            openBtn.textContent = "Pick a date";
            openBtn.disabled = true;
            openBtn.classList.remove("enabled");
        }
    },
});

function updateMonthNav(fp) {
    const cur = new Date(fp.currentYear, fp.currentMonth, 1);
    const min = new Date(MIN_MONTH.getFullYear(), MIN_MONTH.getMonth(), 1);

    const prevBtn = fp.calendarContainer.querySelector(".flatpickr-prev-month");
    if (!prevBtn) return;

    const isAtMinMonth = cur.getTime() <= min.getTime();

    // You can do either:
    prevBtn.style.pointerEvents = isAtMinMonth ? "none" : "auto";
    prevBtn.style.opacity = isAtMinMonth ? "0.3" : "1";
}

function getWorkingSlots(dateStr) {
    const wh = getWorkingHours(dateStr);
    if (!wh) return []; // closed day

    const slots = [];
    for (let h = wh.start; h < wh.end; h++) {
        slots.push(`${pad2(h)}:00`); // 09:00 ... 17:00
    }
    return slots;
}

function isDayFullyFree(dateStr) {
    const slots = getWorkingSlots(dateStr);
    if (!slots.length) return false; // closed

    return slots.every((t) => !isSlotBooked(dateStr, t));
}

function isDayHasAnyFreeHour(dateStr) {
    const slots = getWorkingSlots(dateStr);
    if (!slots.length) return false;

    return slots.some((t) => !isSlotBooked(dateStr, t));
}

/**
 *  Your rules:
 * 1h  => any free slot in that day
 * 9h  => full day free (ONLY works Sun-Thu because Fri has 3 slots)
 * 18h => date + next date must both be fully free (Sun-Thu + next day also full day)
 */
function isDateAvailableByPackage(dateStr, packHours) {
    const wh = getWorkingHours(dateStr);
    if (!wh) return false; // closed

    if (packHours === 1) {
        return isDayHasAnyFreeHour(dateStr);
    }

    if (packHours === 9) {
        // must be a 9-slot day (Sun-Thu 9-18)
        const slotsToday = getWorkingSlots(dateStr);
        if (slotsToday.length < 9) return false;
        return isDayFullyFree(dateStr);
    }

    if (packHours === 18) {
        // needs two consecutive FULL days free (today + next day)
        const slotsToday = getWorkingSlots(dateStr);
        if (slotsToday.length < 9) return false;

        const next = nextDate(dateStr, 1);
        const slotsNext = getWorkingSlots(next);
        if (slotsNext.length < 9) return false;

        return isDayFullyFree(dateStr) && isDayFullyFree(next);
    }

    return false;
}

function nextDate(dateStr, days = 1) {
    const d = new Date(dateStr + "T00:00:00");
    d.setDate(d.getDate() + days); // auto moves to next month/year
    return `${d.getFullYear()}-${pad2(d.getMonth() + 1)}-${pad2(d.getDate())}`;
}

continueBtn.addEventListener("click", () => {
    if (!selectedDate || !selectedStartTime) return;

    openModal();

    //  lock modal hours to selected package (1 / 9 / 18)
    selectedHours = selectedPackHours;
    //  update modal UI using the chosen start time + chosen package
    setHours(selectedHours);
});

// packRadios().forEach(r => {
//   r.addEventListener("change", () => {
//     selectedPackHours = parseInt(r.value, 10) || 1;

//     setHours(selectedPackHours);

//     if (fpInstance) fpInstance.redraw();
//     if (selectedDate) renderTimeSlots(selectedDate);
//   });
// });

// ===== Book date => open time grid =====
openBtn.addEventListener("click", () => {
    if (!selectedDate) return;
    gridWrap.style.display = "none";
    gridMini.style.display = "none";

    showTimeView();
    renderTimeSlots(selectedDate);
});

// Back to calendar
backBtn.addEventListener("click", showDateView);

// Modal close handlers
modalClose.addEventListener("click", closeModal);
modalCancel.addEventListener("click", closeModal);

modal.addEventListener("click", (e) => {
    if (e.target === modal) closeModal();
});

document.addEventListener("keydown", (e) => {
    if (e.key === "Escape" && modal.classList.contains("show")) closeModal();
});

// Confirm booking
async function submitBooking(payload) {
    const res = await fetch("/booking/confirm", {
        method: "POST",
        credentials: "same-origin",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": window.MX_CSRF,
            Accept: "application/json",
        },
        body: JSON.stringify(payload),
    });

    const data = await res.json().catch(() => ({}));

    if (!res.ok || !data.status) {
        alert(data.message || "Booking failed");
        return;
    }

    alert("Booking confirmed! ID: " + data.booking_id);

    setTimeout(() => {
        window.location.href = "/";
    }, 2000);
}

const LIFT_DATA = {
    four: {
        img: "assets/images/rentals/four-post.png",
        points: [
            "Heavy-duty four-post support",
            "Perfect for long-hour jobs",
            "Maximum stability & safety",
        ],
    },
    two: {
        img: "assets/images/rentals/two-post.png",
        points: [
            "Quick vehicle access",
            "Ideal for mechanical repairs",
            "Compact and space efficient",
        ],
    },
    scissor: {
        img: "assets/images/rentals/scissor.png",
        points: [
            "Low profile design",
            "Fast lifting operation",
            "Great for tire & brake work",
        ],
    },
    flat: {
        img: "assets/images/rentals/moto-lift.png",
        points: [
            "Designed for motorcycles",
            "Easy loading & unloading",
            "Stable flat platform",
        ],
    },
    flat2: {
        img: "assets/images/rentals/alignment-rack.png",
        points: [
            "Precision wheel alignment",
            "Extended ramp length",
            "Perfect for alignment jobs",
        ],
    },
};
document.querySelectorAll(".mx-liftbtn").forEach((btn) => {
    btn.addEventListener("click", () => {
        // active state
        document
            .querySelectorAll(".mx-liftbtn")
            .forEach((b) => b.classList.remove("active"));
        btn.classList.add("active");

        const liftKey = btn.dataset.lift;
        const lift = LIFT_DATA[liftKey];
        if (!lift) return;

        // update image
        document.getElementById("mxLiftPreviewImg").src = lift.img;

        // update bullet points
        const ul = document.getElementById("mxLiftPoints");
        ul.innerHTML = lift.points.map((p) => `<li>${p}</li>`).join("");
    });
});

document.querySelectorAll(".mx-pricecard").forEach((card) => {
    card.addEventListener("click", () => {
        // UI state
        document.querySelectorAll(".mx-pricecard").forEach((c) => {
            c.classList.remove("mx-selected");
            c.classList.add("mx-dimmed");
        });

        card.classList.add("mx-selected");
        card.classList.remove("mx-dimmed");

        //  set package hours
        selectedPackHours = parseInt(card.dataset.hours, 10) || 1;

        //  LOCK / UNLOCK +/- buttons
        toggleHourControls(selectedPackHours === 9 || selectedPackHours === 18);

        //  repaint calendar availability colors
        if (fpInstance) fpInstance.redraw();

        //  IMPORTANT: update time grid if date already chosen
        if (selectedDate) {
            renderTimeSlots(selectedDate);
        }
    });
});

function selectPackage(hours) {
    selectedPackHours = hours;

    // UI active state
    document.querySelectorAll(".mx-pricecard").forEach((c) => {
        const h = parseInt(c.dataset.hours, 10);
        const isActive = h === hours;
        c.classList.toggle("mx-selected", isActive);
        c.classList.toggle("mx-dimmed", !isActive);
    });

    // lock/unlock +/- buttons
    toggleHourControls(hours === 9 || hours === 18);

    // repaint calendar + update time grid if already opened
    if (fpInstance) fpInstance.redraw();
    if (selectedDate) renderTimeSlots(selectedDate);
}

//  default = 1 hour
window.addEventListener("DOMContentLoaded", () => {
    selectPackage(1);
});

//Login & Registration pop up part

const authStateEl = document.getElementById("mx-auth-state");
window.MX_IS_LOGGED_IN = authStateEl?.dataset?.loggedIn === "1";
window.MX_CSRF = document
    .querySelector('meta[name="csrf-token"]')
    .getAttribute("content");

function mxShowErr(el, msg) {
    el.classList.remove("d-none");
    el.textContent = msg;
}

function mxHideErr(el) {
    el.classList.add("d-none");
    el.textContent = "";
}

function mxGetBookingPayload() {
    return {
        date: selectedDate,
        start: selectedStartTime,
        hours: selectedHours,
        total: getRatePerHour(selectedHours) * selectedHours,
        lift: document.querySelector(".mx-liftbtn.active")?.dataset.lift,
        package: selectedPackHours,
        workstation: 1,
    };
}

// call your existing booking submit method here
function mxContinueBookingAfterAuth() {
    const payload = JSON.parse(
        sessionStorage.getItem("mx_booking_payload") || "null"
    );
    if (!payload) return;

    submitBooking(payload);

    sessionStorage.removeItem("mx_booking_payload");
}

document.addEventListener("DOMContentLoaded", function () {
    const confirmBtn = document.getElementById("mxModalConfirm");
    const authModalEl = document.getElementById("mxAuthModal");
    const authModal = new bootstrap.Modal(authModalEl);

    const loginForm = document.getElementById("mxLoginForm");
    const registerForm = document.getElementById("mxRegisterForm");

    const loginErr = document.getElementById("mxLoginErr");
    const regErr = document.getElementById("mxRegErr");

    // 1) Intercept confirm booking
    confirmBtn?.addEventListener("click", function () {
        const check = validateConsecutiveCrossDay(
            selectedDate,
            selectedStartTime,
            selectedHours
        );
        if (!check.ok) return alert(check.message);

        const payload = mxGetBookingPayload();
        sessionStorage.setItem("mx_booking_payload", JSON.stringify(payload));

        if (window.MX_IS_LOGGED_IN) {
            mxContinueBookingAfterAuth();
            return;
        }

        closeModal();

        authModal.show();
    });

    // 2) Login submit (AJAX)
    loginForm?.addEventListener("submit", async function (e) {
        e.preventDefault();
        mxHideErr(loginErr);

        const fd = new FormData(loginForm);

        const res = await fetch(LOGIN_URL, {
            method: "POST",
            credentials: "same-origin",
            headers: {
                "X-CSRF-TOKEN": window.MX_CSRF,
                Accept: "application/json",
            },
            body: fd,
        });

        const data = await res.json().catch(() => ({}));

        if (!res.ok) {
            mxShowErr(loginErr, data.message || "Login failed.");
            return;
        }

        window.MX_IS_LOGGED_IN = true;
        if (data.csrf) {
            window.MX_CSRF = data.csrf;

            const meta = document.querySelector('meta[name="csrf-token"]');
            if (meta) meta.setAttribute("content", data.csrf);
        }

        authModal.hide();
        mxContinueBookingAfterAuth();
    });

    // 3) Register submit (AJAX)
    registerForm?.addEventListener("submit", async function (e) {
        e.preventDefault();
        mxHideErr(regErr);

        const fd = new FormData(registerForm);

        const res = await fetch(REGISTER_URL, {
            method: "POST",
            credentials: "same-origin",
            headers: {
                "X-CSRF-TOKEN": window.MX_CSRF,
                Accept: "application/json",
            },
            body: fd,
        });

        const data = await res.json().catch(() => ({}));

        if (!res.ok) {
            // show first validation message if exists
            let msg = data.message || "Register failed.";
            if (data.errors) {
                const firstKey = Object.keys(data.errors)[0];
                if (firstKey) msg = data.errors[firstKey][0];
            }
            mxShowErr(regErr, msg);
            return;
        }

        window.MX_IS_LOGGED_IN = true;
        authModal.hide();
        mxContinueBookingAfterAuth();
    });
});
