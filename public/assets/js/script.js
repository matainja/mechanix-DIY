// =====================================================
// MECHANIX - MAIN JAVASCRIPT FILE
// All functionality organized and separated
// =====================================================

(function() {
    'use strict';

    // NEW ADD
    const forgotPassword = document.getElementById('forgotPasswordBtn');
    if (forgotPassword) {
        forgotPassword.style.display='none';
    }

    // =====================================================
    // GLOBAL VARIABLES
    // =====================================================
    let authModalInstance = null;
    let forgotPasswordModalInstance = null;
    let otpTimerInterval = null;

    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content || '';
    
    // Route URLs
    const routeElement = document.getElementById('mx-routes');
    const LOGIN_URL = routeElement?.dataset?.loginUrl || '';
    const REGISTER_URL = routeElement?.dataset?.registerUrl || '';

    // =====================================================
    // UTILITY FUNCTIONS
    // =====================================================
    const Utils = {
        showError: function(element, message) {
            if (!element) return;
            element.textContent = message;
            element.classList.remove('d-none');
        },

        hideError: function(element) {
            if (!element) return;
            element.textContent = '';
            element.classList.add('d-none');
        },

        closeNavbar: function() {
            const navbarCollapse = document.getElementById('navbarNav');
            if (navbarCollapse && window.innerWidth < 992) {
                const bsCollapse = bootstrap.Collapse.getInstance(navbarCollapse);
                if (bsCollapse) {
                    bsCollapse.hide();
                }
            }
        },

        showForgotStep: function(stepId) {
            const steps = ['fpStepEmail', 'fpStepOtp', 'fpStepReset'];
            steps.forEach(step => {
                const element = document.getElementById(step);
                if (element) {
                    element.classList.add('d-none');
                }
            });
            const targetStep = document.getElementById(stepId);
            if (targetStep) {
                targetStep.classList.remove('d-none');
            }
        }
    };

    // =====================================================
    // MODAL HANDLER
    // =====================================================
    const ModalHandler = {
        init: function() {
            const authModalEl = document.getElementById('mxAuthModal');
            const forgotModalEl = document.getElementById('forgotPasswordModal');

            if (authModalEl) {
                authModalInstance = new bootstrap.Modal(authModalEl);
                
                // Reset form when modal closes
                authModalEl.addEventListener('hidden.bs.modal', function() {
                    ModalHandler.resetAuthModal();
                });
            }

            if (forgotModalEl) {
                forgotPasswordModalInstance = new bootstrap.Modal(forgotModalEl);
                
                // Reset forgot password flow when modal closes
                forgotModalEl.addEventListener('hidden.bs.modal', function() {
                    ForgotPasswordHandler.reset();
                });
            }
        },

        showAuthModal: function(tabType = 'login') {
            if (!authModalInstance) return;

            const loginTabBtn = document.getElementById('loginTab');
            const registerTabBtn = document.getElementById('registerTab');

            if (tabType === 'register' && registerTabBtn) {
                const registerTabInstance = new bootstrap.Tab(registerTabBtn);
                registerTabInstance.show();
            } else if (loginTabBtn) {
                const loginTabInstance = new bootstrap.Tab(loginTabBtn);
                loginTabInstance.show();
            }

            authModalInstance.show();
        },

        hideAuthModal: function() {
            if (authModalInstance) {
                authModalInstance.hide();
            }
        },

        showForgotModal: function() {
            if (forgotPasswordModalInstance) {
                forgotPasswordModalInstance.show();
                Utils.showForgotStep('fpStepEmail');
            }
        },

        hideForgotModal: function() {
            if (forgotPasswordModalInstance) {
                forgotPasswordModalInstance.hide();
            }
        },

        resetAuthModal: function() {
            const registerForm = document.getElementById('registerFormMain');
            const loginForm = document.getElementById('loginFormMain');
            const registerError = document.getElementById('registerErrorMsg');
            const loginError = document.getElementById('loginErrorMsg');

            if (registerForm) registerForm.reset();
            if (loginForm) loginForm.reset();
            if (registerError) Utils.hideError(registerError);
            if (loginError) Utils.hideError(loginError);

            // Switch back to login tab
            const loginTabBtn = document.getElementById('loginTab');
            if (loginTabBtn) {
                const loginTabInstance = new bootstrap.Tab(loginTabBtn);
                loginTabInstance.show();
            }
        }
    };

    // =====================================================
    // LOGIN HANDLER
    // =====================================================
    const LoginHandler = {
        init: function() {
            const loginForm = document.getElementById('loginFormMain');
            if (!loginForm) return;

            loginForm.addEventListener('submit', LoginHandler.handleSubmit);
        },

        handleSubmit: async function(e) {
            e.preventDefault();
             // If membership.js is active on this page, let it handle login
    if (window.MX_MEMBERSHIP_PAGE) return;
            const loginError = document.getElementById('loginErrorMsg');
            Utils.hideError(loginError);

            const formData = new FormData(e.target);

            try {
                const response = await fetch(LOGIN_URL, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await response.json().catch(() => ({}));

                if (!response.ok) {
                    Utils.showError(loginError, data.message || 'Login failed. Please try again.');
                    return;
                }

                // Success
                ModalHandler.hideAuthModal();
                window.location.href = '/admin';

            } catch (error) {
                Utils.showError(loginError, 'Network error. Please try again.');
                console.error('Login error:', error);
            }
        }
    };

    // =====================================================
    // REGISTER HANDLER
    // =====================================================
    const RegisterHandler = {
        init: function() {
            const registerForm = document.getElementById('registerFormMain');
            if (!registerForm) return;

            registerForm.addEventListener('submit', RegisterHandler.handleSubmit);
        },

        handleSubmit: async function(e) {
            e.preventDefault();
             // If membership.js is active on this page, let it handle login
    if (window.MX_MEMBERSHIP_PAGE) return;
            const registerError = document.getElementById('registerErrorMsg');
            Utils.hideError(registerError);

            const formData = new FormData(e.target);

            try {
                const response = await fetch(REGISTER_URL, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await response.json().catch(() => ({}));

                if (!response.ok) {
                    let errorMessage = data.message || 'Registration failed. Please try again.';
                    
                    // Handle validation errors
                    if (data.errors) {
                        const firstErrorKey = Object.keys(data.errors)[0];
                        if (firstErrorKey && data.errors[firstErrorKey][0]) {
                            errorMessage = data.errors[firstErrorKey][0];
                        }
                    }
                    
                    Utils.showError(registerError, errorMessage);
                    return;
                }

                // Success
                ModalHandler.hideAuthModal();
                window.location.href = '/';

            } catch (error) {
                Utils.showError(registerError, 'Network error. Please try again.');
                console.error('Register error:', error);
            }
        }
    };

    // =====================================================
    // AUTH BUTTON HANDLERS
    // =====================================================
    const AuthButtonHandler = {
        init: function() {
            // Mobile buttons
            const mobileLoginBtn = document.getElementById('mobileLoginBtn');
            const mobileRegisterBtn = document.getElementById('mobileRegisterBtn');

            // Desktop buttons
            const desktopLoginBtn = document.getElementById('desktopLoginBtn');
            const desktopRegisterBtn = document.getElementById('desktopRegisterBtn');

            // Login buttons
            if (mobileLoginBtn) {
                mobileLoginBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    Utils.closeNavbar();
                    ModalHandler.showAuthModal('login');
                });
            }

            if (desktopLoginBtn) {
                desktopLoginBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    ModalHandler.showAuthModal('login');
                });
            }

            // Register buttons
            if (mobileRegisterBtn) {
                mobileRegisterBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    Utils.closeNavbar();
                    ModalHandler.showAuthModal('register');
                });
            }

            if (desktopRegisterBtn) {
                desktopRegisterBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    ModalHandler.showAuthModal('register');
                });
            }

            // Forgot password button
            const forgotPasswordBtn = document.getElementById('forgotPasswordBtn');
            if (forgotPasswordBtn) {
                forgotPasswordBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    ModalHandler.hideAuthModal();
                    ModalHandler.showForgotModal();
                });
            }
        }
    };

    // =====================================================
    // FORGOT PASSWORD HANDLER
    // =====================================================
    const ForgotPasswordHandler = {
        init: function() {
            const sendOtpBtn = document.getElementById('fpSendOtpBtn');
            const verifyOtpBtn = document.getElementById('fpVerifyOtpBtn');
            const resendOtpBtn = document.getElementById('fpResendOtpBtn');
            const resetPasswordBtn = document.getElementById('fpResetPasswordBtn');

            if (sendOtpBtn) {
                sendOtpBtn.addEventListener('click', ForgotPasswordHandler.sendOtp);
            }

            if (verifyOtpBtn) {
                verifyOtpBtn.addEventListener('click', ForgotPasswordHandler.verifyOtp);
            }

            if (resendOtpBtn) {
                resendOtpBtn.addEventListener('click', ForgotPasswordHandler.resendOtp);
            }

            if (resetPasswordBtn) {
                resetPasswordBtn.addEventListener('click', ForgotPasswordHandler.resetPassword);
            }
        },

        sendOtp: async function() {
            const emailInput = document.getElementById('fpEmailInput');
            const sendBtn = document.getElementById('fpSendOtpBtn');
            const loader = document.getElementById('fpOtpLoader');
            const btnText = sendBtn?.querySelector('.btn-text');

            if (!emailInput || !emailInput.value.trim()) {
                alert('Please enter your email address');
                return;
            }

            // Show loader
            if (sendBtn) sendBtn.disabled = true;
            if (loader) loader.classList.remove('d-none');
            if (btnText) btnText.textContent = 'Sending...';

            try {
                const response = await fetch('/forgot-password/send-otp', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    },
                    body: JSON.stringify({ email: emailInput.value.trim() })
                });

                const data = await response.json().catch(() => ({}));

                if (!response.ok) {
                    alert(data.error || data.message || 'Failed to send OTP. Please try again.');
                    return;
                }

                // Success - move to OTP step
                Utils.showForgotStep('fpStepOtp');
                ForgotPasswordHandler.startOtpTimer();

            } catch (error) {
                alert('Network error. Please try again.');
                console.error('Send OTP error:', error);
            } finally {
                // Hide loader
                if (sendBtn) sendBtn.disabled = false;
                if (loader) loader.classList.add('d-none');
                if (btnText) btnText.textContent = 'Send OTP';
            }
        },

        verifyOtp: async function() {
            const emailInput = document.getElementById('fpEmailInput');
            const otpInput = document.getElementById('fpOtpInput');

            if (!otpInput || !otpInput.value.trim()) {
                alert('Please enter the OTP');
                return;
            }

            try {
                const response = await fetch('/forgot-password/verify-otp', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    },
                    body: JSON.stringify({
                        email: emailInput.value.trim(),
                        otp: otpInput.value.trim()
                    })
                });

                if (!response.ok) {
                    alert('Invalid or expired OTP. Please try again.');
                    return;
                }

                // Success - move to reset password step
                Utils.showForgotStep('fpStepReset');

            } catch (error) {
                alert('Network error. Please try again.');
                console.error('Verify OTP error:', error);
            }
        },

        resendOtp: function() {
            ForgotPasswordHandler.sendOtp();
        },

        resetPassword: async function() {
            const emailInput = document.getElementById('fpEmailInput');
            const newPassword = document.getElementById('fpNewPassword');
            const confirmPassword = document.getElementById('fpConfirmPassword');

            if (!newPassword || !confirmPassword) return;

            if (!newPassword.value || !confirmPassword.value) {
                alert('Please fill in both password fields');
                return;
            }

            if (newPassword.value !== confirmPassword.value) {
                alert('Passwords do not match');
                return;
            }

            try {
                const response = await fetch('/forgot-password/reset', {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    },
                    body: JSON.stringify({
                        email: emailInput.value.trim(),
                        password: newPassword.value,
                        password_confirmation: confirmPassword.value
                    })
                });

                if (!response.ok) {
                    alert('Failed to reset password. Please try again.');
                    return;
                }

                // Success
                alert('Password updated successfully! 🎉');
                ModalHandler.hideForgotModal();
                ForgotPasswordHandler.reset();
                ModalHandler.showAuthModal('login');

            } catch (error) {
                alert('Network error. Please try again.');
                console.error('Reset password error:', error);
            }
        },

        startOtpTimer: function() {
            const resendBtn = document.getElementById('fpResendOtpBtn');
            if (!resendBtn) return;

            // Clear any existing timer
            if (otpTimerInterval) {
                clearInterval(otpTimerInterval);
            }

            let timeLeft = 60;
            resendBtn.disabled = true;
            resendBtn.textContent = `Wait ${timeLeft}s`;

            otpTimerInterval = setInterval(function() {
                timeLeft--;

                if (timeLeft <= 0) {
                    clearInterval(otpTimerInterval);
                    resendBtn.disabled = false;
                    resendBtn.textContent = 'Resend OTP';
                    return;
                }

                resendBtn.textContent = `Wait ${timeLeft}s`;
            }, 1000);
        },

        reset: function() {
            // Clear timer
            if (otpTimerInterval) {
                clearInterval(otpTimerInterval);
                otpTimerInterval = null;
            }

            // Reset all inputs
            const fpEmailInput = document.getElementById('fpEmailInput');
            const fpOtpInput = document.getElementById('fpOtpInput');
            const fpNewPassword = document.getElementById('fpNewPassword');
            const fpConfirmPassword = document.getElementById('fpConfirmPassword');

            if (fpEmailInput) fpEmailInput.value = '';
            if (fpOtpInput) fpOtpInput.value = '';
            if (fpNewPassword) fpNewPassword.value = '';
            if (fpConfirmPassword) fpConfirmPassword.value = '';

            // Reset resend button
            const resendBtn = document.getElementById('fpResendOtpBtn');
            if (resendBtn) {
                resendBtn.disabled = false;
                resendBtn.textContent = 'Resend OTP';
            }

            // Show first step
            Utils.showForgotStep('fpStepEmail');
        }
    };

    // =====================================================
    // SERVICE ITEMS HANDLER
    // =====================================================
    const ServiceHandler = {
        init: function() {
            document.addEventListener('click', function(e) {
                const serviceItem = e.target.closest('.service-item-custom');
                if (!serviceItem) return;

                const link = serviceItem.dataset.link;
                if (link) {
                    window.location.href = link;
                }
            });
        }
    };

    // =====================================================
    // INITIALIZE ALL
    // =====================================================
    function init() {
        ModalHandler.init();
        LoginHandler.init();
        RegisterHandler.init();
        AuthButtonHandler.init();
        ForgotPasswordHandler.init();
        ServiceHandler.init();
    }

    // =====================================================
    // DOM READY
    // =====================================================
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();
function closeGuestMemberSuccessModal() {

    $('#mxGuestMemberSuccessModal')
        .removeClass('show')
        .attr('aria-hidden', 'true');
}

// NEW ADD

$('#mxAuthModal').on('hidden.bs.modal', function () {

    // Reset forms
    $('#guestBookingForm')[0].reset();
    $('#mxLoginForm')[0].reset();
    $('#mxRegisterForm')[0].reset();

    // Clear errors
    $('#guestErrorMsg,#loginErrorMsg,#registerErrorMsg')
        .addClass('d-none')
        .text('');

    // Re-enable buttons
    $('#mxAuthModal button[type=submit]')
        .prop('disabled', false);

    // Default tab
    $('#guestTab').tab('show');
});