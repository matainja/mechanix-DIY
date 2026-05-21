@extends('layouts.main')

@section('title', 'Membership Plans – Mechanix D.I.Y.')

@push('styles')
<style>
/* ── Reset & base ────────────────────────────────── */
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

/* ── Page wrapper ────────────────────────────────── */
.mem-wrap {
    min-height: 100vh;
    background: #0a0a0a;
    padding: 80px 0 100px;
}

/* ── Hero ────────────────────────────────────────── */
.mem-hero {
    text-align: center;
    margin-bottom: 64px;
}
.mem-hero-eyebrow {
    display: inline-block;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 3px;
    text-transform: uppercase;
    color: #dd2b31;
    background: rgba(221,43,49,.1);
    border: 1px solid rgba(221,43,49,.25);
    padding: 6px 16px;
    border-radius: 20px;
    margin-bottom: 20px;
}
.mem-hero h1 {
    font-family: 'Bebas Neue', 'Impact', sans-serif;
    font-size: clamp(3rem, 7vw, 5.5rem);
    color: #fff;
    line-height: 1;
    letter-spacing: 2px;
    margin-bottom: 16px;
}
.mem-hero h1 span { color: #dd2b31; }
.mem-hero p {
    color: #94a3b8;
    font-size: 1.1rem;
    max-width: 480px;
    margin: 0 auto;
}

/* ── Cards grid ──────────────────────────────────── */
.mem-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 28px;
    max-width: 1100px;
    margin: 0 auto;
    padding: 0 20px;
}

/* ── Plan card ───────────────────────────────────── */
.plan-card {
    background: #111;
    border: 1px solid #1e1e1e;
    border-radius: 20px;
    padding: 40px 36px 36px;
    position: relative;
    transition: transform .35s ease, box-shadow .35s ease, border-color .35s ease;
    overflow: hidden;
}
.plan-card::before {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(ellipse at top left, rgba(221,43,49,.07) 0%, transparent 60%);
    pointer-events: none;
}
.plan-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 24px 60px rgba(0,0,0,.5);
    border-color: #333;
}
.plan-card.featured {
    border-color: #dd2b31;
    background: linear-gradient(160deg, #1a0e0e 0%, #0f0f0f 60%);
    box-shadow: 0 0 0 1px rgba(221,43,49,.3), 0 20px 60px rgba(221,43,49,.12);
}
.plan-card.featured:hover {
    transform: translateY(-10px);
    box-shadow: 0 0 0 1px rgba(221,43,49,.5), 0 28px 80px rgba(221,43,49,.2);
}

/* badge */
.plan-badge {
    position: absolute;
    top: 20px; right: 20px;
    background: #dd2b31;
    color: #fff;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 2px;
    text-transform: uppercase;
    padding: 4px 12px;
    border-radius: 20px;
}

/* plan header */
.plan-name {
    font-size: 1.1rem;
    font-weight: 700;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: 2px;
    margin-bottom: 20px;
}
.plan-price-row {
    display: flex;
    align-items: baseline;
    gap: 4px;
    margin-bottom: 6px;
}
.plan-price-sym { font-size: 1.6rem; font-weight: 700; color: #fff; }
.plan-price-val {
    font-family: 'Bebas Neue', 'Impact', sans-serif;
    font-size: 4rem;
    color: #fff;
    line-height: 1;
}
.plan-price-period { font-size: .9rem; color: #64748b; margin-left: 4px; }
.plan-duration {
    font-size: .85rem;
    color: #64748b;
    margin-bottom: 28px;
}

/* divider */
.plan-divider {
    height: 1px;
    background: #1e1e1e;
    margin-bottom: 24px;
}
.plan-card.featured .plan-divider {
    background: rgba(221,43,49,.2);
}

/* features */
.plan-features {
    list-style: none;
    margin-bottom: 32px;
}
.plan-features li {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #cbd5e1;
    font-size: .95rem;
    padding: 9px 0;
    border-bottom: 1px solid rgba(255,255,255,.04);
}
.plan-features li:last-child { border-bottom: none; }
.plan-features li .feat-icon {
    width: 18px; height: 18px;
    background: rgba(34,197,94,.15);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.plan-features li .feat-icon i { color: #22c55e; font-size: .6rem; }

/* CTA button */
.btn-plan {
    width: 100%;
    padding: 15px;
    font-size: 1rem;
    font-weight: 700;
    letter-spacing: 1px;
    text-transform: uppercase;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    transition: all .3s;
    position: relative;
    overflow: hidden;
}
.btn-plan::after {
    content: '';
    position: absolute;
    inset: 0;
    background: rgba(255,255,255,0);
    transition: background .2s;
}
.btn-plan:hover::after { background: rgba(255,255,255,.07); }

.btn-plan-primary {
    background: linear-gradient(135deg, #dd2b31 0%, #791218 100%);
    color: #fff;
    box-shadow: 0 6px 24px rgba(221,43,49,.35);
}
.btn-plan-primary:hover {
    transform: scale(1.03);
    box-shadow: 0 10px 32px rgba(221,43,49,.5);
}
.btn-plan-outline {
    background: transparent;
    border: 1.5px solid #333;
    color: #cbd5e1;
}
.btn-plan-outline:hover {
    border-color: #dd2b31;
    color: #fff;
}

/* ── Loading skeleton ────────────────────────────── */
.plan-skeleton {
    background: #111;
    border: 1px solid #1e1e1e;
    border-radius: 20px;
    padding: 40px 36px;
    animation: skelPulse 1.4s ease-in-out infinite;
}
.skel-line {
    background: #1e1e1e;
    border-radius: 6px;
    margin-bottom: 14px;
}
@keyframes skelPulse {
    0%, 100% { opacity: 1; }
    50% { opacity: .5; }
}

/* ════════════════════════════════════════════════════
   AUTH MODAL
════════════════════════════════════════════════════ */
#mxAuthModal .modal-content {
    background: #111;
    color: #fff;
    border: 1px solid #222;
    border-radius: 16px;
}
#mxAuthModal .nav-tabs {
    border-bottom: 1px solid #222;
}
#mxAuthModal .nav-link {
    color: #64748b;
    background: transparent;
    border: none;
    border-bottom: 2px solid transparent;
    border-radius: 0;
    padding: 10px 16px;
    font-size: .9rem;
    font-weight: 600;
    transition: color .2s;
}
#mxAuthModal .nav-link:hover { color: #cbd5e1; }
#mxAuthModal .nav-link.active {
    color: #dd2b31;
    border-bottom-color: #dd2b31;
    background: transparent;
}
#mxAuthModal .form-control {
    background: #1a1a1a;
    border: 1px solid #2a2a2a;
    color: #fff;
    border-radius: 8px;
}
#mxAuthModal .form-control:focus {
    background: #1f1f1f;
    border-color: #dd2b31;
    box-shadow: 0 0 0 3px rgba(221,43,49,.15);
    color: #fff;
}
.btn-auth {
    background: linear-gradient(135deg, #dd2b31 0%, #791218 100%);
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 13px;
    font-weight: 700;
    letter-spacing: .5px;
    width: 100%;
    transition: opacity .2s;
}
.btn-auth:hover { opacity: .9; }

/* ════════════════════════════════════════════════════
   PAY MODAL  (same style as reference)
════════════════════════════════════════════════════ */
.mx-modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.75);
    backdrop-filter: blur(4px);
    z-index: 1060;
    align-items: center;
    justify-content: center;
}
.mx-modal-overlay.show { display: flex; }

.mx-modal-card {
    background: #111;
    border: 1px solid #222;
    border-radius: 20px;
    width: 100%;
    max-width: 480px;
    margin: 16px;
    overflow: hidden;
}
.mx-modal-head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    padding: 24px 28px 20px;
    border-bottom: 1px solid #1e1e1e;
}
.mx-modal-title { font-size: 1.15rem; font-weight: 700; color: #fff; }
.mx-modal-sub { font-size: .85rem; color: #64748b; margin-top: 4px; }
.mx-modal-sub strong { color: #dd2b31; }
.mx-modal-x {
    background: none; border: none; color: #64748b;
    font-size: 1.6rem; line-height: 1; cursor: pointer;
    transition: color .2s;
}
.mx-modal-x:hover { color: #fff; }
.mx-modal-body { padding: 24px 28px; }
.mx-modal-actions {
    padding: 16px 28px 24px;
    display: flex; align-items: center; justify-content: space-between;
    border-top: 1px solid #1e1e1e;
}

/* pay tabs */
.mxs-pay-tabs { display: flex; gap: 8px; margin-bottom: 24px; }
.mxs-pay-tab {
    flex: 1; padding: 10px; background: #1a1a1a;
    border: 1px solid #2a2a2a; border-radius: 8px;
    color: #64748b; font-size: .8rem; font-weight: 600;
    cursor: pointer; transition: all .2s;
}
.mxs-pay-tab:hover { border-color: #444; color: #cbd5e1; }
.mxs-pay-tab.active {
    background: rgba(221,43,49,.1);
    border-color: #dd2b31;
    color: #dd2b31;
}
.mxs-pay-panel { display: none; }
.mxs-pay-panel.active { display: block; }

/* card preview */
.mx-card-preview {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
    border-radius: 14px;
    padding: 22px 24px;
    margin-bottom: 20px;
    position: relative;
    min-height: 120px;
}
.mx-card-chip {
    width: 36px; height: 26px;
    background: linear-gradient(135deg, #d4af37, #f5e6a3);
    border-radius: 5px;
    margin-bottom: 14px;
}
.mx-card-number-display {
    font-family: 'Courier New', monospace;
    font-size: 1.1rem;
    color: #fff;
    letter-spacing: 3px;
    margin-bottom: 14px;
}
.mx-card-bottom { display: flex; justify-content: space-between; }
.mx-card-meta-label { font-size: .65rem; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; }
.mx-card-meta-value { font-size: .85rem; color: #fff; font-weight: 600; }

/* pay inputs */
.mx-pay-fields { display: flex; flex-wrap: wrap; gap: 12px; }
.mx-field-wrap { display: flex; flex-direction: column; gap: 6px; }
.mx-field-wrap.full { width: 100%; }
.mx-field-wrap.half { flex: 1; min-width: 130px; }
.mx-field-wrap label { font-size: .75rem; color: #64748b; font-weight: 600; letter-spacing: .5px; text-transform: uppercase; }
.mx-pay-input {
    background: #1a1a1a;
    border: 1px solid #2a2a2a;
    color: #fff;
    border-radius: 8px;
    padding: 11px 14px;
    font-size: .9rem;
    outline: none;
    transition: border-color .2s, box-shadow .2s;
    width: 100%;
}
.mx-pay-input:focus {
    border-color: #dd2b31;
    box-shadow: 0 0 0 3px rgba(221,43,49,.15);
}
.mx-pay-input::placeholder { color: #334155; }

/* UPI */
.mx-upi-wrap { text-align: center; padding: 10px 0 20px; }
.mx-upi-icon { font-size: 2.5rem; color: #dd2b31; margin-bottom: 12px; }
.mx-upi-label { color: #64748b; font-size: .9rem; margin-bottom: 16px; }

/* Net banking */
.mx-nb-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
.mx-nb-option {
    display: flex; align-items: center; gap: 10px;
    background: #1a1a1a;
    border: 1px solid #2a2a2a;
    border-radius: 8px;
    padding: 12px 14px;
    cursor: pointer;
    transition: border-color .2s;
}
.mx-nb-option:has(input:checked) { border-color: #dd2b31; }
.mx-nb-option span { color: #cbd5e1; font-size: .9rem; font-weight: 600; }
.mx-nb-option input { accent-color: #dd2b31; }

/* pay error */
.mx-pay-error {
    background: rgba(221,43,49,.1);
    border: 1px solid rgba(221,43,49,.3);
    color: #fca5a5;
    border-radius: 8px;
    padding: 10px 14px;
    font-size: .85rem;
    margin-bottom: 16px;
}

/* secure badge */
.mx-secure-badge {
    display: flex; align-items: center; gap: 6px;
    color: #22c55e;
    font-size: .78rem;
    font-weight: 600;
}

/* pay button */
.mx-pay-btn {
    background: linear-gradient(135deg, #dd2b31 0%, #791218 100%);
    color: #fff;
    border: none;
    border-radius: 10px;
    padding: 13px 28px;
    font-size: .95rem;
    font-weight: 700;
    letter-spacing: .5px;
    cursor: pointer;
    display: flex; align-items: center; gap: 8px;
    transition: opacity .2s, transform .2s;
}
.mx-pay-btn:hover { opacity: .9; transform: scale(1.02); }
.mx-pay-btn:disabled { opacity: .6; transform: none; cursor: not-allowed; }

/* spinner */
.mx-spinner {
    width: 16px; height: 16px;
    border: 2px solid rgba(255,255,255,.3);
    border-top-color: #fff;
    border-radius: 50%;
    animation: spin .7s linear infinite;
    display: inline-block;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* success toast */
.mx-toast {
    position: fixed;
    bottom: 30px; right: 30px;
    background: #111;
    border: 1px solid #22c55e;
    border-radius: 12px;
    padding: 16px 20px;
    color: #fff;
    font-size: .9rem;
    display: flex; align-items: center; gap: 12px;
    box-shadow: 0 8px 32px rgba(0,0,0,.5);
    z-index: 2000;
    transform: translateY(80px);
    opacity: 0;
    transition: transform .4s ease, opacity .4s ease;
    max-width: 340px;
}
.mx-toast.visible { transform: translateY(0); opacity: 1; }
.mx-toast-icon { color: #22c55e; font-size: 1.3rem; flex-shrink: 0; }
</style>
@endpush

@section('content')

<main class="mem-wrap">
  <div class="container">

    {{-- Hero --}}
    <div class="mem-hero">
      <div class="mem-hero-eyebrow">Membership</div>
      <h1>Become a <span>Member</span></h1>
      <p>Get unlimited access to our premium facilities with exclusive member benefits.</p>
    </div>

    {{-- Plans Grid --}}
    <div class="mem-grid" id="membershipPlansContainer">
      {{-- Skeleton loaders while JS fetches --}}
      @for ($i = 0; $i < 3; $i++)
      <div class="plan-skeleton">
        <div class="skel-line" style="height:14px;width:40%;margin-bottom:20px;"></div>
        <div class="skel-line" style="height:52px;width:60%;margin-bottom:8px;"></div>
        <div class="skel-line" style="height:12px;width:30%;margin-bottom:30px;"></div>
        <div class="skel-line" style="height:12px;width:80%;"></div>
        <div class="skel-line" style="height:12px;width:70%;"></div>
        <div class="skel-line" style="height:12px;width:75%;margin-bottom:30px;"></div>
        <div class="skel-line" style="height:46px;width:100%;border-radius:10px;"></div>
      </div>
      @endfor
    </div>

  </div>
</main>

{{-- ═══════════════════════════════════════════════════
     AUTH MODAL
════════════════════════════════════════════════════ --}}
<div class="modal fade" id="mxAuthModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title text-white fw-bold">Continue to Purchase</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-3">
                <ul class="nav nav-tabs mb-0" id="authTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="loginTab" data-bs-toggle="tab"
                            data-bs-target="#loginTabPane" type="button" role="tab">Login</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="registerTab" data-bs-toggle="tab"
                            data-bs-target="#registerTabPane" type="button" role="tab">Register</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="guestMemberTab" data-bs-toggle="tab"
                            data-bs-target="#guestMemberPane" type="button" role="tab">Guest</button>
                    </li>
                </ul>

                <div class="tab-content pt-4" id="authTabsContent">

                    {{-- Login --}}
                    <div class="tab-pane fade show active" id="loginTabPane" role="tabpanel">
                        <div id="loginErrorMsg" class="alert alert-danger d-none mb-3"></div>
                        <form id="mxLoginForm">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label small text-white">Email</label>
                                <input type="email" class="form-control" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small text-white">Password</label>
                                <input type="password" class="form-control" name="password" required>
                            </div>
                            <button type="submit" class="btn-auth mt-2">Login</button>
                        </form>
                    </div>

                    {{-- Register --}}
                    <div class="tab-pane fade" id="registerTabPane" role="tabpanel">
                        <div id="registerErrorMsg" class="alert alert-danger d-none mb-3"></div>
                        <form id="mxRegisterForm">
                            @csrf
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label small text-white">Email</label>
                                    <input type="email" class="form-control form-control-sm" name="email" required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label small text-white">Mobile</label>
                                    <input type="text" class="form-control form-control-sm" name="mobile_no" required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label small text-white">Password</label>
                                    <input type="password" class="form-control form-control-sm" name="password" required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label small text-white">Confirm</label>
                                    <input type="password" class="form-control form-control-sm" name="password_confirmation" required>
                                </div>
                            </div>
                            <button type="submit" class="btn-auth mt-3">Create Account</button>
                        </form>
                    </div>

                    {{-- Guest --}}
                    <div class="tab-pane fade" id="guestMemberPane" role="tabpanel">
                        <div id="guestMemberErrorMsg" class="alert alert-danger d-none mb-3"></div>
                        <div class="alert mb-3" style="background:#1a2a1a;border:1px solid #2a4a2a;color:#86efac;font-size:13px;">
                            <i class="fa-solid fa-info-circle me-1"></i>
                            Your request will be sent to admin for approval after payment.
                        </div>
                        <form id="guestMemberForm">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label small text-white">Full Name</label>
                                <input type="text" class="form-control" id="guestMemberName" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small text-white">Email</label>
                                <input type="email" class="form-control" id="guestMemberEmail" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small text-white">Phone</label>
                                <input type="tel" class="form-control" id="guestMemberPhone" required>
                            </div>
                            <button type="submit" class="btn-auth">Continue to Payment</button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════
     PAYMENT MODAL
════════════════════════════════════════════════════ --}}
<div id="mxPayModal" class="mx-modal-overlay" aria-hidden="true">
    <div class="mx-modal-card" role="dialog" aria-modal="true">
        <div class="mx-modal-head">
            <div>
                <div id="mxPayTitle" class="mx-modal-title">Membership Payment</div>
                <div class="mx-modal-sub">Amount due: <strong id="mxPayAmount">$0</strong></div>
            </div>
            <button type="button" class="mx-modal-x" id="mxPayClose">×</button>
        </div>
        <div class="mx-modal-body">
            <div class="mxs-pay-tabs">
                <button type="button" class="mxs-pay-tab active" data-tab="card">
                    <i class="fa-regular fa-credit-card"></i> Card
                </button>
                <button type="button" class="mxs-pay-tab" data-tab="upi">
                    <i class="fa-solid fa-mobile-screen-button"></i> UPI
                </button>
                <button type="button" class="mxs-pay-tab" data-tab="netbanking">
                    <i class="fa-solid fa-building-columns"></i> Net Banking
                </button>
            </div>

            <div id="mxPayError" class="mx-pay-error d-none"></div>

            {{-- Card --}}
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
                    <div class="mx-field-wrap full">
                        <label>Card Number</label>
                        <input type="text" id="mxCardNum" class="mx-pay-input" placeholder="1234 5678 9012 3456" maxlength="19">
                    </div>
                    <div class="mx-field-wrap full">
                        <label>Cardholder Name</label>
                        <input type="text" id="mxCardName" class="mx-pay-input" placeholder="Name on card">
                    </div>
                    <div class="mx-field-wrap half">
                        <label>Expiry</label>
                        <input type="text" id="mxCardExp" class="mx-pay-input" placeholder="MM / YY" maxlength="7">
                    </div>
                    <div class="mx-field-wrap half">
                        <label>CVV</label>
                        <input type="password" id="mxCardCvv" class="mx-pay-input" placeholder="•••" maxlength="3">
                    </div>
                </div>
            </div>

            {{-- UPI --}}
            <div class="mxs-pay-panel" id="mxPayPanel-upi">
                <div class="mx-upi-wrap">
                    <i class="fa-solid fa-mobile-screen-button mx-upi-icon"></i>
                    <p class="mx-upi-label">Enter your UPI ID</p>
                    <input type="text" class="mx-pay-input" id="mxUpiId" placeholder="yourname@upi">
                </div>
            </div>

            {{-- Net Banking --}}
            <div class="mxs-pay-panel" id="mxPayPanel-netbanking">
                <div class="mx-nb-grid">
                    @foreach (['SBI', 'HDFC', 'ICICI', 'Axis'] as $bank)
                    <label class="mx-nb-option">
                        <input type="radio" name="mxBank" value="{{ strtolower($bank) }}">
                        <span>{{ $bank }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="mx-modal-actions">
            <div class="mx-secure-badge"><i class="fa-solid fa-shield-halved"></i> Secured</div>
            <button type="button" class="mx-pay-btn" id="mxPayNowBtn">
                <span id="mxPayBtnText">Pay <span id="mxPayBtnAmt">$0</span></span>
                <span id="mxPaySpinner" class="mx-spinner d-none"></span>
            </button>
        </div>
    </div>
</div>

{{-- Auth state for JS --}}
<div id="mx-auth-state" data-logged-in="{{ auth()->check() ? '1' : '0' }}"></div>

{{-- Success toast --}}
<div class="mx-toast" id="mxToast">
    <i class="fa-solid fa-circle-check mx-toast-icon"></i>
    <div id="mxToastMsg">Membership request submitted! Admin will review shortly.</div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('assets/js/membership.js') }}"></script>
@endpush