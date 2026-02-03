
// document.addEventListener("DOMContentLoaded", function () {
//   const navbar = document.querySelector(".navbar");

//   window.addEventListener("scroll", function () {
//     if (window.scrollY > 50) {
//       navbar.style.backgroundColor = "#1a1a1a";
//       navbar.style.boxShadow = "0 2px 10px rgba(0, 0, 0, 0.5)";
//     } else {
//       navbar.style.backgroundColor = "#2a2a2a";
//       navbar.style.boxShadow = "none";
//     }
//   });

//   const navLinks = document.querySelectorAll(".nav-link");
//   navLinks.forEach((link) => {
//     link.addEventListener("click", function (e) {
//       const href = this.getAttribute("href");
//       if (href.startsWith("#")) {
//         e.preventDefault();
//         const target = document.querySelector(href);
//         if (target) {
//           target.scrollIntoView({
//             behavior: "smooth",
//             block: "start",
//           });
//         }

//         const navbarCollapse = document.querySelector(".navbar-collapse");
//         if (navbarCollapse.classList.contains("show")) {
//           const bsCollapse = new bootstrap.Collapse(navbarCollapse);
//           bsCollapse.hide();
//         }
//       }
//     });
//   });

//   const serviceItems = document.querySelectorAll(".service-item");
//   serviceItems.forEach((item) => {
//     item.addEventListener("mouseenter", function () {
//       this.style.cursor = "pointer";
//     });
//   });

//   const buttons = document.querySelectorAll(".btn");
//   buttons.forEach((button) => {
//     button.addEventListener("click", function (e) {
//       const href = this.getAttribute("href");
//       if (href && href.startsWith("#")) {
//         e.preventDefault();
//         console.log("Button clicked:", href);
//       }
//     });
//   });
// });

document.addEventListener("click", (e) => {
  const item = e.target.closest(".service-item-custom");
  if (!item) return;

  const link = item.dataset.link;
  if (link) window.location.href = link;
});
//Routes for login & R
const routeEl = document.getElementById("mx-routes");
const LOGIN_URL = routeEl?.dataset?.loginUrl;
const REGISTER_URL = routeEl?.dataset?.registerUrl;
document.addEventListener("DOMContentLoaded", function () {

    const loginForm = document.getElementById("mxLoginForm");
    const registerForm = document.getElementById("mxRegisterForm");

    const loginErr = document.getElementById("mxLoginErr");
    const regErr = document.getElementById("mxRegErr");

    const authModalEl = document.getElementById("mxAuthModal");
    const authModal = authModalEl
        ? new bootstrap.Modal(authModalEl)
        : null;

    function mxHideErr(el) {
        if (!el) return;
        el.classList.add("d-none");
        el.innerText = "";
    }

    function mxShowErr(el, msg) {
        if (!el) return;
        el.innerText = msg;
        el.classList.remove("d-none");
    }

    // LOGIN
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
        authModal?.hide();

        // redirect
        window.location.href = "/";
    });

    // REGISTER
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
            let msg = data.message || "Register failed.";
            if (data.errors) {
                const firstKey = Object.keys(data.errors)[0];
                if (firstKey) msg = data.errors[firstKey][0];
            }
            mxShowErr(regErr, msg);
            return;
        }

        window.MX_IS_LOGGED_IN = true;
        authModal?.hide();

        window.location.href = "/";
    });

});

document.addEventListener("DOMContentLoaded", function () {

    const openRegisterBtn = document.getElementById("openRegister");

    const loginTab = document.getElementById("mxTabLogin");
    const registerTab = document.getElementById("mxTabRegister");

    const authModalEl = document.getElementById("mxAuthModal");
    const authModal = authModalEl
        ? new bootstrap.Modal(authModalEl)
        : null;

    openRegisterBtn?.addEventListener("click", function (e) {
        e.preventDefault();

        // open modal
        authModal?.show();

        // remove active from login
        loginTab.classList.remove("show", "active");

        // make register active
        registerTab.classList.add("show", "active");
    });

});


document.addEventListener("DOMContentLoaded", function () {

    const authModalEl = document.getElementById("mxAuthModal");
    const authModal = authModalEl
        ? new bootstrap.Modal(authModalEl)
        : null;

    const loginTabBtn = document.querySelector(
        'button[data-bs-target="#mxTabLogin"]'
    );
    const registerTabBtn = document.querySelector(
        'button[data-bs-target="#mxTabRegister"]'
    );

    // LOGIN click → Login tab
    document.getElementById("openLogin")?.addEventListener("click", function (e) {
        e.preventDefault();
        authModal?.show();
        bootstrap.Tab.getOrCreateInstance(loginTabBtn).show();
    });

    // SIGN UP click → Register tab
    document.getElementById("openRegister")?.addEventListener("click", function (e) {
        e.preventDefault();
        authModal?.show();
        bootstrap.Tab.getOrCreateInstance(registerTabBtn).show();
    });

});


document.addEventListener("DOMContentLoaded", function () {

    const authModalEl = document.getElementById("mxAuthModal");

    const registerForm = document.getElementById("mxRegisterForm");
    const regErr = document.getElementById("mxRegErr");

    const loginTabBtn = document.querySelector(
        'button[data-bs-target="#mxTabLogin"]'
    );

    if (!authModalEl) return;

    authModalEl.addEventListener("hidden.bs.modal", function () {

        //  Reset ONLY register form
        registerForm?.reset();

        //  Clear register errors only
        regErr && (regErr.innerText = "");
        regErr?.classList.add("d-none");

        //  Optional: go back to Login tab
        if (loginTabBtn) {
            bootstrap.Tab.getOrCreateInstance(loginTabBtn).show();
        }
    });

});



let timer = 60;

function startTimer(btn){
    btn.disabled = true;

    let t = setInterval(() => {
        timer--;
        btn.innerText = `Wait ${timer}s`;

        if(timer <= 0){
            clearInterval(t);
            btn.disabled = false;
            btn.innerText = "Resend OTP";
            timer = 60;
        }
    }, 1000);
}

document.addEventListener("DOMContentLoaded", function () {

    // ==============================
    // Setup
    // ==============================

    const csrf = document.querySelector('meta[name="csrf-token"]').content;

    const authModalEl = document.getElementById("mxAuthModal");
    const forgotModalEl = document.getElementById("mxForgotModal");

    const authModal = authModalEl ? new bootstrap.Modal(authModalEl) : null;
    const forgotModal = forgotModalEl ? new bootstrap.Modal(forgotModalEl) : null;

    const emailInput = document.getElementById("fpEmail");

    const sendBtn   = document.getElementById("sendOtpBtn");
    const verifyBtn = document.getElementById("verifyOtpBtn");
    const resetBtn  = document.getElementById("resetPassBtn");


    // ==============================
    // Open Forgot Modal
    // ==============================

    document.getElementById("mxForgotBtn")?.addEventListener("click", () => {
        
        authModal?.hide();
        forgotModal?.show();
        showStep("stepEmail");
    });


    // ==============================
    // Send OTP
    // ==============================

    sendBtn?.addEventListener("click", async () => {
    

        if (!emailInput.value) return alert("Enter email first");

        sendBtn.disabled = true;

        const res = await fetch('/forgot-password/send-otp', {
            method: 'POST',
            credentials: 'same-origin',   
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf
            },
            body: JSON.stringify({ email: emailInput.value })
        });

        sendBtn.disabled = false;

        if (!res.ok) {
            const data = await res.json().catch(() => ({}));
            return alert(data.error || data.message || "Failed to send OTP");
        }

        showStep('stepOtp');
        startTimer(sendBtn);
    });


    // ==============================
    // Verify OTP
    // ==============================

    verifyBtn?.addEventListener("click", async () => {

        const otp = document.getElementById('fpOtp').value;

        if (!otp) return alert("Enter OTP");

        const res = await fetch('/forgot-password/verify-otp', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf
            },
            body: JSON.stringify({
                email: emailInput.value,
                otp
            })
        });

        if (!res.ok) {
            const data = await res.json().catch(() => ({}));
            return alert(data.error || "Invalid / expired OTP");
        }

        showStep('stepReset');
    });


    // ==============================
    // Reset Password
    // ==============================

    resetBtn?.addEventListener("click", async () => {

        const pass  = document.getElementById('fpPass').value;
        const pass2 = document.getElementById('fpPass2').value;

        if (!pass || pass !== pass2) return alert("Passwords don't match");

        const res = await fetch('/forgot-password/reset', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf
            },
            body: JSON.stringify({
                email: emailInput.value,
                password: pass,
                password_confirmation: pass2
            })
        });

        if (!res.ok) {
            const data = await res.json().catch(() => ({}));
            return alert(data.error || "Reset failed");
        }

        alert("Password updated successfully 🎉");

        forgotModal?.hide();
        authModal?.show();
    });


    // ==============================
    // Helpers
    // ==============================

    function showStep(id) {
        ['stepEmail','stepOtp','stepReset'].forEach(s =>
            document.getElementById(s)?.classList.add('d-none')
        );
        document.getElementById(id)?.classList.remove('d-none');
    }

    function startTimer(btn) {
        let time = 60;

        btn.disabled = true;
        btn.innerText = `Wait ${time}s`;

        const t = setInterval(() => {
            time--;
            btn.innerText = `Wait ${time}s`;

            if (time <= 0) {
                clearInterval(t);
                btn.disabled = false;
                btn.innerText = "Resend OTP";
            }
        }, 1000);
    }

});
