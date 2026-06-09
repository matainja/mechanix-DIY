@extends('admin.layouts.admin')

@section('title', 'Bookings')

@section('content')
    <div class="pc-container">
        <div class="pc-content">
            <!-- [ breadcrumb ] start -->
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h5 class="m-b-10">Bookings</h5>
                            </div>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Home</a></li>
                                <li class="breadcrumb-item"><a href="javascript: void(0)">Management</a></li>
                                <li class="breadcrumb-item" aria-current="page">Bookings</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ breadcrumb ] end -->

            <!-- [ Main Content ] start -->
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" id="bookingTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="today-tab" data-bs-toggle="tab" href="#today" role="tab">
                                Today's Bookings
                                <span class="badge bg-primary ms-1">{{ $todayBookings->count() }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="all-tab" data-bs-toggle="tab" href="#all" role="tab">
                                All Bookings
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="date-tab" data-bs-toggle="tab" href="#bydate" role="tab">
                                <i class="ti ti-calendar me-1"></i> By Date
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="month-tab" data-bs-toggle="tab" href="#bymonth" role="tab">
                                <i class="ti ti-calendar-month me-1"></i> By Month
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="year-tab" data-bs-toggle="tab" href="#byyear" role="tab">
                                <i class="ti ti-calendar-stats me-1"></i> By Year
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="card-body">
                    <div class="tab-content" id="bookingTabsContent">

                        <!-- TODAY'S BOOKINGS TAB -->
                        <div class="tab-pane fade show active" id="today" role="tabpanel">
                            <small class="text-muted d-block mb-3">
                                Showing all bookings for today — {{ now()->format('d M Y') }}
                            </small>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover align-middle">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Customer</th>
                                            <th>Start Time</th>
                                            <th>End Time</th>
                                            <th>Duration (Hours)</th>
                                            <th>Equipment Type</th>
                                            <th>Rate per Hour</th>
                                            <th>Status</th>
                                            <th>Total</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($todayBookings as $index => $booking)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    @if ($booking->booking_type === 'guest')
                                                        <strong>{{ $booking->guest_name ?? 'Guest User' }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ $booking->guest_phone ?? '—' }}</small>
                                                    @else
                                                        <strong>{{ ucfirst(strtok($booking->user?->email ?? 'User', '@')) }}</strong>
                                                        <br>
                                                        <small
                                                            class="text-muted">{{ $booking->user?->email ?? '—' }}</small>
                                                    @endif
                                                </td>
                                                <td>{{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }}</td>
                                                <td>{{ \Carbon\Carbon::parse($booking->start_time)->addHours($booking->hours)->format('H:i') }}
                                                </td>
                                                <td>{{ $booking->hours }}</td>
                                                <td>{{ $booking->lift_type }}</td>
                                                <td>${{ number_format($booking->rate_per_hour, 2) }}</td>
                                                <td>
                                                    <span
                                                        class="badge 
                                            @if ($booking->status == 'confirmed') bg-success
                                            @elseif($booking->status == 'pending') bg-warning text-dark
                                            @else bg-danger @endif">
                                                        {{ ucfirst($booking->status) }}
                                                    </span>
                                                </td>
                                                <td>${{ number_format($booking->total, 2) }}</td>
                                                <td>
                                                    @if ($booking->status === 'pending')
                                                        <button class="btn btn-success btn-sm me-1"
                                                            onclick="bookingAction({{ $booking->id }}, 'approve')">
                                                            <i class="ti ti-check"></i>
                                                        </button>
                                                        <button class="btn btn-warning btn-sm me-1"
                                                            onclick="bookingAction({{ $booking->id }}, 'cancel')">
                                                            <i class="ti ti-x"></i>
                                                        </button>
                                                    @elseif($booking->status === 'confirmed')
                                                        <button class="btn btn-warning btn-sm me-1"
                                                            onclick="bookingAction({{ $booking->id }}, 'cancel')">
                                                            <i class="ti ti-x"></i>
                                                        </button>
                                                    @endif
                                                    <button class="btn btn-danger btn-sm"
                                                        onclick="bookingAction({{ $booking->id }}, 'delete')">
                                                        <i class="ti ti-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="10" class="text-center text-muted py-4">
                                                    <i class="ti ti-calendar-off fs-4 d-block mb-2"></i>
                                                    No bookings for today.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- ALL BOOKINGS TAB -->
                        <div class="tab-pane fade" id="all" role="tabpanel">
                            <small class="text-muted d-block mb-3">
                                Approved bookings will be confirmed, cancelled bookings will be released, and deleted
                                bookings will be permanently removed.
                            </small>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover align-middle">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Booking Date</th>
                                            <th>Customer</th>
                                            <th>Start Time</th>
                                            <th>End Time</th>
                                            <th>Duration (Hours)</th>
                                            <th>Equipment Type</th>
                                            <th>Rate per Hour</th>
                                            <th>Status</th>
                                            <th>Total</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($bookings as $booking)
                                            <tr>
                                                <td>{{ $bookings->total() - ($bookings->firstItem() + $loop->index - 1) }}
                                                </td>
                                                <td>{{ \Carbon\Carbon::parse($booking->date)->format('d M Y') }}</td>
                                                <td>
                                                    @if ($booking->booking_type === 'guest')
                                                        <strong>{{ $booking->guest_name ?? 'Guest User' }}</strong>
                                                        <br>
                                                        <small
                                                            class="text-muted">{{ $booking->guest_phone ?? '—' }}</small>
                                                    @else
                                                        <strong>{{ ucfirst(strtok($booking->user?->email ?? 'User', '@')) }}</strong>
                                                        <br>
                                                        <small
                                                            class="text-muted">{{ $booking->user?->email ?? '—' }}</small>
                                                    @endif
                                                </td>
                                                <td>{{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }}</td>
                                                <td>{{ \Carbon\Carbon::parse($booking->start_time)->addHours($booking->hours)->format('H:i') }}
                                                </td>
                                                <td>{{ $booking->hours }}</td>
                                                <td>{{ $booking->lift_type }}</td>
                                                <td>${{ number_format($booking->rate_per_hour, 2) }}</td>
                                                <td>
                                                    <span
                                                        class="badge 
                                            @if ($booking->status == 'confirmed') bg-success
                                            @elseif($booking->status == 'pending') bg-warning text-dark
                                            @else bg-danger @endif">
                                                        {{ ucfirst($booking->status) }}
                                                    </span>
                                                </td>
                                                <td>${{ number_format($booking->total, 2) }}</td>
                                                <td>
                                                    @if ($booking->status === 'pending')
                                                        <button class="btn btn-success btn-sm me-1"
                                                            onclick="bookingAction({{ $booking->id }}, 'approve')">
                                                            <i class="ti ti-check"></i>
                                                        </button>
                                                        <button class="btn btn-warning btn-sm me-1"
                                                            onclick="bookingAction({{ $booking->id }}, 'cancel')">
                                                            <i class="ti ti-x"></i>
                                                        </button>
                                                    @elseif($booking->status === 'confirmed')
                                                        <button class="btn btn-warning btn-sm me-1"
                                                            onclick="bookingAction({{ $booking->id }}, 'cancel')">
                                                            <i class="ti ti-x"></i>
                                                        </button>
                                                    @endif
                                                    <button class="btn btn-danger btn-sm"
                                                        onclick="bookingAction({{ $booking->id }}, 'delete')">
                                                        <i class="ti ti-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class="mt-3">{{ $bookings->links('pagination::bootstrap-5') }}</div>
                            </div>
                        </div>
                    <!-- BY DATE TAB -->
<div class="tab-pane fade" id="bydate" role="tabpanel">
    <div class="row g-2 align-items-end mb-3">
        <div class="col-auto">
            <label class="form-label mb-1">Select Date</label>
            <input type="date" id="filter_date" class="form-control">
        </div>
        <div class="col-auto">
            <button class="btn btn-primary ajax-filter-btn" data-tab="bydate">
                <i class="ti ti-search me-1"></i> Search
            </button>
        </div>
    </div>
    <div class="filter-meta text-muted small mb-3 d-none"></div>
    <div class="filter-result"></div>
</div>

<!-- BY MONTH TAB -->
<div class="tab-pane fade" id="bymonth" role="tabpanel">
    <div class="row g-2 align-items-end mb-3">
        <div class="col-auto">
            <label class="form-label mb-1">Month</label>
            <select id="filter_month" class="form-select">
                @foreach (range(1, 12) as $m)
                    <option value="{{ $m }}">
                        {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-auto">
            <label class="form-label mb-1">Year</label>
            <select id="filter_month_year" class="form-select">
                @foreach (range(now()->year, now()->year - 4) as $y)
                    <option value="{{ $y }}">{{ $y }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-auto">
            <button class="btn btn-primary ajax-filter-btn" data-tab="bymonth">
                <i class="ti ti-search me-1"></i> Search
            </button>
        </div>
    </div>
    <div class="filter-meta text-muted small mb-3 d-none"></div>
    <div class="filter-result"></div>
</div>

<!-- BY YEAR TAB -->
<div class="tab-pane fade" id="byyear" role="tabpanel">
    <div class="row g-2 align-items-end mb-3">
        <div class="col-auto">
            <label class="form-label mb-1">Year</label>
            <select id="filter_year" class="form-select">
                @foreach (range(now()->year, now()->year - 4) as $y)
                    <option value="{{ $y }}">{{ $y }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-auto">
            <button class="btn btn-primary ajax-filter-btn" data-tab="byyear">
                <i class="ti ti-search me-1"></i> Search
            </button>
        </div>
    </div>
    <div class="filter-meta text-muted small mb-3 d-none"></div>
    <div class="filter-result"></div>
</div>

                    </div>
                </div>
            </div>
            <!-- [ Main Content ] end -->

        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const urlParams = new URLSearchParams(window.location.search);
                const activeTab = urlParams.get('tab');

                // also auto-detect tab from filter params if tab param missing
                let tab = activeTab;
                if (!tab) {
                    if (urlParams.get('filter_date')) tab = 'bydate';
                    else if (urlParams.get('filter_month')) tab = 'bymonth';
                    else if (urlParams.get('filter_year')) tab = 'byyear';
                }

                if (tab) {
                    const tabEl = document.querySelector('#' + tab + '-tab');
                    if (tabEl) new bootstrap.Tab(tabEl).show();
                }
            });

            (function() {
                const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '';

                window.bookingAction = async function(id, action) {
                    if (action === 'delete') {
                        if (!confirm('Delete this booking permanently?')) return;
                    }

                    const config = {
                        approve: {
                            url: `/admin/bookings/${id}/approve`,
                            method: 'POST'
                        },
                        cancel: {
                            url: `/admin/bookings/${id}/cancel`,
                            method: 'POST'
                        },
                        delete: {
                            url: `/admin/bookings/${id}`,
                            method: 'DELETE'
                        },
                    };

                    const {
                        url,
                        method
                    } = config[action];

                    try {
                        const res = await fetch(url, {
                            method,
                            credentials: 'same-origin',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': CSRF,
                                'Accept': 'application/json',
                            },
                        });
                        const data = await res.json();

                        if (!res.ok || !data.status) {
                            alert(data.message || 'Action failed.');
                            return;
                        }

                        // Reload and preserve current tab
                        const currentTab = new URLSearchParams(window.location.search).get('tab');
                        if (currentTab) {
                            location.reload();
                        } else {
                            location.reload();
                        }

                    } catch (e) {
                        alert('Network error. Please try again.');
                    }
                };
            })();
        </script>

       <script>
(function () {
    const filterUrl = "{{ route('admin.bookings.filter') }}";

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.ajax-filter-btn');
        if (!btn) return;

        const tab    = btn.dataset.tab;
        const pane   = document.getElementById(tab);
        const meta   = pane.querySelector('.filter-meta');
        const result = pane.querySelector('.filter-result');

        let params = new URLSearchParams();

        if (tab === 'bydate') {
            const val = document.getElementById('filter_date').value;
            if (!val) { alert('Please select a date.'); return; }
            params.set('filter_date', val);

        } else if (tab === 'bymonth') {
            params.set('filter_month',      document.getElementById('filter_month').value);
            params.set('filter_month_year', document.getElementById('filter_month_year').value);

        } else if (tab === 'byyear') {
            params.set('filter_year', document.getElementById('filter_year').value);
        }

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Loading...';
        result.innerHTML = '';
        meta.classList.add('d-none');

        fetch(filterUrl + '?' + params.toString(), {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function (res) {
            if (!res.ok) throw new Error('Server error');
            return res.json();
        })
        .then(function (res) {
            meta.classList.remove('d-none');
            meta.innerHTML = 'Showing bookings for <strong>' + res.label + '</strong> — <strong>' + res.count + ' booking(s)</strong>';

            result.innerHTML = res.count > 0
                ? '<div class="table-responsive">' + res.html + '</div>'
                : '<div class="text-center text-muted py-4"><i class="ti ti-inbox fs-4 d-block mb-2"></i>No bookings found.</div>';
        })
        .catch(function () {
            result.innerHTML = '<div class="text-danger">Something went wrong. Please try again.</div>';
        })
        .finally(function () {
            btn.disabled = false;
            btn.innerHTML = '<i class="ti ti-search me-1"></i> Search';
        });
    });
})();
</script>
    @endpush
@endsection
