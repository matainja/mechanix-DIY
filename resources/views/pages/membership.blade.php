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
    font-size: clamp(1.7rem, 2.5vw, 2.2rem);
    color: #fff;
    line-height: 1;
    letter-spacing: 1px;
    margin-bottom: 12px;
}
.mem-hero h1 span { color: #dd2b31; }
.mem-hero p {
    color: #94a3b8;
    font-size: 1.1rem;
    max-width: 480px;
    margin: 0 auto;
}

/* ── Cards grid → List layout ────────────────────── */
.mem-grid {
    display: flex;
    flex-direction: column;
    gap: 20px;
    max-width: 1250px;
    margin: 0 auto;
    padding: 0 24px;
    width: 100%;
}


/* ── Plan card → Plan row ────────────────────────── */
.plan-card {
    background: #111;
    border: 1px solid #1e1e1e;
    border-radius: 18px;
    padding: 34px 42px;
    min-height: 150px;
    position: relative;
    display: flex;
    align-items: center;
    gap: 42px;
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
    transform: translateX(4px);
    box-shadow: 0 12px 40px rgba(0,0,0,.4);
    border-color: #333;
}
.plan-card.featured {
    border-color: #dd2b31;
    background: linear-gradient(160deg, #1a0e0e 0%, #0f0f0f 60%);
    box-shadow: 0 0 0 1px rgba(221,43,49,.3), 0 8px 40px rgba(221,43,49,.12);
}
.plan-card.featured:hover {
    transform: translateX(4px);
    box-shadow: 0 0 0 1px rgba(221,43,49,.5), 0 12px 50px rgba(221,43,49,.2);
}

/* price block — fixed width left column */
.plan-price-block {
    flex-shrink: 0;
    width: 160px;
    display: flex;
    flex-direction: column;
}
.plan-price-row {
    display: flex;
    align-items: baseline;
    gap: 4px;
    margin-bottom: 4px;
}
.plan-price-sym { font-size: 1.4rem; font-weight: 700; color: #fff; }
.plan-price-val {
    font-family: 'Bebas Neue', 'Impact', sans-serif;
    font-size: 3.2rem;
    color: #fff;
    line-height: 1;
}
.plan-price-period { font-size: .85rem; color: #64748b; margin-left: 4px; }
.plan-duration {
    font-size: .8rem;
    color: #64748b;
}

/* vertical divider */
.plan-divider {
    width: 1px;
    height: 100px;
    background: #1e1e1e;
    flex-shrink: 0;
}
.plan-card.featured .plan-divider {
    background: rgba(221,43,49,.2);
}

/* plan info — grows to fill */
.plan-info {
    flex: 1;
    min-width: 0;
}
.plan-name {
    font-size: 1rem;
    font-weight: 700;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: 2px;
    margin-bottom: 12px;
}

/* features inline */
.plan-features {
    list-style: none;
    display: flex;
    flex-wrap: wrap;
    gap: 12px 28px;
    margin-bottom: 0;
}
.plan-features li {
    display: flex;
    align-items: center;
    gap: 7px;
    color: #cbd5e1;
    font-size: .88rem;
    padding: 0;
    border-bottom: none;
}
.plan-features li .feat-icon {
    width: 16px; height: 16px;
    background: rgba(34,197,94,.15);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.plan-features li .feat-icon i { color: #22c55e; font-size: .55rem; }

/* CTA — right column */
.plan-cta {
    flex-shrink: 0;
    width: 160px;
    display: flex;
    flex-direction: column;
    align-items: stretch;
}
.btn-plan {
    width: 100%;
    padding: 13px;
    font-size: .9rem;
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

/* badge repositioned for row layout */
.plan-badge {
    position: absolute;
    top: 16px; right: 16px;
    background: #dd2b31;
    color: #fff;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 2px;
    text-transform: uppercase;
    padding: 4px 12px;
    border-radius: 20px;
}

/* skeleton → row shape */
.plan-skeleton {
    background: #111;
    border: 1px solid #1e1e1e;
    border-radius: 16px;
    padding: 28px 36px;
    display: flex;
    align-items: center;
    gap: 32px;
    animation: skelPulse 1.4s ease-in-out infinite;
}

/* ── Responsive: stack on small screens ──────────── */
@media (max-width: 700px) {
   .plan-card {
    flex-direction: column;
    align-items: stretch;
    gap: 22px;
    padding: 26px 22px;
    width: 100%;
}
    .plan-price-block { width: 100%; flex-direction: row; align-items: baseline; gap: 12px; }
    .plan-divider { width: 100%; height: 1px; }
    .plan-cta { width: 100%; }
    .plan-features { flex-direction: column; gap: 6px; }
    .plan-skeleton { flex-direction: column; }
}
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
   AUTH MODAL  (identical to booking page)
════════════════════════════════════════════════════ */
#mxAuthModal .modal-content {
    background: #1f1f1f;
    color: #fff;
    border-radius: 10px;
    border: none;
}
#mxAuthModal .nav-tabs {
    border-bottom: 1px solid #333;
}
#mxAuthModal .nav-link {
    color: #fff;
    background: #2d2d2d;
    border: none;
    border-bottom: 2px solid transparent;
    border-radius: 0;
    padding: 10px 16px;
    font-size: .9rem;
    font-weight: 600;
    transition: color .2s;
}
#mxAuthModal .nav-link:hover { color: #fff; background: #333; }
#mxAuthModal .nav-link.active {
    color: #dd2b31;
    border-bottom-color: #dd2b31;
    background: #2d2d2d;
}
#mxAuthModal .form-control {
    background: #2d2d2d;
    border: none;
    color: #fff;
    border-radius: 8px;
    box-shadow: none;
    outline: none;
    -webkit-text-fill-color: #fff;
}
#mxAuthModal .form-control:focus {
    background: #2d2d2d;
    border-color: transparent;
    box-shadow: none;
    color: #fff;
}
.btn-auth {
    background: linear-gradient(180deg, rgba(221,43,49,1) 0%, rgb(119,17,23) 100%);
    color: #fff;
    border: 2px solid #791218;
    border-radius: 0;
    height: 46px;
    font-weight: 700;
    letter-spacing: 1px;
    width: 100%;
    transition: opacity .2s;
}
.btn-auth:hover { opacity: .9; color: #fff; }

/* ════════════════════════════════════════════════════
   PAY MODAL
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
.mx-upi-hint { color: #475569; font-size: .78rem; margin-top: 8px; }

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
    animation: mxSpin .7s linear infinite;
    display: inline-block;
}
@keyframes mxSpin { to { transform: rotate(360deg); } }

/* ════════════════════════════════════════════════════
   GUEST SUCCESS MODAL  (identical style to booking)
════════════════════════════════════════════════════ */
.mx-success-card {
    max-width: 520px;
    max-height: 90vh;
    overflow-y: auto;
}
.mx-success-anim {
    display: flex;
    justify-content: center;
    padding: 28px 0 16px;
}
.mx-checkmark {
    width: 56px; height: 56px;
}
.mx-checkmark-circle {
    stroke: #22c55e;
    stroke-width: 2;
    stroke-dasharray: 166;
    stroke-dashoffset: 166;
    animation: mxStroke .6s cubic-bezier(.65,0,.45,1) forwards;
}
.mx-checkmark-check {
    stroke: #22c55e;
    stroke-width: 2;
    stroke-dasharray: 48;
    stroke-dashoffset: 48;
    animation: mxStroke .3s cubic-bezier(.65,0,.45,1) .6s forwards;
}
@keyframes mxStroke { to { stroke-dashoffset: 0; } }

.mx-success-title {
    text-align: center;
    font-size: 1.4rem;
    font-weight: 800;
    color: #fff;
    margin-bottom: 6px;
}
.mx-success-sub {
    text-align: center;
    color: #64748b;
    font-size: .9rem;
    margin-bottom: 20px;
}
.mx-success-sub strong { color: #dd2b31; }

/* receipt rows */
.mx-receipt { padding: 0 28px; }
.mx-receipt-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid #1e1e1e;
}
.mx-receipt-row:last-child { border-bottom: none; }
.mx-receipt-label { color: #64748b; font-size: .85rem; }
.mx-receipt-value { color: #fff; font-size: .9rem; font-weight: 600; }
.mx-receipt-total .mx-receipt-value { color: #dd2b31; font-size: 1.05rem; }
.mx-receipt-divider { height: 1px; background: #1e1e1e; margin: 6px 0; }

/* call-to-confirm box */
.mx-garage-contact {
    background: #1e293b;
    padding: 20px;
    border-radius: 8px;
    margin: 20px 28px;
    text-align: center;
}
.mx-phone-link {
    color: #22c55e;
    font-size: 28px;
    font-weight: bold;
    text-decoration: none;
}
.mx-phone-link:hover { color: #16a34a; }

.mx-success-actions {
    padding: 16px 28px 24px;
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    justify-content: center;
    border-top: 1px solid #1e1e1e;
}
.mx-btn-solid {
    background: linear-gradient(135deg, #dd2b31 0%, #791218 100%);
    color: #fff;
    border: none;
    border-radius: 10px;
    padding: 12px 24px;
    font-size: .9rem;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: opacity .2s;
    text-decoration: none;
}
.mx-btn-solid:hover { opacity: .9; color: #fff; }
.mx-btn-outline {
    background: transparent;
    color: #cbd5e1;
    border: 1.5px solid #333;
    border-radius: 10px;
    padding: 12px 24px;
    font-size: .9rem;
    font-weight: 600;
    cursor: pointer;
    transition: border-color .2s;
}
.mx-btn-outline:hover { border-color: #dd2b31; color: #fff; }

/* ── Member success modal (logged-in flow) ── */
.mx-member-success-body { padding: 0 28px 24px; }

/* countdown timer */
.mx-timer-box {
    background: #1a2a1a;
    border: 1px solid #2a4a2a;
    border-radius: 8px;
    padding: 16px 20px;
    margin: 0 28px 16px;
    display: flex;
    align-items: center;
    gap: 14px;
}
.mx-timer-box i { font-size: 24px; color: #f59e0b; }
.mx-timer-label { color: #fbbf24; font-weight: 700; font-size: .95rem; }
.mx-timer-label span { color: #f59e0b; }
.mx-timer-note { color: #e2e8f0; font-size: 12px; margin-top: 4px; }
/* MODAL FIT SCREEN */
/* MODAL FIT SCREEN - FIXED */
.mx-success-card{
    width:95%;
    max-width:480px;
    max-height:95vh;
    overflow-y:auto;           /* Changed to auto */
    border-radius:18px;
    display:flex;
    flex-direction:column;
    padding:22px;
    background: #111;
    border: 1px solid #222;
}

/* Ensure body content can scroll */
.mx-success-card .mx-receipt,
.mx-success-card .mx-garage-contact,
.mx-success-card .mx-success-actions {
    flex-shrink: 0;
}

/* Make sure actions stay visible */
.mx-success-actions {
    margin-top: auto;
    padding-top: 20px;
    border-top: 1px solid #1e1e1e;
}

/* BODY */
.mx-member-success-body{
    overflow:hidden;
}

/* CONTACT SECTION */
.mx-garage-contact{
    margin-top:20px;
    padding-top:18px;
    border-top:1px solid rgba(255,255,255,0.08);
    text-align:center;
}

.mx-contact-title{
    color:#94a3b8;
    margin-bottom:12px;
    font-size:12px;
    font-weight:700;
    letter-spacing:1.2px;
}

/* PHONE BUTTON */
.mx-phone-link{
    display:flex;
    align-items:center;
    justify-content:center;
    gap:14px;
    background:linear-gradient(180deg,#dc2626,#7f1d1d);
    color:#fff;
    text-decoration:none;
    padding:14px 18px;
    border-radius:14px;
    transition:.3s ease;
}

.mx-phone-link:hover{
    color:#fff;
    transform:translateY(-2px);
}

/* ICON */
.mx-phone-link i{
    font-size:22px;
}

/* PHONE TEXT */
.mx-phone-content{
    display:flex;
    flex-direction:column;
    align-items:flex-start;
    line-height:1.2;
}

.mx-phone-number{
    font-size:18px;
    font-weight:700;
}

.mx-phone-extension{
    font-size:11px;
    opacity:.8;
    letter-spacing:1px;
}

/* HOURS */
.mx-office-hours{
    margin-top:12px;
    color:#94a3b8;
    font-size:11px;
    line-height:1.5;
}

/* MOBILE */
@media(max-width:576px){

    .mx-success-card{
        width:92%;
        padding:18px;
    }

    .mx-phone-number{
        font-size:16px;
    }

    .mx-success-actions{
        flex-direction:column;
        gap:10px;
    }

    .mx-success-actions a,
    .mx-success-actions button{
        width:100%;
    }
}
.mx-success-card{
    position: relative;
}

.mx-modal-close{
    position: absolute;
    top: 12px;
    right: 14px;

    width: 36px;
    height: 36px;

    border: none;
    border-radius: 50%;

    background: rgba(255,255,255,0.08);
    color: #fff;

    font-size: 28px;
    line-height: 1;

    cursor: pointer;

    display: flex;
    align-items: center;
    justify-content: center;

    transition: 0.25s ease;
}

.mx-modal-close:hover{
    background: #dd2b31;
    transform: rotate(90deg);
}

.mx-garage-contact-small{
    margin-top:12px;
    padding:10px 12px;
    border-radius:10px;
    background:rgba(255,255,255,0.04);
    text-align:center;
}

.mx-contact-mini-title{
    margin:0 0 6px;
    font-size:11px;
    font-weight:700;
    letter-spacing:.5px;
    color:#94a3b8;
}

.mx-phone-link-mini{
    display:inline-flex;
    align-items:center;
    gap:6px;
    font-size:14px;
    font-weight:600;
    color:#fff;
    text-decoration:none;
}

.mx-phone-link-mini i{
    font-size:12px;
}

.mx-office-mini{
    margin:6px 0 0;
    font-size:10px;
    color:#94a3b8;
    line-height:1.4;
}
</style>
@endpush

@section('content')

<main class="mem-wrap">
  <div class="container">

    {{-- Hero --}}
    <div class="mem-hero">
      {{-- <div class="mem-hero-eyebrow">Membership</div> --}}
      <h1>Become a <span>Member</span></h1>
      <p>Get unlimited access to our premium facilities with exclusive member benefits.</p>
    </div>

    {{-- Plans Grid --}}
    <div class="mem-grid" id="membershipPlansContainer">
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
     AUTH MODAL  (exact same as booking page)
════════════════════════════════════════════════════ --}}
<div class="modal fade" id="mxAuthModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background:#1f1f1f;color:#fff;border-radius:10px;border:none;">
            <div class="modal-header border-0">
                <h5 class="modal-title text-white">Continue to Purchase</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <ul class="nav nav-tabs" id="authTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link text-white bg-dark" id="loginTab" data-bs-toggle="tab"
                            data-bs-target="#loginTabPane" type="button" role="tab">Login</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link text-white bg-dark" id="registerTab" data-bs-toggle="tab"
                            data-bs-target="#registerTabPane" type="button" role="tab">Register</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active text-white bg-dark" id="guestMemberTab" data-bs-toggle="tab"
                            data-bs-target="#guestMemberPane" type="button" role="tab">Guest</button>
                    </li>
                </ul>

                <div class="tab-content pt-3" id="authTabsContent">

                    {{-- LOGIN --}}
                    <div class="tab-pane fade" id="loginTabPane" role="tabpanel">
                        <div id="loginErrorMsg" class="alert alert-danger d-none mb-3"></div>
                        <form id="mxLoginForm">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label small text-white">Email</label>
                                <input type="email" class="form-control border-0" name="email"
                                    style="background:#2d2d2d;color:#fff;box-shadow:none;outline:none;border:none;" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small text-white">Password</label>
                                <input type="password" class="form-control border-0" name="password"
                                    style="background:#2d2d2d;color:#fff;box-shadow:none;outline:none;border:none;" required>
                            </div>
                            <button type="submit" class="btn btn-auth w-100 mt-2">Login</button>
                        </form>
                    </div>

                    {{-- REGISTER --}}
                    <div class="tab-pane fade" id="registerTabPane" role="tabpanel">
                        <div id="registerErrorMsg" class="alert alert-danger d-none mb-3"></div>
                        <form id="mxRegisterForm">
                            @csrf
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label small text-white">Email</label>
                                    <input type="email" class="form-control border-0 form-control-sm" name="email"
                                        style="background:#2d2d2d;color:#fff;box-shadow:none;outline:none;border:none;" required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label small text-white">Mobile</label>
                                    <input type="text" class="form-control border-0 form-control-sm" name="mobile_no"
                                        style="background:#2d2d2d;color:#fff;box-shadow:none;outline:none;border:none;" required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label small text-white">Password</label>
                                    <input type="password" class="form-control border-0 form-control-sm" name="password"
                                        style="background:#2d2d2d;color:#fff;box-shadow:none;outline:none;border:none;" required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label small text-white">Confirm</label>
                                    <input type="password" class="form-control border-0 form-control-sm" name="password_confirmation"
                                        style="background:#2d2d2d;color:#fff;box-shadow:none;outline:none;border:none;" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-auth w-100 mt-3">Create Account</button>
                        </form>
                    </div>

                    {{-- GUEST --}}
                    <div class="tab-pane fade show active" id="guestMemberPane" role="tabpanel">
                        <div id="guestMemberErrorMsg" class="alert alert-danger d-none mb-3"></div>
                        <div class="alert mb-3" style="background:#1a2a1a;border:1px solid #2a4a2a;color:#86efac;font-size:13px;">
                            <i class="fa-solid fa-info-circle me-1"></i>
                            Your membership request will be sent to admin for approval after payment.
                        </div>
                        <form id="guestMemberForm">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label small text-white">Full Name</label>
                                <input type="text" class="form-control border-0" id="guestMemberName"
                                    placeholder="Enter your full name"
                                    style="background:#2d2d2d;color:#fff;box-shadow:none;outline:none;border:none;-webkit-text-fill-color:#fff;" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small text-white">Email</label>
                                <input type="email" class="form-control border-0" id="guestMemberEmail"
                                    placeholder="Enter your email"
                                    style="background:#2d2d2d;color:#fff;box-shadow:none;outline:none;border:none;-webkit-text-fill-color:#fff;" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small text-white">US Phone Number</label>
                                <input type="tel" class="form-control border-0" id="guestMemberPhone"
                                    placeholder="(XXX) XXX-XXXX"
                                    style="background:#2d2d2d;color:#fff;box-shadow:none;outline:none;border:none;-webkit-text-fill-color:#fff;" required>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="guestMemberAgree" required>
                                <label class="form-check-label small text-white" for="guestMemberAgree">
                                    I understand this request requires admin approval
                                </label>
                            </div>
                            <button type="submit" class="btn btn-auth w-100">Submit Your Request</button>
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
    <div class="mx-modal-card mx-pay-card" role="dialog" aria-modal="true" aria-labelledby="mxPayTitle">
        <div class="mx-modal-head">
            <div>
                <div id="mxPayTitle" class="mx-modal-title">Membership Payment</div>
                <div class="mx-modal-sub">Amount due: <strong id="mxPayAmount">$0</strong></div>
            </div>
            <button type="button" class="mx-modal-x" id="mxPayClose" aria-label="Close">×</button>
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
                        <input type="text" id="mxCardNum" class="mx-pay-input"
                            placeholder="1234 5678 9012 3456" maxlength="19" inputmode="numeric">
                    </div>
                    <div class="mx-field-wrap full">
                        <label>Cardholder Name</label>
                        <input type="text" id="mxCardName" class="mx-pay-input" placeholder="Name on card">
                    </div>
                    <div class="mx-field-wrap half">
                        <label>Expiry</label>
                        <input type="text" id="mxCardExp" class="mx-pay-input"
                            placeholder="MM / YY" maxlength="7" inputmode="numeric">
                    </div>
                    <div class="mx-field-wrap half">
                        <label>CVV</label>
                        <input type="password" id="mxCardCvv" class="mx-pay-input"
                            placeholder="•••" maxlength="3" inputmode="numeric">
                    </div>
                </div>
            </div>

            {{-- UPI --}}
            <div class="mxs-pay-panel" id="mxPayPanel-upi">
                <div class="mx-upi-wrap">
                    <i class="fa-solid fa-mobile-screen-button mx-upi-icon"></i>
                    <p class="mx-upi-label">Enter your UPI ID</p>
                    <input type="text" class="mx-pay-input" id="mxUpiId"
                        placeholder="yourname@upi"
                        style="max-width:280px;margin:0 auto;display:block;">
                    <p class="mx-upi-hint">e.g. name@okaxis, name@ybl, name@paytm</p>
                </div>
            </div>

            {{-- Net Banking --}}
            <div class="mxs-pay-panel" id="mxPayPanel-netbanking">
                <div class="mx-nb-grid">
                    @foreach (['SBI', 'HDFC', 'ICICI', 'Axis', 'Kotak', 'Yes Bank', 'PNB', 'BOB'] as $bank)
                    <label class="mx-nb-option">
                        <input type="radio" name="mxBank" value="{{ strtolower(str_replace(' ', '', $bank)) }}">
                        <span>{{ $bank }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="mx-modal-actions">
            <div class="mx-secure-badge">
                <i class="fa-solid fa-shield-halved"></i> 256-bit SSL secured
            </div>
            <button type="button" class="mx-pay-btn" id="mxPayNowBtn">
                <span id="mxPayBtnText">Pay <span id="mxPayBtnAmt">$0</span></span>
                <span id="mxPaySpinner" class="mx-spinner d-none"></span>
            </button>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════
     GUEST MEMBERSHIP SUCCESS MODAL
     (same style as booking guest success — phone shown)
════════════════════════════════════════════════════ --}}
{{-- ═══════════════════════════════════════════════════
     GUEST MEMBERSHIP SUCCESS MODAL (Fixed)
════════════════════════════════════════════════════ --}}
<div id="mxGuestMemberSuccessModal" class="mx-modal-overlay" aria-hidden="true">
    <div class="mx-modal-card mx-success-card" role="dialog" aria-modal="true">
<button type="button"
        class="mx-modal-close"
        onclick="closeGuestMemberSuccessModal()">

    &times;

</button>
        <div class="mx-success-anim">
            <svg class="mx-checkmark" viewBox="0 0 52 52" xmlns="http://www.w3.org/2000/svg">
                <circle class="mx-checkmark-circle" cx="26" cy="26" r="25" fill="none"/>
                <path class="mx-checkmark-check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
            </svg>
        </div>

        <div class="mx-success-title">Request Submitted!</div>
        {{-- <div class="mx-success-sub">Request ID: <strong id="mxGuestMemberReqId">—</strong></div> --}}

        <!-- Scrollable Content Area -->
        <div class="success-modal-body">
            {{-- Timer box --}}
            {{-- <div class="mx-timer-box">
                <i class="fa-solid fa-clock"></i>
                <div>
                    <div class="mx-timer-label">
                        Awaiting admin approval · <span id="mxGuestMemberTimer">30:00</span>
                    </div>
                    <div class="mx-timer-note">You'll be contacted at the number you provided</div>
                </div>
            </div> --}}

            {{-- Call to confirm --}}
           <div class="mx-garage-contact mx-garage-contact-small">

    <p class="mx-contact-mini-title">
        CALL TO CONFIRM
    </p>

    <a href="tel:+11234567890" class="mx-phone-link-mini">
        <i class="fa-solid fa-phone"></i>
       732-730-7712 EXTENSION 3
    </a>

    <p class="mx-office-mini">
        Mon–Fri 9AM–6PM | Sat 9AM–12PM
    </p>

</div>

            {{-- Receipt --}}
            <div class="mx-receipt">
                <div class="mx-receipt-row">
                    <span class="mx-receipt-label">Full Name</span>
                    <span class="mx-receipt-value" id="mxGMsName">—</span>
                </div>
                <div class="mx-receipt-row">
                    <span class="mx-receipt-label">Email</span>
                    <span class="mx-receipt-value" id="mxGMsEmail">—</span>
                </div>
                <div class="mx-receipt-row">
                    <span class="mx-receipt-label">Phone</span>
                    <span class="mx-receipt-value" id="mxGMsPhone">—</span>
                </div>
                <div class="mx-receipt-divider"></div>
                <div class="mx-receipt-row">
                    <span class="mx-receipt-label">Plan</span>
                    <span class="mx-receipt-value" id="mxGMsPlan">—</span>
                </div>
                <div class="mx-receipt-row">
                    <span class="mx-receipt-label">Duration</span>
                    <span class="mx-receipt-value" id="mxGMsDuration">—</span>
                </div>
                <div class="mx-receipt-row mx-receipt-total">
                    <span class="mx-receipt-label">Amount Paid</span>
                    <span class="mx-receipt-value" id="mxGMsAmount">—</span>
                </div>
            </div>
        </div>

        <!-- Actions - Always Visible -->
        <div class="mx-success-actions">
            {{-- <button id="continueToPaymentBtn" class="mx-btn-solid">
                <i class="fa-solid fa-credit-card"></i> Continue to Payment
            </button> --}}
            <a href="tel:+11234567890" class="mx-btn-outline" style="text-decoration:none;">
                <i class="fa-solid fa-phone"></i> Call to Confirm
            </a>
        </div>

    </div>
</div>
{{-- ═══════════════════════════════════════════════════
     LOGGED-IN MEMBER SUCCESS MODAL
════════════════════════════════════════════════════ --}}
<div id="mxMemberSuccessModal" class="mx-modal-overlay" aria-hidden="true">
    <div class="mx-modal-card mx-success-card" role="dialog" aria-modal="true">
<button type="button"
        class="mx-modal-close"
        onclick="closeMemberSuccessModal()">

    &times;

</button>
        <div class="mx-success-anim">
            <svg class="mx-checkmark" viewBox="0 0 52 52" xmlns="http://www.w3.org/2000/svg">
                <circle class="mx-checkmark-circle" cx="26" cy="26" r="25" fill="none"/>
                <path class="mx-checkmark-check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
            </svg>
        </div>

        <div class="mx-success-title">Membership Activated!</div>
        <div class="mx-success-sub">
            Welcome to <strong id="mxMemberPlanName">—</strong>
        </div>

        <div class="mx-member-success-body">

            <div class="mx-receipt">

                <div class="mx-receipt-row">
                    <span class="mx-receipt-label">Plan</span>
                    <span class="mx-receipt-value" id="mxMsName">—</span>
                </div>

                <div class="mx-receipt-row">
                    <span class="mx-receipt-label">Duration</span>
                    <span class="mx-receipt-value" id="mxMsDuration">—</span>
                </div>

                <div class="mx-receipt-row mx-receipt-total">
                    <span class="mx-receipt-label">Amount Paid</span>
                    <span class="mx-receipt-value" id="mxMsAmount">—</span>
                </div>

            </div>

           <!-- Garage Contact -->
<div class="mx-garage-contact">

    <p class="mx-contact-title">
        CALL US NOW TO CONFIRM
    </p>

    <a href="tel:7327307712" class="mx-phone-link">

        <i class="fa-solid fa-phone-volume"></i>

        <div class="mx-phone-content">

            <span class="mx-phone-number">
                732-730-7712
            </span>

            <span class="mx-phone-extension">
                EXTENSION 3
            </span>

        </div>

    </a>

    <p class="mx-office-hours">
        Office Hours:
        Mon–Fri 9AM–6PM |
        Sat 9AM–12PM
    </p>

</div>

        </div>

        <div class="mx-success-actions">

            <button type="button"
                    class="mx-btn-outline"
                    onclick="location.reload()">
                Done
            </button>

            <a href="/rentals"
               class="mx-btn-solid"
               style="text-decoration:none;">

                <i class="fa-solid fa-gauge"></i>
                Rentals

            </a>

        </div>

    </div>
</div>
{{-- ------------------------------------------------------------------------------ --}}
{{-- Auth state for JS --}}
<div id="mx-auth-state" data-logged-in="{{ auth()->check() ? '1' : '0' }}"></div>
<!-- Routes for JS -->
    <div id="mx-routes" data-login-url="{{ route('popup.login') }}" data-register-url="{{ route('popup.register') }}"></div>

    {{-- Auth Modal --}}
    <div class="modal fade" id="mxAuthModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background:#1f1f1f; color:#fff; border-radius:10px;">

                <div class="modal-header border-0">
                    <h5 class="modal-title text-white">Continue to Book</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">

                    {{-- Tabs --}}
                    <ul class="nav nav-tabs" id="authTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active text-white bg-dark" id="loginTab" data-bs-toggle="tab" data-bs-target="#loginTabPane" type="button" role="tab" aria-controls="loginTabPane" aria-selected="true" style="border:none; border-radius:5px 5px 0 0;">
                                Login
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link text-white bg-dark" id="registerTab" data-bs-toggle="tab" data-bs-target="#registerTabPane" type="button" role="tab" aria-controls="registerTabPane" aria-selected="false" style="border:none; border-radius:5px 5px 0 0;">
                                Register
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content pt-3" id="authTabsContent">

                        {{-- LOGIN TAB --}}
                        <div class="tab-pane fade show active" id="loginTabPane" role="tabpanel" aria-labelledby="loginTab">
                            <div id="loginErrorMsg" class="alert alert-danger d-none"></div>

                            <form id="loginFormMain">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label small text-white">Email</label>
                                    <input type="email" class="form-control border-0" name="email" id="loginEmail" style="background:#2d2d2d;color:#fff;box-shadow:none;outline:none;border:none;" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small text-white">Password</label>
                                    <input type="password" class="form-control border-0" name="password" id="loginPassword" style="background:#2d2d2d;color:#fff;box-shadow:none;outline:none;border:none;" required>
                                </div>

                                <div class="text-end mt-2">
                                    <a href="#" id="forgotPasswordBtn" class="small text-danger btn">
                                        Forgot password?
                                    </a>
                                </div>

                                <button type="submit" class="btn w-100 text-white fw-semibold mt-3" style="background: linear-gradient(180deg, rgba(221,43,49,1) 0%, rgb(119,17,23) 100%); border:2px solid #791218; height:46px; letter-spacing:1px;">
                                    Login
                                </button>
                            </form>
                        </div>

                        {{-- REGISTER TAB --}}
                        <div class="tab-pane fade" id="registerTabPane" role="tabpanel" aria-labelledby="registerTab">
                            <div id="registerErrorMsg" class="alert alert-danger d-none"></div>

                            <form id="registerFormMain">
                                @csrf
                                <div class="row g-2">
                                    <div class="col-6">
                                        <label class="form-label small text-white">Email</label>
                                        <input type="email" class="form-control border-0 form-control-sm" name="email" id="registerEmail" style="background:#2d2d2d;color:#fff;box-shadow:none;outline:none;border:none;" required>
                                    </div>

                                    <div class="col-6">
                                        <label class="form-label small text-white">Mobile</label>
                                        <input type="text" class="form-control border-0 form-control-sm" name="mobile_no" id="registerMobile" style="background:#2d2d2d;color:#fff;box-shadow:none;outline:none;border:none;" required>
                                    </div>

                                    <div class="col-6">
                                        <label class="form-label small text-white">Password</label>
                                        <input type="password" class="form-control border-0 form-control-sm" name="password" id="registerPassword" style="background:#2d2d2d;color:#fff;box-shadow:none;outline:none;border:none;" required>
                                    </div>

                                    <div class="col-6">
                                        <label class="form-label small text-white">Confirm</label>
                                        <input type="password" class="form-control border-0 form-control-sm" name="password_confirmation" id="registerPasswordConfirm" style="background:#2d2d2d;color:#fff;box-shadow:none;outline:none;border:none;" required>
                                    </div>
                                </div>

                                <button type="submit" class="btn w-100 text-white fw-semibold mt-3" style="background: linear-gradient(180deg, rgba(221,43,49,1) 0%, rgb(119,17,23) 100%); border:2px solid #791218; height:46px; letter-spacing:1px;">
                                    Create Account
                                </button>
                            </form>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Forgot Password Modal --}}
    <div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm" style="background: linear-gradient(180deg,#1f1f1f,#2a2a2a); border-radius:10px;">
            <div class="modal-content mt-4 p-4" style="background: transparent; border:none;">

                <!-- Step 1: Email -->
                <div id="fpStepEmail">
                    <h6 class="mb-3 text-white">Reset Password</h6>
                    <input type="email" id="fpEmailInput" class="form-control form-control-sm mb-2" placeholder="Enter email" style="background:#2d2d2d;color:#fff;border:none;">
                    <button class="btn btn-primary w-100 btn-sm d-flex align-items-center justify-content-center gap-2" id="fpSendOtpBtn">
                        <span class="btn-text">Send OTP</span>
                        <span class="spinner-border spinner-border-sm d-none" id="fpOtpLoader"></span>
                    </button>
                </div>

                <!-- Step 2: OTP -->
                <div id="fpStepOtp" class="d-none">
                    <label class="form-label small text-white">Please enter the OTP sent to your email</label>
                    <input type="text" id="fpOtpInput" class="form-control form-control-sm mb-2 text-center" placeholder="Enter OTP" style="background:#2d2d2d;color:#fff;border:none;">
                    <button class="btn btn-success w-100 btn-sm" id="fpVerifyOtpBtn">
                        Verify OTP
                    </button>
                    <button class="btn btn-link btn-sm w-100 text-white" id="fpResendOtpBtn">
                        Resend OTP
                    </button>
                </div>

                <!-- Step 3: New Password -->
                <div id="fpStepReset" class="d-none text-white w-100" style="background:#1f1f1f; padding:16px; border-radius:10px;">
                    <label class="form-label small">New Password</label>
                    <input type="password" id="fpNewPassword" class="form-control border-0 mb-3" style="background:#2d2d2d;color:#fff;box-shadow:none;outline:none;border:none;">

                    <label class="form-label small">Confirm Password</label>
                    <input type="password" id="fpConfirmPassword" class="form-control border-0 mb-3" style="background:#2d2d2d;color:#fff;box-shadow:none;outline:none;border:none;">

                    <button id="fpResetPasswordBtn" class="btn w-100 text-white fw-semibold shadow" style="background: linear-gradient(180deg, rgba(221,43,49,1) 0%, rgb(119,17,23) 100%); border: 2px solid #791218; height:46px; letter-spacing:1px;">
                        Reset Password
                    </button>
                </div>

            </div>
        </div>
    </div>
{{-- Card input live preview --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    var cn = document.getElementById('mxCardNum');
    var nm = document.getElementById('mxCardName');
    var ex = document.getElementById('mxCardExp');
    if (!cn) return;
    cn.addEventListener('input', function () {
        var v = this.value.replace(/\D/g,'').padEnd(16,'•').slice(0,16).match(/.{1,4}/g);
        document.getElementById('mxCardDisplay').textContent = v ? v.join(' ') : '•••• •••• •••• ••••';
    });
    nm.addEventListener('input', function () {
        document.getElementById('mxCardNameDisplay').textContent = this.value.toUpperCase() || 'YOUR NAME';
    });
    ex.addEventListener('input', function () {
        document.getElementById('mxCardExpDisplay').textContent = this.value || 'MM / YY';
    });
});
</script>

@endsection

@push('scripts')
<script src="{{ asset('assets/js/membership.js') }}"></script>
<script src="{{ asset('assets/js/script.js') }}"></script>
@endpush