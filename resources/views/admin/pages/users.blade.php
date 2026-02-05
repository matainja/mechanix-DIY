@extends('admin.layouts.admin')

@section('title', 'Users')

@section('content')
<div class="pc-container">
    <div class="pc-content">
<!-- [ breadcrumb ] start -->
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h5 class="m-b-10">Users</h5>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Home</a></li>
                            <li class="breadcrumb-item"><a href="javascript: void(0)">Management</a></li>
                            <li class="breadcrumb-item" aria-current="page">Users</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ breadcrumb ] end -->
        <div class="page-header">
            <div class="page-block">
                <h5 class="m-b-10">Users</h5>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5>All Users</h5>
            </div>

            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>Role</th>
                            {{-- <th>Verified</th> --}}
                            <th>Joined</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($users as $user)
                        <tr>
                             <td>{{ $users->firstItem() + $loop->index }}</td>

                            <td>{{ $user->email }}</td>

                            <td>{{ $user->mobile_no ?? '-' }}</td>

                            {{-- Role --}}
                            <td>
                                {{-- @if($user->role == 1) --}}
                                    {{-- <span class="badge bg-danger">Admin</span> --}}
                                {{-- @else --}}
                                    <span class="badge bg-primary">User</span>
                                {{-- @endif --}}
                            </td>

                            {{-- Email verified --}}
                            {{-- <td>
                                @if($user->email_verified_at)
                                    <span class="badge bg-success">Verified</span>
                                @else
                                    <span class="badge bg-warning">Not Verified</span>
                                @endif
                            </td> --}}

                            <td>{{ $user->created_at->format('d M Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-3">{{ $users->links('pagination::bootstrap-5') }}</div>
            </div>
            
        </div>
     
    </div>
</div>
@endsection
