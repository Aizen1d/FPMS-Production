@extends('layouts.default')

@section('title', 'PUPQC - Task Instructions')

@section('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('faculty/css/faculty_get_task_instructions.css') }}">
@endsection

@section('body')
<div class="overlay"></div>
@include('layouts.faculty_navbar')

@if (session('selectedIn') == 'department')
    @include('layouts.faculty_show_department_selected_task_sidebar') 
@else
    @include('layouts.faculty_selected_task_sidebar')
@endif

@include('layouts.notification_side')

<div class="container-fluid margin">
    <div class="row">
        <div class="col-6">
            <h1 class="my-4 title">{{ $departmentName }} Department</h1>
        </div>
    </div>

    <div class="container-fluid task-list" style="position: relative;">
        <div class="row">
            <div class="col-12">
                <div class="row">
                    <div class="task-name-container">
                        <h3 class="my-4 selected-task-name">{{ $taskName }}</h3>
                    </div>
                    <div class="date-container">
                        <h3 class="my-4 dates-name">Date Created<br><span class="lighter">{{ $date_created_date }} at {{ $date_created_time }}</span></h3>
                    </div>
                    <div class="date-container">
                        <h3 class="my-4 dates-name">Date Updated<br><span class="lighter">{{ $date_updated_date }} at {{ $date_updated_time }}</span></h3>
                    </div>
                    <div class="date-container">
                        @if (Carbon\Carbon::parse($due_date)->isPast())
                            <h3 class="my-4 dates-name">Due Date<br><span class="lighter text-danger">{{ $date_due_date }} at {{ $date_due_time }}</span></h3>
                        @else
                            <h3 class="my-4 dates-name">Due Date<br><span class="lighter">{{ $date_due_date }} at {{ $date_due_time }}</span></h3>
                        @endif
                    </div>
                </div>

                <div class="row">
                    <div class="" style="width: 45%">
                        <div class="description-container">
                            <h3 class="description-label">Description:</h3>
                            <textarea class="task-description-content" id="description" name="description" rows="4" cols="50" placeholder="Task description.." readonly>{{ $description }}</textarea>

                        </div>
                        <div class="attached-files-materials-container">
                            <h3 class="description-label">Attached material files:<button style="display: none; visibility: hidden;" class="download-all-button py-1" onclick="downloadAllAttachments()">Download All</button></h3>

                            <div id="preview" class="preview-no-items" style="text-align:center; z-index: 19; position:relative">
                                <p class="preview-label">No files are attached for this task</p>

                                <div id="loading-overlay-files" class="loading-uploaded-files" style="display: none; justify-content: center; align-items: center; border-radius: 25px; z-index: 99; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: white;">
                                    <div style="display: flex; flex-direction: column; align-items: center;">
                                        <div class="spinner-border text-dark" role="status">
                                            <span class="sr-only">Loading...</span>
                                        </div>
                                        <div id="loading-uploaded-text" style="margin-top: 3px;">Retrieving uploaded files</div>
                                    </div>
                                </div>
                            </div>
                            <div style="display: none;" id="fileNames" data-folder-path="{{ $folderPath }}"></div>
                        </div>
                    </div>

                    <div class="" style="width: 55%; display: flex;">
                        <div class="members-assigned-container">
                            <h3 class="members-label">Assigned Members</h3>
                    
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
                        <div class="submission-container">
                            <h3 class="submissions-label">Submissions</h3>
                            @if ($turnedInDate && $turnedInTime)
                                @if (Carbon\Carbon::parse($due_date)->isPast())
                                    <h3 class="turned-in-label">Turned in Late: <span class="turned-in-data" style="font-weight: normal;">{{ $turnedInDate }} at {{ $turnedInTime }}</span></h3>
                                @else
                                    <h3 class="turned-in-label">Turned in: <span class="turned-in-data" style="font-weight: normal;">{{ $turnedInDate }} at {{ $turnedInTime }}</span></h3>
                                @endif

                                @if ($decision)
                                    <h3 class="output-status-label" style='display:block'>Output Status: <span class="output-status-data" style="font-weight: normal;">{{ $decision }}</span></h3>
                                @else
                                    <h3 class="output-status-label" style='display:block'>Output Status: <span class="output-status-data" style="font-weight: normal;">Not decided</span></h3>
                                @endif
                            @else
                                <h3 class="output-status-label" style='display:none'><span class="output-status-data" style="font-weight: normal;"></span></h3>
                                <h3 class="turned-in-label">Turned in: <span class="turned-in-data" style="font-weight: normal;">Not turned in yet</span></h3>
                            @endif

                            <label for="file-upload" class="custom-file-upload">
                                <i class="fa fa-cloud-upload px-1" style="color: #82ceff;"></i> Upload Files
                            </label>
                            <input id="file-upload" type="file" multiple accept=".docx,.pdf,.xls,.xlsx,.png,.jpeg,.jpg,.ppt,.pptx" />

                            <div id="preview-submissions" class="preview-no-items-submissions" style="text-align:center; z-index: 19; position:relative">
                                <p class="preview-label">Drop your files here</p>

                                <div id="loading-overlay-files" class="loading-uploaded-files-submissions" style="display: none; justify-content: center; align-items: center; border-radius: 25px; z-index: 99; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: white;">
                                    <div style="display: flex; flex-direction: column; align-items: center;">
                                        <div class="spinner-border text-dark" role="status">
                                            <span class="sr-only">Loading...</span>
                                        </div>
                                        <div id="loading-submission" style="margin-top: 3px;">Retrieving submitted files</div>
                                    </div>
                                </div>
                                <div style="display: none;" id="submissionFileNames" data-folder-path="{{ $submissionAttachments }}"></div>
                            </div>

                            <textarea class="submission-note-content" id="description" name="description" rows="4" cols="50" placeholder="Add your notes here..">{{ $submissionDescription }}</textarea>
                            <button class="create-btn create-task turn-in-button" onclick="turnIn()">Turn in</button>
                        </div>
                    </div>
                </div>

            </div>
            
        </div>
    </div>
</div>

<script>
    var uploadedFilesLoaded = true;
    var uploadedSubmissionFilesLoaded = true;
    var taskID = '{{ $taskID }}';
    var initialTaskName = '{{ $taskName }}'; // Used to update the task in the database if ever
    var currentDepartmentName = '{{ $departmentName }}';
    var selectedMembers = []; // selected items or checkboxes in department members
    var selectedFiles = []; // Files selected to be uploaded
    var additionalFiles = []; // Additional files selected 

    var selectedFilesSubmission = [];
    var additionalFilesSubmission = []; // Additional files selected 

    var isTurnedIn = '{{ $isTurnedIn }}';
    var isDownloadAllAvailable = false;
    var isCurrentlyTurnedIn = false;

    function elementsOnTurnIn(submit_status, output_status, date_submitted_date, date_submitted_time) {
        document.querySelector('#preview-submissions').style.height = '18.5vh';
        document.querySelector('.submission-note-content').style.height = '29%';

        document.getElementById('file-upload').disabled = true;

        document.querySelector('.custom-file-upload').style.cursor = 'default';
        document.querySelector('.custom-file-upload').style.backgroundColor = '#dbd9d9';
        document.querySelector('.custom-file-upload').style.boxShadow = 'none';
        document.querySelector('.custom-file-upload').disabled = true;
        
        let h3 = document.querySelector('.turned-in-label');

        if (submit_status === 'Late Completed') {
            h3.textContent = 'Turned in Late:'
        } 
        else {
            h3.textContent = 'Turned in:'
        }

        let newSpan = document.createElement('span');
        h3.appendChild(newSpan);
        
        newSpan.className = 'turned-in-data';
        newSpan.textContent = ' ' + date_submitted_date + ' at ' + date_submitted_time;
        newSpan.style = 'font-weight: normal'
        
        let outputH3 = document.querySelector('.output-status-label');
        let outputSpan = document.createElement('span');

        outputH3.style.display = 'block';
        outputH3.textContent = 'Output Status: ';
        outputH3.appendChild(outputSpan);

        outputSpan.textContent = output_status;
        outputSpan.className = 'output-status-data';
        outputSpan.style = 'font-weight: normal'

        outputH3.parentNode.insertBefore(h3, outputH3);
        
        document.querySelector('.submission-note-content').readOnly = true;

        document.querySelector('.turn-in-button').style.backgroundColor = '#dbd9d9';
        document.querySelector('.turn-in-button').style.color = 'black';
        document.querySelector('.turn-in-button').disabled = false;
        document.querySelector('.turn-in-button').innerHTML = 'Unsubmit';

        let removeButtons = document.querySelectorAll('.remove-file-submission');
        removeButtons.forEach(function(removeButton) {
            removeButton.style.display = 'none';
        });
        isCurrentlyTurnedIn = true;
    }

    function elementsOnUnsubmit() {
        //document.querySelector('.loading-create-task').style.display = 'none'
        document.querySelector('.create-task').disabled = false;
        document.querySelector('.create-task').style.backgroundColor = 'var(--muted_maroon)';

        document.querySelector('.turned-in-label').textContent = 'Turned in:';
        
        let newSpan = document.createElement('span');
        newSpan.className = 'turned-in-data';
        newSpan.textContent = ' Not turned in yet';
        newSpan.style = 'font-weight: normal'

        let h3 = document.querySelector('.turned-in-label');
        h3.appendChild(newSpan);

        let outputH3 = document.querySelector('.output-status-label');
        outputH3.style.display = 'none';

        document.getElementById('file-upload').disabled = false;
        document.querySelector('.custom-file-upload').style.cursor = 'pointer';
        document.querySelector('.custom-file-upload').style.backgroundColor = '#fff';
        document.querySelector('.custom-file-upload').style.boxShadow = 'rgba(0, 0, 0, 0.26) 0px 1.5px 12px';
        document.querySelector('.custom-file-upload').disabled = false;

        document.querySelector('#preview-submissions').style.height = '19.5vh';

        document.querySelector('.submission-note-content').readOnly = false;
        document.querySelector('.submission-note-content').style.height = '32%';

        document.querySelector('.turn-in-button').style.color = 'white';
        document.querySelector('.turn-in-button').style.backgroundColor = 'var(--muted_maroon)';

        let removeButtons = document.querySelectorAll('.remove-file-submission');
        removeButtons.forEach(function(removeButton) {
            removeButton.style.display = 'block';
        });
        document.querySelector('.turn-in-button').innerHTML = 'Turn in';
        
        //showNotification("Task successfully unsubmitted.", '#1dad3cbc');
        return isCurrentlyTurnedIn = false;
    }

    function downloadAllAttachments() {
        if (isDownloadAllAvailable) {
            let folderPath = document.querySelector('#fileNames').dataset.folderPath;
            const data = new FormData();
            data.append('folderPath', folderPath);
            data.append('taskName', '{{ $taskName }}');
            data.append('department', '{{ $departmentName }}');

            showNotification("Zip file is downloading, a save prompt will appear in a moment.", '#1dad3cbc');

            let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            fetch('/faculty-tasks/get-task/download-all-file', {
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
                    fetch('/faculty-tasks/get-task/download-all-file/delete-temp-zip', {
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
                showNotification("Error occurred, please try again later.", '#fe3232bc');
            });
            
        }
    }

    // Send async request to server to retrieve uploaded files on this task
    document.addEventListener('DOMContentLoaded', () => {
        
        if (isTurnedIn) {
            document.getElementById('file-upload').disabled = true;

            document.querySelector('.custom-file-upload').style.cursor = 'default';
            document.querySelector('.custom-file-upload').style.backgroundColor = '#dbd9d9';
            document.querySelector('.custom-file-upload').style.boxShadow = 'none';
            document.querySelector('.custom-file-upload').disabled = true;

            document.querySelector('.submission-note-content').readOnly = true;

            document.querySelector('.turn-in-button').style.backgroundColor = '#dbd9d9';
            document.querySelector('.turn-in-button').style.color = 'black';

            document.querySelector('#preview-submissions').style.height = '18.5vh';
            document.querySelector('.submission-note-content').style.height = '29%';
            isCurrentlyTurnedIn = true;
        }
        else {
            document.querySelector('#preview-submissions').style.height = '19.5vh';
            document.querySelector('.submission-note-content').style.height = '32%';
            isCurrentlyTurnedIn = false;
        }

        setTimeout(() => {
            let folderPath = document.querySelector('#fileNames').dataset.folderPath;

            // Fetching the attached files
                uploadedFilesLoaded = false;
                let dots = '';
                let interval = setInterval(() => {
                    dots += '.';
                    if (dots.length > 3) {
                        dots = '';
                    }
                    document.getElementById('loading-uploaded-text').innerHTML = 'Retrieving uploaded files' + dots;
                }, 300);

                document.querySelector('.loading-uploaded-files').style.display = 'flex';
                fetch('/faculty-tasks/get-task/get-attachments?folderPath=' + encodeURIComponent(folderPath))
                    .then(response => response.json())
                    .then(fileNames => {
                    if (folderPath) {
                        //document.querySelector('.download-all-button').style.visibility = 'visible';
                        // Loop through the file names and create a new File object for each one
                        for (let fileName of fileNames) {
                            let file = new File([], fileName);
                            selectedFiles.push(file);
                        }
                        document.querySelector('.loading-uploaded-files').style.display = 'none';

                        // Update the content of the placeholder element with the file names
                        let fileNamesElement = document.querySelector('#fileNames');
                        fileNamesElement.innerHTML = fileNames.join(', ');

                        clearInterval(interval);
                        updatePreview();
                        
                        isDownloadAllAvailable = true;
                        uploadedFilesLoaded = true;
                    }
                    else {
                        document.querySelector('.loading-uploaded-files').style.display = 'none';
                        uploadedFilesLoaded = true;
                        clearInterval(interval);
                    }
                });
            

            let submissionFiles = document.querySelector('#submissionFileNames').dataset.folderPath;

            if (submissionFiles) {
                // Fetching the submitted files when turned in
                uploadedSubmissionFilesLoaded = false;
                
                let dots = '';
                let interval = setInterval(() => {
                    dots += '.';
                    if (dots.length > 3) {
                        dots = '';
                    }
                    document.querySelector('.turn-in-button').innerHTML = 'Loading' + dots;
                    if (isTurnedIn) {
                        document.getElementById('loading-submission').innerHTML = 'Retrieving submitted files' + dots;
                    }
                    else {
                        document.getElementById('loading-submission').innerHTML = 'Retrieving files' + dots;
                    }
                }, 300);

                document.querySelector('.turn-in-button').disabled = true;
                document.querySelector('.turn-in-button').style.color = 'black';
                document.querySelector('.turn-in-button').style.backgroundColor = '#dbd9d9';
                document.querySelector('.loading-uploaded-files-submissions').style.display = 'flex';

                fetch('/faculty-tasks/get-task/get-submissions-attachments?folderPath=' + encodeURIComponent(submissionFiles))
                    .then(response => response.json())
                    .then(fileNames => {
                        // Loop through the file names and create a new File object for each one
                        for (let fileName of fileNames) {
                            let file = new File([], fileName);
                            selectedFilesSubmission.push(file);
                        }

                        // Update the content of the placeholder element with the file names
                        let fileNamesElement2 = document.querySelector('#submissionFileNames');
                        fileNamesElement2.innerHTML = fileNames.join(', ');

                        clearInterval(interval);
                        updateSubmissionsPreview();
                        uploadedSubmissionFilesLoaded = true;

                        if (!isTurnedIn) {
                            document.querySelector('.turn-in-button').style.backgroundColor = 'var(--muted_maroon)';
                            document.querySelector('.turn-in-button').style.color = 'white';
                            document.querySelector('.turn-in-button').innerHTML = 'Turn in';
                        }
                        else {
                            let removeButtons = document.querySelectorAll('.remove-file-submission');
                            removeButtons.forEach(function(removeButton) {
                                removeButton.style.display = 'none';
                            });
                            document.querySelector('.turn-in-button').innerHTML = 'Unsubmit';
                        }

                        document.querySelector('.turn-in-button').disabled = false;
                        //document.querySelector('.loading-uploaded-files-submissions').style.display = 'none';
                    });
            }
            else { // If no submission files to retrieve
                if (isTurnedIn) {
                    document.querySelector('.turn-in-button').innerHTML = 'Unsubmit';
                }
            }

        }, 100); // Delay the execution by 1 second
    });

    // Turn in //

    function turnIn() {
        if (isCurrentlyTurnedIn) { // Perform unsubmit
            const data = new FormData();
            data.append('taskname', '{{ $taskName }}');
            document.querySelector('.create-task').disabled = true;
            document.querySelector('.create-task').style.backgroundColor = '#dbd9d9';

            let dots = '';
            let interval = setInterval(() => {
                dots += '.';
                if (dots.length > 3) {
                    dots = '';
                }
                document.querySelector('.turn-in-button').innerHTML = 'Unsubmitting' + dots;
            }, 300);

            let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                fetch('/faculty-tasks/get-task/instructions/unsubmit', {
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
                    clearInterval(interval);
                    elementsOnUnsubmit();
                })
                .catch(error => {
                    console.log(error);
                    clearInterval(interval);
                    showNotification("Error occured, please unsubmit again later.", '#fe3232bc');
                });
        }
        else { // Perform turn-in / submit
            var description = document.querySelector('.submission-note-content').value;
            const data = new FormData();
            // append your task data to the form data
            data.append('faculty', '{{ $departmentName }}');
            data.append('taskname', '{{ $taskName }}');
            data.append('description', description);

            // append the files to the form data
            for (const file of selectedFilesSubmission) {
                data.append('files[]', file);
            }

            let dots = '';
            let interval = setInterval(() => {
                dots += '.';
                if (dots.length > 3) {
                    dots = '';
                }
                document.querySelector('.turn-in-button').innerHTML = 'Turning in' + dots;
            }, 300);

            // Disable the submission container components while turning in
            document.getElementById('file-upload').disabled = true;

            document.querySelector('.custom-file-upload').style.cursor = 'default';
            document.querySelector('.custom-file-upload').style.backgroundColor = '#dbd9d9';
            document.querySelector('.custom-file-upload').style.boxShadow = 'none';
            document.querySelector('.custom-file-upload').disabled = true;

            document.querySelector('.submission-note-content').readOnly = true;

            let removeButtons = document.querySelectorAll('.remove-file-submission');
            removeButtons.forEach(function(removeButton) {
                removeButton.style.display = 'none';
            });

            document.querySelector('.turn-in-button').disabled = true;
            document.querySelector('.turn-in-button').style.color = 'black';
            document.querySelector('.turn-in-button').style.backgroundColor = '#dbd9d9';
            let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                fetch('/faculty-tasks/get-task/instructions/turn-in', {
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
                    elementsOnTurnIn(result.submit_status, result.output_status, result.date_submitted_date, result.date_submitted_time);
                    clearInterval(interval);

                    additionalFilesSubmission.length = 0;
                    showNotification("Task successfully turned in.", '#1dad3cbc');
                })
                .catch(error => {
                    console.log(error);

                    clearInterval(interval);
                    additionalFilesSubmission.length = 0;
                    showNotification("Error occured, please submit again later.", '#fe3232bc');
                });
        }
    }

    /// File Upload ///

    document.getElementById("file-upload").onchange = function() {
        var files = document.getElementById("file-upload").files;
        if (!isCurrentlyTurnedIn) {
            if (files.length === 0) {
                // The user clicked the cancel button in the file upload dialog
                console.log('Upload cancelled');
            } else {
                handleFiles(files);
            }
        }
    };

    var dropZone = document.getElementById("preview-submissions");
    dropZone.addEventListener("dragover", function(evt) {
        evt.preventDefault();
    }, false);

    dropZone.addEventListener("drop", function(evt) {
        if (!isCurrentlyTurnedIn) {
            evt.preventDefault();
            var files = evt.dataTransfer.files;
            handleFiles(files);
        }
    }, false);

    function handleFiles(files) {
        for (var i = 0; i < files.length; i++) {
            var file = files[i];
            var isDuplicate = selectedFilesSubmission.some(function(selectedFile) {
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
                selectedFilesSubmission.push(file);
                additionalFilesSubmission.push(file);
            } else {
                showNotification("Only allowed files only (Images, Docx, PDF, PPTX, Excel)", '#fe3232bc');
                //alert("Only docx, pdf, excel, png and jpeg files are allowed.");
                break;
            }
        }
        console.log(selectedFilesSubmission);
        updateSubmissionsPreview();
    }

    function updateSubmissionsPreview() {
        var preview = document.getElementById("preview-submissions");
        preview.innerHTML = "";
        for (var i = 0; i < selectedFilesSubmission.length; i++) {
            var file = selectedFilesSubmission[i];
            var div = document.createElement("div");
            div.className = "file-container-submission";
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
            removeButton.className = "remove-file remove-file-submission";
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
                        var url = '/faculty-tasks/get-task/preview-file-selected?department=' + encodeURIComponent(department) + '&taskName=' + encodeURIComponent(taskName) + '&filename=' + encodeURIComponent(filename);

                        // Open a new tab with the loading page
                        var newTab = window.open('/faculty-tasks/get-task/preview-file-selected/loading');

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

        if (selectedFilesSubmission.length <= 0) {
            preview.innerHTML = "Drop your files here";
            preview.classList.add('preview-no-items-submissions');
        } else {
            preview.classList.remove('preview-no-items-submissions');
        }
    }

    document.getElementById("preview-submissions").addEventListener("click", function(evt) {
        if (evt.target.classList.contains("remove-file-submission")) {
            var index = parseInt(evt.target.dataset.index);
            selectedFilesSubmission.splice(index, 1);
            updateSubmissionsPreview();
            document.getElementById("file-upload").value = "";

            console.log(selectedFilesSubmission);
        }
    });

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
                    params.append('department', '{{ $departmentName }}');
                    params.append('taskName', '{{ $taskName }}');
                    params.append('filename', f.name);

                    // Send the request to the server
                    let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    showNotification("File will be downloaded in a moment.", '#1dad3cbc');
                    fetch('/faculty-tasks/get-task/download-file', {
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
                        var url = '/faculty-tasks/get-task/preview-file-selected?department=' + encodeURIComponent(department) + '&taskName=' + encodeURIComponent(taskName) + '&filename=' + encodeURIComponent(filename);

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
</script>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script>
@endsection