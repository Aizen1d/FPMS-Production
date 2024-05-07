@extends('layouts.default')

@section('title', 'PUPQC - Memo Instructions')

@section('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('admin/css/admin_get_task_instructions.css') }}">
@endsection

@section('body')
<div class="overlay"></div>
@include('layouts.admin_navbar')

@if (session('selectedIn') == 'department')
    @include('layouts.admin_show_department_selected_task_sidebar') 
@else
    @include('layouts.admin_selected_task_sidebar')
@endif

@include('layouts.notification_side')

<div class="container-fluid margin">
    <div class="row">
        <div class="col-6">
            <h1 class="my-4 title">{{ $departmentName }} Program</h1>
        </div>
        <div class="col-6 drop-down-container">
            <button class="my-4 create-btn edit-task-btn" onclick="editTask()">Edit Memo</button>
        </div>
    </div>

    <div class="container-fluid task-list" style="position: relative;">
        <div class="row task-info">
            <div class="col-9" style="position: relative; border-right: 1px solid #cccccc;">
                <div class="create-task-popup">
                    <div class="row">
                        <div class="col-9" style="display: flex; justify-content: space-between;">
                            <h4 class="create-label">Click memo name to edit:</h4>

                            <div class="task-name">
                                <label for="task" id="taskLabel">{{ $taskName }}</label>
                                <input type="text" id="task" class="edit">
                            </div>
                        </div>

                    </div>

                    <div class="body-frame">
                        <div class="row">
                            <div class="col-9">
                                <div class="drop-down create-dropdown">
                                    <div class="wrapper">
                                        <div class="selected">Select to assign members</div>
                                    </div>
                                    <i class="fa fa-caret-down caret2"></i>

                                    <div class="list create-list">
                                        @foreach ($members as $member)
                                        <div class="item2">
                                            <input type="checkbox" id="all" class="member-checkbox">
                                            <img src="{{ asset('admin/images/PUPLogo.png') }}" alt="">
                                            <div class="member-text">
                                                {{ $member->first_name }} {{ $member->middle_name ? $member->middle_name . ' ' : '' }}{{ $member->last_name }}
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="col-3 deadline-column">
                                <label class="set-deadline-label">Set Deadline</label>
                                <input type="datetime-local" id="date-time-picker" min="1997-01-01" max="2030-01-01" value="{{ date('Y-m-d\TH:i', strtotime($due_date)) }}">
                            </div>
                        </div>

                        <label class="task-description-label" for="description">Description:</label><br>
                        <textarea class="task-description-content" id="description" name="description" rows="4" cols="50" placeholder="Enter your description here..">{{ $description }}</textarea>

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
                            <div id="preview" class="preview-no-items" style="text-align:center; z-index: 19; position:relative">
                                <p class="preview-label">Uploaded files are displayed here</p>

                                <div id="loading-overlay-files" class="loading-uploaded-files" style="display: none; justify-content: center; align-items: center; border-radius: 25px; z-index: 99; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: white;">
                                    <div style="display: flex; flex-direction: column; align-items: center;">
                                        <div class="spinner-border text-dark" role="status">
                                            <span class="sr-only">Loading...</span>
                                        </div>
                                        <div id="loading-uploaded-text" style="margin-top: 3px;">Retrieving uploaded files..</div>
                                    </div>
                                </div>
                            </div>
                            <div style="display: none;" id="fileNames" data-folder-path="{{ $folderPath }}"></div>
                        </div>

                        <button class="create-btn create-task" onclick="createTask()">Save Memo</button>
                    </div>

                    <div id="loading-overlay" class="loading-create-task" style="display: none; justify-content: center; align-items: center; border-radius: 25px; z-index: 99; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: white;">
                        <div style="display: flex; flex-direction: column; align-items: center;">
                            <div class="spinner-border text-dark" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                            <div id="loading-text" style="margin-top: 3px;">Creating memo, this may take a few seconds.</div>
                        </div>
                    </div>
                </div>

                <div id="loading-overlay" class="loading-save-task" style="display: none; justify-content: center; align-items: center; border-radius: 25px; z-index: 99; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: white;">
                    <div style="display: flex; flex-direction: column; align-items: center;">
                        <div class="spinner-border text-dark" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <div id="loading-text" style="margin-top: 3px;">Updating task, this may take a few seconds.</div>
                    </div>
                </div>

            </div>

            <div class="col-3 parent-members-col">
                <div class="row members-assigned-row column-1 d-flex align-items-center justify-content-center">
                    <h5 class="my-3 column-name" style="z-index: 100;">Members Assigned ({{ $numberOfAssignedMembers }})</h5>
                </div>

                @foreach ($assignedMembers as $memberName)
                <div class="row item-row">
                    <div class="column-1 d-flex align-items-center">
                        <img src="{{ asset('faculty/images/user-profile.png') }}" alt=" " class="mx-3 member-picture">
                        <h5 class="item-row-content my-2 column-1-text">
                            {{ $memberName }}
                        </h5>
                    </div>
                </div>
                @endforeach

            </div>
        </div>

    </div>

    <script>
        var uploadedFilesLoaded = true;
        var taskID = '{{ $taskID }}';
        var initialTaskName = '{{ $taskName }}'; // Used to update the task in the database if ever
        var currentDepartmentName = '{{ $departmentName }}';
        var selectedMembers = []; // selected items or checkboxes in department members
        var selectedFiles = []; // Files selected to be uploaded
        var additionalFiles = []; // Additional files selected 

        // Send async request to server to retrieve uploaded files on this task
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                let folderPath = document.querySelector('#fileNames').dataset.folderPath;

                if (folderPath) {
                    uploadedFilesLoaded = false;
                    document.querySelector('.edit-task-btn').style.backgroundColor = '#dbd9d9';
                    document.querySelector('.loading-uploaded-files').style.display = 'flex';
                    fetch('/admin-tasks/get-task/get-attachments?folderPath=' + encodeURIComponent(folderPath))
                        .then(response => response.json())
                        .then(fileNames => {
                            // Loop through the file names and create a new File object for each one
                            for (let fileName of fileNames) {
                                let file = new File([], fileName);
                                selectedFiles.push(file);
                            }
                            document.querySelector('.edit-task-btn').style.backgroundColor = 'var(--muted_maroon)';
                            document.querySelector('.loading-uploaded-files').style.display = 'none';

                            // Update the content of the placeholder element with the file names
                            let fileNamesElement = document.querySelector('#fileNames');
                            fileNamesElement.innerHTML = fileNames.join(', ');

                            updatePreview();
                            uploadedFilesLoaded = true;

                            let removeButtons = document.querySelectorAll('.remove-file');
                            removeButtons.forEach(function(removeButton) {
                                removeButton.style.display = 'none';
                            });

                            

                        });
                }
            }, 100); // Delay the execution by 1 second
        });

        var isReadOnlyTask = true;

        function readOnlyTask() {
            // Disable member selection dropdown
            isReadOnlyTask = true;

            const createTaskPopup = document.querySelector('.create-task-popup');

            // Disable all input elements
            const inputs = createTaskPopup.querySelectorAll('input');
            for (let input of inputs) {
                input.disabled = true;
            }

            // Make all textarea elements read-only
            const textareas = createTaskPopup.querySelectorAll('textarea');
            for (let textarea of textareas) {
                textarea.readOnly = true;
            }

            // Disable all button elements
            const buttons = createTaskPopup.querySelectorAll('button');
            for (let button of buttons) {
                button.disabled = true;
            }

            // Remove the 'x' button on displayed files
            let removeButtons = document.querySelectorAll('.remove-file');
            removeButtons.forEach(function(removeButton) {
                removeButton.style.display = 'none';
            });

            // Change colors when read only
            document.querySelector('.create-dropdown').style.backgroundColor = '#dbd9d9';
            document.querySelector('.create-dropdown').style.cursor = 'default';

            document.querySelector('.custom-file-upload').style.backgroundColor = '#dbd9d9';
            document.querySelector('.custom-file-upload').style.cursor = 'default';

            document.querySelector('.create-task').style.backgroundColor = '#dbd9d9';
            document.querySelector('.create-task').style.cursor = 'default';

        }

        readOnlyTask();

        // Select checkboxes of assigned department members
        let assignedTo = "{{ $assignedto }}";
        let assignedToNames = assignedTo.split(", ");

        let textElements = document.querySelectorAll(".member-text");

        for (let name of assignedToNames) {
            let memberElement = Array.prototype.filter.call(textElements, function(element) {
                return element.textContent.trim() === name;
            })[0];

            if (memberElement) {
                let checkboxElement = memberElement.parentElement.querySelector(".member-checkbox");
                checkboxElement.checked = true;
                selectedMembers.push(memberElement.textContent.trim());
            }
        }

        // Get a reference to all the checkboxes
        let checkboxes = document.querySelectorAll('.member-checkbox');

        // Loop through all the checkboxes
        checkboxes.forEach(function(checkbox) {
            // Add an event listener to this checkbox that listens for changes to its checked property
            checkbox.addEventListener('change', function() {
                // Get the name of the person associated with this checkbox
                let personName = this.nextElementSibling.nextElementSibling.textContent.trim();

                // Check if the checkbox is checked
                if (this.checked) {
                    // If the checkbox is checked, add the person to the selectedMembers array
                    selectedMembers.push(personName);
                    console.log(selectedMembers);
                } else {
                    // If the checkbox is not checked, remove the person from the selectedMembers array
                    let index = selectedMembers.indexOf(personName);
                    if (index !== -1) {
                        selectedMembers.splice(index, 1);
                        console.log(selectedMembers);
                    }
                }
            });
        });

        const elements = document.querySelectorAll('.list-group-item');

        // create task popup
        function editTask() {
            if (uploadedFilesLoaded) {
                if (isReadOnlyTask === true) {
                    isReadOnlyTask = false;
                    document.querySelector('.edit-task-btn').innerHTML = 'Cancel editing task';

                    const createTaskPopup = document.querySelector('.create-task-popup');

                    // Disable all input elements
                    const inputs = createTaskPopup.querySelectorAll('input');
                    for (let input of inputs) {
                        input.disabled = false;
                    }

                    // Make all textarea elements read-only
                    const textareas = createTaskPopup.querySelectorAll('textarea');
                    for (let textarea of textareas) {
                        textarea.readOnly = false;
                    }

                    // Disable all button elements
                    const buttons = createTaskPopup.querySelectorAll('button');
                    for (let button of buttons) {
                        button.disabled = false;
                    }

                    // Remove the 'x' button on displayed files
                    let removeButtons = document.querySelectorAll('.remove-file');
                    removeButtons.forEach(function(removeButton) {
                        removeButton.style.display = 'block';
                    });

                    // Change colors when read only
                    document.querySelector('.create-dropdown').style.backgroundColor = '#fff';
                    document.querySelector('.create-dropdown').style.cursor = 'pointer';

                    document.querySelector('.custom-file-upload').style.backgroundColor = '#fff';
                    document.querySelector('.custom-file-upload').style.cursor = 'pointer';

                    document.querySelector('.create-task').style.backgroundColor = 'var(--muted_maroon)';
                    document.querySelector('.create-task').style.cursor = 'pointer';
                } else {
                    readOnlyTask();
                    document.querySelector('.edit-task-btn').innerHTML = 'Edit Task';
                }
            }
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
            if (isReadOnlyTask === false) {
                list2.classList.toggle('show');
                caret2.classList.toggle('fa-rotate');
            }
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
            if (!isReadOnlyTask) {
                evt.preventDefault();
            }
        }, false);

        dropZone.addEventListener("drop", function(evt) {
            if (!isReadOnlyTask) {
                evt.preventDefault();
                var files = evt.dataTransfer.files;
                handleFiles(files);
            }
        }, false);

        function handleFiles(files) {
            for (var i = 0; i < files.length; i++) {
                var file = files[i];
                var isDuplicate = selectedFiles.some(function(selectedFile) {
                    return selectedFile.name === file.name;
                });
                if (isDuplicate) {
                    continue;
                }
                if (file.type.startsWith("image/") ||
                    file.type === "application/vnd.openxmlformats-officedocument.wordprocessingml.document" ||
                    file.type === "application/pdf" ||
                    file.type === "application/vnd.ms-excel" ||
                    file.type === "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" ||
                    file.type === "application/vnd.ms-powerpoint" ||
                    file.type === "application/vnd.openxmlformats-officedocument.presentationml.presentation") {
                    selectedFiles.push(file);
                    additionalFiles.push(file);
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
                var clickableElement;
                if (file.type.startsWith("image/")) {
                    var img = document.createElement("img");
                    img.file = file;
                    div.appendChild(img);
                    clickableElement = img;
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
                    clickableElement = p;
                }
                var removeButton = document.createElement("button");
                removeButton.textContent = "x";
                removeButton.className = "remove-file";
                removeButton.dataset.index = i;
                div.appendChild(removeButton);
                preview.appendChild(div);

                // Add event listener to the clickable element
                (function(f) {
                    clickableElement.addEventListener('click', function() {
                        if (additionalFiles.includes(f)) {
                            // File selected using the file input element
                            // Create a temporary URL for the file
                            var url = URL.createObjectURL(f);

                            // Open the file in a new tab
                            window.open(url, '_blank');
                        } else {
                            // File retrieved from Google Drive
                            // Generate URL for the file
                            var department = '{{ $departmentName }}';
                            var taskName = '{{ $taskName }}';
                            var filename = f.name;
                            var url = '/admin-tasks/get-task/preview-file-selected?department=' + encodeURIComponent(department) + '&taskName=' + encodeURIComponent(taskName) + '&filename=' + encodeURIComponent(filename);

                            // Open a new tab with the loading page
                            var newTab = window.open('/admin-tasks/get-task/preview-file-selected/loading');

                            // Fetch the URL for the file
                            fetch(url)
                                .then(response => response.json())
                                .then(data => {
                                    // Wait for 1 second before updating the URL of the new tab
                                    setTimeout(() => {
                                        // Update the URL of the new tab
                                        newTab.location.href = data.url;
                                    }, 100);
                                });
                        }
                    });
                })(file);

                // Add CSS for hover effect
                clickableElement.style.cursor = 'pointer';
                clickableElement.addEventListener('mouseover', function() {
                    this.style.textDecoration = 'underline';
                });
                clickableElement.addEventListener('mouseout', function() {
                    this.style.textDecoration = 'none';
                });
            }

            if (selectedFiles.length <= 0) {
                preview.innerHTML = "Files uploaded are displayed here";
                preview.classList.add('preview-no-items');
            } else {
                preview.classList.remove('preview-no-items');
            }
        }

        document.getElementById("preview").addEventListener("click", function(evt) {
            if (!isReadOnlyTask) {
                if (evt.target.classList.contains("remove-file")) {
                    var index = parseInt(evt.target.dataset.index);
                    selectedFiles.splice(index, 1);
                    updatePreview();
                    document.getElementById("file-upload").value = "";

                    console.log(selectedFiles);
                }
            }
        });


        function createTask() {
            //console.log(selectedFiles);
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
            data.append('taskID', taskID);
            data.append('initialTaskName', initialTaskName);
            data.append('selectFaculty', currentDepartmentName);
            data.append('taskname', taskLabel.textContent)
            data.append('assignto', JSON.stringify(assignto));
            data.append('dateTimePicker', dateTimePicker);
            data.append('description', description);

            // append the files to the form data
            for (const file of selectedFiles) {
                data.append('files[]', file);
            }

            //console.log(data.get('assignto'));
            document.querySelector('.loading-save-task').style.display = 'flex';
            document.querySelector('.edit-task-btn').innerHTML = 'Saving task..';
            document.querySelector('.edit-task-btn').disabled = true;
            document.querySelector('.edit-task-btn').style.backgroundColor = '#dbd9d9';
            document.querySelector('.edit-task-btn').style.color = 'black';
            let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            fetch('/admin-tasks/update-task', {
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
                        document.querySelector('.loading-save-task').style.display = 'none';

                        document.querySelector('.edit-task-btn').innerHTML = 'Edit Task';
                        document.querySelector('.edit-task-btn').disabled = false;
                        document.querySelector('.edit-task-btn').style.backgroundColor = 'var(--muted_maroon)';
                        document.querySelector('.edit-task-btn').style.color = 'white';

                        showNotification(result.error, '#fe3232bc');
                    } else {
                        document.querySelector('.loading-save-task').style.display = 'none';
                        document.querySelector('.edit-task-btn').innerHTML = 'Edit Task';
                        document.querySelector('.edit-task-btn').disabled = false;
                        document.querySelector('.edit-task-btn').style.backgroundColor = 'var(--muted_maroon)';
                        document.querySelector('.edit-task-btn').style.color = 'white';

                        editTask();
                        additionalFiles.length = 0;
                        updateTable(result.assigned_to);

                        initialTaskName = result.newTaskName;

                        showNotification("(" + data.get('taskname') + ") task is successfully updated.", '#1dad3cbc');
                    }
                })
                .catch(error => {
                    console.error(error);

                    document.querySelector('.edit-task-btn').innerHTML = 'Edit Task';
                    document.querySelector('.edit-task-btn').disabled = false;
                    document.querySelector('.edit-task-btn').style.backgroundColor = 'var(--muted_maroon)';
                    document.querySelector('.edit-task-btn').style.color = 'white';

                    document.querySelector('.loading-save-task').style.display = 'none';
                    showNotification("Error occured, please create task later.", '#fe3232bc');
                });
        }

        function updateTable(assignedTo) {
            let rows = document.querySelectorAll('.item-row');
            rows.forEach(row => {
                row.remove();
            });

            let parentElement = document.querySelector('.parent-members-col');
            let count = 0;
            assignedTo.forEach(name => {
                const row = document.createElement('div');
                row.classList.add('row', 'item-row');
                row.innerHTML = `
                            <div class="column-1 d-flex align-items-center">
                                <img src="{{ asset('faculty/images/user-profile.png') }}" alt=" " class="mx-3 member-picture">
                                <h5 class="item-row-content my-2 column-1-text">
                                    ${name}
                                </h5>
                            </div>
                        `;
                parentElement.appendChild(row);
                count++;
            });

            // Update the Members Assigned text
            let membersAssignedElement = document.querySelector('.members-assigned-row .column-name');
            membersAssignedElement.textContent = `Members Assigned (${count})`;
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
                    console.log(task.task_name);
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
                    <h5 class="task-row-content my-2 task-name-text">${task.task_name}</h5>
                </div>
                <div class="col-3 faculty-name">
                    <img src="${task.faculty_image}" alt=" ">
                    <h5 class="task-row-content px-3 my-2 faculty-name-text">${task.faculty_name}</h5>
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