@extends('layouts/contentLayoutMaster')

@section('title', 'Account Settings')

@section('vendor-style')
<!-- vendor css files -->
@endsection
@section('page-style')
<!-- Page css files -->
@endsection

@section('content')

@include('panels.response')

<!-- account setting page -->
<section id="page-account-settings">
    @php
        $user = auth()->user();
    @endphp
    <div class="row">
        <!-- left menu section -->
        <div class="col-md-3 mb-2 mb-md-0">
            <ul class="nav nav-pills flex-column nav-left" id="my-tab">
                <!-- general -->
                <li class="nav-item">
                    <a class="nav-link active" id="account-pill-general" data-toggle="pill"
                        href="#account-vertical-general" aria-expanded="true">
                        <i data-feather="user" class="font-medium-3 mr-1"></i>
                        <span class="font-weight-bold">General</span>
                    </a>
                </li>
                <!-- change password -->
                <li class="nav-item">
                    <a class="nav-link" id="account-pill-password" data-toggle="pill" href="#account-vertical-password"
                        aria-expanded="false">
                        <i data-feather="lock" class="font-medium-3 mr-1"></i>
                        <span class="font-weight-bold">Change Password</span>
                    </a>
                </li>
                <!-- information -->
            </ul>
        </div>
        <!--/ left menu section -->

        <!-- right content section -->
        <div class="col-md-9">
            <div class="card">
                <div class="card-body">
                    <div class="tab-content">
                        <!-- general tab -->
                        <div role="tabpanel" class="tab-pane active" id="account-vertical-general"
                            aria-labelledby="account-pill-general" aria-expanded="true">
                            <!-- header media -->
                            <div class="media">
                                <a href="javascript:void(0);" class="mr-25">
                                    <img src="{{asset('images/portrait/small/avatar-s-11.jpg')}}" alt="vendor logo"
                                        class="user-avatar user-logo users-avatar-shadow rounded mr-50 my-25 cursor-pointer"
                                        height="80" width="80" />
                                </a>

                                {{-- userID div is for app-user-edit.js file. It is being used for fetching user id.
                                --}}
                                <div style="display:none" id="userID">{{ $user->id }}</div>

                                <!-- upload and reset button -->
                                <div class="media-body mt-75 ml-1">
                                    <label for="change-logo" class="btn btn-primary mb-75 mr-75">Upload</label>
                                    <input type="file" id="change-logo" hidden accept="image/*" />
                                    <button onclick="deleteLogo()" class="btn btn-outline-danger mb-75">Remove</button>
                                </div>
                                <!--/ upload and reset button -->
                            </div>
                            <!--/ header media -->

                            <!-- form -->
                            {{-- TODO: Edit Form to better reflect field data --}}
                            <form class="jquery-val-form mt-2" method="POST" enctype="multipart/form-data"
                                action="{{ route('profile.update') }}">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="col-lg-6 col-sm-12">
                                        <div class="form-group">
                                            <label class="form-label" for="name">Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="name" name="name"
                                                placeholder="John Doe" value="{{ old('name', $user->name) }}" required />
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-sm-12">
                                        <div class="form-group">
                                            <label class="form-label" for="email">Email</label>
                                            <input type="email" class="form-control" id="email" name="email"
                                                placeholder="Email" readonly value="{{ old('email', $user->email) }}" />
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary mt-2 mr-1">Save changes</button>
                                        <a class="text-dark btn btn-outline-secondary mt-2"
                                            href="">Cancel</a>
                                    </div>
                                </div>
                            </form>
                            <!--/ form -->
                        </div>
                        <!--/ general tab -->

                        <!-- change password -->
                        <div class="tab-pane fade" id="account-vertical-password" role="tabpanel"
                            aria-labelledby="account-pill-password" aria-expanded="false">
                            <!-- form -->
                            <form class="jquery-val-form mt-2" method="POST"
                                action="{{ route('password.profile.update') }}">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="col-lg-6 col-sm-12">
                                        <div class="form-group">
                                            <label class="form-label" for="old-password">Old Password</label>
                                            <div class="input-group form-password-toggle input-group-merge">
                                                <input type="password" class="form-control" id="old-password"
                                                    name="old-password" autocomplete="new-password"
                                                    placeholder="Old Password" />
                                                <div class="input-group-append">
                                                    <div class="input-group-text cursor-pointer">
                                                        <i data-feather="eye"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6 col-sm-12">
                                        <div class="form-group">
                                            <label class="form-label" for="new-password">New Password</label>
                                            <div class="input-group form-password-toggle input-group-merge">
                                                <input type="password" id="new-password" name="new-password"
                                                    class="form-control" autocomplete="new-password"
                                                    placeholder="New Password" />
                                                <div class="input-group-append">
                                                    <div class="input-group-text cursor-pointer">
                                                        <i data-feather="eye"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-sm-12">
                                        <div class="form-group">
                                            <label class="form-label" for="new_password-confirmation">Retype New
                                                Password</label>
                                            <div class="input-group form-password-toggle input-group-merge">
                                                <input type="password" class="form-control"
                                                    id="new-password_confirmation" name="new-password_confirmation"
                                                    placeholder="Confirm New Password" />
                                                <div class="input-group-append">
                                                    <div class="input-group-text cursor-pointer"><i
                                                            data-feather="eye"></i></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary mr-1 mt-1">Save changes</button>
                                        <a class="text-dark btn btn-outline-secondary mt-1"
                                            href="">Cancel</a>
                                    </div>
                                </div>
                            </form>
                            <!--/ form -->
                        </div>
                        <!--/ change password -->
                    </div>
                </div>
            </div>
        </div>
        <!--/ right content section -->
    </div>
</section>
<!-- / account setting page -->
@endsection

@section('vendor-script')
<!-- vendor files -->
<script src="{{ asset(mix('vendors/js/forms/validation/jquery.validate.min.js')) }}"></script>
<script src="{{ asset(mix('vendors/js/forms/select/select2.full.min.js')) }}"></script>
<script src="{{ asset(mix('vendors/js/extensions/sweetalert2.all.min.js')) }}"></script>
@endsection
@section('page-script')
<!-- Page js files -->
{{-- <script src="{{ asset(mix('js/scripts/mgshop/page-account-settings.js')) }}"></script> --}}
<script src="{{ asset(mix('js/scripts/forms/form-select2.js')) }}"></script>
<script src="{{ asset(mix('js/scripts/forms/form-file-uploader.js')) }}"></script>
<script src="https://kit.fontawesome.com/575e285a0f.js" crossorigin="anonymous"></script>
{{-- Custom Scripts --}}
<script>
    // ajax call for removing logo
        function deleteLogo(){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                type: "DELETE",
                url: '/user/logo/delete/'+'{{ $user->id }}',
                success: function(success){
                    userLogo = $('.user-logo');
                    userLogo.attr('src','{{ asset('storage/logos/default.svg') }}');
                },
                error: function(error){
                    alert("Error while removing logo");
                }
            });
        }
</script>

{{-- stay on the same tab on page reload --}}
<script>
    $(document).ready(function(){
            $('.nav-link').on('show.bs.tab', function(e) {
                localStorage.setItem('activeTab', $(e.target).attr('href'));
            });
            var activeTab = localStorage.getItem('activeTab');
            if(activeTab){
                $('#my-tab a[href="' + activeTab + '"]').tab('show');
            }
        });
</script>

{{-- Profile form validation --}}
<script>
    $(document).ready(function() {
        // Add custom regex validation method
        $.validator.addMethod("regex", function(value, element, regexp) {
            var re = new RegExp(regexp);
            return this.optional(element) || re.test(value);
        }, "Please check your input.");

        // Validate profile form
        $('.jquery-val-form').validate({
            rules: {
                'name': {
                    required: true,
                    minlength: 2,
                    regex: /^[a-zA-Z0-9\s]+$/
                }
            },
            messages: {
                'name': {
                    required: 'Please enter your name',
                    minlength: 'Name must be at least 2 characters',
                    regex: 'Name may only contain letters, numbers, and spaces. Special characters are not allowed.'
                }
            }
        });
    });
</script>
@endsection