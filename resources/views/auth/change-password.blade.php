@extends('layouts/fullLayoutMaster')

@section('title', 'Change Password')

@section('page-style')
{{-- Page Css files --}}
<link rel="stylesheet" href="{{ asset(mix('css/base/pages/page-auth.css')) }}">
<style>
  .brand-logo {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-bottom: 1.5rem;
    padding: 0.5rem 0;
  }
  .brand-logo img {
    max-width: 150px;
    max-height: 80px;
    height: auto;
    width: auto;
    object-fit: contain;
    display: block;
  }
</style>
@endsection

@section('content')
<div class="auth-wrapper auth-v1 px-2">
  <div class="auth-inner py-2">
    <!-- Change Password -->
    <div class="card mb-0">
      <div class="card-body">
        <a href="javascript:void(0);" class="brand-logo">
          <img src="{{asset('images/logo/LOGO.svg')}}" alt="TrackLet">
        </a>

        <h4 class="card-title mb-1">Change Your Password 🔒</h4>
        <p class="card-text mb-2">For security reasons, you must change your password before continuing.</p>

        @if (session('warning'))
        <div class="alert alert-warning border-left-3 border-left-warning shadow-sm" role="alert">
          <div class="alert-body">
            <div class="d-flex align-items-start">
              <i data-feather="alert-triangle" class="font-medium-3 mr-2 mt-25"></i>
              <div>
                <h6 class="alert-heading mb-1 font-weight-bolder">Warning</h6>
                <p class="mb-0">{{ session('warning') }}</p>
              </div>
            </div>
          </div>
        </div>
        @endif

        @if ($errors->any())
        <div class="alert alert-danger border-left-3 border-left-danger shadow-sm" role="alert">
          <div class="alert-body">
            <div class="d-flex align-items-start">
              <i data-feather="alert-circle" class="font-medium-3 mr-2 mt-25"></i>
              <div class="flex-grow-1">
                <h6 class="alert-heading mb-1 font-weight-bolder">Validation Errors</h6>
                <ul class="mb-0 pl-1">
                  @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            </div>
          </div>
        </div>
        @endif

        <form class="auth-change-password-form mt-2" method="POST" action="{{ route('password.change.submit') }}">
          @csrf
          <div class="form-group">
            <label for="current-password" class="form-label">Current Password</label>
            <div class="input-group input-group-merge form-password-toggle">
              <input type="password" class="form-control form-control-merge @error('current_password') is-invalid @enderror" 
                     id="current-password" name="current_password" tabindex="1" 
                     placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" 
                     aria-describedby="current-password" required autofocus />
              <div class="input-group-append">
                <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
              </div>
              @error('current_password')
              <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
              </span>
              @enderror
            </div>
          </div>

          <div class="form-group">
            <label for="new-password" class="form-label">New Password</label>
            <div class="input-group input-group-merge form-password-toggle">
              <input type="password" class="form-control form-control-merge @error('new_password') is-invalid @enderror" 
                     id="new-password" name="new_password" tabindex="2" 
                     placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" 
                     aria-describedby="new-password" required minlength="8" />
              <div class="input-group-append">
                <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
              </div>
            </div>
            @error('new_password')
            <span class="invalid-feedback" role="alert">
              <strong>{{ $message }}</strong>
            </span>
            @enderror
          </div>

          <div class="form-group">
            <label for="new-password-confirmation" class="form-label">Confirm New Password</label>
            <div class="input-group input-group-merge form-password-toggle">
              <input type="password" class="form-control form-control-merge" 
                     id="new-password-confirmation" name="new_password_confirmation" tabindex="3" 
                     placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" 
                     aria-describedby="new-password-confirmation" required minlength="8" />
              <div class="input-group-append">
                <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
              </div>
            </div>
          </div>

          <button type="submit" class="btn btn-primary btn-block" tabindex="4">Change Password</button>
        </form>
      </div>
    </div>
    <!-- /Change Password -->
  </div>
</div>
@endsection

