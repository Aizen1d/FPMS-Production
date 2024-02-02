@extends('layouts.default')

@section('title', 'PUPQC - Faculty Reset Password')

@section('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('auth/css/faculty_forgot_reset.css') }}">
@endsection

@section('body')
    <div class="login container input-shadow">
        <div class="components">
            <div class="logo">
                <img src="{{ asset('auth/images/user.svg') }}" width="125px" height="125px" alt="logo">
            </div>
            
            <h1 class="my-3 title">Faculty Reset Password</h1>

            <div class="input mb-4">
                <form action="{{ route('faculty-password-reset-action') }}" method="post">
                    <label class="form-label label-css instructions" for="username">
                    You can enter your new password below</label>

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
                    <input type="hidden" name="token" value="{{ $token }}">
                    <div class="form-group mx-5">
                        <label class="form-label label-css" for="email">Email</label>
                            <input
                                class="form-control input-shadow"
                                type="email"
                                id="email"
                                name="email" 
                                value="{{ $email ?? old('email') }}"
                                readonly
                            />
                        
                            <span style="font-size: 90%;" class="text-danger">@error('email') {{ $message }} @enderror</span>
                    </div>

                    <div class="form-group mx-5">
                        <label class="form-label label-css" for="password">Password</label>
                            <input
                                class="form-control input-shadow"
                                type="password"
                                id="password"
                                name="password" 
                            />

                            <span style="font-size: 90%;" class="text-danger">@error('password') {{ $message }} @enderror</span>
                    </div>

                    <div class="form-group mx-5">
                        <label class="form-label label-css" for="password_confirmation">Confirm Password</label>
                            <input
                                class="form-control input-shadow"
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation" 
                            />

                            <span style="font-size: 90%;" class="text-danger">@error('password') {{ $message }} @enderror</span>
                    </div>

                    <div class="submit">
                        <button class="sign-in mx-3 my-2">Reset</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script>
@endsection