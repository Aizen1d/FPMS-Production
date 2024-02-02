@extends('layouts.default')

@section('title', 'PUPQC - Admin Forgot Password')

@section('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('auth/css/admin_forgot_password.css') }}">
@endsection

@section('body')
    <div class="login container input-shadow">
        <div class="components">
            <div class="logo">
                <img src="{{ asset('auth/images/user.svg') }}" width="125px" height="125px" alt="logo">
            </div>
            
            <h1 class="my-3 title">Admin Forgot Password</h1>

            <div class="input mb-4">
                <form action="{{ route('admin-password-link') }}" method="post">
                    <label class="form-label label-css instructions" for="username">
                    If you have forgotten your password or want to change it for security reasons, 
                    you can reset it by entering your registered email when you created your account.</label>

                    @if(Session::get('success'))
                        <div class="alert alert-success" style="text-align: center;">
                            {{ Session::get('success') }}
                        </div>
                    @endif

                    @if(Session::get('fail'))
                        <div class="alert alert-danger" style="text-align: center;">
                            {{ Session::get('fail') }}
                        </div>
                    @endif

                    @csrf
                    <div class="form-group mx-5">
                        <label class="form-label label-css email" for="email">Email</label>
                            <input
                                class="form-control input-shadow"
                                type="email"
                                id="email"
                                name="email" 
                                value="{{ old('email') }}"
                            />
                        
                            <span style="font-size: 90%;" class="text-danger">@error('email') {{ $message }} @enderror</span>
                    </div>

                    <div class="submit">
                        <button type="button" onclick="returnAdminLogin()" class="return mx-3 my-2">Return</button>
                        <button class="sign-in mx-3 my-2">Send</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function returnAdminLogin() {
            event.preventDefault();

            window.location.href = "{{ route('login-admin') }}"
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script>
@endsection