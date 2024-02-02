<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" type="text/css" href="{{ asset('admin/css/admin_show_department_sidebar.css') }}">
    </head>
<body>
    <nav id="sidebarMenu" class="collapse d-lg-block sidebar">
        <div class="position-sticky">
            <div class="list-group list-group-flush mx-3">
                <a href="{{ route('admin-home/show-department/return') }}" class="main list-group-item list-group-item-action py-2 ripple bg ">
                    <i class="fa fa-reply"></i> 
                </a>
                <a href="{{ route('admin-home/show-department/assigned-tasks') }}" class="main list-group-item list-group-item-action py-2 ripple bg 
                        {{ (request()->is('admin-home/show-department/assigned-tasks')) ? 'active' : '' }}">
                    <span><b>All Department Tasks</b></span>
                </a>
                <div id="sub-task">
                    <a href="{{ route('admin-home/show-department/assigned-tasks/completed') }}?category=completed" class="list-group-item list-group-item-action py-2 sub ripple bg
                    {{ (request()->is('admin-home/show-department/assigned-tasks/completed')) ? 'active' : '' }}">
                        <span>Completed</span>
                    </a>
                    <a href="{{ route('admin-home/show-department/assigned-tasks/late-completed') }}?category=late-completed" class="list-group-item list-group-item-action py-2 sub ripple bg
                    {{ (request()->is('admin-home/show-department/assigned-tasks/late-completed')) ? 'active' : '' }}">
                        <span>Late Completed</span>
                    </a>
                    <a href="{{ route('admin-home/show-department/assigned-tasks/ongoing') }}?category=ongoing" class="list-group-item list-group-item-action py-2 sub ripple bg
                    {{ (request()->is('admin-home/show-department/assigned-tasks/ongoing')) ? 'active' : '' }}"">
                        <span>Ongoing</span>
                    </a>
                    <a href="{{ route('admin-home/show-department/assigned-tasks/missing') }}?category=missing" class="list-group-item list-group-item-action py-2 sub ripple bg
                    {{ (request()->is('admin-home/show-department/assigned-tasks/missing')) ? 'active' : '' }}"">
                        </i><span>Missing</span>
                    </a>
                </div>
                <a href="{{ route('admin-home/show-department/members') }}" class="main list-group-item list-group-item-action py-2 ripple bg 
                        {{ (request()->is('admin-home/show-department/members')) ? 'active' : '' }}">
                    <span><b>Members</b></span>
                </a>
                <a href="{{ route('admin-home/show-department/overview') }}" class="main list-group-item list-group-item-action py-2 ripple bg 
                        {{ (request()->is('admin-home/show-department/overview')) ? 'active' : '' }}">
                    <span><b>Department Overview</b></span>
                </a>
            </div>
        </div>
    </nav>
</body>
</html>
