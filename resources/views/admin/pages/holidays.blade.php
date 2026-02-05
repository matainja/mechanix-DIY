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
                                    {{-- <td>{{ $loop->iteration }}</td> --}} 
                                    <td>{{ $holidays->firstItem() + $loop->index }}</td>

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
                <div class="mt-3">{{ $holidays->links('pagination::bootstrap-5') }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Add Holiday Modal -->
<!-- Add Holiday Modal -->
<div class="modal fade" id="addHolidayModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Add Holidays</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">

        <form action="{{ route('admin.holidays.storeBulk') }}" method="POST">
          @csrf

          <div class="row g-3">

            {{-- Holiday Type --}}
            <div class="col-md-6">
              <label class="form-label">Holiday Type</label>
              <select id="holidayType" name="holiday_label" class="form-select" required>
                <option value="">Select Label</option>
                <option value="National Holiday">National Holiday</option>
                <option value="Weekend">Weekend</option>
                <option value="Others">Others</option>
              </select>
            </div>

            {{-- Custom label --}}
            <div class="col-md-6 d-none" id="customLabelBox">
              <label class="form-label">Custom Label</label>
              <input type="text" name="custom_label" class="form-control" placeholder="Enter custom holiday name">
            </div>


            {{-- From Date --}}
            <div class="col-md-6">
              <label class="form-label">From Date</label>
              <input type="date" name="from_date" min="{{ date('Y-m-d') }}" class="form-control">
            </div>

            {{-- To Date --}}
            <div class="col-md-6">
              <label class="form-label">To Date</label>
              <input type="date" name="to_date" min="{{ date('Y-m-d') }}" class="form-control">
            </div>


            {{-- Repeat weekday dropdown --}}
            <div class="col-md-6">
              <label class="form-label">Repeat Day (Optional)</label>
              <select name="weekday" class="form-select">
                <option value="">-- No Repeat --</option>
                <option value="0">Sunday</option>
                <option value="1">Monday</option>
                <option value="2">Tuesday</option>
                <option value="3">Wednesday</option>
                <option value="4">Thursday</option>
                <option value="5">Friday</option>
                <option value="6">Saturday</option>
              </select>

              <small class="text-muted">
                If selected → repeat every week inside date range
              </small>
            </div>


            {{-- Manual multiple dates --}}
            <div class="col-md-12">
              <label class="form-label">Manual Dates (Optional)</label>

              <input type="text"
                     id="manualDates"
                     class="form-control"
                     placeholder="Click to select multiple dates">

              <small class="text-muted">
                Select any custom days manually (add/remove freely)
              </small>

              <input type="hidden" name="manual_dates" id="manualDatesInput">
            </div>


            {{-- Info --}}
            <div class="col-12">
              <div class="alert alert-info mb-0">
                You can:
                • create holidays by range  
                • repeat weekly  
                • OR manually pick dates  
                • OR combine both  
              </div>
            </div>


            <div class="col-12 text-end mt-3">
              <button class="btn btn-primary px-4">Save Holidays</button>
            </div>

          </div>
        </form>

      </div>
    </div>
  </div>
</div>


    </div>
  </div>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>document.addEventListener("DOMContentLoaded", function () {

    // show custom label only when Others selected
    const type = document.getElementById('holidayType');
    const customBox = document.getElementById('customLabelBox');

    type.addEventListener('change', () => {
        customBox.classList.toggle('d-none', type.value !== 'Others');
    });


    // multiple date picker
    flatpickr("#manualDates", {
        mode: "multiple",
        dateFormat: "Y-m-d",
        onChange: function(selectedDates, dateStr) {
            document.getElementById('manualDatesInput').value = dateStr;
        }
    });

});
</script>

@endsection
