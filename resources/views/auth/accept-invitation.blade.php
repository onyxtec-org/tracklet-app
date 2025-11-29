@extends('layouts.contentLayoutMaster')

@section('title', 'Accept Invitation')

@section('content')
@include('panels.response')

<div class="row">
    <div class="col-md-8 col-lg-6 mx-auto">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Accept Invitation</h4>
                <p class="card-text mb-0">You've been invited to join <strong>{{ $invitation->organization->name }}</strong></p>
            </div>
            <div class="card-body">
                @if($invitation->isExpired())
                    <div class="alert alert-danger border-left-3 border-left-danger alert-dismissible fade show shadow-sm" role="alert">
                        <div class="alert-body">
                            <div class="d-flex align-items-start">
                                <i data-feather="alert-circle" class="font-medium-3 mr-2 mt-25"></i>
                                <div>
                                    <h6 class="alert-heading mb-1 font-weight-bolder">Invitation Expired</h6>
                                    <p class="mb-0">This invitation has expired. Please contact the organization administrator for a new invitation.</p>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @elseif($invitation->isAccepted())
                    <div class="alert alert-warning border-left-3 border-left-warning alert-dismissible fade show shadow-sm" role="alert">
                        <div class="alert-body">
                            <div class="d-flex align-items-start">
                                <i data-feather="alert-triangle" class="font-medium-3 mr-2 mt-25"></i>
                                <div>
                                    <h6 class="alert-heading mb-1 font-weight-bolder">Already Accepted</h6>
                                    <p class="mb-0">This invitation has already been accepted.</p>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @else
                    <form method="POST" action="{{ route('organization.invitation.accept', $invitation->token) }}" class="auth-login-form">
                        @csrf
                        
                        <div class="form-group">
                            <label class="form-label" for="email">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ $invitation->email }}" readonly required>
                            <input type="hidden" name="email" value="{{ $invitation->email }}">
                            <small class="form-text text-muted">This email must match the invitation email.</small>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="name">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" placeholder="John Doe" required>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <div class="d-flex justify-content-between">
                                <label class="form-label" for="password">Password <span class="text-danger">*</span></label>
                            </div>
                            <div class="input-group input-group-merge form-password-toggle">
                                <input type="password" class="form-control form-control-merge @error('password') is-invalid @enderror" 
                                       id="password" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" required>
                                <div class="input-group-append">
                                    <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                                </div>
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <small class="form-text text-muted">Minimum 8 characters</small>
                        </div>
                        
                        <div class="form-group">
                            <div class="d-flex justify-content-between">
                                <label class="form-label" for="password_confirmation">Confirm Password <span class="text-danger">*</span></label>
                            </div>
                            <div class="input-group input-group-merge form-password-toggle">
                                <input type="password" class="form-control form-control-merge" 
                                       id="password_confirmation" name="password_confirmation" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" required>
                                <div class="input-group-append">
                                    <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-block">
                            <i data-feather="check" class="mr-50"></i>
                            Accept Invitation & Create Account
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

