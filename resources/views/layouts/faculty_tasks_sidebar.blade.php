<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" type="text/css" href="{{ asset('faculty/css/faculty_tasks_sidebar.css') }}">
    </head>
<body>
    <nav id="sidebarMenu" class="collapse d-lg-block sidebar">
        <div class="position-sticky">
            <div class="list-group list-group-flush mx-3">
            <a href="{{ route('faculty-tasks') }}" class="main list-group-item list-group-item-action py-2 ripple bg 
                    {{ (request()->is('faculty-tasks')) ? 'active' : '' }}"  aria-expanded="true">
                <span><b>All Tasks</b></span>
            </a>
            <div id="sub-task">
                <a href="{{ route('faculty-tasks/completed') }}?category=completed" class="list-group-item list-group-item-action py-2 sub ripple bg
                {{ (request()->is('faculty-tasks/completed')) ? 'active' : '' }}">
                    <span>Completed</span>
                </a>
                <a href="{{ route('faculty-tasks/late-completed') }}?category=late-completed" class="list-group-item list-group-item-action py-2 sub ripple bg
                {{ (request()->is('faculty-tasks/late-completed')) ? 'active' : '' }}">
                    <span>Late Completed</span>
                </a>
                <a href="{{ route('faculty-tasks/ongoing') }}?category=ongoing" class="list-group-item list-group-item-action py-2 sub ripple bg
                {{ (request()->is('faculty-tasks/ongoing')) ? 'active' : '' }}">
                    <span>Ongoing</span>
                </a>
                <a href="{{ route('faculty-tasks/missing') }}?category=missing" class="list-group-item list-group-item-action py-2 sub ripple bg
                {{ (request()->is('faculty-tasks/missing')) ? 'active' : '' }}">
                    <span>Missing</span>
                </a>
            </div>
            
            </div>
        </div>
    </nav>
</body>
</html>
