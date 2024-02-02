<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" type="text/css" href="{{ asset('admin/css/requests_sidebar.css') }}">
    </head>
<body>
    <nav id="sidebarMenu" class="collapse d-lg-block sidebar">
        <div class="position-sticky">
            <div class="list-group list-group-flush mx-3">
                <a href="{{ route('admin-requests/account') }}" class="main list-group-item list-group-item-action py-2 ripple bg 
                        {{ (request()->is('admin-requests/account')) ? 'active' : '' }}">
                    <span><b>Account</b></span>
                </a>
                <a href="{{ route('admin-requests/department') }}" class="main list-group-item list-group-item-action py-2 ripple bg 
                        {{ (request()->is('admin-requests/department')) ? 'active' : '' }}">
                    <span><b>Program</b></span>
                </a>
                
            </div>
        </div>
    </nav>
</body>
</html>
