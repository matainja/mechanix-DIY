/**
 * membership.js  —  Mechanix D.I.Y.
 *
 * NEW in this version:
 *  • When a logged-in user clicks "Join Membership", we first check via
 *    GET /membership/my-membership whether they already have an active plan.
 *    If yes → show the "Already a Member" modal with their current plan details
 *    and a call-admin prompt instead of the payment/request flow.
 *  • Guest users are never checked (they have no account record).
 *  • All existing flows (guest form, logged-in request, pay modal, success
 *    modals) are unchanged.
 */

$(function () {

    window.MX_CSRF = $('meta[name="csrf-token"]').attr('content') || '';
    window.MX_IS_LOGGED_IN = $('#mx-auth-state').data('logged-in') == '1';

    var selectedPlan          = null;
    var currentGuestRequestId = null;
    var isGuestFlow           = false;

    /* ================================================================
       MODAL HELPERS
    ================================================================ */
    function openModal(id)  { $(id).addClass('show').attr('aria-hidden', 'false'); }
    function closeModal(id) { $(id).removeClass('show').attr('aria-hidden', 'true'); }

    // Add this near the top with other helper functions
function showPendingRequestModal(data) {
    if (!data.pending_request) return;
    
    var msg = data.message || 'You have a pending request.';
    var planName = data.pending_request.plan_name || 'Membership';
    var createdAt = data.pending_request.created_at || '—';
    var hoursLeft = data.pending_request.hours_left || 0;
    
    alert(msg + '\n\nPlan: ' + planName + '\nSubmitted: ' + createdAt + '\nTime remaining: ' + hoursLeft + ' hours');
}

    /* ================================================================
       PHONE FORMATTING
    ================================================================ */
    $('#guestMemberPhone').on('input', function () {
        var v = $(this).val().replace(/\D/g, '');
        var f = '';
        if (v.length > 0) f = '(' + v.substring(0, 3);
        if (v.length >= 4) f += ') ' + v.substring(3, 6);
        if (v.length >= 7) f += '-' + v.substring(6, 10);
        $(this).val(f);
    });

    /* ================================================================
       LOAD & RENDER PLANS
    ================================================================ */
    async function loadPlans() {
        try {
            var res  = await fetch('/membership/plans', {
                method: 'GET',
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json' },
            });
            var data = await res.json();
            if (data.status && data.plans && data.plans.length) {
                renderPlans(data.plans);
            } else {
                $('#membershipPlansContainer').html(
                    '<div style="text-align:center;color:#64748b;padding:40px 0;">No membership plans available.</div>'
                );
            }
        } catch (e) {
            $('#membershipPlansContainer').html(
                '<div style="text-align:center;color:#dd2b31;padding:40px 0;">Failed to load plans.</div>'
            );
        }
    }

    function renderPlans(plans) {
        window.membershipPlans = plans;
        var html = '';
        plans.forEach(function (plan, idx) {
            var features = [];
            try { features = JSON.parse(plan.features || '[]'); } catch (e) { features = []; }
            var featured = idx === 1;

            var featureItems = features.map(function (f) {
                return '<div class="feature-item"><div class="check">✓</div>' + f + '</div>';
            }).join('');

            html += '<div class="membership-card ' + (featured ? 'featured' : '') + '">' +
                '<div class="membership-title">' + plan.name + '</div>' +
                (featured ? '<div class="plan-badge">POPULAR</div>' : '') +
                '<div class="top-sec-wrap">' +
                    '<div class="top-sec">' +
                        '<div class="top-section-left">' +
                            '<div class="price-section">' +
                                '<div class="price"><span>$</span>' + plan.price + '</div>' +
                            '</div>' +
                            '<div class="validity">Valid for ' + plan.duration_days + ' days</div>' +
                        '</div>' +
                        '<div class="features">' + featureItems + '</div>' +
                    '</div>' +
                '</div>' +
                '<div class="btn-wrapper">' +
                    '<button class="join-btn" data-plan-id="' + plan.id + '">' +
                        '<span>Join Membership</span>' +
                    '</button>' +
                '</div>' +
            '</div>';
        });
        $('#membershipPlansContainer').html(html);
    }

    /* ================================================================
       CHECK EXISTING MEMBERSHIP (logged-in users only)
       Returns the membership object if active, or null.
    ================================================================ */
    async function checkExistingMembership() {
        try {
            var res  = await fetch('/membership/my-membership', {
                method: 'GET',
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json' },
            });
            var data = await res.json();
            return (data.status && data.membership) ? data.membership : null;
        } catch (_) {
            return null;   // on network error, let the flow continue normally
        }
    }

    /* ================================================================
       SHOW "ALREADY A MEMBER" MODAL
    ================================================================ */
    function showAlreadyMemberModal(membership) {
        var features = [];
        try { features = JSON.parse(membership.features || '[]'); } catch (_) { features = []; }

        // Plan name + price
        $('#mxAMPlanName').text(membership.plan_name);
        $('#mxAMPrice').text('$' + parseFloat(membership.price).toFixed(2));
        $('#mxAMDuration').text(membership.duration_days + ' days');

        // Dates & days left
        $('#mxAMStartDate').text(membership.start_date);
        $('#mxAMEndDate').text(membership.end_date);
        $('#mxAMDaysLeft').text(membership.days_left + ' day' + (membership.days_left !== 1 ? 's' : '') + ' remaining');

        // Feature list
        if (features.length) {
            var html = features.map(function (f) {
                return '<li><span class="mx-am-check">✓</span>' + f + '</li>';
            }).join('');
            $('#mxAMFeatureList').html(html).closest('.mx-am-features-wrap').show();
        } else {
            $('#mxAMFeatureList').closest('.mx-am-features-wrap').hide();
        }

        openModal('#mxAlreadyMemberModal');
    }

    /* ================================================================
       SELECT PLAN — entry point for "Join Membership" button
    ================================================================ */
    $(document).on('click', '.join-btn[data-plan-id]', async function () {
        var planId = $(this).data('plan-id');
        selectedPlan = (window.membershipPlans || []).find(function (p) { return p.id == planId; });
        if (!selectedPlan) { alert('Plan not found.'); return; }

        sessionStorage.setItem('mx_membership_plan', JSON.stringify(selectedPlan));

        if (window.MX_IS_LOGGED_IN) {
            // ── CHECK EXISTING MEMBERSHIP FIRST ──
            var $btn = $(this).prop('disabled', true).find('span').text('Checking…');
            var existing = await checkExistingMembership();
            $(this).prop('disabled', false).find('span').text('Join Membership');

            if (existing) {
                // User already has an active membership — show info modal
                showAlreadyMemberModal(existing);
                return;
            }

            // No active membership — proceed with request
            isGuestFlow = false;
            submitLoggedInRequestWithoutPayment();

        } else {
            // Guest — no membership check, show auth modal
            isGuestFlow = true;
            var modal = new bootstrap.Modal(document.getElementById('mxAuthModal'));
            modal.show();
            bootstrap.Tab.getOrCreateInstance(document.getElementById('guestMemberTab')).show();
        }
    });

    /* ================================================================
       AUTH — LOGIN
    ================================================================ */
    $('#mxLoginForm').on('submit', async function (e) {
        e.preventDefault();
        var $err = $('#loginErrorMsg').addClass('d-none').text('');
        try {
            var res  = await fetch('/popup-login', {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.MX_CSRF,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    email:    $(this).find('[name=email]').val(),
                    password: $(this).find('[name=password]').val(),
                }),
            });
            var data = await res.json().catch(function () { return {}; });
            if (!res.ok) {
                $err.text(data.message || 'Login failed.').removeClass('d-none');
                return;
            }
            bootstrap.Modal.getInstance(document.getElementById('mxAuthModal')).hide();
            sessionStorage.setItem('mx_membership_plan', JSON.stringify(selectedPlan));
            sessionStorage.setItem('mx_after_login', 'true');
            window.location.reload();
        } catch (_) {
            $err.text('Network error.').removeClass('d-none');
        }
    });

    /* ================================================================
       CHECK AFTER LOGIN RELOAD
    ================================================================ */
    $(document).ready(async function () {
        if (sessionStorage.getItem('mx_after_login') === 'true') {
            sessionStorage.removeItem('mx_after_login');
            var planData = sessionStorage.getItem('mx_membership_plan');
            if (planData) {
                selectedPlan = JSON.parse(planData);
                window.MX_IS_LOGGED_IN = true;
                isGuestFlow = false;

                // Check membership before proceeding
                var existing = await checkExistingMembership();
                if (existing) {
                    showAlreadyMemberModal(existing);
                    return;
                }

                setTimeout(function () {
                    submitLoggedInRequestWithoutPayment();
                }, 300);
            }
        }
    });

    /* ================================================================
       AUTH — REGISTER
    ================================================================ */
    $('#mxRegisterForm').on('submit', async function (e) {
        e.preventDefault();
        var $err = $('#registerErrorMsg').addClass('d-none').text('');
        try {
            var res  = await fetch('/popup-register', {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.MX_CSRF,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    email:                 $(this).find('[name=email]').val(),
                    mobile_no:             $(this).find('[name=mobile_no]').val(),
                    password:              $(this).find('[name=password]').val(),
                    password_confirmation: $(this).find('[name=password_confirmation]').val(),
                }),
            });
            var data = await res.json().catch(function () { return {}; });
            if (!res.ok) {
                $err.text(
                    data.errors
                        ? Object.values(data.errors).flat().join(' ')
                        : (data.message || 'Registration failed.')
                ).removeClass('d-none');
                return;
            }
            bootstrap.Modal.getInstance(document.getElementById('mxAuthModal')).hide();
            sessionStorage.setItem('mx_membership_plan', JSON.stringify(selectedPlan));
            sessionStorage.setItem('mx_after_login', 'true');
            window.location.reload();
        } catch (_) {
            $err.text('Network error.').removeClass('d-none');
        }
    });

    /* ================================================================
       AUTH — GUEST FORM
    ================================================================ */
    $('#guestMemberForm').on('submit', async function (e) {
        e.preventDefault();
        var $err  = $('#guestMemberErrorMsg').addClass('d-none').text('');
        var name  = $('#guestMemberName').val().trim();
        var email = $('#guestMemberEmail').val().trim();
        var phone = $('#guestMemberPhone').val().replace(/\D/g, '');

        if (!name || !email) {
            $err.text('Please fill all fields.').removeClass('d-none'); return;
        }
        if (phone.length !== 10) {
            $err.text('Please enter a valid 10-digit US phone number.').removeClass('d-none'); return;
        }

        var formattedPhone = '+1' + phone;
        sessionStorage.setItem('mx_guest_member_name',  name);
        sessionStorage.setItem('mx_guest_member_email', email);
        sessionStorage.setItem('mx_guest_member_phone', formattedPhone);

        var planData = sessionStorage.getItem('mx_membership_plan');
        if (planData) { selectedPlan = JSON.parse(planData); }

        var $btn = $(this).find('button[type=submit]').prop('disabled', true);

        try {
            var res  = await fetch('/membership/guest-request', {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.MX_CSRF,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    membership_plan_id: selectedPlan.id,
                    guest_name:         name,
                    guest_email:        email,
                    guest_phone:        formattedPhone,
                    amount_paid:        selectedPlan.price,
                    payment_method:     'pending',
                }),
            });
            var data = await res.json();

            if (!res.ok || !data.status) {
                $err.text(data.message || 'Failed to submit request.').removeClass('d-none');
                $btn.prop('disabled', false);
                return;
            }

            currentGuestRequestId = data.request_id;
            sessionStorage.setItem('mx_guest_request_id', currentGuestRequestId);
            bootstrap.Modal.getInstance(document.getElementById('mxAuthModal')).hide();
            showGuestSuccessModal(name, email, formattedPhone);

        } catch (err) {
            $err.text('Network error. Please try again.').removeClass('d-none');
            $btn.prop('disabled', false);
        }
    });

    function showGuestSuccessModal(name, email, phone) {
        $('#mxGMsName').text(name);
        $('#mxGMsEmail').text(email);
        $('#mxGMsPhone').text(phone);
        $('#mxGMsPlan').text(selectedPlan.name);
        $('#mxGMsDuration').text(selectedPlan.duration_days + ' days');
        $('#mxGMsAmount').text('$' + parseFloat(selectedPlan.price).toFixed(2));
        openModal('#mxGuestMemberSuccessModal');
    }

    /* ================================================================
       PAYMENT MODAL (kept for future use)
    ================================================================ */
    function openMembershipPayModal() {
        if (!selectedPlan) return;
        var price = '$' + selectedPlan.price;
        $('#mxPayTitle').text('Membership Payment');
        $('#mxPayAmount').text(price);
        $('#mxPayBtnText').html('Pay <span id="mxPayBtnAmt">' + price + '</span>');
        $('#mxCardNum, #mxCardExp, #mxCardCvv, #mxCardName, #mxUpiId').val('');
        $('input[name="mxBank"]').prop('checked', false);
        $('#mxCardDisplay').text('•••• •••• •••• ••••');
        $('#mxCardNameDisplay').text('YOUR NAME');
        $('#mxCardExpDisplay').text('MM / YY');
        $('#mxPayError').addClass('d-none').text('');
        $('#mxPayNowBtn').prop('disabled', false);
        $('#mxPaySpinner').addClass('d-none');
        $('.mxs-pay-tab').removeClass('active').filter('[data-tab="card"]').addClass('active');
        $('.mxs-pay-panel').removeClass('active');
        $('#mxPayPanel-card').addClass('active');
        openModal('#mxPayModal');
    }

    /* ================================================================
       PAY BUTTON (kept for future)
    ================================================================ */
    $('#mxPayNowBtn').on('click', function () {
        simulateDemoPayment(function () {
            if (isGuestFlow) {
                processGuestPayment();
            } else {
                submitLoggedInRequest();
            }
        });
    });

    /* ================================================================
       DEMO PAYMENT SIMULATION
    ================================================================ */
    function simulateDemoPayment(onSuccess) {
        var $btn = $('#mxPayNowBtn');
        var $sp  = $('#mxPaySpinner');
        var $err = $('#mxPayError');

        if ($('.mxs-pay-tab.active').data('tab') === 'card') {
            if (!$('#mxCardNum').val().replace(/\s/g, '').match(/^\d{16}$/) ||
                !$('#mxCardExp').val().match(/\d{2}\s*\/\s*\d{2}/) ||
                !$('#mxCardCvv').val().match(/^\d{3}$/) ||
                !$('#mxCardName').val().trim()) {
                $err.text('Please fill all card details correctly.').removeClass('d-none');
                return;
            }
        }

        $err.addClass('d-none');
        $btn.prop('disabled', true);
        $('#mxPayBtnText').text('Processing…');
        $sp.removeClass('d-none');

        setTimeout(function () {
            $sp.addClass('d-none');
            $('#mxPayBtnText').text('✓ Payment Successful!');
            setTimeout(function () {
                closeModal('#mxPayModal');
                onSuccess();
            }, 700);
        }, 1800);
    }

    /* ================================================================
       GUEST PAYMENT
    ================================================================ */
    async function processGuestPayment() {
        var requestId = currentGuestRequestId || sessionStorage.getItem('mx_guest_request_id');
        var method    = $('.mxs-pay-tab.active').data('tab') || 'card';
        if (!requestId) { alert('Request ID not found. Please try again.'); return; }
        if (!selectedPlan) {
            var planData = sessionStorage.getItem('mx_membership_plan');
            if (planData) selectedPlan = JSON.parse(planData);
        }
        try {
            var res  = await fetch('/membership/guest-payment', {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.MX_CSRF,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    request_id:         requestId,
                    membership_plan_id: selectedPlan.id,
                    amount_paid:        selectedPlan.price,
                    payment_method:     method,
                }),
            });
            var data = await res.json();
            if (!res.ok || !data.status) { alert(data.message || 'Payment failed.'); return; }
            sessionStorage.removeItem('mx_membership_plan');
            sessionStorage.removeItem('mx_guest_member_name');
            sessionStorage.removeItem('mx_guest_member_email');
            sessionStorage.removeItem('mx_guest_member_phone');
            sessionStorage.removeItem('mx_guest_request_id');
            showLoggedInSuccessModal();
        } catch (err) {
            alert('Network error. Please try again.');
        }
    }

    /* ================================================================
       LOGGED-IN REQUEST (with payment modal — future use)
    ================================================================ */
    async function submitLoggedInRequest() {
        var method = $('.mxs-pay-tab.active').data('tab') || 'card';
        try {
            var res  = await fetch('/membership/request', {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.MX_CSRF,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    membership_plan_id: selectedPlan.id,
                    amount_paid:        selectedPlan.price,
                    payment_method:     method,
                }),
            });
            var data = await res.json();
            if (!res.ok || !data.status) { alert(data.message || 'Request failed.'); return; }
            sessionStorage.removeItem('mx_membership_plan');
            showLoggedInSuccessModal();
        } catch (err) {
            alert('Network error. Please try again.');
        }
    }

    /* ================================================================
       LOGGED-IN REQUEST WITHOUT PAYMENT (admin-approval flow, current default)
    ================================================================ */
    async function submitLoggedInRequestWithoutPayment() {
        try {
            var res  = await fetch('/membership/request', {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.MX_CSRF,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    membership_plan_id: selectedPlan.id,
                    amount_paid:        selectedPlan.price,
                    payment_method:     'pending',
                }),
            });

            // In submitLoggedInRequestWithoutPayment function, after the fetch:
if (!res.ok || !data.status) {
    if (res.status === 429 && data.pending_request) {
        showPendingRequestModal(data);
    } else {
        alert(data.message || 'Request failed.');
    }
    return;
}
            var data = await res.json();
            if (!res.ok || !data.status) { alert(data.message || 'Request failed.'); return; }
            sessionStorage.removeItem('mx_membership_plan');
            showLoggedInSuccessModal();
        } catch (err) {
            alert('Network error. Please try again.');
        }
    }

    function showLoggedInSuccessModal() {
        $('#mxMemberPlanName').text(selectedPlan.name);
        $('#mxMsName').text(selectedPlan.name);
        $('#mxMsDuration').text(selectedPlan.duration_days + ' days');
        $('#mxMsAmount').text('$' + parseFloat(selectedPlan.price).toFixed(2));
        openModal('#mxMemberSuccessModal');
    }

    /* ================================================================
       CARD FORMATTING
    ================================================================ */
    $('#mxCardNum').on('input', function () {
        var r = $(this).val().replace(/\D/g, '').slice(0, 16);
        $(this).val(r.match(/.{1,4}/g) ? r.match(/.{1,4}/g).join(' ') : r);
        var v = r.padEnd(16, '•').slice(0, 16).match(/.{1,4}/g);
        $('#mxCardDisplay').text(v ? v.join(' ') : '•••• •••• •••• ••••');
    });
    $('#mxCardName').on('input', function () {
        $('#mxCardNameDisplay').text($(this).val().toUpperCase() || 'YOUR NAME');
    });
    $('#mxCardExp').on('input', function () {
        var v = $(this).val().replace(/\D/g, '').slice(0, 4);
        if (v.length >= 3) v = v.slice(0, 2) + ' / ' + v.slice(2);
        $(this).val(v);
        $('#mxCardExpDisplay').text(v || 'MM / YY');
    });

    /* ================================================================
       PAY TABS & CLOSE
    ================================================================ */
    $(document).on('click', '.mxs-pay-tab', function () {
        $('.mxs-pay-tab').removeClass('active');
        $(this).addClass('active');
        $('.mxs-pay-panel').removeClass('active');
        $('#mxPayPanel-' + $(this).data('tab')).addClass('active');
    });
    $('#mxPayClose').on('click', function () { closeModal('#mxPayModal'); });
    $('#mxPayModal').on('click', function (e) {
        if ($(e.target).is('#mxPayModal')) closeModal('#mxPayModal');
    });

    /* ================================================================
       INIT
    ================================================================ */
    loadPlans();
});

/* ── Global close helpers (called from blade onclick) ── */
function closeMemberSuccessModal() {
    $('#mxMemberSuccessModal').removeClass('show').attr('aria-hidden', 'true');
}
function closeGuestMemberSuccessModal() {
    $('#mxGuestMemberSuccessModal').removeClass('show').attr('aria-hidden', 'true');
}
function closeAlreadyMemberModal() {
    $('#mxAlreadyMemberModal').removeClass('show').attr('aria-hidden', 'true');
}