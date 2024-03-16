@extends('layouts.default')

@section('title', 'PUPQC - Department Tasks')

@section('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('admin/css/admin_show_department_assigned_tasks.css') }}">
@endsection

@section('body')
<div class="overlay"></div>
@include('layouts.admin_navbar')
@include('layouts.admin_show_department_sidebar')
@include('layouts.notification_side')

<div class="container-fluid margin">
    <div class="row">
        <div class="col-4">
            <h1 class="my-4 title">{{ $department }} Program</h1>
        </div>
        <div class="col-2 pages">
            {{ $tasks->links()  }}
        </div>
        <div class="col-6 drop-down-container">
            <input type="text" class="search-input mx-5" id="search-input" placeholder="Search task name...">
            <div id="search-results"></div>

            <button class="my-4 create-btn" onclick="createNewTask()">Create Task</button>
        </div>
    </div>

    <div class="create-task-popup">
        <div class="row">
            <div class="col-9" style="display: flex; justify-content: space-between;">
                <h4 class="create-label">Create Task</h4>

                <div class="task-name">
                    <label for="task" id="taskLabel">Click to set task name:</label>
                    <input type="text" id="task" class="edit">
                </div>
            </div>
            <div class="col-3">
                <button class="close-task-btn" onclick="closeNewTask()"><i class="fa fa-times"></i></button>
            </div>
        </div>

        <div class="body-frame">
            <div class="row">
                <div class="col-6">
                    <div class="drop-down create-dropdown">
                        <div class="wrapper">
                            <div class="selected">Select to assign members</div>
                        </div>
                        <i class="fa fa-caret-down caret2"></i>

                        <div class="list create-list">
                            @foreach ($members as $index => $member)
                            <div class="item2">
                                <input type="checkbox" id="checkbox-{{ $index }}">
                                <img src="{{ asset('admin/images/PUPLogo.png') }}" alt="">
                                <div class="text">
                                    {{ $member->first_name }}{{ $member->middle_name ? ' ' . $member->middle_name : '' }} {{ $member->last_name }}
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-3"></div>
                <div class="col-3 deadline-column">
                    <label class="set-deadline-label">Set Deadline</label>
                    <input type="datetime-local" id="date-time-picker" min="1997-01-01" max="2030-01-01">
                </div>
            </div>

            <label class="task-description-label" for="description">Task Description:</label><br>
            <textarea class="task-description-content" id="description" name="description" rows="4" cols="50" placeholder="Enter your description here.."></textarea>

            <div style="display: flex; flex-direction: row">
                <div style="margin-right: 20px">
                    <label for="file-upload" class="custom-file-upload">
                        <i class="fa fa-cloud-upload px-1" style="color: #82ceff;"></i> Upload Files
                    </label>
                    <input id="file-upload" type="file" multiple accept=".docx,.pdf,.xls,.xlsx,.png,.jpeg,.jpg,.ppt,.pptx" />
                    <div id="drop-zone">
                        <p>Drop your files here</p>
                    </div>
                </div>
                <div id="preview" class="preview-no-items" style="text-align:center; z-index: 99;">
                    <p class="preview-label">Files uploaded are displayed here</p>
                </div>
            </div>

            <button class="create-btn create-task" onclick="createTask()">Create Task</button>
        </div>

        <div id="loading-overlay" class="loading-create-task" style="display: none; justify-content: center; align-items: center; border-radius: 25px; z-index: 99; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: white;">
            <div style="display: flex; flex-direction: column; align-items: center;">
                <div class="spinner-border text-dark" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <div id="loading-text" style="margin-top: 3px;">Creating task, this may take a few seconds.</div>
            </div>
        </div>

    </div>

    <div class="container-fluid task-list" style="position: relative;">
        <div class="row">
            <div class="col-4">
                <h5 class="my-3 column-name" style="z-index: 100; position: relative;">Task Name</h5>
            </div>
            <div class="col-4">
                <h5 class="my-3 column-name" style="z-index: 100; position: relative;">Date Created</h5>
            </div>
            <div class="col-4">
                <h5 class="my-3 column-name" style="z-index: 100; position: relative;">Due Date</h5>
            </div>
        </div>

        @foreach ($tasks as $task)
        <div class="row task-row">
            <div class="col-4">
                <h5 class="task-row-content my-2 task-name-text" style="text-align:left; margin-left: 41%">{{ $task->task_name }}</h5>
            </div>
            <div class="col-4">
                <h5 class="task-row-content my-2 date-created" style="text-align:left; margin-left: 39%">{{ date('F j, Y', strtotime($task->created_at)) }}<br>{{ date('g:i A', strtotime($task->created_at)) }}</h5>
            </div>
            <div class="col-4">
                @if (Carbon\Carbon::parse($task->due_date)->isPast())
                <h5 class="task-row-content my-2 text-danger due-date" style="text-align:left; margin-left: 42%">{{ date('F j, Y', strtotime($task->due_date)) }}<br>{{ date('g:i A', strtotime($task->due_date)) }}</h5>
                @else
                <h5 class="task-row-content my-2 due-date" style="text-align:left; margin-left: 42%">{{ date('F j, Y', strtotime($task->due_date)) }}<br>{{ date('g:i A', strtotime($task->due_date)) }}</h5>
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
        var selectedMembers = []; // selected items or checkboxes in department members
        var selectedFiles = []; // Files selected to be uploaded

        // create task popup
        function createNewTask() {
            let memberCount = "{{ $memberCount }}";
            if (memberCount > 0) {
                const popup = document.querySelector('.create-task-popup');
                popup.style.display = 'block';

                void popup.offsetWidth;
                popup.classList.add('create-task-popup-animate');

                const overlay = document.querySelector('.overlay');
                overlay.classList.add('blur');
            } else {
                showNotification("Unable to create task, no member in this department.", '#fe3232bc');
            }
        }

        function closeNewTask() {
            const popup = document.querySelector('.create-task-popup');
            popup.style.display = 'none';
            popup.classList.remove('create-task-popup-animate');

            const overlay = document.querySelector('.overlay');
            overlay.classList.remove('blur');
        }

        function resetTaskInputs() {
            // Clear the value of the task name input
            document.querySelector('#taskLabel').textContent = 'Click to set task name:';
            document.querySelector("#task").value = '';

            // Clear selected members
            const assignMembersCheckboxes = document.querySelectorAll('.item2 input[type="checkbox"]');
            assignMembersCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            selectedMembers.length = 0;

            // Clear the value of the deadline input
            document.querySelector('#date-time-picker').value = '';

            // Clear the value of the description textarea
            document.querySelector('#description').value = '';

            // Clear the file upload input
            selectedFiles.length = 0;
            document.querySelector('#file-upload').value = null;

            // Clear the preview element
            const previewElement = document.querySelector('#preview');
            previewElement.innerHTML = '<p class="preview-label">Files uploaded are displayed here</p>';
            previewElement.classList.add('preview-no-items');
        }

        // Edit task name
        let taskLabel = document.getElementById("taskLabel");
        let taskInput = document.getElementById("task");

        taskLabel.addEventListener("click", function() {
            taskLabel.classList.add("edit");
            taskInput.classList.remove("edit");
            if (taskLabel.textContent === "Click to set task name:") {
                taskInput.value = "";
            } else {
                taskInput.value = taskLabel.textContent;
            }
            taskInput.focus();
        });

        taskInput.addEventListener("blur", function() {
            taskLabel.classList.remove("edit");
            taskInput.classList.add("edit");
            if (taskInput.value === "") {
                taskLabel.textContent = "Click to set task name:";
            } else {
                taskLabel.textContent = taskInput.value;
            }
        });

        function adjustFontSize() {
            let fontSize = parseInt(window.getComputedStyle(taskLabel).getPropertyValue("font-size"));
            while (taskLabel.scrollWidth > taskLabel.offsetWidth && fontSize > 10) {
                fontSize--;
                taskLabel.style.fontSize = fontSize + "px";
            }
        }

        adjustFontSize();

        window.addEventListener("resize", adjustFontSize);

        //////// Create Task dropdown select members to assign to ////////
        const dropdown2 = document.querySelector('.create-dropdown');
        const list2 = document.querySelector('.create-list');
        const caret2 = document.querySelector('.caret2');

        dropdown2.addEventListener('click', () => {
            list2.classList.toggle('show');
            caret2.classList.toggle('fa-rotate');
        });

        document.addEventListener('click', (e) => {
            if (!dropdown2.contains(e.target)) {
                list2.classList.remove('show');
                caret2.classList.remove('fa-rotate');
            }
        });

        let items = document.querySelectorAll('.item2');
        items.forEach(item => {
            item.addEventListener('click', (event) => {
                event.stopPropagation();
            });
        });

        // Assign to checkbox scripts
        let checkboxes = document.querySelectorAll('input[type="checkbox"]');
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', () => {
                let itemText = checkbox.nextElementSibling.nextElementSibling.textContent;
                if (checkbox.checked) {
                    selectedMembers.push(itemText.trim());
                } else {
                    let index = selectedMembers.indexOf(itemText.trim());
                    if (index !== -1) {
                        selectedMembers.splice(index, 1);
                    }
                }
            });
        });

        // Create task date time picker
        const picker = document.querySelector('#date-time-picker');

        picker.addEventListener('change', (event) => {
            console.log(event.target.value);
        });

        /// File Upload ///

        document.getElementById("file-upload").onchange = function() {
            var files = document.getElementById("file-upload").files;
            if (files.length === 0) {
                // The user clicked the cancel button in the file upload dialog
                console.log('Upload cancelled');
            } else {
                handleFiles(files);
            }
        };

        var dropZone = document.getElementById("drop-zone");
        dropZone.addEventListener("dragover", function(evt) {
            evt.preventDefault();
        }, false);
        dropZone.addEventListener("drop", function(evt) {
            evt.preventDefault();
            var files = evt.dataTransfer.files;
            handleFiles(files);
        }, false);

        function handleFiles(files) {
            for (var i = 0; i < files.length; i++) {
                var file = files[i];
                if (file.type.startsWith("image/") ||
                    file.type === "application/vnd.openxmlformats-officedocument.wordprocessingml.document" ||
                    file.type === "application/pdf" ||
                    file.type === "application/vnd.ms-excel" ||
                    file.type === "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" ||
                    file.type === "application/vnd.ms-powerpoint" ||
                    file.type === "application/vnd.openxmlformats-officedocument.presentationml.presentation") {
                    selectedFiles.push(file);
                } else {
                    showNotification("Only allowed files only (Images, Docx, PDF, PPTX, Excel)", '#fe3232bc');
                    //alert("Only docx, pdf, excel, png and jpeg files are allowed.");
                    break;
                }
            }
            updatePreview();
        }

        function updatePreview() {
            var preview = document.getElementById("preview");
            preview.innerHTML = "";
            for (var i = 0; i < selectedFiles.length; i++) {
                var file = selectedFiles[i];
                var div = document.createElement("div");
                div.className = "file-container";
                if (file.type.startsWith("image/")) {
                    var img = document.createElement("img");
                    img.file = file;
                    div.appendChild(img);
                    var reader = new FileReader();
                    reader.onload = (function(aImg) {
                        return function(e) {
                            aImg.src = e.target.result;
                        };
                    })(img);
                    reader.readAsDataURL(file);
                } else {
                    var p = document.createElement("p");
                    p.textContent = file.name;
                    div.appendChild(p);
                }
                var removeButton = document.createElement("button");
                removeButton.textContent = "x";
                removeButton.className = "remove-file";
                removeButton.dataset.index = i;
                div.appendChild(removeButton);
                preview.appendChild(div);
            }

            if (selectedFiles.length <= 0) {
                preview.innerHTML = "Files uploaded are displayed here";
                preview.classList.add('preview-no-items');
            } else {
                preview.classList.remove('preview-no-items');
            }
        }

        document.getElementById("preview").addEventListener("click", function(evt) {
            if (evt.target.classList.contains("remove-file")) {
                var index = parseInt(evt.target.dataset.index);
                selectedFiles.splice(index, 1);
                updatePreview();
            }
        });

        function createTask() {
            const taskLabel = document.getElementById("taskLabel");
            const assignto = selectedMembers;
            const dateTimePicker = document.querySelector('#date-time-picker').value;
            const description = document.querySelector('.task-description-content').value;

            // Validations
            if (taskLabel.textContent === "Click to set task name:") {
                showNotification("Set task name first.", '#fe3232bc');
                return;
            }
            if (assignto.length === 0) {
                showNotification("Assign to a faculty member first.", '#fe3232bc');
                return;
            }
            if (!dateTimePicker) {
                showNotification("Set a deadline first.", '#fe3232bc');
                return;
            }
            if (!description) {
                showNotification("Give atleast task description.", '#fe3232bc');
                return;
            }

            const data = new FormData();
            // append your task data to the form data
            data.append('taskname', taskLabel.textContent)
            data.append('assignto', JSON.stringify(assignto));
            data.append('selectFaculty', "{{ $department }}");
            data.append('dateTimePicker', dateTimePicker);
            data.append('description', description);

            // append the files to the form data
            for (const file of selectedFiles) {
                data.append('files[]', file);
            }
            
            console.log(assignto);

            document.querySelector('.loading-create-task').style.display = 'flex';
            let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            fetch('/admin-home/show-department/assigned-tasks/create-task', {
                    headers: {
                        "Accept": "application/json, text-plain, */*",
                        "X-Requested-With": "XMLHttpRequest",
                        "X-CSRF-TOKEN": token
                    },
                    method: 'POST',
                    credentials: "same-origin",
                    body: data
                })
                .then(response => response.json())
                .then(result => {
                    if (result.error) {
                        document.querySelector('.loading-create-task').style.display = 'none';
                        showNotification(result.error, '#fe3232bc');
                    } else {
                        document.querySelector('.loading-create-task').style.display = 'none';

                        tasks = result.allTasks;
                        newlyAdded = result.newlyAddedTask;
                        requestAnimationFrame(renderTasks);
                        resetTaskInputs();
                        closeNewTask();
                        showNotification("(" + data.get('taskname') + ") task is successfully created.", '#1dad3cbc');
                    }
                })
                .catch(error => {
                    document.querySelector('.loading-create-task').style.display = 'none';
                    showNotification("Error occured, please create task later.", '#fe3232bc');
                });
        }

        // Loading message while creating task
        let div = document.getElementById("loading-text");
        let text = ["Creating task, this may take a few seconds.",
            "Creating task, this may take a few seconds..",
            "Creating task, this may take a few seconds..."
        ];
        let i = 0;
        setInterval(function() {
            div.innerHTML = text[i];
            i = (i + 1) % text.length;
        }, 400);

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
            fetch(`/admin-home/show-department/assigned-tasks/search?query=${query}`)
                .then(response => response.json())
                .then(newTasks => {
                    tasks = newTasks;
                    newlyAdded = null;
                    requestAnimationFrame(renderTasks);

                    // Hide the loading overlay
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

        // Handle task row click
        function getSelectedTaskRow(taskName) {
            let url = new URL('/admin-tasks/get-task', window.location.origin);
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
                <div class="col-4">
                    <h5 class="task-row-content my-2 date-created" style="text-align:left; margin-left: 39%">${task.date_created_formatted}<br>${task.date_created_time}</h5>
                </div>
                <div class="col-4">
                    <h5 class="task-row-content my-2 due-date" style="text-align:left; margin-left: 42%">${task.due_date_formatted}<br>${task.due_date_time}</h5>
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