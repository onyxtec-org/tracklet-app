@extends('layouts/fullLayoutMaster')

@section('title', 'Register Organization')

@section('page-style')
{{-- Page Css files --}}
<link rel="stylesheet" href="{{ asset(mix('css/base/pages/page-auth.css')) }}">
<style>
  .auth-wrapper.auth-v1 .auth-inner {
    max-width: 50%;
    margin-left: -148px;
  }
  .brand-logo {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-bottom: 1.5rem;
    padding: 0.5rem 0;
  }
  .brand-logo img {
    max-width: 200px;
    max-height: 100px;
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
    <!-- Register v1 -->
    <div class="card mb-0">
      <div class="card-body">
        <a href="javascript:void(0);" class="brand-logo">
          <img src="{{asset('images/logo/LOGO.svg')}}" alt="TrackLet">
        </a>

        <h4 class="card-title mb-1">Register Your Organization! 🚀</h4>
        <p class="card-text mb-2">Create your organization account and start the adventure</p>

        <form class="auth-register-form mt-2" method="POST" action="{{ route('organization.register') }}">
          @csrf
          <div class="form-group">
            <label for="organization_name" class="form-label">Organization Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('organization_name') is-invalid @enderror" id="organization_name" name="organization_name" placeholder="Acme Corporation" aria-describedby="organization_name" tabindex="1" autofocus value="{{ old('organization_name') }}" required />
            @error('organization_name')
            <span class="invalid-feedback" role="alert">
              <strong>{{ $message }}</strong>
            </span>
            @enderror
          </div>
          <div class="form-group">
            <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="John Doe" aria-describedby="name" tabindex="2" value="{{ old('name') }}" required />
            @error('name')
            <span class="invalid-feedback" role="alert">
              <strong>{{ $message }}</strong>
            </span>
            @enderror
          </div>
          <div class="form-group">
            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="admin@example.com" aria-describedby="email" tabindex="3" value="{{ old('email') }}" required />
            @error('email')
            <span class="invalid-feedback" role="alert">
              <strong>{{ $message }}</strong>
            </span>
            @enderror
            <small class="form-text text-muted">This will be your login email and organization contact email.</small>
          </div>
          <div class="form-group">
            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
            <div class="input-group input-group-merge form-password-toggle">
              <input type="password" class="form-control form-control-merge @error('password') is-invalid @enderror" id="password" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" tabindex="4" required />
              <div class="input-group-append">
                <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
              </div>
            </div>
            @error('password')
            <span class="invalid-feedback" role="alert">
              <strong>{{ $message }}</strong>
            </span>
            @enderror
            <small class="form-text text-muted">Minimum 8 characters</small>
          </div>
          <div class="form-group">
            <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
            <div class="input-group input-group-merge form-password-toggle">
              <input type="password" class="form-control form-control-merge" id="password_confirmation" name="password_confirmation" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password_confirmation" tabindex="5" required />
              <div class="input-group-append">
                <span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="custom-control custom-checkbox">
              <input class="custom-control-input" type="checkbox" id="register-privacy-policy" tabindex="6" />
              <label class="custom-control-label" for="register-privacy-policy">
                I agree to the <a href="{{ route('legal.terms') }}" target="_blank">Terms and Conditions</a> and <a href="{{ route('legal.privacy') }}" target="_blank">Privacy Policy</a>
              </label>
            </div>
          </div>
          <div class="alert alert-info border-left-3 border-left-info mb-2 shadow-sm">
            <div class="alert-body">
              <div class="d-flex align-items-center">
                <i data-feather="info" class="font-medium-3 mr-2"></i>
                <div>
                  <strong class="d-block mb-50">Important:</strong>
                  <span>After registration, you'll need to complete your annual subscription to start using Tracklet.</span>
                </div>
              </div>
            </div>
          </div>
          <button type="submit" class="btn btn-primary btn-block" tabindex="7">Register Organization</button>
        </form>

        <p class="text-center mt-2">
          <span>Already have an account?</span>
          <a href="{{ route('login') }}">
            <span>Sign in instead</span>
          </a>
        </p>
      </div>
    </div>
    <!-- /Register v1 -->
  </div>
</div>
@endsection

