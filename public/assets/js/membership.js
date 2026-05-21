// $(function () {

//     /* ================================================================
//        GLOBAL STATE
//     ================================================================ */
//     window.MX_CSRF = $('meta[name="csrf-token"]').attr('content') || '';
//     window.MX_IS_LOGGED_IN = (function () {
//         var v = $('#mx-auth-state').data('logged-in');
//         return (v === 1 || v === '1' || v === true);
//     }());

//     var selectedPlan = null;

//     /* ================================================================
//        MODAL HELPERS
//     ================================================================ */
//     function openModal(id)  { $(id).addClass('show').attr('aria-hidden', 'false'); }
//     function closeModal(id) { $(id).removeClass('show').attr('aria-hidden', 'true'); }
//     function openPayModal()  { openModal('#mxPayModal'); }
//     function closePayModal() { closeModal('#mxPayModal'); }

//     /* ================================================================
//        PHONE FORMATTING (same as booking)
//     ================================================================ */
//     $('#guestMemberPhone').on('input', function () {
//         var v = $(this).val().replace(/\D/g, '');
//         var f = '';
//         if (v.length > 0) f = '(' + v.substring(0, 3);
//         if (v.length >= 4) f += ') ' + v.substring(3, 6);
//         if (v.length >= 7) f += '-' + v.substring(6, 10);
//         $(this).val(f);
//     });

//     /* ================================================================
//        LOAD PLANS
//     ================================================================ */
//     async function loadPlans() {
//         try {
//             var res  = await fetch('/membership/plans', {
//                 method: 'GET',
//                 credentials: 'same-origin',
//                 headers: { 'Accept': 'application/json' },
//             });
//             var data = await res.json();
//             if (data.status && data.plans && data.plans.length > 0) {
//                 renderPlans(data.plans);
//             } else {
//                 $('#membershipPlansContainer').html(
//                     '<div style="grid-column:1/-1;text-align:center;color:#64748b;padding:40px 0;">No membership plans available.</div>'
//                 );
//             }
//         } catch (err) {
//             $('#membershipPlansContainer').html(
//                 '<div style="grid-column:1/-1;text-align:center;color:#dd2b31;padding:40px 0;">Failed to load plans.</div>'
//             );
//         }
//     }

//     /* ================================================================
//        RENDER PLANS
//     ================================================================ */
//     function renderPlans(plans) {
//         window.membershipPlans = plans;
//         var html = '';
//         plans.forEach(function (plan, idx) {
//             var features = [];
//             try { features = JSON.parse(plan.features || '[]'); } catch (e) { features = []; }
//             var featured  = idx === 1;
//             var featureItems = features.map(function (f) {
//                 return '<li><span class="feat-icon"><i class="fa-solid fa-check"></i></span>' + f + '</li>';
//             }).join('');

//             html += '<div class="plan-card ' + (featured ? 'featured' : '') + '">' +
//                 (featured ? '<div class="plan-badge">POPULAR</div>' : '') +
//                 '<div class="plan-name">' + plan.name + '</div>' +
//                 '<div class="plan-price-row">' +
//                     '<span class="plan-price-sym">$</span>' +
//                     '<span class="plan-price-val">' + plan.price + '</span>' +
//                 '</div>' +
//                 '<div class="plan-duration">Valid for ' + plan.duration_days + ' days</div>' +
//                 '<div class="plan-divider"></div>' +
//                 '<ul class="plan-features">' + featureItems + '</ul>' +
//                 '<button class="btn-plan ' + (featured ? 'btn-plan-primary' : 'btn-plan-outline') +
//                 '" data-plan-id="' + plan.id + '">Join Membership</button>' +
//             '</div>';
//         });
//         $('#membershipPlansContainer').html(html);
//     }

//     /* ================================================================
//        PLAN BUTTON CLICK
//     ================================================================ */
//     $(document).on('click', '.btn-plan[data-plan-id]', function () {
//         var planId = $(this).data('plan-id');
//         selectedPlan = (window.membershipPlans || []).find(function (p) { return p.id == planId; });
//         if (!selectedPlan) { alert('Plan not found.'); return; }

//         sessionStorage.setItem('mx_membership_plan', JSON.stringify(selectedPlan));

//         if (window.MX_IS_LOGGED_IN) {
//             openMembershipPayModal();
//         } else {
//             // Open auth modal with guest tab active (same as booking)
//             var modal = new bootstrap.Modal(document.getElementById('mxAuthModal'));
//             modal.show();
//             // Activate guest tab by default
//             var guestTab = document.getElementById('guestMemberTab');
//             bootstrap.Tab.getOrCreateInstance(guestTab).show();
//         }
//     });

//     /* ================================================================
//        OPEN PAYMENT MODAL
//     ================================================================ */
//     function openMembershipPayModal() {
//         if (!selectedPlan) return;
//         var price = '$' + selectedPlan.price;

//         $('#mxPayAmount').text(price);
//         $('#mxPayTitle').text('Membership Payment');

//         // Reset
//         $('#mxCardNum, #mxCardExp, #mxCardCvv, #mxCardName, #mxUpiId').val('');
//         $('input[name="mxBank"]').prop('checked', false);
//         $('#mxCardDisplay').text('•••• •••• •••• ••••');
//         $('#mxCardNameDisplay').text('YOUR NAME');
//         $('#mxCardExpDisplay').text('MM / YY');
//         $('#mxPayError').addClass('d-none').text('');
//         $('#mxPayNowBtn').prop('disabled', false);
//         $('#mxPaySpinner').addClass('d-none');
//         $('#mxPayBtnText').html('Pay <span id="mxPayBtnAmt">' + price + '</span>');
//         $('.mxs-pay-tab').removeClass('active').filter('[data-tab="card"]').addClass('active');
//         $('.mxs-pay-panel').removeClass('active');
//         $('#mxPayPanel-card').addClass('active');

//         openPayModal();
//     }

//     /* ================================================================
//        AUTH — LOGIN
//     ================================================================ */
//     $('#mxLoginForm').on('submit', async function (e) {
//         e.preventDefault();
//         var $err = $('#loginErrorMsg').addClass('d-none').text('');
//         try {
//             var res  = await fetch('/popup-login', {
//                 method: 'POST',
//                 credentials: 'same-origin',
//                 headers: {
//                     'Content-Type': 'application/json',
//                     'X-CSRF-TOKEN': window.MX_CSRF,
//                     'Accept': 'application/json',
//                 },
//                 body: JSON.stringify({
//                     email:    $(this).find('[name=email]').val(),
//                     password: $(this).find('[name=password]').val(),
//                 }),
//             });
//             var data = await res.json().catch(function () { return {}; });
//             if (!res.ok) { $err.text(data.message || 'Login failed.').removeClass('d-none'); return; }

//             window.MX_IS_LOGGED_IN = true;
//             var planData = sessionStorage.getItem('mx_membership_plan');
//             if (planData) { selectedPlan = JSON.parse(planData); }
//             bootstrap.Modal.getInstance(document.getElementById('mxAuthModal')).hide();
//             openMembershipPayModal();
//         } catch (_) {
//             $err.text('Network error.').removeClass('d-none');
//         }
//     });

//     /* ================================================================
//        AUTH — REGISTER
//     ================================================================ */
//     $('#mxRegisterForm').on('submit', async function (e) {
//         e.preventDefault();
//         var $err = $('#registerErrorMsg').addClass('d-none').text('');
//         try {
//             var res  = await fetch('/popup-register', {
//                 method: 'POST',
//                 credentials: 'same-origin',
//                 headers: {
//                     'Content-Type': 'application/json',
//                     'X-CSRF-TOKEN': window.MX_CSRF,
//                     'Accept': 'application/json',
//                 },
//                 body: JSON.stringify({
//                     email:                 $(this).find('[name=email]').val(),
//                     mobile_no:             $(this).find('[name=mobile_no]').val(),
//                     password:              $(this).find('[name=password]').val(),
//                     password_confirmation: $(this).find('[name=password_confirmation]').val(),
//                 }),
//             });
//             var data = await res.json().catch(function () { return {}; });
//             if (!res.ok) {
//                 $err.text(
//                     data.errors ? Object.values(data.errors).flat().join(' ') : (data.message || 'Registration failed.')
//                 ).removeClass('d-none');
//                 return;
//             }
//             window.MX_IS_LOGGED_IN = true;
//             var planData = sessionStorage.getItem('mx_membership_plan');
//             if (planData) { selectedPlan = JSON.parse(planData); }
//             bootstrap.Modal.getInstance(document.getElementById('mxAuthModal')).hide();
//             openMembershipPayModal();
//         } catch (_) {
//             $err.text('Network error.').removeClass('d-none');
//         }
//     });

//     /* ================================================================
//        AUTH — GUEST
//     ================================================================ */
//     $('#guestMemberForm').on('submit', function (e) {
//         e.preventDefault();
//         var $err  = $('#guestMemberErrorMsg').addClass('d-none').text('');
//         var name  = $('#guestMemberName').val().trim();
//         var email = $('#guestMemberEmail').val().trim();
//         var phone = $('#guestMemberPhone').val().replace(/\D/g, '');

//         if (!name || !email) {
//             $err.text('Please fill all fields.').removeClass('d-none'); return;
//         }
//         if (phone.length !== 10) {
//             $err.text('Please enter a valid 10-digit US phone number.').removeClass('d-none'); return;
//         }

//         sessionStorage.setItem('mx_guest_member_name',  name);
//         sessionStorage.setItem('mx_guest_member_email', email);
//         sessionStorage.setItem('mx_guest_member_phone', '+1' + phone);

//         var planData = sessionStorage.getItem('mx_membership_plan');
//         if (planData) { selectedPlan = JSON.parse(planData); }

//         bootstrap.Modal.getInstance(document.getElementById('mxAuthModal')).hide();
//         openMembershipPayModal();
//     });

//     /* ================================================================
//        PAYMENT TABS
//     ================================================================ */
//     $(document).on('click', '.mxs-pay-tab', function () {
//         var tab = $(this).data('tab');
//         $('.mxs-pay-tab').removeClass('active');
//         $(this).addClass('active');
//         $('.mxs-pay-panel').removeClass('active');
//         $('#mxPayPanel-' + tab).addClass('active');
//     });

//     /* ================================================================
//        CARD FORMATTING
//     ================================================================ */
//     $('#mxCardNum').on('input', function () {
//         var r = $(this).val().replace(/\D/g, '').slice(0, 16);
//         $(this).val(r.match(/.{1,4}/g) ? r.match(/.{1,4}/g).join(' ') : r);
//         var v = r.padEnd(16, '•').slice(0, 16).match(/.{1,4}/g);
//         $('#mxCardDisplay').text(v ? v.join(' ') : '•••• •••• •••• ••••');
//     });
//     $('#mxCardName').on('input', function () {
//         $('#mxCardNameDisplay').text($(this).val().toUpperCase() || 'YOUR NAME');
//     });
//     $('#mxCardExp').on('input', function () {
//         var v = $(this).val().replace(/\D/g, '').slice(0, 4);
//         if (v.length >= 3) v = v.slice(0, 2) + ' / ' + v.slice(2);
//         $(this).val(v);
//         $('#mxCardExpDisplay').text(v || 'MM / YY');
//     });

//     /* ================================================================
//        PAY MODAL CLOSE
//     ================================================================ */
//     $('#mxPayClose').on('click', closePayModal);
//     $('#mxPayModal').on('click', function (e) {
//         if ($(e.target).is('#mxPayModal')) closePayModal();
//     });

//     /* ================================================================
//        PAY BUTTON
//     ================================================================ */
//     $('#mxPayNowBtn').on('click', function () {
//         simulateDemoPayment(function () {
//             submitMembershipRequest();
//         });
//     });

//     /* ================================================================
//        DEMO PAYMENT (same as booking)
//     ================================================================ */
//     function simulateDemoPayment(onSuccess) {
//         var $btn = $('#mxPayNowBtn');
//         var $sp  = $('#mxPaySpinner');
//         var $err = $('#mxPayError');

//         if ($('.mxs-pay-tab.active').data('tab') === 'card') {
//             if (!$('#mxCardNum').val().replace(/\s/g, '').match(/^\d{16}$/) ||
//                 !$('#mxCardExp').val().match(/\d{2}\s*\/\s*\d{2}/) ||
//                 !$('#mxCardCvv').val().match(/^\d{3}$/) ||
//                 !$('#mxCardName').val().trim()) {
//                 $err.text('Please fill all card details correctly.').removeClass('d-none');
//                 return;
//             }
//         }

//         $err.addClass('d-none');
//         $btn.prop('disabled', true);
//         $('#mxPayBtnText').text('Processing…');
//         $sp.removeClass('d-none');

//         setTimeout(function () {
//             $sp.addClass('d-none');
//             $('#mxPayBtnText').text('✓ Payment Successful!');
//             setTimeout(function () {
//                 closePayModal();
//                 onSuccess();
//             }, 700);
//         }, 1800);
//     }

//     /* ================================================================
//        SUBMIT MEMBERSHIP REQUEST
//     ================================================================ */
//     async function submitMembershipRequest() {
//         if (!selectedPlan) { alert('Plan not found.'); return; }

//         var isGuest  = !window.MX_IS_LOGGED_IN;
//         var endpoint = isGuest ? '/membership/guest-request' : '/membership/request';
//         var method   = $('.mxs-pay-tab.active').data('tab');

//         var payload = {
//             membership_plan_id: selectedPlan.id,
//             amount_paid:        selectedPlan.price,
//             payment_method:     method,
//         };

//         if (isGuest) {
//             payload.guest_name  = sessionStorage.getItem('mx_guest_member_name')  || '';
//             payload.guest_email = sessionStorage.getItem('mx_guest_member_email') || '';
//             payload.guest_phone = sessionStorage.getItem('mx_guest_member_phone') || '';
//         }

//         try {
//             var res  = await fetch(endpoint, {
//                 method: 'POST',
//                 credentials: 'same-origin',
//                 headers: {
//                     'Content-Type': 'application/json',
//                     'X-CSRF-TOKEN': window.MX_CSRF,
//                     'Accept': 'application/json',
//                 },
//                 body: JSON.stringify(payload),
//             });
//             var data = await res.json();

//             if (!res.ok || !data.status) {
//                 alert(data.message || 'Request failed. Please try again.');
//                 return;
//             }

//             // Cleanup
//             sessionStorage.removeItem('mx_membership_plan');
//             sessionStorage.removeItem('mx_guest_member_name');
//             sessionStorage.removeItem('mx_guest_member_email');
//             sessionStorage.removeItem('mx_guest_member_phone');

//             // Show success modal
//             showMemberSuccessModal();

//         } catch (err) {
//             alert('Network error. Please try again.');
//         }
//     }

//     /* ================================================================
//        SUCCESS MODAL
//     ================================================================ */
//     function showMemberSuccessModal() {
//         if (!selectedPlan) return;
//         $('#mxMemberPlanName').text(selectedPlan.name);
//         $('#mxMsName').text(selectedPlan.name);
//         $('#mxMsDuration').text(selectedPlan.duration_days + ' days');
//         $('#mxMsAmount').text('$' + parseFloat(selectedPlan.price).toFixed(2));
//         openModal('#mxMemberSuccessModal');
//     }

//     /* ================================================================
//        INIT
//     ================================================================ */
//     loadPlans();
// });



$(function () {

    window.MX_CSRF = $('meta[name="csrf-token"]').attr('content') || '';
    window.MX_IS_LOGGED_IN = $('#mx-auth-state').data('logged-in') == '1';

    var selectedPlan = null;
    var currentGuestRequestId = null;

    /* ====================== MODAL HELPERS ====================== */
    function openModal(id)  { $(id).addClass('show').attr('aria-hidden', 'false'); }
    function closeModal(id) { $(id).removeClass('show').attr('aria-hidden', 'true'); }

    /* ====================== PHONE FORMATTING ====================== */
    $('#guestMemberPhone').on('input', function () {
        var v = $(this).val().replace(/\D/g, '');
        var f = '';
        if (v.length > 0) f = '(' + v.substring(0, 3);
        if (v.length >= 4) f += ') ' + v.substring(3, 6);
        if (v.length >= 7) f += '-' + v.substring(6, 10);
        $(this).val(f);
    });

    /* ====================== LOAD & RENDER PLANS ====================== */
    async function loadPlans() {
        try {
            var res = await fetch('/membership/plans', {
                method: 'GET',
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json' },
            });
            var data = await res.json();
            if (data.status && data.plans?.length) {
                renderPlans(data.plans);
            }
        } catch (e) {
            $('#membershipPlansContainer').html('<div style="grid-column:1/-1;text-align:center;color:#dd2b31;padding:40px 0;">Failed to load plans.</div>');
        }
    }

    function renderPlans(plans) {
        window.membershipPlans = plans;
        var html = '';
        plans.forEach(function (plan, idx) {
            var features = [];
            try { features = JSON.parse(plan.features || '[]'); } catch (e) { features = []; }
            var featured = idx === 1;
            var featureItems = features.map(f => 
                '<li><span class="feat-icon"><i class="fa-solid fa-check"></i></span>' + f + '</li>'
            ).join('');

            html += `<div class="plan-card ${featured ? 'featured' : ''}">
                ${featured ? '<div class="plan-badge">POPULAR</div>' : ''}
                <div class="plan-name">${plan.name}</div>
                <div class="plan-price-row">
                    <span class="plan-price-sym">$</span>
                    <span class="plan-price-val">${plan.price}</span>
                </div>
                <div class="plan-duration">Valid for ${plan.duration_days} days</div>
                <div class="plan-divider"></div>
                <ul class="plan-features">${featureItems}</ul>
                <button class="btn-plan ${featured ? 'btn-plan-primary' : 'btn-plan-outline'}" data-plan-id="${plan.id}">Join Membership</button>
            </div>`;
        });
        $('#membershipPlansContainer').html(html);
    }

    /* ====================== SELECT PLAN ====================== */
    $(document).on('click', '.btn-plan[data-plan-id]', function () {
        selectedPlan = window.membershipPlans?.find(p => p.id == $(this).data('plan-id'));
        if (!selectedPlan) return alert('Plan not found.');

        sessionStorage.setItem('mx_membership_plan', JSON.stringify(selectedPlan));

        if (window.MX_IS_LOGGED_IN) {
            openMembershipPayModal();
        } else {
            var modal = new bootstrap.Modal(document.getElementById('mxAuthModal'));
            modal.show();
            bootstrap.Tab.getOrCreateInstance(document.getElementById('guestMemberTab')).show();
        }
    });

    /* ====================== GUEST SUBMIT REQUEST ====================== */
    $('#guestMemberForm').on('submit', async function (e) {
        e.preventDefault();
        var $err = $('#guestMemberErrorMsg').addClass('d-none').text('');

        var name  = $('#guestMemberName').val().trim();
        var email = $('#guestMemberEmail').val().trim();
        var phone = $('#guestMemberPhone').val().replace(/\D/g, '');

        if (!name || !email || phone.length !== 10) {
            $err.text('Please fill all fields correctly.').removeClass('d-none');
            return;
        }

        try {
            var res = await fetch('/membership/guest-request', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.MX_CSRF,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    membership_plan_id: selectedPlan.id,
                    guest_name: name,
                    guest_email: email,
                    guest_phone: '+1' + phone,
                    amount_paid: selectedPlan.price
                })
            });

            var data = await res.json();

            if (!res.ok || !data.status) {
                $err.text(data.message || 'Failed to submit request').removeClass('d-none');
                return;
            }

            currentGuestRequestId = data.request_id || data.id;
            bootstrap.Modal.getInstance(document.getElementById('mxAuthModal')).hide();
            showGuestRequestSuccessModal();

        } catch (err) {
            $err.text('Network error. Please try again.').removeClass('d-none');
        }
    });

    function showGuestRequestSuccessModal() {
        $('#mxGuestMemberReqId').text(currentGuestRequestId || 'REQ-' + Date.now().toString().slice(-6));
        $('#mxGMsName').text($('#guestMemberName').val());
        $('#mxGMsEmail').text($('#guestMemberEmail').val());
        $('#mxGMsPhone').text($('#guestMemberPhone').val());
        $('#mxGMsPlan').text(selectedPlan.name);
        $('#mxGMsDuration').text(selectedPlan.duration_days + ' days');
        $('#mxGMsAmount').text('$' + parseFloat(selectedPlan.price).toFixed(2));

        openModal('#mxGuestMemberSuccessModal');
    }

    /* ====================== CONTINUE TO PAYMENT ====================== */
    $(document).on('click', '#continueToPaymentBtn', function () {
        closeModal('#mxGuestMemberSuccessModal');
        openMembershipPayModal();
    });

    /* ====================== PAYMENT MODAL ====================== */
    function openMembershipPayModal() {
        if (!selectedPlan) return;

        $('#mxPayTitle').text('Complete Payment');
        $('#mxPayAmount').text('$' + selectedPlan.price);
        $('#mxPayBtnAmt').text('$' + selectedPlan.price);

        $('#mxCardNum, #mxCardExp, #mxCardCvv, #mxCardName, #mxUpiId').val('');
        $('input[name="mxBank"]').prop('checked', false);
        $('#mxPayError').addClass('d-none').text('');

        openModal('#mxPayModal');
    }

    /* ====================== PAY BUTTON ====================== */
    $('#mxPayNowBtn').on('click', function () {
        simulateDemoPayment(async function () {
            await activateGuestMembership();
        });
    });

    /* ====================== DEMO PAYMENT FUNCTION (Fixed) ====================== */
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

    async function activateGuestMembership() {
        if (!currentGuestRequestId) return alert('Request ID not found');

        try {
            var res = await fetch('/membership/guest-payment', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.MX_CSRF,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    request_id: currentGuestRequestId,
                    membership_plan_id: selectedPlan.id,
                    amount_paid: selectedPlan.price,
                    payment_method: $('.mxs-pay-tab.active').data('tab')
                })
            });

            var data = await res.json();

            if (data.status) {
                showFinalActivatedSuccessModal();
            } else {
                alert(data.message || 'Activation failed');
            }
        } catch (e) {
            alert('Payment processing failed');
        }
    }

    function showFinalActivatedSuccessModal() {
        $('#mxMemberPlanName').text(selectedPlan.name);
        $('#mxMsName').text(selectedPlan.name);
        $('#mxMsDuration').text(selectedPlan.duration_days + ' days');
        $('#mxMsAmount').text('$' + parseFloat(selectedPlan.price).toFixed(2));
        openModal('#mxMemberSuccessModal');
    }

    /* ====================== CARD FORMATTING ====================== */
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

    /* Payment Tabs & Close */
    $(document).on('click', '.mxs-pay-tab', function () {
        $('.mxs-pay-tab').removeClass('active');
        $(this).addClass('active');
        $('.mxs-pay-panel').removeClass('active');
        $('#mxPayPanel-' + $(this).data('tab')).addClass('active');
    });

    $('#mxPayClose').on('click', () => closeModal('#mxPayModal'));
    $('#mxPayModal').on('click', function (e) {
        if ($(e.target).is('#mxPayModal')) closeModal('#mxPayModal');
    });

    /* ====================== INIT ====================== */
    loadPlans();
});