<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" type="text/css" href="{{ asset('admin/css/tasks_sidebar.css') }}">
    </head>
<body>
    <nav id="sidebarMenu" class="collapse d-lg-block sidebar">
        <div class="position-sticky">
            <div class="list-group list-group-flush mx-3">
                <a href="{{ route('admin-tasks') }}" class="main list-group-item list-group-item-action py-2 ripple bg 
                        {{ (request()->is('admin-tasks')) ? 'active' : '' }}"  aria-expanded="true">
                    <span><b>All Memo</b></span>
                </a>
                <div id="sub-task">
                    <a href="{{ route('admin-tasks/completed') }}?category=completed" class="list-group-item list-group-item-action py-2 sub ripple bg
                    {{ (request()->is('admin-tasks/completed')) ? 'active' : '' }}">
                        <span>Completed</span>
                    </a>
                    <a href="{{ route('admin-tasks/late-completed') }}?category=late-completed" class="list-group-item list-group-item-action py-2 sub ripple bg
                    {{ (request()->is('admin-tasks/late-completed')) ? 'active' : '' }}">
                        <span>Late Completed</span>
                    </a>
                    <a href="{{ route('admin-tasks/ongoing') }}?category=ongoing" class="list-group-item list-group-item-action py-2 sub ripple bg
                    {{ (request()->is('admin-tasks/ongoing')) ? 'active' : '' }}"">
                        <span>Ongoing</span>
                    </a>
                    <a href="{{ route('admin-tasks/missing') }}?category=missing" class="list-group-item list-group-item-action py-2 sub ripple bg
                    {{ (request()->is('admin-tasks/missing')) ? 'active' : '' }}"">
                        </i><span>Missing</span>
                    </a>
                </div>
            </div>

            <div class="list-group list-group-flush mx-3">
                <label class="main mx-3 py-2 ripple bg" aria-expanded="true">
                    <span><b>Attendance to Functions</b></span>
                </label>
                <div id="sub-task">
                    <a href="{{ route('admin-tasks/attendance') }}" class="list-group-item list-group-item-action py-2 sub ripple bg
                    {{ (request()->is('admin-tasks/attendance')) ? 'active' : '' }}">
                        <span>Faculties Attendance</span>
                    </a>
                    <a href="{{ route('admin-tasks/functions') }}" class="list-group-item list-group-item-action py-2 sub ripple bg
                    {{ (request()->is('admin-tasks/functions')) ? 'active' : '' }}">
                        <span>Functions</span>
                    </a>
                </div>
            </div>

            <div class="list-group list-group-flush mx-3">
                <a href="{{ route('admin-tasks/faculty-performance') }}" class="main list-group-item list-group-item-action py-2 ripple bg 
                        {{ (request()->is('admin-tasks/faculty-performance')) ? 'active' : '' }}"  aria-expanded="true">
                    <span><b>Faculty Performance</b></span>
                </a>
            </div>
        </div>
    </nav>
</body>
</html>
