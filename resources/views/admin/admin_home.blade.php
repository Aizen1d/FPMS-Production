@extends('layouts.default')
@include('layouts.loader')

@section('title', 'PUPQC - Admin Home')

@section('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/css/admin_home.css') }}">
@endsection

@section('body')
@include('layouts.admin_navbar')
@include('layouts.notification_side')

<div class="container-fluid background">
    <div class="container text-center">
        <h1 class="faculty-name py-2">PROGRAMS</h1>
        @foreach ($departments->chunk(3) as $chunk)
            <div class="row py-5">
                @foreach ($chunk as $department)
                    <div class="col faculty">
                        <img class="click-receiver faculty-image" data-department="{{ $department->department_name }}" 
                            src="{{ asset('admin/images/home.svg') }}" width="200vw" height="200vh" alt="">
                        <h3 class="faculty-name py-2">
                            {{ $department->department_name }}
                        </h3>
                        <h6 class="faculty-members">
                            {{ $department->number_of_members }} {{ $department->number_of_members > 1 ? 'Members' : 'Member' }}
                        </h6>
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
</div>

<script>
    var elements = document.querySelectorAll('.click-receiver');
    for (var i = 0; i < elements.length; i++) {
        elements[i].addEventListener('click', function(event) {
            var target = event.target;
            var department = target.dataset.department;

            let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            let url = new URL('/admin-home/show-department', window.location.origin);
            url.searchParams.append('department', department);

            window.location.href = url;
        });
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script>
@endsection