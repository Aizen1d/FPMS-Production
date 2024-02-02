@extends('layouts.default')
@include('layouts.loader')

@section('title', 'PUPQC - Faculty Home')

@section('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('faculty/css/faculty_department_request.css') }}">
@endsection

@section('body')
@include('layouts.faculty_navbar')
@include('layouts.notification_side')

<div class="container-fluid background">
    <div class="container text-center">
        <h1 class="faculty-name py-2">Request Pending</h1>
        <div class="row py-5">
            <div class="col faculty">
                <img class="faculty-image" src="{{ asset('admin/images/PUPLogo.png') }}" width="250vw" height="250vh" alt="">
                <h3 class="faculty-name py-2">{{ $department->department_name }}</h3>
                <h6 class="faculty-members">{{ $department->number_of_members }} Members</h6>
                <h5 class="request-message py-2">Your join request in {{ $department->department_name }} Program is now pending. <br>Please wait for admin to accept it.</h3>
            </div>
        </div>
    </div>
</div>

<script>

</script>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script>
@endsection