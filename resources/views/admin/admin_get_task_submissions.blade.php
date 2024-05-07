@extends('layouts.default')

@section('title', 'PUPQC - Memo Submissions')

@section('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('admin/css/admin_get_task_submissions.css') }}">
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
            
        </div>
    </div>

    <div class="container-fluid task-list" style="position: relative;">
        <div class="row task-info">
            <div class="col-5 task-conents" style="position: relative; border-right: 1px solid #cccccc;">
                <div class="row">
                    <div class="col-12">
                        <h5 class="my-3 task-name-label" style="z-index: 100; position: relative;">Memo name: <span style="font-weight: normal;">{{ $taskName }}</span></h5>
                    </div>
                </div>

                <h5 class="my-3 submitted-by" style="z-index: 100; position: relative;"><b>Submitted by:</b> </h5>

                <div class="attached-files-materials-container">
                    <h3 class="description-label">Attached material files:<button style="display: none; visibility: hidden;" class="download-all-button py-1" onclick="downloadAllAttachments()">Download All</button></h3>

                    <div id="preview" class="preview-no-items" style="text-align:center; z-index: 19; position:relative">
                        <p class="preview-label">Output files are displayed here</p>
                    </div>

                    <div id="loading-output-files" class="loading-uploaded-files" style="display: none; justify-content: center; align-items: center; border-radius: 25px; z-index: 99; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: white;">
                        <div style="display: flex; flex-direction: column; align-items: center;">
                            <div class="spinner-border text-dark" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                            <div id="loading-uploaded-text" style="margin-top: 3px;">Retrieving output files</div>
                        </div>
                    </div>
                    
                    <div style="display: none;" id="fileNames" data-folder-path="{{ $folderPath }}"></div>
                </div>

                <div class="description-container">
                    <h3 class="description-label">Output notes:</h3>
                    <textarea class="task-description-content" id="description" name="description" rows="4" cols="50" readonly></textarea>
                </div>

                <div class="row decision-row justify-content-center my-3">
                    <button class="approve-btn mx-2" onclick="approveOutput()">Approve</button>
                    <button class="reject-btn mx-2" onclick="rejectOutput()">Reject</button>
                </div>

            </div>

            <div class="col-7">
                <div class="row">
                    <div class="col-4">
                        <h5 class="my-3 column-name" style="z-index: 100; position: relative;">Members Assigned</h5>
                    </div>
                    <div class="col-2">
                        <h5 class="my-3 column-name" style="z-index: 100; position: relative;">Status</h5>
                    </div>
                    <div class="col-2">
                        <h5 class="my-3 column-name" style="z-index: 100; position: relative;">Decision</h5>
                    </div>
                    <div class="col-2">
                        <h5 class="my-3 column-name" style="z-index: 100; position: relative;">Submitted on</h5>
                    </div>
                    <div class="col-2">
                        <h5 class="my-3 column-name" style="z-index: 100; position: relative;">Deadline</h5>
                    </div>
                </div>
                
                <div class="member-contents">
                @foreach ($assignedMembers as $memberName)
                <div class="row item-row">
                    <div class="col-4">
                        <div class="row">
                            <div class="column-1 d-flex align-items-center mx-4">
                                <img src="{{ asset('faculty/images/user-profile.png') }}" alt=" " class="mx-3 member-picture">
                                <h5 class="member-row-content my-2 column-1-text">
                                    {{ $memberName->submitted_by }}
                                </h5>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-2">
                        <h5 class="item-row-content text-center my-2 column-1-text" data-member-status="{{ $memberName->submitted_by }}">
                            {{ $memberName->status }}
                        </h5>
                    </div>
                    <div class="col-2">
                        <h5 class="item-row-content text-center my-2 column-1-text" data-member="{{ $memberName->submitted_by }}">
                            {{ $memberName->decision }}
                        </h5>
                    </div>
                    <div class="col-2">
                        @if ($memberName->date_submitted)
                            @if (Carbon\Carbon::parse($deadline)->lt(Carbon\Carbon::parse($memberName->date_submitted)))
                                <h5 class="task-row-content my-2 text-danger column-1-text due-date">{{ date('F j, Y', strtotime($memberName->date_submitted)) }}<br>{{ date('g:i A', strtotime($memberName->date_submitted)) }}</h5>
                            @else
                                <h5 class="task-row-content my-2 column-1-text due-date">{{ date('F j, Y', strtotime($memberName->date_submitted)) }}<br>{{ date('g:i A', strtotime($memberName->date_submitted)) }}</h5>
                            @endif
                        @else
                            <h5 class="task-row-content my-2 column-1-text due-date">Not yet</h5>
                        @endif
                    </div>
                    <div class="col-2">
                        <h5 class="item-row-content text-center my-2 column-1-text" data-member="{{ $memberName->submitted_by }}">
                            {{ date('F j, Y', strtotime($deadline)) }}<br>{{ date('g:i A', strtotime($deadline)) }}
                        </h5>
                    </div>
                </div>
                @endforeach
                </div>
            </div>
        </div>
    </div>

    <script>
      	// Disable buttons at first to prevent misuse
		document.querySelector('.approve-btn').style.backgroundColor = '#dbd9d9';
        document.querySelector('.reject-btn').style.backgroundColor = '#dbd9d9';

		document.querySelector('.approve-btn').disabled = true;
        document.querySelector('.reject-btn').disabled = true;
      
        var uploadedFilesLoaded = true;
        var taskID = '{{ $taskID }}';
        var initialTaskName = '{{ $taskName }}'; // Used to update the task in the database if ever
        var currentDepartmentName = '{{ $departmentName }}';
        var selectedMembers = []; // selected items or checkboxes in department members
        var selectedFiles = []; // Files selected to be uploaded
        var additionalFiles = []; // Additional files selected 

        var isDownloadAllAvailable = true;
        var currentlySelectedMemberInRows = '';

        function approveOutput() {
            let data = new FormData();
            data.append('memberName', currentlySelectedMemberInRows);
            data.append('taskName', '{{ $taskName }}');
            data.append('decision', 'approve');

            let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            fetch('/admin-tasks/get-task/submissions/decide', {
                headers: {
                    "Accept": "application/json, text-plain, */*",
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": token
                },
                method: 'POST',
                body: data,
                credentials: "same-origin",
            })
            .then(response => response.json())
            .then(data => {
                console.log(data);

                showNotification(currentlySelectedMemberInRows + "'s output was approved.", '#1dad3cbc');

                const decisionElement = document.querySelector(`[data-member="${currentlySelectedMemberInRows}"]`);
                decisionElement.textContent = data;
            })
            .catch(error => {
                console.log(error);
            });
        }

        function rejectOutput() {
            let data = new FormData();
            data.append('memberName', currentlySelectedMemberInRows);
            data.append('taskName', '{{ $taskName }}');
            data.append('decision', 'reject');

            let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            fetch('/admin-tasks/get-task/submissions/decide', {
                headers: {
                    "Accept": "application/json, text-plain, */*",
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": token
                },
                method: 'POST',
                body: data,
                credentials: "same-origin",
            })
            .then(response => response.json())
            .then(data => {
                console.log(data);
        
                showNotification(currentlySelectedMemberInRows + "'s output was rejected.", '#1dad3cbc');
                
                const decisionElement = document.querySelector(`[data-member="${currentlySelectedMemberInRows}"]`);
                decisionElement.textContent = data;
            })
            .catch(error => {
                console.log(error);
            });
        }

        function downloadAllAttachments() {
            if (isDownloadAllAvailable) {
                isDownloadAllAvailable = false;
                let folderPath = document.querySelector('#fileNames').dataset.folderPath;
                const data = new FormData();
                data.append('folderPath', folderPath);
                data.append('taskName', '{{ $taskName }}');
                data.append('department', '{{ $departmentName }}');
                data.append('memberName', currentlySelectedMemberInRows);

                showNotification("Zip file is downloading, a save prompt will appear in a moment.", '#1dad3cbc');

                let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                fetch('/admin-tasks/get-task/download-all-file', {
                    headers:{
                        "Accept": "application/json, text-plain, */*",
                        "X-Requested-With": "XMLHttpRequest",
                        "X-CSRF-TOKEN": token
                    },
                    method: 'POST',
                    body: data,
                    credentials: "same-origin",
                })
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                    
                    // Get the zip file ID from the response
                    var fileId = data.fileId;

                    // Generate the download URL
                    var downloadUrl = 'https://drive.google.com/u/0/uc?id=' + fileId + '&export=download';

                    // Create a temporary anchor element
                    var a = document.createElement('a');
                    a.href = downloadUrl;
                    a.download = data.fileName + ' Task';

                    // Append the anchor element to the body and click it to start the download
                    document.body.appendChild(a);
                    a.click();

                    // Remove the anchor element from the body
                    document.body.removeChild(a);
                    isDownloadAllAvailable = true;

                    // Wait for 60 seconds before deleting the zip file from Google Drive
                    setTimeout(function () {
                        // Send a POST request to the server to delete the zip file from Google Drive
                        let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        let folderNameData = new FormData();
                        folderNameData.append('zipName', data.fileName + ' Task');
                        fetch('/admin-tasks/get-task/download-all-file/delete-temp-zip', {
                            headers:{
                                "Accept": "application/json, text-plain, */*",
                                "X-Requested-With": "XMLHttpRequest",
                                "X-CSRF-TOKEN": token
                            },
                            method: 'POST',
                            body: folderNameData,
                            credentials: "same-origin",
                        })
                        .then(response => response.json())
                        .then(data => {
                            console.log(data);
                            // Check if the file was deleted successfully
                            if (data.success) {
                                // File deleted successfully
                                console.log('Zip file deleted successfully');
                            } else {
                                // An error occurred
                                console.log('Error deleting zip file:', data.error);
                            }
                        })
                        .catch(error => {
                            console.log(error);
                        });
                    }, 60000);
                })
                .catch(error => {
                    console.log(error);
                    isDownloadAllAvailable = true;
                    showNotification("Error occurred, please try again later.", '#fe3232bc');
                });
            }
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
                // Create download button
                var downloadButton = document.createElement("button");
                downloadButton.className = "download-file";
                downloadButton.dataset.index = i;
                div.appendChild(downloadButton);

                // Create download icon
                var downloadIcon = document.createElementNS("http://www.w3.org/2000/svg", "svg");
                downloadIcon.setAttribute("width", "1vw");
                downloadIcon.setAttribute("height", "2vh");
                downloadIcon.setAttribute("fill", "currentColor");
                downloadIcon.setAttribute("class", "bi bi-download");
                downloadIcon.setAttribute("viewBox", "0 0 16 16");

                // Create path elements
                var path1 = document.createElementNS("http://www.w3.org/2000/svg", "path");
                path1.setAttribute("d", "M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z");

                var path2 = document.createElementNS("http://www.w3.org/2000/svg", "path");
                path2.setAttribute("d", "M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z");

                // Append path elements to download icon
                downloadIcon.appendChild(path1);
                downloadIcon.appendChild(path2);

                // Append download icon to download button
                downloadButton.appendChild(downloadIcon);
                (function(f) {
                    downloadButton.addEventListener('click', function() {
                        // Set up the parameters for the request
                        var params = new URLSearchParams();
                        params.append('memberName', currentlySelectedMemberInRows);
                        params.append('department', '{{ $departmentName }}');
                        params.append('taskName', '{{ $taskName }}');
                        params.append('filename', f.name);

                        // Send the request to the server
                        let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                        showNotification("File will be downloaded in a moment.", '#1dad3cbc');
                        fetch('/admin-tasks/get-task/download-file', {
                            headers: {
                                "Accept": "application/json, text-plain, */*",
                                "X-Requested-With": "XMLHttpRequest",
                                "X-CSRF-TOKEN": token
                            },
                            method: 'POST',
                            body: params,
                            credentials: "same-origin",
                        })
                        .then(response => response.json())
                        .then(data => {
                            // Get the file ID from the response
                            var fileId = data.fileId;

                            // Generate the download URL
                            var downloadUrl = 'https://drive.google.com/u/0/uc?id=' + fileId + '&export=download';

                            // Create a temporary anchor element
                            var a = document.createElement('a');
                            a.href = downloadUrl;
                            a.download = f.name;

                            // Append the anchor element to the body and click it to start the download
                            document.body.appendChild(a);
                            a.click();

                            // Remove the anchor element from the body
                            document.body.removeChild(a);
                        });
                    });
                })(file);

                var removeButton = document.createElement("button");
                removeButton.textContent = "x";
                removeButton.className = "remove-file";
                removeButton.style.display = 'none';
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
                preview.innerHTML = "No output files retreived";
                preview.classList.add('preview-no-items');
            } else {
                preview.classList.remove('preview-no-items');
            }

            if (selectedFiles.length > 0) {
                document.querySelector('.download-all-button').style.visibility = 'Visible';
            }
            else{
                document.querySelector('.download-all-button').style.visibility = 'hidden';
            }
        }

        var isOutputFilesLoaded = true;

        // Handle task row click
        function getSelectedTaskRow(getMemberName) {
            let memberName = getMemberName.trim();
            let taskId = '{{ $taskID }}';

            var data = new FormData();
            data.append('memberName', memberName);
            data.append('id', taskId);

            currentlySelectedMemberInRows = memberName;
            document.querySelector('.approve-btn').style.backgroundColor = '#dbd9d9';
            document.querySelector('.reject-btn').style.backgroundColor = '#dbd9d9';

            document.querySelector('.approve-btn').disabled = true;
            document.querySelector('.reject-btn').disabled = true;

            // Send the request to the server
            isOutputFilesLoaded = false;
            let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                fetch('/admin-tasks/get-task/submissions/get-attachments', {
                    headers: {
                        "Accept": "application/json, text-plain, */*",
                        "X-Requested-With": "XMLHttpRequest",
                        "X-CSRF-TOKEN": token
                    },
                    method: 'POST',
                    body: data,
                    credentials: "same-origin",
                })
                .then(response => response.json())
                .then(data => {
                    fileNames = data.fileNames;

                    for (let fileName of fileNames) {
                        let file = new File([], fileName);
                        selectedFiles.push(file);
                    }

                    document.querySelector('.task-description-content').textContent = data.description;
                    document.querySelector('.submitted-by').innerHTML = '<b>Submitted by:</b> ' + memberName;

                    const statusElement = document.querySelector(`[data-member-status="${currentlySelectedMemberInRows}"]`);

                    if (statusElement.textContent.trim() === 'Completed' || statusElement.textContent.trim() === 'Late Completed') {

                    document.querySelector('.approve-btn').style.backgroundColor = 'rgba(29, 173, 60, 0.737)';
                    document.querySelector('.reject-btn').style.backgroundColor = 'rgba(254, 50, 50, 0.737)';

                    document.querySelector('.approve-btn').disabled = false;
                    document.querySelector('.reject-btn').disabled = false;

                    }

                    updatePreview();
                    document.querySelector('#loading-output-files').style.display = 'none';

                    isOutputFilesLoaded = true;
                    selectedFiles.length = 0;
                })
                .catch(error => {
                    document.querySelector('#loading-output-files').style.display = 'none';

                    if (statusElement.textContent === 'Completed' || statusElement.textContent === 'Late Completed') {

                    document.querySelector('.approve-btn').style.backgroundColor = 'rgba(29, 173, 60, 0.737)';
                    document.querySelector('.reject-btn').style.backgroundColor = 'rgba(254, 50, 50, 0.737)';

                    document.querySelector('.approve-btn').disabled = false;
                    document.querySelector('.reject-btn').disabled = false;

                    }
                    
                    isOutputFilesLoaded = true;
                    selectedFiles.length = 0;

                    console.log(error);
                });
        }

        const getRows = document.querySelectorAll('.item-row');
        let activeRow = null; 

        for (let i = 0; i < getRows.length; i++) {
            const getTaskRow = getRows[i];
            const getMemberName = getTaskRow.querySelector('.member-row-content').textContent;

            getTaskRow.addEventListener('click', function() {
                if (isOutputFilesLoaded) {
                    // Remove the active-row class from the previously active row
                    if (activeRow) {
                        activeRow.classList.remove('active-row');
                    }

                    // Add the active-row class to the clicked row
                    this.classList.add('active-row');

                    // Update the activeRow variable
                    activeRow = this;

                    document.querySelector('#loading-output-files').style.display = 'flex';
                    getSelectedTaskRow(getMemberName);
                }
            });
        }

        // Add event listeners for all rows of task
        const getTaskRows = document.querySelectorAll('.item-row');

        for (let i = 0; i < getTaskRows.length; i++) {
            const getTaskRow = getTaskRows[i];
            const getMemberName = getTaskRow.querySelector('.member-row-content').textContent;

            getTaskRow.addEventListener('click', function() {
                if (isOutputFilesLoaded) {
                    selectedFiles.length = 0;
                    document.querySelector('#loading-output-files').style.display = 'flex';
                    getSelectedTaskRow(getMemberName);
                }
            });
        }

        function renderTasks(data) {
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