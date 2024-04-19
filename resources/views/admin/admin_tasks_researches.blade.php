@extends('layouts.default')

@section('title', 'PUPQC - Researches')

@section('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('admin/css/admin_tasks_researches.css') }}">
@endsection

@section('body')
<div class="overlay"></div>
@include('layouts.admin_navbar')
@include('layouts.admin_tasks_sidebar')
@include('layouts.notification_side')

<div class="container-fluid margin">
    <div class="row">
        <div class="col-4">
            <h1 class="my-4 title">Researches</h1>
        </div>
        <div class="col-2 pages">
            {{ $researches->links()  }}
        </div>
        <div class="col-6 drop-down-container">
            <input type="text" class="search-input mx-5" id="search-input" placeholder="Search research title...">
            <div id="search-results"></div>
        </div>
    </div>

    <div class="container-fluid task-list" style="position: relative;">
        <div class="row">
            <div class="col-3">
                <h5 class="my-3 column-name" style="z-index: 100; position: relative;">Title</h5>
            </div>
            <div class="col-2">
                <h5 class="my-3 column-name" style="z-index: 100; position: relative;">Type</h5>
            </div>
            <div class="col-4">
                <h5 class="my-3 column-name" style="z-index: 100; position: relative;">Authors</h5>
            </div>
            <div class="col-3">
                <h5 class="my-3 column-name" style="z-index: 100; position: relative;">Created At</h5>
            </div>
        </div>

        <div class="task-container">
            @foreach ($researches as $research)
            <div class="row task-row" onclick="getSelectedResearchRow({{ $research }})">
                <div class="col-3">
                    <h5 class="task-row-content my-2 task-name-text" style="text-align:left; margin-left: 45%">{{ $research->title }}</h5>
                </div>
                <div class="col-2">
                    <h5 class="task-row-content my-2 task-name-text" style="text-align:left; margin-left: 42%">{{ $research->type }}</h5>
                </div>
                <div class="col-4">
                    <h5 class="task-row-content my-2 task-name-text" style="text-align:left; margin-left: 43%">{{ $research->authors }}</h5>
                </div>
                <div class="col-3">
                    <h5 class="task-row-content my-2 date-created" style="text-align:left; margin-left: 39%">
                        {{ date('F j, Y', strtotime($research->created_at)) }}
                        <br>
                        {{ date('g:i A', strtotime($research->created_at)) }}
                    </h5>
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
        const elements = document.querySelectorAll('.list-group-item');
        var selectedMembers = []; // selected items or checkboxes in department members
        var selectedFiles = []; // Files selected to be uploaded

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
            document.getElementById('loading-overlay-search').style.display = 'flex';

            const query = encodeURIComponent(event.target.value);
            fetch(`/admin-tasks/researches/search?query=${query}`)
                .then(response => response.json())
                .then(data => {
                    const researches = Object.values(data.researches)
                    const researchContainer = document.querySelector('.task-container');
                    researchContainer.innerHTML = '';

                    researches.forEach(research => {
                        const row = `
                            <div class="row task-row" onclick="getSelectedResearchRow(${JSON.stringify(research)})">
                                <div class="col-3">
                                    <h5 class="task-row-content my-2 task-name-text" style="text-align:left; margin-left: 45%">${research.title}</h5>
                                </div>
                                <div class="col-2">
                                    <h5 class="task-row-content my-2 task-name-text" style="text-align:left; margin-left: 42%">${research.type}</h5>
                                </div>
                                <div class="col-4">
                                    <h5 class="task-row-content my-2 task-name-text" style="text-align:left; margin-left: 43%">${research.authors}</h5>
                                </div>
                                <div class="col-3">
                                    <h5 class="task-row-content my-2 date-created" style="text-align:left; margin-left: 39%">
                                        ${research.date_created_formatted}
                                        <br>
                                        ${research.date_created_time}
                                    </h5>
                                </div>
                            </div>
                        `;

                        researchContainer.innerHTML += row;
                    });

                    document.getElementById('loading-overlay-search').style.display = 'none';
                });
        }, 50);

        searchInput.addEventListener('input', debouncedInputHandler);

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
                <div class="col-4">
                    <h5 class="task-row-content my-2 task-name-text" style="text-align:left; margin-left: 41%">${task.task_name}</h5>
                </div>
                <div class="col-3">
                    <div style="text-align:left; margin-left: 31%; display: flex">
                        <img src="${task.faculty_image}" alt=" ">
                        <h5 class="task-row-content px-3 my-3 faculty-name-text">${task.faculty_name}</h5>
                    </div>
                </div>
                <div class="col-2">
                    <h5 class="task-row-content my-2 date-created" style="text-align:left; margin-left: 27%">${task.date_created_formatted}<br>${task.date_created_time}</h5>
                </div>
                <div class="col-3">
                    <h5 class="task-row-content my-2 due-date" style="text-align:left; margin-left: 40%">${task.due_date_formatted}<br>${task.due_date_time}</h5>
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

        // NEW FUNCTIONALITY FOR RESEARCHES
        function getSelectedResearchRow(research) {
            console.log(research);
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script>
    @endsection