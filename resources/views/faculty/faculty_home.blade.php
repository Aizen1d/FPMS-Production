@extends('layouts.default')

@section('title', 'PUPQC - Faculty Home')

@section('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('faculty/css/faculty_home.css') }}">
@endsection

@section('body')
@include('layouts.faculty_navbar')
@include('layouts.notification_side')

<div class="container-fluid background">
    <div class="container text-center">
        <h1 class="faculty-name py-2">PROGRAMS</h1>
        @foreach ($departments->chunk(3) as $chunk)
            <div class="row py-5">
                @foreach ($chunk as $department)
                    <div class="col faculty">
                        <img class="faculty-image" src="{{ asset('admin/images/PUPLogo.png') }}" width="200vw" height="200vh" alt="">
                        <h3 class="faculty-name py-2">{{ $department->department_name }}</h3>
                        <h6 class="faculty-members">
                            {{ $department->number_of_members }} {{ $department->number_of_members > 1 ? 'Members' : 'Member' }}
                        </h6>
                        <a style="text-decoration: none;">
                            <button class="join" data-department="{{ $department->department_name }}" onclick="departmentJoinClicked(this)">
                                Join
                            </button>
                        </a>
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
    <div class="row py-5">
        <div class="col faculty d-flex justify-content-center"> <!-- Add d-flex and justify-content-center classes -->
            <div class="links d-flex justify-content-center">
                {{ $departments->links() }}
            </div>
        </div>
    </div>
</div>

<script>
    const images = document.querySelectorAll('.faculty-image');
    const joins = document.querySelectorAll('.join');

    images.forEach((image, index) => {
        let join = joins[index]
        image.addEventListener('mouseover', () => {
            image.classList.add('hover');
            join.classList.add('join-show');
        });

        image.addEventListener('mouseout', () => {
            image.classList.remove('hover');
            join.classList.remove('join-show');
        });

        join.addEventListener('mouseover', () => {
            join.classList.add('join-show');
            image.classList.add('hover');
        })
    });

    var isJoining = false;

    function departmentJoinClicked(event){
        var departmentName = event.getAttribute('data-department');
        console.log(departmentName);

        // One click only
        if (isJoining === true) {
            return;
        }

        isJoining = true;
        let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        fetch('/faculty-home/join-department', {
            method: 'POST',
            credentials: "same-origin",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json, text-plain, */*",
                "X-Requested-With": "XMLHttpRequest",
                "X-CSRF-TOKEN": token
            },
            body: JSON.stringify({
                department: departmentName
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = '/faculty-home'; 
            }
            else {
                showNotification("Please set up your full name in your profile page first.", '#fe3232bc');
            }
        })
        .catch(error => {
            console.log(error);
            showNotification("Error occured, try again later.", '#fe3232bc');
        });
    }
    
</script>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script>
@endsection