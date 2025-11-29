@extends('layouts/fullLayoutMaster')

@section('title', 'Forgot Password')

@section('page-style')
{{-- Page Css files --}}
<link rel="stylesheet" href="{{ asset(mix('css/base/pages/page-auth.css')) }}">
@endsection

@section('content')
<div class="auth-wrapper auth-v1 px-2">
  
  <div class="auth-inner py-2">
    @if (session('success'))
    <div class="alert alert-success border-left-3 border-left-success alert-dismissible fade show shadow-sm mb-2" role="alert">
        <div class="alert-body">
            <div class="d-flex align-items-center">
                <i data-feather="check-circle" class="font-medium-3 mr-2"></i>
                <div>
                    <strong class="d-block mb-50">Success!</strong>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    </div>
    @endif
  
    @if (session('error'))
    <div class="alert alert-danger border-left-3 border-left-danger alert-dismissible fade show shadow-sm mb-2" role="alert">
        <div class="alert-body">
            <div class="d-flex align-items-center">
                <i data-feather="x-circle" class="font-medium-3 mr-2"></i>
                <div>
                    <strong class="d-block mb-50">Error!</strong>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    </div>
    @endif
  
    @if ($errors->any())
    <div class="alert alert-danger border-left-3 border-left-danger alert-dismissible fade show alert-validation-msg shadow-sm mb-2" role="alert">
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
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    </div>
    @endif
    <!-- Forgot Password v1 -->
    <div class="card mb-0">
      <div class="card-body">
        <a href="javascript:void(0);" class="brand-logo" style="display: flex; justify-content: center; align-items: center; margin-bottom: 1.5rem; padding: 0.5rem 0;">
          <img src="{{asset('images/logo/LOGO.svg')}}" alt="TrackLet" style="max-width: 200px; max-height: 100px; height: auto; width: auto; object-fit: contain; display: block;">
        </a>

        <h4 class="card-title mb-1">Forgot Password? 🔒</h4>
        <p class="card-text mb-2">Enter your email and we'll send you instructions to reset your password</p>

        <form class="auth-forgot-password-form mt-2" method="POST" action="{{ route('password.email') }}">
          @csrf
          <div class="form-group">
            <label for="forgot-password-email" class="form-label">Email</label>
            <input type="text" class="form-control @error('email') is-invalid @enderror" id="forgot-password-email" name="email" value="{{ old('email') }}" placeholder="john@example.com" aria-describedby="forgot-password-email" tabindex="1" autofocus />
             @error('email')
              <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
              </span>
            @enderror
          </div>
          <button type="submit" class="btn btn-primary btn-block" tabindex="2">Send reset link</button>
        </form>

        <p class="text-center mt-2">
          @if (Route::has('login'))
          <a href="{{ route('login') }}"> <i data-feather="chevron-left"></i> Back to login </a>
          @endif
        </p>
      </div>
    </div>
    <!-- /Forgot Password v1 -->
  </div>
</div>
@endsection
