$(function () {

    /* ================================================================
       GLOBAL STATE
    ================================================================ */
    window.MX_CSRF = $('meta[name="csrf-token"]').attr('content') || '';
    window.MX_IS_LOGGED_IN = (function () {
        var v = $('#mx-auth-state').data('logged-in');
        return (v === 1 || v === '1' || v === true);
    }());

    var selectedPlan = null;

    /* ================================================================
       TOAST
    ================================================================ */
    function showToast(msg, isError) {
        var $t = $('#mxToast');
        $('#mxToastMsg').text(msg);
        $t.css('border-color', isError ? '#dd2b31' : '#22c55e');
        $t.find('.mx-toast-icon')
            .removeClass('fa-circle-check fa-circle-xmark')
            .addClass(isError ? 'fa-circle-xmark' : 'fa-circle-check')
            .css('color', isError ? '#dd2b31' : '#22c55e');
        $t.addClass('visible');
        setTimeout(function () { $t.removeClass('visible'); }, 4500);
    }

    /* ================================================================
       PAY MODAL OPEN / CLOSE
    ================================================================ */
    function openPayModal() {
        $('#mxPayModal').addClass('show').attr('aria-hidden', 'false');
    }
    function closePayModal() {
        $('#mxPayModal').removeClass('show').attr('aria-hidden', 'true');
    }

    $('#mxPayClose').on('click', closePayModal);
    $('#mxPayModal').on('click', function (e) {
        if ($(e.target).is('#mxPayModal')) closePayModal();
    });

    /* ================================================================
       LOAD PLANS
    ================================================================ */
    async function loadPlans() {
        try {
            var res = await fetch('/membership/plans', {
                method: 'GET',
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json' },
            });
            var data = await res.json();

            if (data.status && data.plans && data.plans.length > 0) {
                renderPlans(data.plans);
            } else {
                $('#membershipPlansContainer').html(
                    '<div style="grid-column:1/-1;text-align:center;color:#64748b;padding:40px 0;">No membership plans available.</div>'
                );
            }
        } catch (err) {
            console.error('Error loading plans:', err);
            $('#membershipPlansContainer').html(
                '<div style="grid-column:1/-1;text-align:center;color:#dd2b31;padding:40px 0;">Failed to load plans. Please try again.</div>'
            );
        }
    }

    /* ================================================================
       RENDER PLANS
    ================================================================ */
    function renderPlans(plans) {
        window.membershipPlans = plans;
        var html = '';

        plans.forEach(function (plan, idx) {
            var features = [];
            try { features = JSON.parse(plan.features || '[]'); } catch (e) { features = []; }

            var featured = idx === 1;
            var featuredClass = featured ? 'featured' : '';
            var btnClass = featured ? 'btn-plan-primary' : 'btn-plan-outline';
            var badge = featured ? '<div class="plan-badge">POPULAR</div>' : '';
            var featureItems = features.map(function (f) {
                return '<li><span class="feat-icon"><i class="fa-solid fa-check"></i></span>' + f + '</li>';
            }).join('');

            html += '<div class="plan-card ' + featuredClass + '">' +
                badge +
                '<div class="plan-name">' + plan.name + '</div>' +
                '<div class="plan-price-row">' +
                    '<span class="plan-price-sym">$</span>' +
                    '<span class="plan-price-val">' + plan.price + '</span>' +
                '</div>' +
                '<div class="plan-duration">Valid for ' + plan.duration_days + ' days</div>' +
                '<div class="plan-divider"></div>' +
                '<ul class="plan-features">' + featureItems + '</ul>' +
                '<button class="btn-plan ' + btnClass + '" data-plan-id="' + plan.id + '">Join Membership</button>' +
            '</div>';
        });

        $('#membershipPlansContainer').html(html);
    }

    /* ================================================================
       PLAN BUTTON CLICK
    ================================================================ */
    $(document).on('click', '.btn-plan[data-plan-id]', function () {
        var planId = $(this).data('plan-id');
        selectedPlan = (window.membershipPlans || []).find(function (p) { return p.id == planId; });

        if (!selectedPlan) { showToast('Plan not found.', true); return; }

        if (window.MX_IS_LOGGED_IN) {
            openMembershipPayModal();
        } else {
            sessionStorage.setItem('mx_membership_plan', JSON.stringify(selectedPlan));
            new bootstrap.Modal(document.getElementById('mxAuthModal')).show();
        }
    });

    /* ================================================================
       OPEN PAYMENT MODAL
    ================================================================ */
    function openMembershipPayModal() {
        if (!selectedPlan) return;

        var price = '$' + selectedPlan.price;
        $('#mxPayAmount').text(price);
        $('#mxPayTitle').text('Membership Payment');

        // Reset inputs
        $('#mxCardNum, #mxCardExp, #mxCardCvv, #mxCardName, #mxUpiId').val('');
        $('input[name="mxBank"]').prop('checked', false);
        $('#mxCardDisplay').text('•••• •••• •••• ••••');
        $('#mxCardNameDisplay').text('YOUR NAME');
        $('#mxCardExpDisplay').text('MM / YY');
        $('#mxPayError').addClass('d-none').text('');
        $('#mxPayNowBtn').prop('disabled', false);
        $('#mxPaySpinner').addClass('d-none');
        $('#mxPayBtnText').html('Pay <span id="mxPayBtnAmt">' + price + '</span>');

        // Reset tabs to card
        $('.mxs-pay-tab').removeClass('active').filter('[data-tab="card"]').addClass('active');
        $('.mxs-pay-panel').removeClass('active');
        $('#mxPayPanel-card').addClass('active');

        openPayModal();
    }

    /* ================================================================
       PAYMENT TABS
    ================================================================ */
    $(document).on('click', '.mxs-pay-tab', function () {
        var tab = $(this).data('tab');
        $('.mxs-pay-tab').removeClass('active');
        $(this).addClass('active');
        $('.mxs-pay-panel').removeClass('active');
        $('#mxPayPanel-' + tab).addClass('active');
    });

    /* ================================================================
       CARD INPUT FORMATTING
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
       PAY BUTTON
    ================================================================ */
    $('#mxPayNowBtn').on('click', function () {
        simulateDemoPayment(function () {
            submitMembershipRequest();
        });
    });

    function simulateDemoPayment(onSuccess) {
        var $btn = $('#mxPayNowBtn');
        var $sp  = $('#mxPaySpinner');
        var $err = $('#mxPayError');

        // Validate card tab
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
                closePayModal();
                onSuccess();
            }, 700);
        }, 1800);
    }

    /* ================================================================
       SUBMIT MEMBERSHIP REQUEST
    ================================================================ */
    async function submitMembershipRequest() {
        if (!selectedPlan) { showToast('Plan not found.', true); return; }

        var isGuest  = !window.MX_IS_LOGGED_IN;
        var endpoint = isGuest ? '/membership/guest-request' : '/membership/request';
        var method   = $('.mxs-pay-tab.active').data('tab');

        var payload = {
            membership_plan_id: selectedPlan.id,
            amount_paid: selectedPlan.price,
            payment_method: method,
        };

        if (isGuest) {
            payload.guest_name  = sessionStorage.getItem('mx_guest_member_name')  || '';
            payload.guest_email = sessionStorage.getItem('mx_guest_member_email') || '';
            payload.guest_phone = sessionStorage.getItem('mx_guest_member_phone') || '';
        }

        try {
            var res  = await fetch(endpoint, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.MX_CSRF,
                    'Accept': 'application/json',
                },
                body: JSON.stringify(payload),
            });
            var data = await res.json();

            if (!res.ok || !data.status) {
                showToast(data.message || 'Request failed. Please try again.', true);
                return;
            }

            // Cleanup session
            sessionStorage.removeItem('mx_membership_plan');
            sessionStorage.removeItem('mx_guest_member_name');
            sessionStorage.removeItem('mx_guest_member_email');
            sessionStorage.removeItem('mx_guest_member_phone');

            showToast(data.message || 'Membership request submitted! Admin will review shortly.');

        } catch (err) {
            console.error('Submit error:', err);
            showToast('Network error. Please try again.', true);
        }
    }

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

            window.MX_IS_LOGGED_IN = true;
            var planData = sessionStorage.getItem('mx_membership_plan');
            if (planData) { selectedPlan = JSON.parse(planData); }

            bootstrap.Modal.getInstance(document.getElementById('mxAuthModal')).hide();
            openMembershipPayModal();

        } catch (_) {
            $err.text('Network error.').removeClass('d-none');
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

            window.MX_IS_LOGGED_IN = true;
            var planData = sessionStorage.getItem('mx_membership_plan');
            if (planData) { selectedPlan = JSON.parse(planData); }

            bootstrap.Modal.getInstance(document.getElementById('mxAuthModal')).hide();
            openMembershipPayModal();

        } catch (_) {
            $err.text('Network error.').removeClass('d-none');
        }
    });

    /* ================================================================
       AUTH — GUEST
    ================================================================ */
    $('#guestMemberForm').on('submit', function (e) {
        e.preventDefault();

        var name  = $('#guestMemberName').val().trim();
        var email = $('#guestMemberEmail').val().trim();
        var phone = $('#guestMemberPhone').val().trim();

        if (!name || !email || !phone) {
            $('#guestMemberErrorMsg').text('Please fill all fields.').removeClass('d-none');
            return;
        }

        sessionStorage.setItem('mx_guest_member_name',  name);
        sessionStorage.setItem('mx_guest_member_email', email);
        sessionStorage.setItem('mx_guest_member_phone', phone);

        var planData = sessionStorage.getItem('mx_membership_plan');
        if (planData) { selectedPlan = JSON.parse(planData); }

        bootstrap.Modal.getInstance(document.getElementById('mxAuthModal')).hide();
        openMembershipPayModal();
    });

    /* ================================================================
       INIT
    ================================================================ */
    loadPlans();
});