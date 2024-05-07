@extends('layouts.default')

@section('title', 'PUPQC - Assigned Memo')

@section('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('faculty/css/faculty_home_department_assigned_tasks.css') }}">
@endsection

@section('body')
<div class="overlay"></div>
@include('layouts.faculty_navbar')
@include('layouts.faculty_home_department_sidebar')
@include('layouts.notification_side')

<div class="container-fluid margin">
    <div class="row">
        <div class="col-7">
            <h1 class="my-4 title">{{ $department }} Program <span style="font-size: 0.4em; color: #9c9a9a; font-weight: normal;"></span></h1>
        </div>
        <div class="col-2 pages">
            {{ $tasks->links()  }}
        </div>
        <div class="col-3 drop-down-container">
            <input type="text" class="search-input mx-5" id="search-input" placeholder="Search memo...">
            <div id="search-results"></div>

        </div>
    </div>

    <div class="container-fluid task-list" style="position: relative;">
        <div class="row">
            <div class="col-7">
                <h5 class="my-3 column-name" style="z-index: 100; position: relative;">Memo</h5>
            </div>
            <div class="col-2">
                <h5 class="my-3 column-name" style="z-index: 100; position: relative;">Date Created</h5>
            </div>
            <div class="col-3">
                <h5 class="my-3 column-name" style="z-index: 100; position: relative;">Due Date</h5>
            </div>
        </div>

        @foreach ($tasks as $task)
        <div class="row task-row">
            <div class="col-7">
                <h5 class="task-row-content my-2 task-name-text" style="text-align: left; margin-left: 47%">{{ $task->task_name }}</h5>
            </div>
            <div class="col-2">
                <h5 class="task-row-content my-2 date-created" style="text-align: left; margin-left: 27.5%">{{ date('F j, Y', strtotime($task->date_created)) }}<br>{{ date('g:i A', strtotime($task->date_created)) }}</h5>
            </div>
            <div class="col-3">
                @if (Carbon\Carbon::parse($task->due_date)->isPast())
                <h5 class="task-row-content my-2 text-danger due-date" style="text-align: left; margin-left: 40%">{{ date('F j, Y', strtotime($task->due_date)) }}<br>{{ date('g:i A', strtotime($task->due_date)) }}</h5>
                @else
                <h5 class="task-row-content my-2 due-date" style="text-align: left; margin-left: 40%">{{ date('F j, Y', strtotime($task->due_date)) }}<br>{{ date('g:i A', strtotime($task->due_date)) }}</h5>
                @endif
            </div>
        </div>
        @endforeach

        <div id="loading-overlay-search" style="display: none; border-radius: 25px; z-index: 99; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: white; justify-content: center; align-items: center;">
            <!-- Add your loading spinner or other visual indicator here -->
            <div class="spinner-border text-dark" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
    </div>

    <script>
        const elements = document.querySelectorAll('.list-group-item');
        ///////////////////////////////////////

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
            let getDepartment = '{{ $department }}';
            fetch(`/faculty-home/department/assigned-tasks/category/search?query=${query}&category=missing&department=${getDepartment}`)
                .then(response => response.json())
                .then(newTasks => {
                    console.log(newTasks);
                    tasks = newTasks;
                    newlyAdded = null;
                    requestAnimationFrame(renderTasks);

                    // Hide the loading overlay
                    document.getElementById('loading-overlay-search').style.display = 'none';
                });
        }, 50);

        searchInput.addEventListener('input', debouncedInputHandler);

        // Handle task row click //
        function getSelectedTaskRow(taskName) {
            let url = new URL('/faculty-tasks/get-task', window.location.origin);
            url.searchParams.append('taskName', taskName);
            url.searchParams.append('requestSource', 'department');

            window.location.href = url;
        }

        // Add event listeners for all rows of task
        const getTaskRows = document.querySelectorAll('.task-row');

        for (let i = 0; i < getTaskRows.length; i++) {
        const getTaskRow = getTaskRows[i];
        const getTaskName = getTaskRow.querySelector('.task-name-text').textContent;

        getTaskRow.addEventListener('click', function() {
                getSelectedTaskRow(getTaskName);
            });
        }

        function renderTasksSearch() {
            // Hide all rows
            taskRows.forEach(row => row.style.display = 'none');


            // Show only rows that match fetched data
            for (let i = 0; i < tasks.length; i++) {
                const task = tasks[i];
                const taskRow = taskRows[i];
                console.log(tasks);
                if (taskRow) {
                    taskRow.style.display = '';

                    const taskNameText = taskRow.querySelector('.task-name-text');
                    const facultyNameImg = taskRow.querySelector('.faculty-name img');
                    const facultyNameText = taskRow.querySelector('.faculty-name-text');
                    const dateCreatedElements = taskRow.querySelectorAll('.date-created');
                    const dueDateElements = taskRow.querySelectorAll('.due-date');

                    taskNameText.textContent = task.task_name;
                    facultyNameImg.src = task.faculty_image;
                    facultyNameText.textContent = task.faculty_name;
                    dateCreatedElements[0].innerHTML = `${task.date_created_formatted}<br>${task.date_created_time}`;
                    dueDateElements[0].innerHTML = `${task.due_date_formatted}<br>${task.due_date_time}`;

                    // Check if the due date is in the past and add text-danger
                    if (task.due_date_past) {
                        dueDateElements[0].classList.add('text-danger');
                    } else {
                        dueDateElements[0].classList.remove('text-danger');
                    }
                }
            }
        }

        function renderTasks() {
            // Get the container element for the task rows
            const taskList = document.querySelector('.task-list');

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
                <div class="col-7">
                    <h5 class="task-row-content my-2 task-name-text">${task.task_name}</h5>
                </div>
                <div class="col-2">
                    <h5 class="task-row-content my-2 date-created">${task.date_created_formatted}<br>${task.date_created_time}</h5>
                </div>
                <div class="col-3">
                    <h5 class="task-row-content my-2 due-date">${task.due_date_formatted}<br>${task.due_date_time}</h5>
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