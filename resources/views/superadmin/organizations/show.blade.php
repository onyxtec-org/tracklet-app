@extends('layouts.contentLayoutMaster')

@section('title', 'Organization Details')

@section('vendor-style')
<link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/dataTables.bootstrap4.min.css')) }}">
<link rel="stylesheet" href="{{ asset(mix('vendors/css/tables/datatable/responsive.bootstrap4.min.css')) }}">
@endsection

@section('content')
@include('panels.response')

<div class="row">
    <div class="col-12">
        <!-- Organization Details Card -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">{{ $organization->name }}</h4>
                <div>
                    <a href="{{ route('superadmin.organizations.edit', $organization) }}" class="btn btn-primary">
                        <i data-feather="edit" class="mr-1"></i> Edit
                    </a>
                    @if($organization->registration_source === 'invited' && $organization->invitations->whereNull('accepted_at')->where('expires_at', '>', now())->count() === 0)
                    <form action="{{ route('superadmin.organizations.resend-invitation', $organization) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-warning" onclick="return confirm('Resend invitation to {{ $organization->email }}?')">
                            <i data-feather="mail" class="mr-1"></i> Resend Invitation
                        </button>
                    </form>
                    @endif
                    <form action="{{ route('superadmin.organizations.destroy', $organization) }}" method="POST" class="d-inline" onsubmit="return confirmDelete()">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i data-feather="trash-2" class="mr-1"></i> Delete
                        </button>
                    </form>
                    <a href="{{ route('superadmin.organizations.index') }}" class="btn btn-outline-secondary">
                        <i data-feather="arrow-left" class="mr-1"></i> Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="mb-2">Organization Information</h5>
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Name:</th>
                                <td><strong>{{ $organization->name }}</strong></td>
                            </tr>
                            <tr>
                                <th>Email:</th>
                                <td>{{ $organization->email ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Slug:</th>
                                <td><code>{{ $organization->slug }}</code></td>
                            </tr>
                            <tr>
                                <th>Phone:</th>
                                <td>{{ $organization->phone ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Address:</th>
                                <td>{{ $organization->address ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Registration Source:</th>
                                <td>
                                    @if($organization->registration_source === 'self_registered')
                                        <span class="badge badge-light-primary">Self Registered</span>
                                    @else
                                        <span class="badge badge-light-info">Invited</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h5 class="mb-2">Status & Subscription</h5>
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Status:</th>
                                <td>
                                    @if($organization->is_active)
                                        <span class="badge badge-light-success">Active</span>
                                    @else
                                        <span class="badge badge-light-danger">Inactive</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Subscription:</th>
                                <td>
                                    @if($organization->is_subscribed)
                                        @if($organization->isOnTrial())
                                            <span class="badge badge-light-info">Active (Trial - {{ $organization->trialDaysRemaining() }} days left)</span>
                                        @else
                                            <span class="badge badge-light-success">Subscribed</span>
                                        @endif
                                    @else
                                        <span class="badge badge-light-warning">Not Subscribed</span>
                                    @endif
                                </td>
                            </tr>
                            @if($organization->trial_ends_at)
                            <tr>
                                <th>Trial Ends:</th>
                                <td>{{ $organization->trial_ends_at->format('F d, Y h:i A') }}</td>
                            </tr>
                            @endif
                            @if($organization->subscription_ends_at)
                            <tr>
                                <th>Subscription Ends:</th>
                                <td>{{ $organization->subscription_ends_at->format('F d, Y h:i A') }}</td>
                            </tr>
                            @endif
                            <tr>
                                <th>Stripe ID:</th>
                                <td><code>{{ $organization->stripe_id ?? '-' }}</code></td>
                            </tr>
                            <tr>
                                <th>Created At:</th>
                                <td>{{ $organization->created_at->format('F d, Y h:i A') }}</td>
                            </tr>
                            <tr>
                                <th>Last Updated:</th>
                                <td>{{ $organization->updated_at->format('F d, Y h:i A') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Users Card -->
        <div class="card mt-2">
            <div class="card-header">
                <h4 class="card-title">Users ({{ $organization->users->count() }})</h4>
            </div>
            <div class="card-body">
                @if($organization->users->count() > 0)
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Email Verified</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($organization->users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @foreach($user->roles as $role)
                                        <span class="badge badge-light-primary">{{ ucfirst(str_replace('_', ' ', $role->name)) }}</span>
                                    @endforeach
                                </td>
                                <td>
                                    @if($user->email_verified_at)
                                        <span class="badge badge-light-success">Verified</span>
                                    @else
                                        <span class="badge badge-light-warning">Not Verified</span>
                                    @endif
                                </td>
                                <td>{{ $user->created_at->format('M d, Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted">No users in this organization yet.</p>
                @endif
            </div>
        </div>

        <!-- Invitations Card -->
        @if($organization->registration_source === 'invited')
        <div class="card mt-2">
            <div class="card-header">
                <h4 class="card-title">Invitation History ({{ $organization->invitations->count() }})</h4>
            </div>
            <div class="card-body">
                @if($organization->invitations->count() > 0)
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Sent At</th>
                                <th>Expires At</th>
                                <th>Accepted At</th>
                                <th>Invited By</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($organization->invitations->sortByDesc('created_at') as $invitation)
                            <tr>
                                <td>{{ $invitation->email }}</td>
                                <td>
                                    @if($invitation->isAccepted())
                                        <span class="badge badge-light-success">Accepted</span>
                                    @elseif($invitation->isExpired())
                                        <span class="badge badge-light-danger">Expired</span>
                                    @else
                                        <span class="badge badge-light-warning">Pending</span>
                                    @endif
                                </td>
                                <td>{{ $invitation->created_at->format('M d, Y h:i A') }}</td>
                                <td>{{ $invitation->expires_at->format('M d, Y h:i A') }}</td>
                                <td>
                                    @if($invitation->accepted_at)
                                        {{ $invitation->accepted_at->format('M d, Y h:i A') }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($invitation->inviter)
                                        {{ $invitation->inviter->name }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-muted">No invitations sent yet.</p>
                @endif
            </div>
        </div>
        @endif
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

function confirmDelete() {
    return confirm('Are you sure you want to delete this organization? This will permanently delete all associated data including users, expenses, inventory, assets, and maintenance records. This action cannot be undone!');
}
</script>
@endsection

