@extends('admin.layouts.admin')

@section('title', 'Membership Requests')

@section('content')
<div class="pc-container">
    <div class="pc-content">

        <!-- [ breadcrumb ] start -->
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">

                        <div class="page-header-title">
                            <h5 class="m-b-10">Membership Management</h5>
                        </div>

                        <ul class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.home') }}">Home</a>
                            </li>

                            <li class="breadcrumb-item">
                                <a href="javascript:void(0)">Management</a>
                            </li>

                            <li class="breadcrumb-item" aria-current="page">
                                Membership
                            </li>
                        </ul>

                    </div>
                </div>
            </div>
        </div>
        <!-- [ breadcrumb ] end -->



        <!-- [ Main Card ] start -->
        <div class="card">

            <div class="card-header d-flex justify-content-between align-items-center">

                <ul class="nav nav-tabs card-header-tabs" id="membershipTabs" role="tablist">

                    <li class="nav-item" role="presentation">
                        <button class="nav-link active"
                                id="requests-tab"
                                data-bs-toggle="tab"
                                data-bs-target="#requests"
                                type="button"
                                role="tab">

                            All Membership Requests

                        </button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button class="nav-link"
                                id="plans-tab"
                                data-bs-toggle="tab"
                                data-bs-target="#plans"
                                type="button"
                                role="tab">

                            Membership Plans

                        </button>
                    </li>

                </ul>

                <button type="button"
                        class="btn btn-primary btn-sm"
                        data-bs-toggle="modal"
                        data-bs-target="#addMembershipPlanModal">

                    + Add Membership Plan

                </button>

            </div>

            <div class="card-body">

                <div class="tab-content">

                    {{-- =========================================
                        REQUESTS TAB
                    ========================================== --}}
                    <div class="tab-pane fade show active"
                         id="requests"
                         role="tabpanel">

                        <div class="table-responsive">

                            <table class="table table-striped table-hover align-middle">

                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Email / Phone</th>
                                        <th>Plan</th>
                                        <th>Amount</th>
                                        <th>Method</th>
                                        <th>Requested</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    @forelse($requests as $r)

                                        <tr>

                                            <td>
                                        {{ $requests->total() - ($requests->firstItem() + $loop->index - 1) }}
                                    </td>

                                            <td>
                                                {{ $r->user?->name ?? $r->guest_name ?? '—' }}
                                            </td>

                                            <td>

                                                {{ $r->user?->email ?? $r->guest_email ?? '—' }}

                                                <br>

                                                <small class="text-muted">
                                                    {{
                                                        $r->user?->phone ??
                                                        $r->user?->mobile_no ??
                                                        $r->guest_phone ??
                                                        '—'
                                                    }}
                                                </small>

                                            </td>

                                            <td>
                                                {{ $r->membershipPlan?->name ?? '—' }}
                                            </td>

                                            <td>
                                                ${{ number_format($r->amount_paid, 2) }}
                                            </td>

                                            <td>
                                                {{ $r->payment_method ?? '—' }}
                                            </td>

                                            <td>
                                                {{ $r->created_at->format('d M Y') }}
                                            </td>

                                            <td>

                                                <span class="badge
                                                    @if($r->status === 'approved')
                                                        bg-success
                                                    @elseif($r->status === 'pending')
                                                        bg-warning text-dark
                                                    @else
                                                        bg-danger
                                                    @endif">

                                                    {{ ucfirst($r->status) }}

                                                </span>

                                            </td>

                                            <td>

                                                @if($r->status === 'pending')

                                                    <button class="btn btn-success btn-sm me-1"
                                                            onclick="membershipAction({{ $r->id }}, 'approve')">

                                                        <i class="ti ti-check"></i>

                                                    </button>

                                                    <button class="btn btn-danger btn-sm"
                                                            onclick="membershipAction({{ $r->id }}, 'reject')">

                                                        <i class="ti ti-x"></i>

                                                    </button>

                                                @else

                                                    <span class="text-muted small">—</span>

                                                @endif

                                            </td>

                                        </tr>

                                    @empty

                                        <tr>
                                            <td colspan="9"
                                                class="text-center text-muted py-4">

                                                No membership requests yet.

                                            </td>
                                        </tr>

                                    @endforelse

                                </tbody>

                            </table>
 <div class="mt-3">{{ $requests->links('pagination::bootstrap-5') }}</div>
                        </div>

                    </div>



                    {{-- =========================================
                        MEMBERSHIP PLANS TAB
                    ========================================== --}}
                    <div class="tab-pane fade"
                         id="plans"
                         role="tabpanel">

                        <div class="table-responsive">

                            <table class="table table-striped align-middle">

                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Price</th>
                                        <th>Duration</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    @forelse($plans as $plan)

                                        <tr>

                                            <td>{{ $loop->iteration }}</td>

                                            <td>{{ $plan->name }}</td>

                                            <td>${{ $plan->price }}</td>

                                            <td>{{ $plan->duration_days }} Days</td>

                                            <td>

                                                <form action="{{ route('admin.membership.plan.delete', $plan->id) }}"
                                                      method="POST"
                                                      onsubmit="return confirm('Delete this plan?')">

                                                    @csrf
                                                    @method('DELETE')

                                                    <button class="btn btn-danger btn-sm">
                                                        <i class="ti ti-trash"></i>
                                                    </button>

                                                </form>

                                            </td>

                                        </tr>

                                    @empty

                                        <tr>
                                            <td colspan="5"
                                                class="text-center text-muted py-4">

                                                No membership plans found.

                                            </td>
                                        </tr>

                                    @endforelse

                                </tbody>

                            </table>
                    <div class="mt-3">{{ $plans->links('pagination::bootstrap-5') }}</div>
                        </div>

                    </div>

                </div>

            </div>

        </div>
        <!-- [ Main Card ] end -->

    </div>
</div>



{{-- =========================================
    ADD MEMBERSHIP PLAN MODAL
========================================== --}}
<div class="modal fade"
     id="addMembershipPlanModal"
     tabindex="-1">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">

                <h6 class="modal-title">
                    Add Membership Plan
                </h6>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"></button>

            </div>

            <div class="modal-body">

                <div id="planFormError"
                     class="alert alert-danger d-none"></div>

                <div class="mb-3">

                    <label class="form-label">
                        Plan Name
                    </label>

                    <input type="text"
                           class="form-control"
                           id="planName"
                           placeholder="e.g. Gold">

                </div>

                <div class="row g-2">

                    <div class="col-6">

                        <label class="form-label">
                            Price ($)
                        </label>

                        <input type="number"
                               class="form-control"
                               id="planPrice"
                               placeholder="0.00"
                               min="0"
                               step="0.01">

                    </div>

                    <div class="col-6">

                        <label class="form-label">
                            Duration (days)
                        </label>

                        <input type="number"
                               class="form-control"
                               id="planDuration"
                               placeholder="30"
                               min="1">

                    </div>

                </div>

                <div class="mt-3">

                    <label class="form-label">
                        Features
                        <small class="text-muted">(one per line)</small>
                    </label>

                    <textarea class="form-control"
                              id="planFeatures"
                              rows="5"
                              placeholder="Access to all tools&#10;Priority booking&#10;Dedicated support"></textarea>

                </div>

            </div>

            <div class="modal-footer">

                <button class="btn btn-secondary btn-sm"
                        data-bs-dismiss="modal">

                    Cancel

                </button>

                <button class="btn btn-primary btn-sm"
                        id="savePlanBtn">

                    Save Plan

                </button>

            </div>

        </div>

    </div>

</div>
{{-- =========================================
    NOTES MODAL
========================================== --}}
<div class="modal fade" id="adminNotesModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="adminNotesModalTitle">Admin Notes</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <textarea class="form-control" id="adminNotesInput" rows="3"
                    placeholder="Optional note (required for rejection)"></textarea>
                <div class="invalid-feedback">A reason is required for rejection.</div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-sm" id="adminNotesConfirm">Confirm</button>
            </div>
        </div>
    </div>
</div>


@endsection



@push('scripts')
<script>
(function () {

    const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '';
    let pendingAction = null;

    window.membershipAction = function (id, action) {

        pendingAction = { id, action };

        document.getElementById('adminNotesModalTitle').textContent =
            action === 'approve'
                ? 'Approve Membership'
                : 'Reject Membership (reason required)';

        const confirmBtn = document.getElementById('adminNotesConfirm');

        confirmBtn.textContent =
            action === 'approve'
                ? 'Approve'
                : 'Reject';

        confirmBtn.className =
            action === 'approve'
                ? 'btn btn-success btn-sm'
                : 'btn btn-danger btn-sm';

        document.getElementById('adminNotesInput').value = '';

        document.getElementById('adminNotesInput')
            .classList.remove('is-invalid');

        new bootstrap.Modal(
            document.getElementById('adminNotesModal')
        ).show();
    };



    document.getElementById('adminNotesConfirm')
        .addEventListener('click', async function () {

        if (!pendingAction) return;

        const { id, action } = pendingAction;

        const notes = document.getElementById('adminNotesInput')
            .value.trim();

        if (action === 'reject' && !notes) {

            document.getElementById('adminNotesInput')
                .classList.add('is-invalid');

            return;
        }

        this.disabled = true;
        this.textContent = 'Processing…';

        try {

            const res = await fetch(
                `/admin/membership-requests/${id}/${action}`,
                {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        admin_notes: notes
                    }),
                }
            );

            const data = await res.json();

            bootstrap.Modal.getInstance(
                document.getElementById('adminNotesModal')
            ).hide();

            if (!res.ok || !data.status) {
                alert(data.message || 'Action failed.');
                return;
            }

            location.reload();

        } catch (e) {

            alert('Network error. Please try again.');

        } finally {

            this.disabled = false;
            pendingAction = null;

        }

    });



    document.getElementById('savePlanBtn')
        .addEventListener('click', async function () {

        const name = document.getElementById('planName').value.trim();

        const price = document.getElementById('planPrice').value.trim();

        const duration = document.getElementById('planDuration').value.trim();

        const features = document.getElementById('planFeatures').value.trim();

        const $err = document.getElementById('planFormError');

        $err.classList.add('d-none');

        if (!name || !price || !duration) {

            $err.textContent =
                'Name, price and duration are required.';

            $err.classList.remove('d-none');

            return;
        }

        const featuresArray = features
            ? features.split('\n')
                .map(f => f.trim())
                .filter(f => f)
            : [];

        this.disabled = true;

        this.textContent = 'Saving…';

        try {

            const res = await fetch('/admin/membership-plans', {

                method: 'POST',

                credentials: 'same-origin',

                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF,
                    'Accept': 'application/json',
                },

                body: JSON.stringify({
                    name,
                    price,
                    duration_days: duration,
                    features: JSON.stringify(featuresArray),
                }),

            });

            const data = await res.json();

            if (!res.ok || !data.status) {

                $err.textContent =
                    data.message || 'Failed to save plan.';

                $err.classList.remove('d-none');

                return;
            }

            bootstrap.Modal.getInstance(
                document.getElementById('addMembershipPlanModal')
            ).hide();

            location.reload();

        } catch (e) {

            $err.textContent =
                'Network error. Please try again.';

            $err.classList.remove('d-none');

        } finally {

            this.disabled = false;

            this.textContent = 'Save Plan';

        }

    });

})();
</script>
@endpush