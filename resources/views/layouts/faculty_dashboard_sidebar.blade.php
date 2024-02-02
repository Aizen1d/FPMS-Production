<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" type="text/css" href="{{ asset('faculty/css/faculty_dashboard_sidebar.css') }}">
    </head>
<body>
    <nav id="sidebarMenu" class="collapse d-lg-block sidebar">
        <div class="position-sticky">
            <div class="list-group list-group-flush mx-3">
                <a href="{{ route('faculty-dashboard/my-tasks') }}" class="main list-group-item list-group-item-action py-2 ripple bg 
                        {{ (request()->is('faculty-dashboard/my-tasks')) ? 'active' : '' }}"  aria-expanded="true">
                    <span><b>My Tasks</b></span>
                </a>
                <a href="{{ route('faculty-dashboard/assigned-task/timeline') }}" class="main list-group-item list-group-item-action py-2 ripple bg 
                        {{ (request()->is('faculty-dashboard/assigned-task/timeline')) ? 'active' : '' }}"  aria-expanded="true">
                    <span><b>My Published Tasks</b></span>
                </a>
            </div>
        </div>
    </nav>
</body>
</html>
