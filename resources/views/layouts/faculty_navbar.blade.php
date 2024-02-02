<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" type="text/css" href="{{ asset('faculty/css/faculty_navbar.css') }}">
    </head>
<body>
<nav class="navbar navbar-expand-lg nav navbar-dark">
    <div class="container-fluid">
        <a href="{{ route('faculty-home') }}" class="navbar-brand mx-2">
            <img src="{{ asset('faculty/images/home.svg') }}" width="50vw" height="50vh" alt="">
        </a>

        <h1 class="system mx-5 my-2">Faculty Performance Monitoring System</h1>

        <div class="collapse navbar-collapse">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link content mx-5 {{ (request()->is('faculty-home*')) ? 'navbar-active' : '' }}" 
                    aria-current="page" href="{{ route('faculty-home') }}">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link content mx-5 {{ (request()->is('faculty-tasks*')) ? 'navbar-active' : '' }}"
                     href="{{ route('faculty-tasks') }}">My Tasks</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link content mx-5 {{ (request()->is('faculty-dashboard*')) ? 'navbar-active' : '' }}"
                     href="{{ route('faculty-dashboard/my-tasks') }}">Dashboard</a>
                </li>
            </ul>
        </div>
        
        <li class="nav-item dropdown">
            <button class="btn btn-link nav-link dropdown-toggle mx-4 user" href="#" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="{{ asset('faculty/images/user.png') }}" width="20vw" height="20vh" alt="">
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="{{ route('faculty-profile') }}">Profile</a></li>
                <li><a class="dropdown-item" href="{{ route('logout-faculty') }}">Logout</a></li>
            </ul>
        </li>
    </div>
</nav>  

</body>
</html>
