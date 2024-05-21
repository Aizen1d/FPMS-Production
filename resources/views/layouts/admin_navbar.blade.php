<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" type="text/css" href="{{ asset('admin/css/admin_navbar.css') }}">
    </head>
<body>
<nav class="navbar navbar-expand-lg nav navbar-dark sticky">
    <div class="container-fluid">
        <a href="{{ route('admin-home') }}" class="navbar-brand mx-2">
            <img src="{{ asset('admin/images/home.svg') }}" width="50vw" height="50vh" alt="">
        </a>

        <h1 class="system mx-5 my-2">Faculty Performance Monitoring System</h1>

        <div class="collapse navbar-collapse">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link content mx-5 {{ (request()->is('admin-home*')) ? 'navbar-active' : '' }}" 
                    aria-current="page" href="{{ route('admin-home') }}">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link content mx-5 {{ (request()->is('admin-tasks*')) ? 'navbar-active' : '' }}" 
                    aria-current="page" href="{{ route('admin-tasks') }}">Accomplishments</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link content mx-5 {{ (request()->is('admin-dashboard*')) ? 'navbar-active' : '' }}" 
                    aria-current="page" href="{{ route('admin-dashboard/department-task/statistics') }}">Analytics</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link content mx-5 {{ (request()->is('admin-requests/*')) ? 'navbar-active' : '' }}" 
                    href="{{ route('admin-requests/account') }}">Requests</a>
                </li>
                <li class="nav-item">
                <a class="nav-link content mx-5 {{ (request()->is('admin-logs*')) ? 'navbar-active' : '' }}" 
                    href="{{ route('admin-logs') }}">Logs</a>
                </li>
            </ul>
        </div>
        
        <li class="nav-item dropdown">
            <button class="btn btn-link nav-link dropdown-toggle mx-4 user" href="#" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="{{ asset('admin/images/user.png') }}" width="20vw" height="20vh" alt="">
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="{{ route('logout-admin') }}">Logout</a></li>
            </ul>
        </li>
    </div>
</nav>  

</body>
</html>
