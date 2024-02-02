@extends('layouts.default')
@include('layouts.loader')

@section('title', 'PUPQC - Admin Login')

@section('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('auth/css/login_admin.css') }}">
@endsection

@section('body')
    <div class="login container">
        <div class="components">
            <div class="logo">
                <img src="{{ asset('auth/images/user.svg') }}" width="30%" height="30%" alt="logo">
            </div>
            
            <h1 class="my-3">Admin Login</h1>

            <div class="input mb-4">
                <form action="{{ route('validate-login-admin') }}" method="post">
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
                        <label class="form-label label-css" for="email">Email</label>
                        <div class="input-group">
                            <span class="input-group-text span-color input-shadow" id="emailSpan"><i class="fa fa-user fa-lg fa-fw" aria-hidden="true"></i></span>
                                <input
                                    class="form-control input-shadow user-input"
                                    type="email"
                                    id="emailInput"
                                    name="email" 
                                    value="{{ old('email') }}"
                                />
                        </div>
                        <span class="text-danger">@error('email') {{ $message }} @enderror</span>
                        <div class="invalid-feedback">
                            Please enter your email.
                        </div>
                        
                    </div>
                    
                    <div class="form-group mx-5">
                        <label class="form-label label-css" for="password">Password</label>
                        <div class="input-group">
                            <span class="input-group-text span-color input-shadow" id="passwordSpan"><i class="fa fa-lock fa-lg fa-fw password-toggle" aria-hidden="true"></i></span>
                                <input
                                    class="form-control input-shadow"
                                    type="password"
                                    id="passwordInput"
                                    name="password" 
                                />
                        </div>
                        <span class="text-danger">@error('password') {{ $message }} @enderror</span>
                        <div class="invalid-feedback">
                            Please enter your password.
                        </div>

                        <div class="row px-2 my-2 hyperlinks">
                          
                        </div>
                    </div>

                    <div class="submit">
                        <button type="button" class="return mx-3 my-2" onclick="returnChooseLogin()">Return</button>
                        <button class="sign-in mx-3 my-2">Sign In</button>
                    </div>
                </form>
            </div>
            @include('messages.terms_and_conditions')
        </div>
    </div>

    <script>
        function returnChooseLogin() {
            // prevent the form from being submitted
            event.preventDefault();

            window.location.href = "{{ route('choose-login') }}";
        }

        const userInput = document.getElementById("emailInput");
        const passInput = document.getElementById("passwordInput");

        function checkUserInput() {
            const userIcon = document.querySelector('#emailSpan i');
            if (userInput.value == "") {
                userIcon.style.transition = 'all 0.7s ease-in-out';
                userIcon.style.color = '#e9e9e9';
            }
            else{ 
                userIcon.style.transition = 'all 0.7s ease-in-out';
                userIcon.style.color = '#FFE644';
            }
        }

        function checkPasswordInput() {
            const passIcon = document.querySelector('#passwordSpan i');
            if (passInput.value == "") {
                passIcon.style.transition = 'all 0.7s ease-in-out';
                passIcon.style.color = '#e9e9e9';
            }
            else{ 
                passIcon.style.transition = 'all 0.7s ease-in-out';
                passIcon.style.color = '#FFE644';
            }
        }

        userInput.addEventListener("input", function(event) {
            // Detect when input is written anything.
            checkUserInput();
        });

        passInput.addEventListener("input", function(event) {
            // Detect when input is written anything.
            checkPasswordInput();
        });

        // When page refreshes, check inputs
        checkUserInput();
        checkPasswordInput();
    </script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script>
@endsection