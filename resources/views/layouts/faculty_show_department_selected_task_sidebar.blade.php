<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" type="text/css" href="{{ asset('faculty/css/faculty_selected_task_sidebar.css') }}">
    </head>
<body>
    <nav id="sidebarMenu" class="collapse d-lg-block sidebar">
        <div class="position-sticky">
            <div class="list-group list-group-flush mx-3">
            <a href="{{ route('faculty-home/department/assigned-tasks') }}" class="main list-group-item list-group-item-action py-2 ripple bg"  aria-expanded="true">
                <i class="fa fa-reply"></i> 
            </a>
            <div id="sub-task">
                <a href="{{ route('faculty-tasks/get-task/instructions') }}" class="list-group-item list-group-item-action py-2 sub ripple bg
                {{ (request()->is('faculty-tasks/get-task/instructions')) ? 'active' : '' }}">
                    <span>Instructions</span>
                </a>
            </div>
            </div>
        </div>
    </nav>
</body>
</html>
