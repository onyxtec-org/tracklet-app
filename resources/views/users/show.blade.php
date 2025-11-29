@extends('layouts.contentLayoutMaster')

@section('title', 'User Details')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">{{ $user->name }}</h4>
                <div>
                    <a href="{{ route('users.edit', $user) }}" class="btn btn-primary">
                        <i data-feather="edit" class="mr-1"></i> Edit
                    </a>
                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                        <i data-feather="arrow-left" class="mr-1"></i> Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Name:</th>
                                <td><strong>{{ $user->name }}</strong></td>
                            </tr>
                            <tr>
                                <th>Email:</th>
                                <td>{{ $user->email }}</td>
                            </tr>
                            <tr>
                                <th>Role:</th>
                                <td>
                                    @foreach($user->roles as $role)
                                        <span class="badge badge-light-primary">{{ ucfirst(str_replace('_', ' ', $role->name)) }}</span>
                                    @endforeach
                                </td>
                            </tr>
                            <tr>
                                <th>Organization:</th>
                                <td>{{ $user->organization->name ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Email Verified:</th>
                                <td>
                                    @if($user->email_verified_at)
                                        <span class="badge badge-light-success">Verified</span>
                                        <br><small class="text-muted">{{ $user->email_verified_at->format('M d, Y h:i A') }}</small>
                                    @else
                                        <span class="badge badge-light-warning">Not Verified</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Created At:</th>
                                <td>{{ $user->created_at->format('F d, Y h:i A') }}</td>
                            </tr>
                            <tr>
                                <th>Last Updated:</th>
                                <td>{{ $user->updated_at->format('F d, Y h:i A') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page-script')
<script>
$(function() {
    if (feather) {
        feather.replace({ width: 14, height: 14 });
    }
});
</script>
@endsection



