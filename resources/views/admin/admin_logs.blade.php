@extends('layouts.default')

@section('title', 'PUPQC - Logs')

@section('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('admin/css/admin_logs.css') }}">
@endsection

@section('body')
<div class="overlay"></div>
@include('layouts.admin_navbar')
@include('layouts.admin_logs_sidebar')
@include('layouts.notification_side')

<div class="container-fluid margin">
    <div class="row">
        <div class="col-5">
            <h1 class="my-4 title">Logs <span style="font-size: 0.4em; color: #9c9a9a; font-weight: normal;">Note: Actions made by the admin and faculty are listed here.</span></h1>
        </div>
        <div class="col-3 pages">
            {{ $tasks->links()  }}
        </div>
        <div class="col-4 drop-down-container">
            <input type="text" class="search-input mx-5" id="search-input" placeholder="Search action made..">
            <div id="search-results"></div>
        </div>
    </div>

    <div class="container-fluid task-list" style="position: relative;">
        <div class="row">
            <div class="col-2">
                <h5 class="my-3 column-name" style="z-index: 100; position: relative;">User ID</h5>
            </div>
            <div class="col-2">
                <h5 class="my-3 column-name" style="z-index: 100; position: relative;">User Role</h5>
            </div>
            <div class="col-4">
                <h5 class="my-3 column-name" style="z-index: 100; position: relative; text-align:left; margin-left: 19%">Action Made</h5>
            </div>
            <div class="col-2">
                <h5 class="my-3 column-name" style="z-index: 100; position: relative;">Type of Action</h5>
            </div>
            <div class="col-2">
                <h5 class="my-3 column-name" style="z-index: 100; position: relative;">Date</h5>
            </div>
        </div>

        <div class="task-list-rows-data">
            @foreach ($tasks as $task)
            <div class="row task-row">
                <div class="col-2">
                    @if ($task->user_id)
                        <h5 class="task-row-content my-2 task-name-text" style="text-align:left; margin-left: 45%">{{ $task->user_id }}</h5>
                    @else
                    <h5 class="task-row-content my-2 task-name-text" style="text-align:left; margin-left: 45%">Not Available</h5>
                    @endif
                </div>
                <div class="col-2">
                    <h5 class="task-row-content my-2 task-name-text" style="text-align:left; margin-left: 40%">{{ $task->user_role }}</h5>
                </div>
                <div class="col-4">
                    <h5 class="task-row-content my-2 task-name-text" style="text-align:left; margin-left: 20%">{{ $task->action_made }}</h5>
                </div>
                <div class="col-2">
                    <h5 class="task-row-content my-2 task-name-text" style="text-align:left; margin-left: 30%">{{ $task->type_of_action }}</h5>
                </div>
                <div class="col-2">
                    <h5 class="task-row-content my-2 date-created" style="text-align:left; margin-left: 40%">{{ date('F j, Y', strtotime($task->created_at)) }}<br>{{ date('g:i A', strtotime($task->created_at)) }}</h5>
                </div>
            </div>
            @endforeach
        </div>

        <div id="loading-overlay-search" style="display: none; border-radius: 25px; z-index: 99; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: white; justify-content: center; align-items: center;">
            <!-- Add your loading spinner or other visual indicator here -->
            <div class="spinner-border text-dark" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
    </div>

    <script>
        const searchInput = document.querySelector('#search-input');
        const taskRows = document.querySelectorAll('.task-row');

        let tasks = [];

        function debounce(func, wait) {
            let timeout;
            return function(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), wait);
            }
        }

        const debouncedInputHandler = debounce(function(event) {
            // Set a timeout to show the loading overlay after a delay
            //loadingOverlayTimeout = setTimeout(() => {
            document.getElementById('loading-overlay-search').style.display = 'flex';
            //}, 500);

            const query = encodeURIComponent(event.target.value);
            fetch(`/admin-logs/search?query=${query}`)
                .then(response => response.json())
                .then(newTasks => {
                    tasks = newTasks;
                    newlyAdded = null;
                    requestAnimationFrame(renderTasks);

                    // Clear the timeout and hide the loading overlay
                    document.getElementById('loading-overlay-search').style.display = 'none';
                });
        }, 50);

        searchInput.addEventListener('input', debouncedInputHandler);

        function renderTasks() {
            // Get the container element for the task rows
            const taskList = document.querySelector('.task-list-rows-data');

            // Remove all existing task rows
            taskList.querySelectorAll('.task-row').forEach(row => row.remove());

            // Loop through the tasks array and create a new row for each task
            for (let i = 0; i < tasks.length; i++) {
                const task = tasks[i];

                // Create a new row element
                const taskRow = document.createElement('div');
                taskRow.classList.add('row', 'task-row');

                // Add an event listener to the taskRow element
                taskRow.addEventListener('click', function() {
                    getSelectedTaskRow(task.task_name);
                });

                if (newlyAdded) {
                    if (task.task_name === newlyAdded.task_name) { // Check if it is the newly added task row
                        taskRow.classList.add('newly-added'); // Add the newly-added class to the task row element

                        // Remove the newly-added class after 3 seconds
                        setTimeout(() => {
                            taskRow.classList.remove('newly-added');
                        }, 3000);
                    }
                }

                // Add the task data to the row element
                taskRow.innerHTML = `
                    <div class="col-2">
                        <h5 class="task-row-content my-2 task-name-text" style="text-align:left; margin-left: 45%">${task.user_id ? task.user_id : 'Not Available'}</h5>
                    </div>
                    <div class="col-2">
                        <h5 class="task-row-content my-2 task-name-text" style="text-align:left; margin-left: 40%">${task.user_role}</h5>
                    </div>
                    <div class="col-4">
                        <h5 class="task-row-content my-2 task-name-text" style="text-align:left; margin-left: 20%">${task.action_made}</h5>
                    </div>
                    <div class="col-2">
                        <h5 class="task-row-content my-2 task-name-text" style="text-align:left; margin-left: 30%">${task.type_of_action}</h5>
                    </div>
                    <div class="col-2">
                        <h5 class="task-row-content my-2 date-created" style="text-align:left; margin-left: 40%">${task.date}<br>${task.date_time}</h5>
                    </div>
                `;

                // Check if the due date is in the past and add text-danger
                if (task.due_date_past) {
                    taskRow.querySelector('.due-date').classList.add('text-danger');
                }

                // Append the new row to the container element
                taskList.appendChild(taskRow);
            }
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script>
    @endsection