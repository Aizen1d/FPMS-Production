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
            
            <h1 class="my-3 title">Token Expired</h1>

            <div class="input mb-4">
                @if(Session::get('success'))
                    <div class="alert alert-success" style="text-align: center;">
                        {{ Session::get('success') }}
                    </div>
                @endif

                <div class="alert alert-danger" style="text-align: center;">
                    The token for your forgot password was expired. Please try again.
                </div>
    
                <div class="submit">
                    <a href="{{ route('login-faculty') }}">
                        <button class="sign-in mx-3 my-2">Return</button>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script>
@endsection