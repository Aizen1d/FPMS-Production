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
                <span><b>All Memo</b></span>
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
            
            <a href="{{ route('faculty-tasks/researches') }}" class="main list-group-item list-group-item-action py-2 ripple bg 
                    {{ (request()->is('faculty-tasks/researches')) ? 'active' : '' }}"  aria-expanded="true">
                <span><b>All Researches</b></span>
            </a>
            <div id="sub-task">
                <a href="{{ route('faculty-tasks/researches/completed') }}" class="list-group-item list-group-item-action py-2 sub ripple bg
                {{ (request()->is('faculty-tasks/researches/completed')) ? 'active' : '' }}">
                    <span>Completed</span>
                </a>
                <a href="{{ route('faculty-tasks/researches/presented') }}" class="list-group-item list-group-item-action py-2 sub ripple bg
                {{ (request()->is('faculty-tasks/researches/presented')) ? 'active' : '' }}">
                    <span>Presented</span>
                </a>
                <a href="{{ route('faculty-tasks/researches/published') }}" class="list-group-item list-group-item-action py-2 sub ripple bg
                {{ (request()->is('faculty-tasks/researches/published')) ? 'active' : '' }}"">
                    <span>Published</span>
                </a>
            </div>

            <label class="main mx-3 py-2 ripple bg" aria-expanded="true">
                <span><b>Attendance to Functions</b></span>
            </label>
            <div id="sub-task">
                <a href="{{ route('faculty-tasks/attendance') }}" class="list-group-item list-group-item-action py-2 sub ripple bg
                {{ (request()->is('faculty-tasks/attendance')) ? 'active' : '' }}">
                    <span>My Attendance</span>
                </a>
                <a href="{{ route('faculty-tasks/functions') }}" class="list-group-item list-group-item-action py-2 sub ripple bg
                {{ (request()->is('faculty-tasks/functions')) ? 'active' : '' }}">
                    <span>Functions</span>
                </a>
            </div>
            
            <a href="{{ route('faculty-tasks/seminars') }}" class="main list-group-item list-group-item-action py-2 ripple bg 
                    {{ (request()->is('faculty-tasks/seminars')) ? 'active' : '' }}"  aria-expanded="true">
                <span><b>Trainings & Seminars</b></span>
            </a>

            </div>
        </div>
    </nav>
</body>
</html>
