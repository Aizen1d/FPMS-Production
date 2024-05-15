@extends('layouts.default')

@section('title', 'PUPQC - Functions')

@section('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('faculty/css/faculty_tasks_functions.css') }}">
@endsection

@section('body')
<div class="overlay"></div>
@include('layouts.faculty_navbar')
@include('layouts.faculty_tasks_sidebar')
@include('layouts.notification_side')

<div class="container-fluid margin">
    <div class="row">
        <div class="col-4">
            <h1 class="my-4 title">Functions</h1>
        </div>
        <div class="col-2 pages">
            {{ $items->links()  }}
        </div>
        <div class="col-6 drop-down-container">
            <input type="text" class="search-input mx-5" id="search-input" placeholder="Search function...">
            <div id="search-results"></div>
        </div>
    </div>

    <div class="create-task-popup">
        <div class="row">
          <div class="d-flex flex-col col-9">
            <h5 class="create-label function-label" style="width: 100%;">
            </h5>
          </div>
          <div class="col-3">
            <button class="close-task-btn" onclick="closeAttendance()"><i class="fa fa-times"></i></button>
          </div>
        </div>

        <div class="row mt-3">
            <div class="col-12">
                <div class="ms-3">


                    <div class="d-flex flex-column mt-1">
                      <label for="" class="ms-3">Date Started*</label>
                      <input class="ms-2" type="date" id="date-picker-started" min="1997-01-01" max="2030-01-01">
                    </div>     
                    
                    <div class="d-flex flex-column mt-3">
                      <label for="" class="ms-3">Date Completed*</label>
                      <input class="ms-2" type="date" id="date-picker-completed" min="1997-01-01" max="2030-01-01">
                    </div>

                    <div class="d-flex flex-column mt-3">
                      <label for="" class="ms-3">Status of Attendance*</label>
                      <div class="drop-down create-dropdown-status-attendance">
                          <div class="wrapper">
                              <div class="selected" id="selected-status-attendance-display">Select status of attendance</div>
                          </div>
                          <i class="fa fa-caret-down caret-status-attendance"></i>
                  
                          <div class="list create-list-status-attendance">
                              <div class="status-attendance">
                                  <input type="radio" name="status-attendance" id="Attended">
                                  <div class="text">
                                      Attended
                                  </div>
                              </div>
                              <div class="status-attendance">
                                  <input type="radio" name="status-attendance" id="On-Leave">
                                  <div class="text">
                                      On Leave
                                  </div>
                              </div>
                              <div class="status-attendance">
                                <input type="radio" name="status-attendance" id="Official Business">
                                <div class="text">
                                    Official Business
                                </div>
                            </div>
                          </div>
                      </div>
                    </div>   

                    <div class="reason-for-absence-container" style="display: none">
                      <div class="d-flex flex-column mt-3">
                          <label for="" class="ms-3">Reason for absence*</label>
                          <input class="research-input" id="reason-absence-input" type="text" placeholder="Enter reason for absence">
                      </div>
                    </div>

                    <div class="attendance-proof-container" style="display: none">
                      <div class="d-flex flex-column mt-3 ms-2" style="margin-left: 2% !important">
                        <label for="" style="margin-left: 1% !important">Upload proof of Attendance*</label>
                        <label for="" class="mb-2" style="margin-left: 1% !important; font-size: 8px; color:rgb(232, 79, 79);">Selfie photos are not allowed as supporting document.</label>
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
                                <p class="preview-label">Uploaded files are displayed here</p>
                            </div>
                        </div>
                      </div>
                    </div>

                    <div class="d-flex justify-content-center items-center mt-4">
                        <button class="d-flex justify-content-center items-center create-research-btn" onclick="submitForm()">
                            Create
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div id="loading-overlay" class="loading-create-task" style="display: none; justify-content: center; height: 100vh; align-items: center; border-radius: 25px; z-index: 99; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: white;">
            <div style="display: flex; flex-direction: column; align-items: center;">
                <div class="spinner-border text-dark" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <div id="loading-text" style="margin-top: 3px;">Adding attendance, this may take a few seconds.</div>
            </div>
        </div>
    </div>

    <div class="container-fluid task-list" style="position: relative;">
        <div class="row">
            <div class="col-5">
                <h5 class="my-3 column-name" style="z-index: 100; position: relative;">Brief Description of Activity</h5>
            </div>
            <div class="col-4">
                <h5 class="my-3 column-name" style="z-index: 100; position: relative;">Remarks</h5>
            </div>
            <div class="col-3">
                <h5 class="my-3 column-name" style="z-index: 100; position: relative;">Action</h5>
            </div>
        </div>

        <div class="task-container">
            @foreach ($items as $item)
            <div class="row task-row">
                <div class="col-5">
                    <h5 class="task-row-content my-2 task-name-text" style="text-align:left; margin-left: 32%">{{ $item->brief_description }}</h5>
                </div>
                <div class="col-4">
                    <h5 class="task-row-content my-2 task-name-text" style="text-align:left; margin-left: 43%">{{ $item->remarks }}</h5>
                </div>
                <div class="col-3 d-flex justify-content-center">
                  <button class="add-attendance-btn mx-2" onclick="addAttendance(event, {{ $item }})">
                    Add
                  </button>
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

        let selectedItemId = 0;

        // create task popup
        function addAttendance(event, item) {
            event.stopPropagation();
            event.preventDefault();

            selectedItemId = item.id;

            const functionLabel = document.querySelector('.function-label');
            functionLabel.textContent = `Add Attendance for (${item.brief_description})`;
            
            fetch(`/faculty-tasks/attendance/is-added?id=${item.id}`)
              .then(response => response.json())
              .then(data => {
                  if (data.exists) {
                      showNotification('You have already added attendance for this activity.', '#fe3232bc');
                      return;
                  } 

                  const popup = document.querySelector('.create-task-popup');
                  popup.style.display = 'block';

                  void popup.offsetWidth;
                  popup.classList.add('create-task-popup-animate');

                  const overlay = document.querySelector('.overlay');
                  overlay.classList.add('blur');
              })
              .catch(error => {
                  console.log(error)
                  //console.error('Error:', error);
                  showNotification('An error occurred, please try again.', '#fe3232bc');
              });
        }

        function closeAttendance() {
            resetForm();

            const popup = document.querySelector('.create-task-popup');
            popup.style.display = 'none';
            popup.classList.remove('create-task-popup-animate');

            const overlay = document.querySelector('.overlay');
            overlay.classList.remove('blur');
        }

        function resetForm() {
            const dateStarted = document.getElementById('date-picker-started');
            const dateCompleted = document.getElementById('date-picker-completed');
            const statusAttendanceRadios = document.querySelectorAll('input[name="status-attendance"]');
            const reasonAbsenceInput = document.getElementById('reason-absence-input');
            const selectedStatusAttendance = document.getElementById('selected-status-attendance-display');

            dateStarted.value = '';
            dateCompleted.value = '';
            statusAttendanceRadios.forEach(radio => {
                radio.checked = false;
            });
            reasonAbsenceInput.value = '';
            selectedStatusAttendance.textContent = 'Select status of attendance';

            document.querySelector('.reason-for-absence-container').style.display = 'none';
            document.querySelector('.attendance-proof-container').style.display = 'none';

            selectedFiles = [];
            updatePreview();
        }

        // Add event listener to status of attendance dropdown, if selected on leave, show reason for absence input
        const statusAttendanceRadios2 = document.querySelectorAll('input[name="status-attendance"]');

        statusAttendanceRadios2.forEach(radio => {
            radio.addEventListener('change', () => {
                if (radio.id === 'On-Leave') {
                    document.querySelector('.reason-for-absence-container').style.display = 'block';
                    document.querySelector('.attendance-proof-container').style.display = 'none';
                } else {
                    document.querySelector('.reason-for-absence-container').style.display = 'none';
                    document.querySelector('.attendance-proof-container').style.display = 'block';
                }
            });
        });

        function loadingMessage() {
            let div = document.getElementById("loading-text");
            let text = ["Adding attendance, this may take a few seconds.",
                "Adding attendance, this may take a few seconds..",
                "Adding attendance, this may take a few seconds..."
            ];

            let i = 0;
            setInterval(function() {
                div.innerHTML = text[i];
                i = (i + 1) % text.length;
            }, 400);
        }

        /* Dropdown for faculties
        const dropdown2 = document.querySelector('.create-dropdown-faculties');
        const list2 = document.querySelector('.create-list-faculties');
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
        });*/

        // Dropdown for status of attendance
        const dropdownStatusAttendance = document.querySelector('.create-dropdown-status-attendance');
        const listStatusAttendance = document.querySelector('.create-list-status-attendance');
        const caretStatusAttendance = document.querySelector('.caret-status-attendance');
        const selectedStatusAttendance = document.getElementById('selected-status-attendance-display');

        dropdownStatusAttendance.addEventListener('click', () => {
            listStatusAttendance.classList.toggle('show');
            caretStatusAttendance.classList.toggle('fa-rotate');
        });

        document.addEventListener('click', (e) => {
            if (!dropdownStatusAttendance.contains(e.target)) {
                listStatusAttendance.classList.remove('show');
                caretStatusAttendance.classList.remove('fa-rotate');
            }
        });

        let itemsStatusAttendance = document.querySelectorAll('.status-attendance');
        itemsStatusAttendance.forEach(item => {
            item.addEventListener('click', (event) => {
                event.stopPropagation();
            });
        });

        let statusAttendanceRadios = document.querySelectorAll('input[name="status-attendance"]');
        statusAttendanceRadios.forEach(radio => {
            radio.addEventListener('change', () => {
              selectedStatusAttendance.textContent = radio.parentElement.querySelector('.text').textContent;
            });
        });

        // File upload

        document.getElementById("file-upload").onchange = function() {
            var files = document.getElementById("file-upload").files;
            if (files.length === 0) {
                // The user clicked the cancel button in the file upload dialog
                console.log('Upload cancelled');
            } 
            else {
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
                
                selectedFiles.push(file);
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

                // Reset the value of the file input
                document.getElementById("file-upload").value = null;
            }
        });

        // Search functionality

        const searchInput = document.querySelector('#search-input');
        const taskRows = document.querySelectorAll('.task-row');

        function debounce(func, wait) {
            let timeout;
            return function(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), wait);
            }
        }

        const debouncedInputHandler = debounce(function(event) {
            document.getElementById('loading-overlay-search').style.display = 'flex';
            
            const category = 'Published'
            const query = encodeURIComponent(event.target.value);
            console.log(query);
            fetch(`/faculty-tasks/functions/search?query=${query}`)
                .then(response => response.json())
                .then(data => {
                    console.log(data)
                    const items = Object.values(data.items)
                    const researchContainer = document.querySelector('.task-container');
                    researchContainer.innerHTML = '';

                    items.forEach(research => {
                        const row = `
                            <div class="row task-row">
                                <div class="col-5">
                                    <h5 class="task-row-content my-2 task-name-text" style="text-align:left; margin-left: 32%">${research.brief_description}</h5>
                                </div>
                                <div class="col-4">
                                    <h5 class="task-row-content my-2 task-name-text" style="text-align:left; margin-left: 43%">${research.remarks}</h5>
                                </div>
                                <div class="col-3 d-flex justify-content-center">
                                  <button class="add-attendance-btn mx-2">
                                    Add
                                  </button>
                                </div>
                            </div>
                        `;

                        researchContainer.innerHTML += row;

                        // Add event listener to all row buttons
                        const addAttendanceBtns = document.querySelectorAll('.add-attendance-btn');
                        addAttendanceBtns.forEach(btn => {
                            btn.addEventListener('click', (event) => {
                                addAttendance(event, research);
                            });
                        });
                    });

                    document.getElementById('loading-overlay-search').style.display = 'none';

                    
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('An error occurred, please try again.', '#fe3232bc');
                })
                .finally(() => {
                    document.getElementById('loading-overlay-search').style.display = 'none';
                });
        }, 50);

        searchInput.addEventListener('input', debouncedInputHandler);

        // Form handling

        function validateForm() {
            const dateStarted = document.getElementById('date-picker-started').value;
            const dateCompleted = document.getElementById('date-picker-completed').value;
            const statusAttendanceRadios = document.querySelectorAll('input[name="status-attendance"]');
            const reasonAbsenceInput = document.getElementById('reason-absence-input').value;
            const selectedStatusAttendance = document.getElementById('selected-status-attendance-display').textContent;

            if (dateStarted === '') {
                showNotification('Please select the date started.', '#fe3232bc');
                return false;
            }

            if (dateCompleted === '') {
                showNotification('Please select the date completed.', '#fe3232bc');
                return false;
            }

            let statusAttendanceChecked = false;
            statusAttendanceRadios.forEach(radio => {
                if (radio.checked) {
                    statusAttendanceChecked = true;
                }
            });

            if (!statusAttendanceChecked) {
                showNotification('Please select the status of attendance.', '#fe3232bc');
                return false;
            }

            if (selectedStatusAttendance === 'Select status of attendance') {
                showNotification('Please select the status of attendance.', '#fe3232bc');
                return false;
            }

            if (selectedStatusAttendance.trim() === 'On Leave' && reasonAbsenceInput === '') {
                showNotification('Please enter the reason for absence.', '#fe3232bc');
                return false;
            }
            else if (selectedStatusAttendance.trim() !== 'On Leave' && selectedFiles.length <= 0) {
                showNotification('Please upload proof of attendance.', '#fe3232bc');
                return false;
            }

            return true;
        }

        function submitForm() {
            if (!validateForm()) {
                return;
            }
            
            const dateStarted = document.getElementById('date-picker-started').value;
            const dateCompleted = document.getElementById('date-picker-completed').value;
            const statusAttendanceRadios = document.querySelectorAll('input[name="status-attendance"]');
            const reasonAbsenceInput = document.getElementById('reason-absence-input').value;
            const selectedStatusAttendance = document.getElementById('selected-status-attendance-display').textContent;

            const formData = new FormData();
            formData.append('date_started', dateStarted);
            formData.append('date_completed', dateCompleted);
            formData.append('status_attendance', selectedStatusAttendance.trim());
            formData.append('reason_absence', reasonAbsenceInput);

            for (const file of selectedFiles) {
                formData.append('files[]', file);
            }

            formData.append('function_id', selectedItemId);
            
            const loadingOverlay = document.getElementById('loading-overlay');
            const loadingText = document.getElementById('loading-text');
            const createResearchBtn = document.querySelector('.create-research-btn');

            loadingOverlay.style.display = 'flex';
            createResearchBtn.disabled = true;
            loadingMessage();

            let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            fetch('/faculty-tasks/attendance/create', {
                    headers: {
                        "Accept": "application/json, text-plain, */*",
                        "X-Requested-With": "XMLHttpRequest",
                        "X-CSRF-TOKEN": token
                    },
                    method: 'POST',
                    credentials: "same-origin",
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                    localStorage.setItem('notif_green', 'Attendance added successfully');
                    window.location.href = '/faculty-tasks/attendance';
                    /*if (data.newlyAddedResearch) {
                        showNotification('Research created successfully', '#32fe32bc');
                        closeNewTask();
                        resetForm();

                        let tasks = data.allPublishedResearches;
                        let newlyAdded = data.newlyAddedResearch;
                        refreshTable(tasks, newlyAdded);
                    } 
                    else {
                        showNotification('An error occurred, please try again.', '#fe3232bc');
                    }*/
                })
                .catch((error) => {
                    console.error('Error:', error);
                    showNotification('An error occurred, please try again.', '#fe3232bc');
                })
                .finally(() => {
                    loadingOverlay.style.display = 'none';
                    createResearchBtn.disabled = false;
                });
        }

        function refreshTable(tasks, newlyAdded) {
            // Get the container element for the task rows
            const taskList = document.querySelector('.task-container');

            // Remove all existing task rows
            taskList.querySelectorAll('.task-row').forEach(row => row.remove());

            // Loop through the tasks array and create a new row for each task
            tasks.forEach(task => {
                const row = document.createElement('div');
                row.classList.add('row', 'task-row');

                if (newlyAdded && task.title === newlyAdded.title) {
                    row.classList.add('newly-added'); // Add the newly-added class to the task row element

                    // Remove the newly-added class after 3 seconds
                    setTimeout(() => {
                        row.classList.remove('newly-added');
                    }, 3000);
                }

                row.innerHTML = `
                    <div class="col-4">
                        <h5 class="task-row-content my-2 task-name-text" style="text-align:left; margin-left: 47%">${task.title}</h5>
                    </div>
                    <div class="col-4">
                        <h5 class="task-row-content my-2 task-name-text" style="text-align:left; margin-left: 43.5%">${task.authors}</h5>
                    </div>
                    <div class="col-4">
                        <h5 class="task-row-content my-2 date-created" style="text-align:left; margin-left: 41%">
                            ${task.date_created_formatted}
                                <br>
                            ${task.date_created_time}
                        </h5>
                    </div>
                `;

                taskList.appendChild(row);
            });

            // Show the table
            taskList.style.display = 'block';
        }

        // On row click
        function getSelectedItemRow(research) {
            window.location.href = `/faculty-tasks/researches/view?category=Published&id=${research.id}`;
        }

    </script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script>
@endsection