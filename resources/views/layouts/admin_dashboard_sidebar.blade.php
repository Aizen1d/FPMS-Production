<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" type="text/css" href="{{ asset('admin/css/tasks_sidebar.css') }}">
    </head>
<body>
    <nav id="sidebarMenu" class="collapse d-lg-block sidebar">
        <div class="position-sticky">
            <div class="list-group list-group-flush mx-3">
                <a href="{{ route('admin-dashboard/department-task/statistics') }}" class="main list-group-item list-group-item-action py-2 ripple bg 
                        {{ (request()->is('admin-dashboard/department-task/statistics')) ? 'active' : '' }}"  aria-expanded="true">
                    <span><b>Program Tasks</b></span>
                </a>
                <a href="{{ route('admin-dashboard/department-task/timeline') }}" class="main list-group-item list-group-item-action py-2 ripple bg 
                        {{ (request()->is('admin-dashboard/department-task/timeline')) ? 'active' : '' }}"  aria-expanded="true">
                    <span><b>Assigned Task Timeline</b></span>
                </a>
            </div>
        </div>
    </nav>
</body>
</html>
