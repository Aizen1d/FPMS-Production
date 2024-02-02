<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" type="text/css" href="{{ asset('admin/css/admin_selected_task_sidebar.css') }}">
    </head>
<body>
    <nav id="sidebarMenu" class="collapse d-lg-block sidebar">
        <div class="position-sticky">
            <div class="list-group list-group-flush mx-3">
            <a href="{{ route('admin-home/show-department/assigned-tasks') }}" class="main list-group-item list-group-item-action py-2 ripple bg"  aria-expanded="true">
                <i class="fa fa-reply"></i> 
            </a>
            <div id="sub-task">
                <a href="{{ route('admin-tasks/get-task/instructions') }}" class="list-group-item list-group-item-action py-2 sub ripple bg
                {{ (request()->is('admin-tasks/get-task/instructions')) ? 'active' : '' }}">
                    <span>Instructions</span>
                </a>
                <a href="{{ route('admin-tasks/get-task/submissions') }}" class="list-group-item list-group-item-action py-2 sub ripple bg
                {{ (request()->is('admin-tasks/get-task/submissions')) ? 'active' : '' }}">
                    <span>Submissions</span>
                </a>
                <a href="{{ route('admin-tasks/get-task/task-overview') }}" class="list-group-item list-group-item-action py-2 sub ripple bg
                {{ (request()->is('admin-tasks/get-task/task-overview')) ? 'active' : '' }}">
                    <span>Task Overview</span>
                </a>
            </div>
            
            </div>
        </div>
    </nav>
</body>
</html>
