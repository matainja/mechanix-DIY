@extends('admin.layouts.admin')

@section('title', 'Holidays')

@section('content')
     <div class="pc-container">
    <div class="pc-content">
      <!-- [ breadcrumb ] start -->
      <div class="page-header">
        <div class="page-block">
          <div class="row align-items-center">
            <div class="col-md-12">
              <div class="page-header-title">
                <h5 class="m-b-10">Holidays</h5>
              </div>
              <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="/index.html">Home</a></li>
                <li class="breadcrumb-item"><a href="javascript: void(0)">Management</a></li>
                <li class="breadcrumb-item" aria-current="page">Holidays</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
      <!-- [ breadcrumb ] end -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">All Holidays</h5>

                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addHolidayModal">
                    + Add Holiday
                </button>
            </div>

            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success mb-3">{{ session('success') }}</div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger mb-3">
                        <ul class="mb-0">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-striped align-middle mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Holiday Name</th>
                                <th>Date</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($holidays as $holiday)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $holiday->holiday_name }}</td>
                                    <td>{{ \Carbon\Carbon::parse($holiday->holiday_date)->format('d M Y') }}</td>
                                    <td class="text-end">
                                        <form action="{{ route('admin.holidays.delete', $holiday->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger btn-sm" onclick="return confirm('Delete this holiday?')">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">No holidays added yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- If you use pagination --}}
                {{-- <div class="mt-3">{{ $holidays->links() }}</div> --}}
            </div>
        </div>
    </div>
</div>

<!-- Add Holiday Modal -->
<div class="modal fade" id="addHolidayModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Holiday</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tabSingle" type="button" role="tab">
                            Single Date
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabWeekly" type="button" role="tab">
                            Weekly (e.g., Saturdays for next 4 months)
                        </button>
                    </li>
                </ul>

                <div class="tab-content pt-3">
                    <!-- Single Date Form -->
                    <div class="tab-pane fade show active" id="tabSingle" role="tabpanel">
                        <form action="{{ route('admin.holidays.storeSingle') }}" method="POST">
                            @csrf

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Holiday Name</label>
                                    <input type="text" name="holiday_name" class="form-control" placeholder="e.g., Christmas" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Holiday Date</label>
                                    {{-- Bootstrap calendar style: simplest reliable is HTML date input --}}
                                    <input type="date" name="holiday_date" class="form-control" required>
                                </div>

                                <div class="col-12 d-flex justify-content-end">
                                    <button class="btn btn-primary">Save</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Weekly Range Form -->
                    <div class="tab-pane fade" id="tabWeekly" role="tabpanel">
                        <form action="{{ route('admin.holidays.storeWeekly') }}" method="POST">
                            @csrf

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Event Name</label>
                                    <input type="text" name="holiday_name" class="form-control" value="Saturday Closed" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Repeat Day</label>
                                    <select name="weekday" class="form-select" required>
                                        <option value="6" selected>Saturday</option>
                                        <option value="0">Sunday</option>
                                        <option value="1">Monday</option>
                                        <option value="2">Tuesday</option>
                                        <option value="3">Wednesday</option>
                                        <option value="4">Thursday</option>
                                        <option value="5">Friday</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">From Date</label>
                                    <input type="date" name="from_date" class="form-control" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">To Date</label>
                                    <input type="date" name="to_date" class="form-control" required>
                                </div>

                                <div class="col-12">
                                    <div class="alert alert-info mb-0">
                                        This will auto-create holidays for every selected weekday between the date range.
                                        Duplicate dates will be ignored.
                                    </div>
                                </div>

                                <div class="col-12 d-flex justify-content-end">
                                    <button class="btn btn-primary">Generate & Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

    </div>
  </div>
@endsection
