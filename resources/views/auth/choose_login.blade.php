@extends('layouts.default')
@include('layouts.loader')

@section('title', 'PUPQC - Faculty Management System')

@section('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('auth/css/choose_login.css') }}">
@endsection

@section('body')
    <div class="pick container">
        <div>
            <div class="logo">
                <img src="{{ asset('auth/images/home.svg') }}" width="30%" height="30%" alt="logo">
            </div>

            <h1 class="my-4">Faculty Monitoring System</h1>

            <div class="choices mb-4">
                <button type="button" onclick="goLoginFaculty()" class="faculty mx-2 my-2">Faculty</button>
                <button type="button" onclick="goLoginAdmin()" class="admin mx-2 my-2">Admin</button>
            </div>

            @include('messages.terms_and_conditions')
        </div>
    </div>

    <script>
        function goLoginFaculty() {
            window.location.href = "{{ route('login-faculty') }}";
        }

        function goLoginAdmin() {
            window.location.href = "{{ route('login-admin') }}";
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script>
@endsection