// const TODAY = new Date();

// let dayData = {};
// let bookedSlots = {};

// async function loadCalendarData(monthStr = null, workstation = 1) {
//     const params = new URLSearchParams();
//     if (monthStr) params.append("month", monthStr);
//     params.append("workstation", workstation);

//     const res = await fetch(`/booking/calendar-data?${params.toString()}`, {
//         method: "GET",
//         credentials: "same-origin",
//         headers: { Accept: "application/json" },
//     });

//     const data = await res.json();
//     dayData = data.dayData || {};
//     bookedSlots = data.bookedSlots || {};

//     // repaint calendar with updated dayData
//     if (fpInstance) fpInstance.redraw();
// }

// // call once on page load
// document.addEventListener("DOMContentLoaded", () => {
//     loadCalendarData(null, 1);
// });

// // ===== Elements =====
// const openBtn = document.getElementById("openDayCalendar");
// const openBtnMb = document.getElementById("openDayCalendarMb");
// const calendarWrap = document.getElementById("calendarWrap");
// const gridWrap = document.querySelector(".mx-gridWrap");
// const gridMini = document.querySelector(".mx-legendMini");

// const timeView = document.getElementById("mxTimeView");
// const timeGrid = document.getElementById("mxTimeGrid");
// const backBtn = document.getElementById("mxBackToDate");
// const selectedDateText = document.getElementById("mxSelectedDateText");

// const pickedTimeText = document.getElementById("mxPickedTimeText");
// const continueBtn = document.getElementById("mxContinueBtn");

// // Modal
// const modal = document.getElementById("mxSlotModal");
// const modalClose = document.getElementById("mxModalClose");
// const modalCancel = document.getElementById("mxModalCancel");
// const modalConfirm = document.getElementById("mxModalConfirm");
// const slotText = document.getElementById("mxSlotText");
// const hintText = document.getElementById("mxHintText");

// const hMinus = document.getElementById("mxHMinus");
// const hPlus = document.getElementById("mxHPlus");
// const selectedHoursEl = document.getElementById("mxSelectedHours");
// const totalText = document.getElementById("mxTotalText");

// const packRadios = () =>
//     Array.from(document.querySelectorAll('input[name="mxPack"]'));

// let selectedDate = null;
// let selectedStartTime = null; // "HH:00"
// let selectedHours = 1;



// // ===== Pricing =====
// function getRatePerHour(hours) {
// const selectedCard = document.querySelector('.mx-selected');

// const price = Number(selectedCard.dataset.price);
// const total = Number(selectedCard.dataset.total);
//     console.log("Selected hours:", hours, "Price:", price, "Total:", total);

//     return price; // or return total / hours for dynamic calculation
// }

// function formatMoney(n) {
//     return `$${n.toFixed(0)}`;
// }

// // ===== Helpers =====
// function pad2(n) {
//     return String(n).padStart(2, "0");
// }

// function toggleHourControls(isLocked) {
//     hMinus.disabled = isLocked;
//     hPlus.disabled = isLocked;

//     hMinus.classList.toggle("mx-disabled", isLocked);
//     hPlus.classList.toggle("mx-disabled", isLocked);
// }

// function formatTimePoint(hour24) {
//     const ampm = hour24 >= 12 ? "PM" : "AM";
//     let h = hour24 % 12;
//     if (h === 0) h = 12;
//     return `${h}:00 ${ampm}`;
// }

// function addWorkingHours(startDateStr, startHour, hoursNeeded) {
//     let remaining = hoursNeeded;
//     let curDate = startDateStr;
//     let curHour = startHour;

//     while (remaining > 0) {
//         const wh = getWorkingHours(curDate);
//         if (!wh) {
//             curDate = addDaysStr(curDate, 1);
//             curHour = 0;
//             continue;
//         }

//         if (curHour < wh.start) curHour = wh.start;
//         if (curHour >= wh.end) {
//             curDate = addDaysStr(curDate, 1);
//             curHour = 0;
//             continue;
//         }

//         const available = wh.end - curHour;

//         if (remaining <= available) {
//             curHour += remaining;
//             remaining = 0;
//         } else {
//             remaining -= available;
//             curDate = addDaysStr(curDate, 1);
//             curHour = 0;
//         }
//     }

//     return { endDate: curDate, endHour: curHour };
// }

// function prettyRange(dateStr, startHour, hoursNeeded) {
//     const { endDate, endHour } = addWorkingHours(
//         dateStr,
//         startHour,
//         hoursNeeded
//     );

//     const d1 = new Date(dateStr + "T00:00:00").toLocaleDateString([], {
//         year: "numeric",
//         month: "short",
//         day: "2-digit",
//     });

//     const d2 = new Date(endDate + "T00:00:00").toLocaleDateString([], {
//         year: "numeric",
//         month: "short",
//         day: "2-digit",
//     });

//     const t1 = formatTimePoint(startHour);
//     const t2 = formatTimePoint(endHour);

//     return d1 !== d2
//         ? `${d1} • ${t1} → ${d2} • ${t2}`
//         : `${d1} • ${t1} - ${t2}`;
// }

// function formatTimeLabel(hour24) {
//     const ampm = hour24 >= 12 ? "PM" : "AM";
//     let h = hour24 % 12;
//     if (h === 0) h = 12;
//     return `${h}:00 ${ampm}`;
// }

// function getWorkingHours(dateStr) {
//     const d = new Date(dateStr + "T00:00:00");
//     const day = d.getDay(); // 0 Sun ... 5 Fri ... 6 Sat
//     if (day === 6) return null; // Saturday closed
//     if (day === 5) return { start: 9, end: 12 }; // Friday
//     return { start: 9, end: 18 }; // Sun-Thu
// }

// function openModal() {
//     modal.classList.add("show");
//     modal.setAttribute("aria-hidden", "false");
// }
// function closeModal() {
//     modal.classList.remove("show");
//     modal.setAttribute("aria-hidden", "true");
// }

// // ===== Consecutive validation =====
// function isSlotBooked(dateStr, timeStr) {
//     return (bookedSlots[dateStr] || []).includes(timeStr);
// }

// function getWorkingEndHour(dateStr) {
//     const wh = getWorkingHours(dateStr);
//     return wh ? wh.end : null;
// }

// function addDaysStr(dateStr, days) {
//     const d = new Date(dateStr + "T00:00:00");
//     d.setDate(d.getDate() + days);
//     return `${d.getFullYear()}-${pad2(d.getMonth() + 1)}-${pad2(d.getDate())}`;
// }

// // checks if a single hour slot exists and is free on that date (09:00,10:00,...)
// function isHourFree(dateStr, hour) {
//     const wh = getWorkingHours(dateStr);
//     if (!wh) return false;

//     if (hour < wh.start || hour >= wh.end) return false; // hour slot must exist
//     const t = `${pad2(hour)}:00`;
//     return !isSlotBooked(dateStr, t);
// }

// /**
//  *  Cross-day consecutive check:
//  * Example:
//  *  - 9h  : must fit in same day (Sun-Thu) from selected start
//  *  - 18h : can spill to next day (and next month) automatically
//  */
// function validateConsecutiveCrossDay(startDateStr, startTimeStr, hoursNeeded) {
//     const startH = parseInt(startTimeStr.slice(0, 2), 10);

//     let remaining = hoursNeeded;
//     let curDate = startDateStr;
//     let curHour = startH;

//     // first day: must start within working hours
//     const wh0 = getWorkingHours(curDate);
//     if (!wh0) return { ok: false, message: "Closed this day." };
//     if (curHour < wh0.start || curHour >= wh0.end) {
//         return { ok: false, message: "Start time not in working hours." };
//     }

//     while (remaining > 0) {
//         const wh = getWorkingHours(curDate);
//         if (!wh) return { ok: false, message: `Closed on ${curDate}.` };

//         // if curHour is before start, snap to start (for next days)
//         if (curHour < wh.start) curHour = wh.start;

//         // if curHour reached end, jump to next day
//         if (curHour >= wh.end) {
//             curDate = addDaysStr(curDate, 1);
//             curHour = 0;
//             continue;
//         }

//         // hour must be free
//         if (!isHourFree(curDate, curHour)) {
//             return {
//                 ok: false,
//                 message: `Blocked at ${curDate} ${pad2(curHour)}:00`,
//             };
//         }

//         // consume 1 hour
//         remaining--;
//         curHour++;

//         // if day ended, move to next day
//         if (curHour >= wh.end && remaining > 0) {
//             curDate = addDaysStr(curDate, 1);
//             curHour = 0;
//         }
//     }

//     return { ok: true };
// }

// // ===== Render time slots =====
// function renderTimeSlots(dateStr) {
//     timeGrid.innerHTML = "";
//     selectedDateText.textContent = dateStr;

//     selectedStartTime = null;
//     pickedTimeText.textContent = "None";
//     continueBtn.disabled = true;

//     const wh = getWorkingHours(dateStr);
//     if (!wh) {
//         timeGrid.innerHTML = `<div style="grid-column:1/-1;padding:14px;border:1px solid #eee;border-radius:12px;">
//       Closed.
//     </div>`;
//         return;
//     }

//     //  Decide hours needed from selected pack
//     const hoursNeeded = selectedPackHours; // 1 / 9 / 18

//     //  If pack is 9 or 18 => show ONLY "9:00 AM" start
//     if (hoursNeeded === 9 || hoursNeeded === 18) {
//         const startValue = `${pad2(wh.start)}:00`; // always 09:00
//         const check = validateConsecutiveCrossDay(
//             dateStr,
//             startValue,
//             hoursNeeded
//         );

//         if (!check.ok) {
//             timeGrid.innerHTML = `<div style="grid-column:1/-1;padding:14px;border:1px solid #eee;border-radius:12px;color:#000;">
//         Not available for <b>${hoursNeeded} hours</b> starting 9:00 AM.<br>
//         <small>${check.message}</small>
//       </div>`;
//             continueBtn.disabled = true;
//             return;
//         }

//         const btn = document.createElement("button");
//         btn.type = "button";
//         btn.className = "mx-slot available";
//         btn.textContent = `Start at ${formatTimeLabel(
//             wh.start
//         )} (Full ${hoursNeeded}h)`;
//         btn.dataset.value = startValue;

//         btn.addEventListener("click", () => {
//             document
//                 .querySelectorAll(".mx-slot")
//                 .forEach((b) => b.classList.remove("selected"));
//             btn.classList.add("selected");

//             selectedStartTime = startValue;
//             pickedTimeText.textContent = formatTimeLabel(wh.start);
//             continueBtn.disabled = false;
//         });

//         timeGrid.appendChild(btn);
//         return;
//     }

//     //  Pack = 1h => show all hourly slots
//     for (let h = wh.start; h < wh.end; h++) {
//         const value = `${pad2(h)}:00`;
//         const label = `${formatTimeLabel(h)} - ${formatTimeLabel(h + 1)}`;

//         const btn = document.createElement("button");
//         btn.type = "button";
//         btn.className = "mx-slot available";
//         btn.textContent = label;
//         btn.dataset.value = value;

//         if (isSlotBooked(dateStr, value)) {
//             btn.className = "mx-slot disabled";
//             btn.disabled = true;
//             btn.textContent = `${label} (Booked)`;
//         }

//         btn.addEventListener("click", () => {
//             document
//                 .querySelectorAll(".mx-slot")
//                 .forEach((b) => b.classList.remove("selected"));
//             btn.classList.add("selected");

//             selectedStartTime = value;
//             pickedTimeText.textContent = label;
//             continueBtn.disabled = false;
//         });

//         timeGrid.appendChild(btn);
//     }
// }

// // ===== View switch =====
// function showTimeView() {
//     calendarWrap.style.display = "none";
//     timeView.style.display = "block";
// }
// function showDateView() {
//     timeView.style.display = "none";
//     calendarWrap.style.display = "block";

//     selectedStartTime = null;
//     pickedTimeText.textContent = "None";
//     continueBtn.disabled = true;
// }

// // ===== Modal update =====
// function setHours(val) {
//     selectedHours = Math.max(1, Math.min(val, 48));
//     selectedHoursEl.textContent = selectedHours;

//     const startH = parseInt(selectedStartTime.slice(0, 2), 10);

//     //  Update modal "Selected" with full range (date + time)
//     slotText.textContent = prettyRange(selectedDate, startH, selectedHours);

//     console.log(selectedDate,'test') ;

//     const rate = getRatePerHour(selectedHours);
//     totalText.textContent = formatMoney(rate * selectedHours);

//     const check = validateConsecutiveCrossDay(
//         selectedDate,
//         selectedStartTime,
//         selectedHours
//     );

//     hintText.textContent = check.ok
//         ? `Continuous booking  for ${selectedHours} hour(s).`
//         : check.message;

//     modalConfirm.disabled = !check.ok;
//     modalConfirm.style.opacity = check.ok ? "1" : ".6";
// }

// function applyPackage(packHours) {
//     // pick package minimum hours
//     setHours(packHours);
// }

// // radio change => sets base hours
// packRadios().forEach((r) => {
//     r.addEventListener("change", () => applyPackage(parseInt(r.value, 10)));
// });

// // plus/minus
// hMinus.addEventListener("click", () => setHours(selectedHours - 1));
// hPlus.addEventListener("click", () => setHours(selectedHours + 1));

// let selectedPackHours = 1; // 1, 9, 18
// let fpInstance = null; // flatpickr instance reference
// const MIN_MONTH = new Date();

// // ===== Flatpickr init =====
// fpInstance = flatpickr("#bookingDate", {
//     inline: true,
//     dateFormat: "Y-m-d",
//     disableMobile: true,
//     defaultDate: TODAY,
//     appendTo: calendarWrap,
//     minDate: new Date(), //  user can’t navigate before this date

//     onReady: function (selectedDates, dateStr, fp) {
//         fpInstance = fp;

//         //  disable/hide prev button if on current month
//         updateMonthNav(fp);
//     },

//     onMonthChange: function (selectedDates, dateStr, fp) {
//         updateMonthNav(fp);
//         fp.redraw(); // repaint day colors

//         const monthStr = `${fp.currentYear}-${String(
//             fp.currentMonth + 1
//         ).padStart(2, "0")}`;
//         loadCalendarData(monthStr, 1); // workstation 1 for now
//     },

//     onYearChange: function (selectedDates, dateStr, fp) {
//         updateMonthNav(fp);
//         fp.redraw();
//     },

//     disable: [
//         function (date) {
//             // 1) Saturday disabled
//             if (date.getDay() === 6) return true;

//             // 2) Disable booked/unavailable from dayData
//             const key = flatpickr.formatDate(date, "Y-m-d");
//             const info = dayData[key];

//             if (!info) return false;

//             return info.status === "unavailable" || info.status === "booked";
//         },
//     ],

//     onDayCreate: function (dObj, dStr, fp, dayElem) {
//         // reset styles
//         dayElem.classList.remove(
//             "day-available",
//             "day-unavailable",
//             "day-booked"
//         );

//         if (dayElem.classList.contains("flatpickr-disabled")) {
//             // If it’s disabled by your disable() logic, mark it visually
//             const key = fp.formatDate(dayElem.dateObj, "Y-m-d");
//             const info = dayData[key];

//             if (info?.status === "booked") dayElem.classList.add("day-booked");
//             else dayElem.classList.add("day-unavailable");

//             return;
//         }

//         if (dayElem.classList.contains("nextMonthDay")) {
//             dayElem.classList.add("day-nextmonth"); //  blue
//             return; // optional: stop further status coloring
//         }

//         if (dayElem.classList.contains("prevMonthDay")) {
//             dayElem.classList.add("day-prevmonth");
//             return;
//         }
//         // If flatpickr disabled (Saturday)
//         if (dayElem.classList.contains("flatpickr-disabled")) {
//             dayElem.classList.add("day-unavailable");
//             return;
//         }

//         const key = fp.formatDate(dayElem.dateObj, "Y-m-d");

//         // If you still want your hard statuses to override:
//         const info = dayData[key];
//         if (info) {
//             if (info.status === "booked") {
//                 dayElem.classList.add("day-booked");
//                 return;
//             }
//             if (info.status === "unavailable") {
//                 dayElem.classList.add("day-unavailable");
//                 return;
//             }
//         }

//         //  MAIN: package-based availability
//         const ok = isDateAvailableByPackage(key, selectedPackHours);
//         dayElem.classList.add(ok ? "day-available" : "day-unavailable");
//     },

//     onChange: function (selectedDates, dateStr, fp) {
//         const ok = isDateAvailableByPackage(dateStr, selectedPackHours);
//         // console.log("date valid?", ok);

//         if (!ok) {
//             selectedDate = null;

//             // TOP button
//             openBtn.textContent = "Pick a valid date";
//             openBtn.disabled = true;
//             openBtn.classList.remove("enabled");

//             // MOBILE button
//             openBtnMb.textContent = "Pick a valid date";
//             openBtnMb.disabled = true;
//             openBtnMb.classList.remove("enabled");

//             return;
//         }

//         selectedDate = dateStr || null;

//         if (selectedDate) {
//             // TOP button
//             openBtn.textContent = `Book for ${selectedDate}`;
//             openBtn.disabled = false;
//             openBtn.classList.add("enabled");

//             // MOBILE button
//             openBtnMb.textContent = `Book for ${selectedDate}`;
//             openBtnMb.disabled = false;
//             openBtnMb.classList.add("enabled");
//         } else {
//             // TOP button
//             openBtn.textContent = "Pick a date";
//             openBtn.disabled = true;
//             openBtn.classList.remove("enabled");

//             // MOBILE button
//             openBtnMb.textContent = "Pick a date";
//             openBtnMb.disabled = true;
//             openBtnMb.classList.remove("enabled");
//         }
//     },
// });

// function updateMonthNav(fp) {
//     const cur = new Date(fp.currentYear, fp.currentMonth, 1);
//     const min = new Date(MIN_MONTH.getFullYear(), MIN_MONTH.getMonth(), 1);

//     const prevBtn = fp.calendarContainer.querySelector(".flatpickr-prev-month");
//     if (!prevBtn) return;

//     const isAtMinMonth = cur.getTime() <= min.getTime();

//     // You can do either:
//     prevBtn.style.pointerEvents = isAtMinMonth ? "none" : "auto";
//     prevBtn.style.opacity = isAtMinMonth ? "0.3" : "1";
// }

// function getWorkingSlots(dateStr) {
//     const wh = getWorkingHours(dateStr);
//     if (!wh) return []; // closed day

//     const slots = [];
//     for (let h = wh.start; h < wh.end; h++) {
//         slots.push(`${pad2(h)}:00`); // 09:00 ... 17:00
//     }
//     return slots;
// }

// function isDayFullyFree(dateStr) {
//     const slots = getWorkingSlots(dateStr);
//     if (!slots.length) return false; // closed

//     return slots.every((t) => !isSlotBooked(dateStr, t));
// }

// function isDayHasAnyFreeHour(dateStr) {
//     const slots = getWorkingSlots(dateStr);
//     if (!slots.length) return false;

//     return slots.some((t) => !isSlotBooked(dateStr, t));
// }

// /**
//  *  Your rules:
//  * 1h  => any free slot in that day
//  * 9h  => full day free (ONLY works Sun-Thu because Fri has 3 slots)
//  * 18h => date + next date must both be fully free (Sun-Thu + next day also full day)
//  */
// function isDateAvailableByPackage(dateStr, packHours) {
//     const wh = getWorkingHours(dateStr);
//     if (!wh) return false; // closed

//     if (packHours === 1) {
//         return isDayHasAnyFreeHour(dateStr);
//     }

//     if (packHours === 9) {
//         // must be a 9-slot day (Sun-Thu 9-18)
//         const slotsToday = getWorkingSlots(dateStr);
//         if (slotsToday.length < 9) return false;
//         return isDayFullyFree(dateStr);
//     }

//     if (packHours === 18) {
//         // needs two consecutive FULL days free (today + next day)
//         const slotsToday = getWorkingSlots(dateStr);
//         if (slotsToday.length < 9) return false;

//         const next = nextDate(dateStr, 1);
//         const slotsNext = getWorkingSlots(next);
//         if (slotsNext.length < 9) return false;

//         return isDayFullyFree(dateStr) && isDayFullyFree(next);
//     }

//     return false;
// }

// function nextDate(dateStr, days = 1) {
//     const d = new Date(dateStr + "T00:00:00");
//     d.setDate(d.getDate() + days); // auto moves to next month/year
//     return `${d.getFullYear()}-${pad2(d.getMonth() + 1)}-${pad2(d.getDate())}`;
// }

// continueBtn.addEventListener("click", () => {
//     if (!selectedDate || !selectedStartTime) return;

//     openModal();
//     total= getRatePerHour(selectedHours) ;
//     console.log("Continue clicked. Selected hours:", selectedHours, "Rate per hour:", getRatePerHour(selectedHours), "Total:", total);

// totalText.textContent = `$${total}`;
//     //  lock modal hours to selected package (1 / 9 / 18)
//     // selectedHours = selectedPackHours;
//     // //  update modal UI using the chosen start time + chosen package
//     // setHours(selectedHours);
// });

// // packRadios().forEach(r => {
// //   r.addEventListener("change", () => {
// //     selectedPackHours = parseInt(r.value, 10) || 1;

// //     setHours(selectedPackHours);

// //     if (fpInstance) fpInstance.redraw();
// //     if (selectedDate) renderTimeSlots(selectedDate);
// //   });
// // });

// // ===== Book date => open time grid =====
// openBtn.addEventListener("click", () => {
//     if (!selectedDate) return;
//     gridWrap.style.display = "none";
//     gridMini.style.display = "none";

//     showTimeView();
//     renderTimeSlots(selectedDate);
// });
// openBtnMb.addEventListener("click", () => {
//     if (!selectedDate) return;
//     gridWrap.style.display = "none";
//     gridMini.style.display = "none";
//     openBtnMb.style.display = "none";

//     showTimeView();
//     renderTimeSlots(selectedDate);
// });

// // Back to calendar
// backBtn.addEventListener("click", () => {
//     showDateView();
//     openBtnMb.style.display = "block";
    
// });

// // Modal close handlers
// modalClose.addEventListener("click", closeModal);
// modalCancel.addEventListener("click", closeModal);

// modal.addEventListener("click", (e) => {
//     if (e.target === modal) closeModal();
// });

// document.addEventListener("keydown", (e) => {
//     if (e.key === "Escape" && modal.classList.contains("show")) closeModal();
// });

// // Confirm booking
// async function submitBooking(payload) {
//     const res = await fetch("/booking/confirm", {
//         method: "POST",
//         credentials: "same-origin",
//         headers: {
//             "Content-Type": "application/json",
//             "X-CSRF-TOKEN": window.MX_CSRF,
//             Accept: "application/json",
//         },
//         body: JSON.stringify(payload),
//     });

//     const data = await res.json().catch(() => ({}));

//     if (!res.ok || !data.status) {
//         alert(data.message || "Booking failed");
//         return;
//     }

//     alert("Booking confirmed! ID: " + data.booking_id);

//     setTimeout(() => {
//         window.location.href = "/booking";
//     }, 2000);
// }

// const LIFT_DATA = {
//     four: {
//         img: "assets/images/rentals/four-post.png",
//         points: [
//             "Heavy-duty four-post support",
//             "Perfect for long-hour jobs",
//             "Maximum stability & safety",
//         ],
//     },
//     two: {
//         img: "assets/images/rentals/two-post.png",
//         points: [
//             "Quick vehicle access",
//             "Ideal for mechanical repairs",
//             "Compact and space efficient",
//         ],
//     },
//     scissor: {
//         img: "assets/images/rentals/scissor.png",
//         points: [
//             "Low profile design",
//             "Fast lifting operation",
//             "Great for tire & brake work",
//         ],
//     },
//     flat: {
//         img: "assets/images/rentals/moto-lift.png",
//         points: [
//             "Designed for motorcycles",
//             "Easy loading & unloading",
//             "Stable flat platform",
//         ],
//     },
//     flat2: {
//         img: "assets/images/rentals/alignment-rack.png",
//         points: [
//             "Precision wheel alignment",
//             "Extended ramp length",
//             "Perfect for alignment jobs",
//         ],
//     },
// };
// document.querySelectorAll(".mx-liftbtn").forEach((btn) => {
//     btn.addEventListener("click", () => {
//         // active state
//         document
//             .querySelectorAll(".mx-liftbtn")
//             .forEach((b) => b.classList.remove("active"));
//         btn.classList.add("active");

//         const liftKey = btn.dataset.lift;
//         const lift = LIFT_DATA[liftKey];
//         if (!lift) return;

//         // update image
//         document.getElementById("mxLiftPreviewImg").src = lift.img;

//         // update bullet points
//         const ul = document.getElementById("mxLiftPoints");
//         ul.innerHTML = lift.points.map((p) => `<li>${p}</li>`).join("");

//         setTimeout(() => {
//             scrollToEl(document.getElementById("hoursSection"));
//         }, 200);
//     });
// });


// document.querySelectorAll(".mx-pricecard").forEach((card) => {
//     card.addEventListener("click", () => {
//         // UI state
//         document.querySelectorAll(".mx-pricecard").forEach((c) => {
//             c.classList.remove("mx-selected");
//             c.classList.add("mx-dimmed");
//         });

//         card.classList.add("mx-selected");
//         card.classList.remove("mx-dimmed");

//         //  set package hours
//         selectedPackHours = parseInt(card.dataset.hours, 10) || 1;

//         //  LOCK / UNLOCK +/- buttons
//         toggleHourControls(selectedPackHours === 9 || selectedPackHours === 18);

//         //  repaint calendar availability colors
//         if (fpInstance) fpInstance.redraw();

//         //  IMPORTANT: update time grid if date already chosen
//         if (selectedDate) {
//             renderTimeSlots(selectedDate);
//         }

//         setTimeout(() => {
//             scrollToEl(document.getElementById("calendarSection"));
//         }, 200);
//     });
// });

// function selectPackage(hours) {
//     selectedPackHours = hours;

//     // UI active state
//     document.querySelectorAll(".mx-pricecard").forEach((c) => {
//         const h = parseInt(c.dataset.hours, 10);
//         const isActive = h === hours;
//         c.classList.toggle("mx-selected", isActive);
//         c.classList.toggle("mx-dimmed", !isActive);
//     });

//     // lock/unlock +/- buttons
//     toggleHourControls(hours === 9 || hours === 18);

//     // repaint calendar + update time grid if already opened
//     if (fpInstance) fpInstance.redraw();
//     if (selectedDate) renderTimeSlots(selectedDate);
// }

// //  default = 1 hour
// window.addEventListener("DOMContentLoaded", () => {
//     selectPackage(1);
// });

// //Login & Registration pop up part

// const authStateEl = document.getElementById("mx-auth-state");
// window.MX_IS_LOGGED_IN = authStateEl?.dataset?.loggedIn === "1";
// window.MX_CSRF = document
//     .querySelector('meta[name="csrf-token"]')
//     .getAttribute("content");

// function mxShowErr(el, msg) {
//     el.classList.remove("d-none");
//     el.textContent = msg;
// }

// function mxHideErr(el) {
//     el.classList.add("d-none");
//     el.textContent = "";
// }

// function mxGetBookingPayload() {
//     return {
//         date: selectedDate,
//         start: selectedStartTime,
//         hours: selectedHours,
//         total: getRatePerHour(selectedHours) * selectedHours,
//         lift: document.querySelector(".mx-liftbtn.active")?.dataset.lift,
//         package: selectedPackHours,
//         workstation: 1,
//     };
// }

// // call your existing booking submit method here
// function mxContinueBookingAfterAuth() {
//     const payload = JSON.parse(
//         sessionStorage.getItem("mx_booking_payload") || "null"
//     );
//     if (!payload) return;

//     submitBooking(payload);

//     sessionStorage.removeItem("mx_booking_payload");
// }

// document.addEventListener("DOMContentLoaded", function () {
//     const confirmBtn = document.getElementById("mxModalConfirm");
//     const authModalEl = document.getElementById("mxAuthModal");
//     const authModal = new bootstrap.Modal(authModalEl);

//     // 1) Intercept confirm booking
//     confirmBtn?.addEventListener("click", function () {
//         const check = validateConsecutiveCrossDay(
//             selectedDate,
//             selectedStartTime,
//             selectedHours
//         );
//         if (!check.ok) return alert(check.message);

//         const payload = mxGetBookingPayload();
//         sessionStorage.setItem("mx_booking_payload", JSON.stringify(payload));

//         if (window.MX_IS_LOGGED_IN) {
//             mxContinueBookingAfterAuth();
//             return;
//         }

//         closeModal();

//         authModal.show();
//     });

// });

// // Mobile dropdown lift selector
// document
//     .querySelectorAll("#mxLiftDropdownMenu .dropdown-item")
//     .forEach((item) => {
//         item.addEventListener("click", function (e) {
//             e.preventDefault();

//             const lift = this.dataset.lift;
//             const label = this.innerText;

//             // Update dropdown text
//             document.getElementById("mxLiftDropdownBtn").innerText = label;

//             // Trigger existing button logic
//             const btn = document.querySelector(
//                 `.mx-liftbtn[data-lift="${lift}"]`
//             );
//             if (btn) btn.click();
//         });
//     });

// function syncBookButtonsVisibility() {
//     const topWrap = document.getElementById("leftupButton");
//     const bottomWrap = document.querySelector(".cal-sub-btn");

//     const topBtn = topWrap?.querySelector("button");
//     const bottomBtn = bottomWrap?.querySelector("button");

//     const topVisible =
//         topWrap && window.getComputedStyle(topWrap).display !== "none";
//     const bottomVisible =
//         bottomWrap && window.getComputedStyle(bottomWrap).display !== "none";

//     // console.log("top:", topVisible, "bottom:", bottomVisible);

//     // Default: disable both
//     if (topBtn) {
//         topBtn.disabled = true;
//         topBtn.style.pointerEvents = "none";
//     }

//     if (bottomBtn) {
//         bottomBtn.disabled = true;
//         bottomBtn.style.pointerEvents = "none";
//     }

//     // Enable ONLY the visible one
//     if (topVisible && topBtn) {
//         topBtn.disabled = false;
//         topBtn.style.pointerEvents = "auto";
//     } else if (bottomVisible && bottomBtn) {
//         bottomBtn.disabled = false;
//         bottomBtn.style.pointerEvents = "auto";
//     }
// }

// syncBookButtonsVisibility();
// // On resize
// window.addEventListener("resize", syncBookButtonsVisibility);

// // scrolling for mobile view
// function scrollToEl(el) {
//     if (!el) return;
//     el.scrollIntoView({
//         behavior: "smooth",
//         block: "start",
//     });
// }

// function scrollToEl(el, offset = 70) {
//     if (!el) return;

//     const y = el.getBoundingClientRect().top + window.pageYOffset - offset;
//     window.scrollTo({
//         top: y,
//         behavior: "smooth",
//     });
// }

// const BookClose = document.getElementById("bookclose");

// function toggleBookClose() {
//     if (!BookClose) return;

//     if (window.innerWidth < 768) {
//         BookClose.style.display = "block";
//     } else {
//         BookClose.style.display = "none";
//     }
// }

// // Run on page load
// toggleBookClose();

// // Run on resize
// window.addEventListener("resize", toggleBookClose);


/**
 * booking.js  –  Full jQuery rewrite
 *
 * Flow after "Confirm" in mxSlotModal:
 *   1. mxSlotModal  → Confirm  → openSummaryModal()
 *   2. mxSummaryModal → Pay Now → openPayModal()
 *   3. mxPayModal   → Pay Btn  → simulateDemoPayment() → submitBooking()
 *   4. mxSuccessModal → Print / Done
 *
 * NOTE: Replace simulateDemoPayment() with real Razorpay SDK call when going live.
 *       Everything else (submitBooking, payload, etc.) stays the same.
 */

$(function () {

    /* ================================================================
       CONSTANTS & STATE
    ================================================================ */
    const TODAY          = new Date();
    let dayData          = {};
    let bookedSlots      = {};
    let selectedDate     = null;
    let selectedStartTime= null;
    let selectedHours    = 1;
    let selectedPackHours= 1;
    let fpInstance       = null;
    const MIN_MONTH      = new Date();

    /* ================================================================
       HELPERS
    ================================================================ */
    function pad2(n) { return String(n).padStart(2, '0'); }

    function formatTimePoint(hour24) {
        const ampm = hour24 >= 12 ? 'PM' : 'AM';
        let h = hour24 % 12 || 12;
        return `${h}:00 ${ampm}`;
    }

    function formatMoney(n) { return `$${Number(n).toFixed(0)}`; }

    function addDaysStr(dateStr, days) {
        const d = new Date(dateStr + 'T00:00:00');
        d.setDate(d.getDate() + days);
        return `${d.getFullYear()}-${pad2(d.getMonth()+1)}-${pad2(d.getDate())}`;
    }

    function nextDate(dateStr, days = 1) { return addDaysStr(dateStr, days); }

    function getWorkingHours(dateStr) {
        const day = new Date(dateStr + 'T00:00:00').getDay();
        if (day === 6) return null;
        if (day === 5) return { start: 9, end: 12 };
        return { start: 9, end: 18 };
    }

    function getWorkingSlots(dateStr) {
        const wh = getWorkingHours(dateStr);
        if (!wh) return [];
        const slots = [];
        for (let h = wh.start; h < wh.end; h++) slots.push(`${pad2(h)}:00`);
        return slots;
    }

    function isSlotBooked(dateStr, timeStr) {
        return (bookedSlots[dateStr] || []).includes(timeStr);
    }

    function isHourFree(dateStr, hour) {
        const wh = getWorkingHours(dateStr);
        if (!wh || hour < wh.start || hour >= wh.end) return false;
        return !isSlotBooked(dateStr, `${pad2(hour)}:00`);
    }

    function isDayFullyFree(dateStr) {
        const slots = getWorkingSlots(dateStr);
        return slots.length > 0 && slots.every(t => !isSlotBooked(dateStr, t));
    }

    function isDayHasAnyFreeHour(dateStr) {
        const slots = getWorkingSlots(dateStr);
        return slots.some(t => !isSlotBooked(dateStr, t));
    }

    function isDateAvailableByPackage(dateStr, packHours) {
        const wh = getWorkingHours(dateStr);
        if (!wh) return false;
        if (packHours === 1)  return isDayHasAnyFreeHour(dateStr);
        if (packHours === 9) {
            return getWorkingSlots(dateStr).length >= 9 && isDayFullyFree(dateStr);
        }
        if (packHours === 18) {
            if (getWorkingSlots(dateStr).length < 9) return false;
            const next = nextDate(dateStr, 1);
            return getWorkingSlots(next).length >= 9 && isDayFullyFree(dateStr) && isDayFullyFree(next);
        }
        return isDayHasAnyFreeHour(dateStr);
    }

    function addWorkingHours(startDateStr, startHour, hoursNeeded) {
        let remaining = hoursNeeded, curDate = startDateStr, curHour = startHour;
        while (remaining > 0) {
            const wh = getWorkingHours(curDate);
            if (!wh) { curDate = addDaysStr(curDate, 1); curHour = 0; continue; }
            if (curHour < wh.start) curHour = wh.start;
            if (curHour >= wh.end)  { curDate = addDaysStr(curDate, 1); curHour = 0; continue; }
            const available = wh.end - curHour;
            if (remaining <= available) { curHour += remaining; remaining = 0; }
            else { remaining -= available; curDate = addDaysStr(curDate, 1); curHour = 0; }
        }
        return { endDate: curDate, endHour: curHour };
    }

    function buildEndLabel(dateStr, startTimeStr, hours) {
        const startH = parseInt(startTimeStr.slice(0,2), 10);
        const { endDate, endHour } = addWorkingHours(dateStr, startH, hours);
        const fmt = d => new Date(d+'T00:00:00').toLocaleDateString([],{month:'short',day:'2-digit',year:'numeric'});
        return endDate !== dateStr ? `${fmt(endDate)} ${formatTimePoint(endHour)}` : formatTimePoint(endHour);
    }

    function prettyRange(dateStr, startHour, hoursNeeded) {
        const { endDate, endHour } = addWorkingHours(dateStr, startHour, hoursNeeded);
        const fmt = d => new Date(d + 'T00:00:00').toLocaleDateString([], { year:'numeric', month:'short', day:'2-digit' });
        const d1 = fmt(dateStr), d2 = fmt(endDate);
        const t1 = formatTimePoint(startHour), t2 = formatTimePoint(endHour);
        return d1 !== d2 ? `${d1} • ${t1} → ${d2} • ${t2}` : `${d1} • ${t1} - ${t2}`;
    }

    function validateConsecutiveCrossDay(startDateStr, startTimeStr, hoursNeeded) {
        const startH = parseInt(startTimeStr.slice(0, 2), 10);
        const wh0    = getWorkingHours(startDateStr);
        if (!wh0) return { ok: false, message: 'Closed this day.' };
        if (startH < wh0.start || startH >= wh0.end) return { ok: false, message: 'Start time not in working hours.' };

        let remaining = hoursNeeded, curDate = startDateStr, curHour = startH;
        while (remaining > 0) {
            const wh = getWorkingHours(curDate);
            if (!wh) return { ok: false, message: `Closed on ${curDate}.` };
            if (curHour < wh.start) curHour = wh.start;
            if (curHour >= wh.end)  { curDate = addDaysStr(curDate, 1); curHour = 0; continue; }
            if (!isHourFree(curDate, curHour)) return { ok: false, message: `Blocked at ${curDate} ${pad2(curHour)}:00` };
            remaining--; curHour++;
            if (curHour >= wh.end && remaining > 0) { curDate = addDaysStr(curDate, 1); curHour = 0; }
        }
        return { ok: true };
    }

    /* ================================================================
       PRICING
    ================================================================ */
    function getSelectedCard()      { return $('.mx-pricecard.mx-selected'); }
    function getRatePerHour()       { return Number(getSelectedCard().data('price')) || 0; }
    function getPackageTotal(hours) { return getRatePerHour() * hours; }

    /* ================================================================
       LIFT LABELS
    ================================================================ */
    const LIFT_LABELS = {
        four:    'Four-Post Lift',
        two:     'Two-Post Lift',
        scissor: 'Scissor Lift',
        flat:    'Motorcycle Lift',
        flat2:   'Alignment Rack'
    };

    function getActiveLiftLabel() {
        const key = $('.mx-liftbtn.active').data('lift');
        return LIFT_LABELS[key] || key || '—';
    }

    /* ================================================================
       MODALS
    ================================================================ */
    function openSlotModal()   { $('#mxSlotModal').addClass('show').attr('aria-hidden','false'); }
    function closeSlotModal()  { $('#mxSlotModal').removeClass('show').attr('aria-hidden','true'); }
    function openSummaryModal(){ $('#mxSummaryModal').addClass('show').attr('aria-hidden','false'); }
    function closeSummaryModal(){ $('#mxSummaryModal').removeClass('show').attr('aria-hidden','true'); }
    function openPayModal()    { $('#mxPayModal').addClass('show').attr('aria-hidden','false'); }
    function closePayModal()   { $('#mxPayModal').removeClass('show').attr('aria-hidden','true'); }
    function openSuccessModal(){ $('#mxSuccessModal').addClass('show').attr('aria-hidden','false'); }

    /* ================================================================
       HOUR CONTROLS
    ================================================================ */
    function toggleHourControls(lock) {
        $('#mxHMinus, #mxHPlus').prop('disabled', lock).toggleClass('mx-disabled', lock);
    }

    function setHours(val) {
        selectedHours = Math.max(selectedPackHours, Math.min(val, 48));
        $('#mxSelectedHours').text(selectedHours);
        const startH = parseInt(selectedStartTime.slice(0, 2), 10);
        $('#mxTotalText').text(formatMoney(getPackageTotal(selectedHours)));
        $('#mxSlotText').text(prettyRange(selectedDate, startH, selectedHours));
        const check = validateConsecutiveCrossDay(selectedDate, selectedStartTime, selectedHours);
        $('#mxHintText').text(check.ok ? `Continuous booking for ${selectedHours} hour(s).` : check.message);
        $('#mxModalConfirm').prop('disabled', !check.ok).css('opacity', check.ok ? '1' : '.6');
    }

    /* ================================================================
       SUMMARY MODAL  –  populate
    ================================================================ */
    function populateAndOpenSummary() {
        const rate     = getRatePerHour();
        const total    = getPackageTotal(selectedHours);
        const startH   = parseInt(selectedStartTime.slice(0,2), 10);
        const dateFmt  = new Date(selectedDate + 'T00:00:00').toLocaleDateString([], { weekday:'long', year:'numeric', month:'long', day:'numeric' });

        $('#mxsWorkstation').text('Workstation 1');
        $('#mxsLift').text(getActiveLiftLabel());
        $('#mxsDate').text(dateFmt);
        $('#mxsStart').text(formatTimePoint(startH));
        $('#mxsDuration').text(`${selectedHours} hour${selectedHours > 1 ? 's' : ''}`);
        $('#mxsEnd').text(buildEndLabel(selectedDate, selectedStartTime, selectedHours));
        $('#mxsRate').text(`${formatMoney(rate)} / hr`);
        $('#mxsHours').text(selectedHours);
        $('#mxsTotal').text(formatMoney(total));

        // prime pay modal amount
        $('#mxPayAmount').text(formatMoney(total));
        $('#mxPayBtnAmt').text(formatMoney(total));

        openSummaryModal();
    }

    /* ================================================================
       TIME SLOTS
    ================================================================ */
    function renderTimeSlots(dateStr) {
        const $grid = $('#mxTimeGrid').empty();
        $('#mxSelectedDateText').text(dateStr);
        selectedStartTime = null;
        $('#mxPickedTimeText').text('None');
        $('#mxContinueBtn').prop('disabled', true);

        const wh = getWorkingHours(dateStr);
        if (!wh) {
            $grid.html('<div style="grid-column:1/-1;padding:14px;border:1px solid #eee;border-radius:12px;">Closed.</div>');
            return;
        }

        if (selectedPackHours === 9 || selectedPackHours === 18) {
            const startValue = `${pad2(wh.start)}:00`;
            const check      = validateConsecutiveCrossDay(dateStr, startValue, selectedPackHours);
            if (!check.ok) {
                $grid.html(`<div style="grid-column:1/-1;padding:14px;border:1px solid #eee;border-radius:12px;color:#000;">
                    Not available for <b>${selectedPackHours} hours</b> starting 9:00 AM.<br>
                    <small>${check.message}</small></div>`);
                $('#mxContinueBtn').prop('disabled', true);
                return;
            }
            $('<button>', { type:'button', class:'mx-slot available', text:`Start at ${formatTimePoint(wh.start)} (Full ${selectedPackHours}h)`, 'data-value': startValue })
                .on('click', function () {
                    $('.mx-slot').removeClass('selected'); $(this).addClass('selected');
                    selectedStartTime = startValue;
                    $('#mxPickedTimeText').text(formatTimePoint(wh.start));
                    $('#mxContinueBtn').prop('disabled', false);
                }).appendTo($grid);
            return;
        }

        for (let h = wh.start; h < wh.end; h++) {
            const value  = `${pad2(h)}:00`;
            const label  = `${formatTimePoint(h)} - ${formatTimePoint(h + 1)}`;
            const booked = isSlotBooked(dateStr, value);
            const $btn   = $('<button>', { type:'button', class:`mx-slot ${booked ? 'disabled' : 'available'}`, text: booked ? `${label} (Booked)` : label, disabled: booked, 'data-value': value });
            if (!booked) {
                $btn.on('click', function () {
                    $('.mx-slot').removeClass('selected'); $(this).addClass('selected');
                    selectedStartTime = value;
                    $('#mxPickedTimeText').text(label);
                    $('#mxContinueBtn').prop('disabled', false);
                });
            }
            $grid.append($btn);
        }
    }

    /* ================================================================
       VIEW SWITCH
    ================================================================ */
    function showTimeView() { $('#calendarWrap').hide(); $('#mxTimeView').show(); }
    function showDateView() {
        $('#mxTimeView').hide(); $('#calendarWrap').show();
        selectedStartTime = null;
        $('#mxPickedTimeText').text('None');
        $('#mxContinueBtn').prop('disabled', true);
    }

    /* ================================================================
       CALENDAR DATA
    ================================================================ */
    async function loadCalendarData(monthStr = null, workstation = 1) {
        const params = new URLSearchParams();
        if (monthStr) params.append('month', monthStr);
        params.append('workstation', workstation);
        const res  = await fetch(`/booking/calendar-data?${params.toString()}`, { method:'GET', credentials:'same-origin', headers:{ Accept:'application/json' } });
        const data = await res.json();
        dayData     = data.dayData     || {};
        bookedSlots = data.bookedSlots || {};
        if (fpInstance) fpInstance.redraw();
    }

    loadCalendarData(null, 1);

    /* ================================================================
       FLATPICKR
    ================================================================ */
    function updateMonthNav(fp) {
        const cur    = new Date(fp.currentYear, fp.currentMonth, 1);
        const min    = new Date(MIN_MONTH.getFullYear(), MIN_MONTH.getMonth(), 1);
        const prevBtn= fp.calendarContainer.querySelector('.flatpickr-prev-month');
        if (!prevBtn) return;
        prevBtn.style.pointerEvents = cur <= min ? 'none' : 'auto';
        prevBtn.style.opacity       = cur <= min ? '0.3'  : '1';
    }

    fpInstance = flatpickr('#bookingDate', {
        inline: true, dateFormat: 'Y-m-d', disableMobile: true,
        defaultDate: TODAY, appendTo: document.getElementById('calendarWrap'), minDate: new Date(),

        onReady:      (s,d,fp) => { fpInstance = fp; updateMonthNav(fp); },
        onMonthChange:(s,d,fp) => { updateMonthNav(fp); fp.redraw(); loadCalendarData(`${fp.currentYear}-${pad2(fp.currentMonth+1)}`, 1); },
        onYearChange: (s,d,fp) => { updateMonthNav(fp); fp.redraw(); },

        disable: [function (date) {
            if (date.getDay() === 6) return true;
            const info = dayData[flatpickr.formatDate(date, 'Y-m-d')];
            return info && (info.status === 'unavailable' || info.status === 'booked');
        }],

        onDayCreate: function (dObj, dStr, fp, dayElem) {
            dayElem.classList.remove('day-available','day-unavailable','day-booked');
            if (dayElem.classList.contains('flatpickr-disabled')) {
                const info = dayData[fp.formatDate(dayElem.dateObj,'Y-m-d')];
                dayElem.classList.add(info?.status === 'booked' ? 'day-booked' : 'day-unavailable'); return;
            }
            if (dayElem.classList.contains('nextMonthDay')) { dayElem.classList.add('day-nextmonth'); return; }
            if (dayElem.classList.contains('prevMonthDay')) { dayElem.classList.add('day-prevmonth'); return; }
            const key  = fp.formatDate(dayElem.dateObj, 'Y-m-d');
            const info = dayData[key];
            if (info?.status === 'booked')      { dayElem.classList.add('day-booked');      return; }
            if (info?.status === 'unavailable') { dayElem.classList.add('day-unavailable'); return; }
            dayElem.classList.add(isDateAvailableByPackage(key, selectedPackHours) ? 'day-available' : 'day-unavailable');
        },

        onChange: function (selectedDates, dateStr) {
            const ok = isDateAvailableByPackage(dateStr, selectedPackHours);
            if (!ok) { selectedDate = null; updateBookBtns('Pick a valid date', true); return; }
            selectedDate = dateStr || null;
            selectedDate ? updateBookBtns(`Book for ${selectedDate}`, false) : updateBookBtns('Pick a date', true);
        }
    });

    function updateBookBtns(text, disabled) {
        $('#openDayCalendar, #openDayCalendarMb').text(text).prop('disabled', disabled).toggleClass('enabled', !disabled);
    }

    /* ================================================================
       PRICE CARDS
    ================================================================ */
    function selectPackage(hours) {
        selectedPackHours = hours;
        $('.mx-pricecard').each(function () {
            const h = parseInt($(this).data('hours'), 10);
            $(this).toggleClass('mx-selected', h === hours).toggleClass('mx-dimmed', h !== hours);
        });
        toggleHourControls(hours === 9 || hours === 18);
        if (fpInstance) fpInstance.redraw();
        if (selectedDate) renderTimeSlots(selectedDate);
    }

    const $firstCard = $('.mx-pricecard.mx-selected').first();
    if ($firstCard.length) { selectedPackHours = parseInt($firstCard.data('hours'),10) || 1; selectedHours = selectedPackHours; }
    selectPackage(selectedPackHours);

    $(document).on('click', '.mx-pricecard', function () {
        const hours = parseInt($(this).data('hours'),10) || 1;
        selectPackage(hours); selectedHours = hours;
        setTimeout(() => scrollToEl($('#calendarSection')[0]), 200);
    });

    /* ================================================================
       LIFT BUTTONS
    ================================================================ */
    const LIFT_DATA = {
        four:    { img:'assets/images/rentals/fourpost.jpg',      points:['Heavy-duty four-post support','Perfect for long-hour jobs','Maximum stability & safety'] },
        two:     { img:'assets/images/rentals/twopost.jpg',       points:['Quick vehicle access','Ideal for mechanical repairs','Compact and space efficient'] },
        scissor: { img:'assets/images/rentals/scissor.jpg',        points:['Low profile design','Fast lifting operation','Great for tire & brake work'] },
        flat:    { img:'assets/images/rentals/motocycle.jpg',      points:['Designed for motorcycles','Easy loading & unloading','Stable flat platform'] },
        flat2:   { img:'assets/images/rentals/allignmentrack.jpg', points:['Precision wheel alignment','Extended ramp length','Perfect for alignment jobs'] }
    };

    $(document).on('click', '.mx-liftbtn', function () {
        $('.mx-liftbtn').removeClass('active'); $(this).addClass('active');
        const lift = LIFT_DATA[$(this).data('lift')];
        if (!lift) return;
        $('#mxLiftPreviewImg').attr('src', lift.img);
        $('#mxLiftPoints').html(lift.points.map(p => `<li>${p}</li>`).join(''));
        setTimeout(() => scrollToEl($('#hoursSection')[0]), 200);
    });

    $(document).on('click', '#mxLiftDropdownMenu .dropdown-item', function (e) {
        e.preventDefault();
        $('#mxLiftDropdownBtn').text($(this).text().trim());
        $(`.mx-liftbtn[data-lift="${$(this).data('lift')}"]`).trigger('click');
    });

    /* ================================================================
       BOOK NOW → time grid
    ================================================================ */
    $('#openDayCalendar').on('click', function () {
        if (!selectedDate) return;
        $('.mx-gridWrap, .mx-legendMini').hide(); showTimeView(); renderTimeSlots(selectedDate);
    });

    $('#openDayCalendarMb').on('click', function () {
        if (!selectedDate) return;
        $('.mx-gridWrap, .mx-legendMini').hide(); $(this).hide();
        showTimeView(); renderTimeSlots(selectedDate);
    });

    $('#mxBackToDate').on('click', function () { showDateView(); $('#openDayCalendarMb').show(); });

    /* ================================================================
       CONTINUE → slot modal (hours picker)
    ================================================================ */
    $('#mxContinueBtn').on('click', function () {
        if (!selectedDate || !selectedStartTime) return;
        selectedHours = selectedPackHours;
        openSlotModal();
        const startH = parseInt(selectedStartTime.slice(0,2), 10);
        $('#mxSlotText').text(prettyRange(selectedDate, startH, selectedHours));
        $('#mxSelectedHours').text(selectedHours);
        $('#mxTotalText').text(formatMoney(getPackageTotal(selectedHours)));
        const check = validateConsecutiveCrossDay(selectedDate, selectedStartTime, selectedHours);
        $('#mxHintText').text(check.ok ? `Continuous booking for ${selectedHours} hour(s).` : check.message);
        $('#mxModalConfirm').prop('disabled', !check.ok).css('opacity', check.ok ? '1' : '.6');
    });

    $('#mxHMinus').on('click', () => setHours(selectedHours - 1));
    $('#mxHPlus').on('click',  () => setHours(selectedHours + 1));

    /* ================================================================
       SLOT MODAL CLOSE
    ================================================================ */
    $('#mxModalClose, #mxModalCancel').on('click', closeSlotModal);
    $('#mxSlotModal').on('click', function (e) { if ($(e.target).is('#mxSlotModal')) closeSlotModal(); });
    $(document).on('keydown', function (e) {
        if (e.key === 'Escape') { closeSlotModal(); closeSummaryModal(); closePayModal(); }
    });

    /* ================================================================
       AUTH STATE
    ================================================================ */
    window.MX_CSRF         = $('meta[name="csrf-token"]').attr('content');
    window.MX_IS_LOGGED_IN = $('#mx-auth-state').data('logged-in') === 1 || $('#mx-auth-state').data('logged-in') === '1';

    function mxGetBookingPayload() {
        return {
            date:        selectedDate,
            start:       selectedStartTime,
            hours:       selectedHours,
            total:       getPackageTotal(selectedHours),
            lift:        $('.mx-liftbtn.active').data('lift'),
            package:     selectedPackHours,
            workstation: 1
        };
    }

    /* ================================================================
       SLOT MODAL CONFIRM  →  auth check  →  summary
    ================================================================ */
    $('#mxModalConfirm').on('click', function () {
        const check = validateConsecutiveCrossDay(selectedDate, selectedStartTime, selectedHours);
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
        const raw = sessionStorage.getItem('mx_booking_payload');
        if (!raw) return;
        const p = JSON.parse(raw);
        selectedDate = p.date; selectedStartTime = p.start;
        selectedHours = p.hours; selectedPackHours = p.package;
        bootstrap.Modal.getInstance(document.getElementById('mxAuthModal'))?.hide();
        populateAndOpenSummary();
    }

    /* ================================================================
       SUMMARY MODAL ACTIONS
    ================================================================ */
    $('#mxSummaryClose').on('click', closeSummaryModal);
    $('#mxSummaryModal').on('click', function (e) { if ($(e.target).is('#mxSummaryModal')) closeSummaryModal(); });

    $('#mxSummaryBack').on('click', function () { closeSummaryModal(); openSlotModal(); });

    $('#mxSummaryPay').on('click', function () {
        closeSummaryModal();
        // Reset pay UI
        $('#mxCardNum, #mxCardExp, #mxCardCvv, #mxCardName').val('');
        $('#mxPayError').addClass('d-none').text('');
        $('#mxPayNowBtn').prop('disabled', false);
        $('#mxPayBtnText').html(`Pay <span id="mxPayBtnAmt">${$('#mxPayAmount').text()}</span>`);
        $('#mxPaySpinner').addClass('d-none');
        // Make sure card tab is active
        $('.mxs-pay-tab').removeClass('active').filter('[data-tab="card"]').addClass('active');
        $('.mxs-pay-panel').removeClass('active'); $('#mxPayPanel-card').addClass('active');
        openPayModal();
    });

    /* ================================================================
       PAY MODAL  –  tabs
    ================================================================ */
    $('#mxPayClose').on('click', closePayModal);
    $('#mxPayModal').on('click', function (e) { if ($(e.target).is('#mxPayModal')) closePayModal(); });

    $(document).on('click', '.mxs-pay-tab', function () {
        const tab = $(this).data('tab');
        $('.mxs-pay-tab').removeClass('active'); $(this).addClass('active');
        $('.mxs-pay-panel').removeClass('active'); $(`#mxPayPanel-${tab}`).addClass('active');
    });

    // Card input formatters
    $('#mxCardNum').on('input', function () {
        let v = $(this).val().replace(/\D/g,'').slice(0,16);
        $(this).val(v.match(/.{1,4}/g)?.join(' ') || v);
    });
    $('#mxCardExp').on('input', function () {
        let v = $(this).val().replace(/\D/g,'').slice(0,4);
        if (v.length >= 3) v = v.slice(0,2) + ' / ' + v.slice(2);
        $(this).val(v);
    });

    /* ================================================================
       DEMO PAYMENT SIMULATION
       ─── SWAP OUT FOR REAL RAZORPAY SDK WHEN GOING LIVE ───
    ================================================================ */
    function simulateDemoPayment(onSuccess) {
        const $btn = $('#mxPayNowBtn'), $spinner = $('#mxPaySpinner'), $err = $('#mxPayError');

        // Minimal demo validation on card tab
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
        $('#mxPayBtnText').text('Processing…');
        $spinner.removeClass('d-none');

        // Simulate 1.8s network delay
        setTimeout(() => {
            $spinner.addClass('d-none');
            $('#mxPayBtnText').text('✓ Payment Successful!');
            setTimeout(() => { closePayModal(); onSuccess(); }, 700);
        }, 1800);
    }

    /*
     * ─── REAL RAZORPAY – plug in here when going live ───
     *
     * function openRealRazorpay(onSuccess) {
     *     const options = {
     *         key:         'YOUR_RAZORPAY_KEY_ID',
     *         amount:      getPackageTotal(selectedHours) * 100,   // paise
     *         currency:    'INR',
     *         name:        'Mechanix D.I.Y.',
     *         description: 'Workstation Booking',
     *         handler: function (response) {
     *             onSuccess(response.razorpay_payment_id);
     *         },
     *         prefill:  { name:'', email:'', contact:'' },
     *         theme:    { color: '#e8282b' }
     *     };
     *     new Razorpay(options).open();
     * }
     */

    /* ================================================================
       PAY NOW BUTTON
    ================================================================ */
    $('#mxPayNowBtn').on('click', function () {
        simulateDemoPayment(function () {
            const payload = JSON.parse(sessionStorage.getItem('mx_booking_payload') || '{}');
            submitBooking(payload);
        });
    });

    /* ================================================================
       SUBMIT BOOKING  →  server
    ================================================================ */
    async function submitBooking(payload) {
        try {
            const res  = await fetch('/booking/confirm', {
                method:'POST', credentials:'same-origin',
                headers:{ 'Content-Type':'application/json','X-CSRF-TOKEN':window.MX_CSRF, Accept:'application/json' },
                body: JSON.stringify(payload)
            });
            const data = await res.json().catch(() => ({}));
            sessionStorage.removeItem('mx_booking_payload');

            if (!res.ok || !data.status) {
                alert(data.message || 'Booking failed. Please try again.');
                return;
            }

            openSuccessReceipt(data.booking_id || ('MX-' + Date.now()), payload);
        } catch (err) {
            // In demo mode the server might not exist – still show success modal
            sessionStorage.removeItem('mx_booking_payload');
            openSuccessReceipt('MX-DEMO-' + Date.now(), payload);
        }
    }

    /* ================================================================
       SUCCESS RECEIPT
    ================================================================ */
    function openSuccessReceipt(bookingId, payload) {
        const rate    = getRatePerHour();
        const total   = getPackageTotal(payload.hours);
        const startH  = parseInt(payload.start.slice(0,2), 10);
        const dateFmt = new Date(payload.date + 'T00:00:00').toLocaleDateString([], { weekday:'long', year:'numeric', month:'long', day:'numeric' });

        $('#mxSuccessBookingId').text(bookingId);
        $('#mxrWorkstation').text('Workstation 1');
        $('#mxrLift').text(LIFT_LABELS[payload.lift] || payload.lift || '—');
        $('#mxrDate').text(dateFmt);
        $('#mxrStart').text(formatTimePoint(startH));
        $('#mxrDuration').text(`${payload.hours} hour${payload.hours > 1 ? 's' : ''}`);
        $('#mxrEnd').text(buildEndLabel(payload.date, payload.start, payload.hours));
        $('#mxrRate').text(`${formatMoney(rate)} / hr`);
        $('#mxrTotal').text(formatMoney(total));

        openSuccessModal();
    }

    /* ================================================================
       PRINT
    ================================================================ */
    $('#mxPrintBtn').on('click', () => window.print());

    /* ================================================================
       AUTH FORMS
    ================================================================ */
    $('#mxLoginForm').on('submit', async function (e) {
        e.preventDefault();
        const $err = $('#mxLoginErr').addClass('d-none').text('');
        const res  = await fetch('/login', {
            method:'POST', credentials:'same-origin',
            headers:{ 'Content-Type':'application/json','X-CSRF-TOKEN':window.MX_CSRF, Accept:'application/json' },
            body: JSON.stringify({ email:$(this).find('[name=email]').val(), password:$(this).find('[name=password]').val() })
        });
        const data = await res.json().catch(() => ({}));
        if (!res.ok) { $err.text(data.message || 'Login failed').removeClass('d-none'); return; }
        window.MX_IS_LOGGED_IN = true;
        mxContinueAfterAuth();
    });

    $('#mxRegisterForm').on('submit', async function (e) {
        e.preventDefault();
        const $err = $('#mxRegErr').addClass('d-none').text('');
        const res  = await fetch('/register', {
            method:'POST', credentials:'same-origin',
            headers:{ 'Content-Type':'application/json','X-CSRF-TOKEN':window.MX_CSRF, Accept:'application/json' },
            body: JSON.stringify({
                email:$(this).find('[name=email]').val(), mobile_no:$(this).find('[name=mobile_no]').val(),
                password:$(this).find('[name=password]').val(), password_confirmation:$(this).find('[name=password_confirmation]').val()
            })
        });
        const data = await res.json().catch(() => ({}));
        if (!res.ok) {
            $err.text(data.errors ? Object.values(data.errors).flat().join(' ') : (data.message||'Registration failed')).removeClass('d-none');
            return;
        }
        window.MX_IS_LOGGED_IN = true;
        mxContinueAfterAuth();
    });

    /* ================================================================
       WORKSTATION TABS
    ================================================================ */
    $(document).on('click', '.mx-w-title', function () {
        $('.mx-w-title').removeClass('active'); $(this).addClass('active');
        loadCalendarData(null, parseInt($(this).data('ws'),10) || 1);
    });

    /* ================================================================
       RESPONSIVE
    ================================================================ */
    function toggleBookClose() { window.innerWidth < 768 ? $('#bookclose').show() : $('#bookclose').hide(); }
    toggleBookClose();
    $(window).on('resize', toggleBookClose);

    function syncBookButtonsVisibility() {
        const topVis    = $('#leftupButton').is(':visible');
        const bottomVis = $('.cal-sub-btn').is(':visible');
        $('#leftupButton button').prop('disabled', !topVis).css('pointerEvents', topVis ? 'auto' : 'none');
        $('.cal-sub-btn button').prop('disabled', !bottomVis).css('pointerEvents', bottomVis ? 'auto' : 'none');
    }
    syncBookButtonsVisibility();
    $(window).on('resize', syncBookButtonsVisibility);

    /* ================================================================
       SCROLL HELPER
    ================================================================ */
    function scrollToEl(el, offset = 70) {
        if (!el) return;
        $('html, body').animate({ scrollTop: $(el).offset().top - offset }, 400);
    }

});