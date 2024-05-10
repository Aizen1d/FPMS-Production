@extends('layouts.default')

@section('title', 'PUPQC - Functions')

@section('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('admin/css/admin_tasks_functions.css') }}">
@endsection

@section('body')
<div class="overlay"></div>
@include('layouts.admin_navbar')
@include('layouts.admin_tasks_sidebar')
@include('layouts.notification_side')

<div class="container-fluid margin">
    <div class="row">
        <div class="col-4">
            <h1 class="my-4 title">College/Office Functions</h1>
        </div>
        <div class="col-2 pages">
            {{ $items->links()  }}
        </div>
        <div class="col-6 drop-down-container">
            <input type="text" class="search-input mx-5" id="search-input" placeholder="Search function...">
            <div id="search-results"></div>

            <button class="my-4 create-btn" onclick="createNewTask()">Add Function</button>
        </div>
    </div>

    <div class="create-task-popup">
        <div class="row">
          <div class="d-flex flex-col col-9">
            <h5 class="create-label">
                Add Function
            </h5>
          </div>
          <div class="col-3">
            <button class="close-task-btn" onclick="closeNewTask()"><i class="fa fa-times"></i></button>
          </div>
        </div>

        <div class="row mt-3">
            <div class="col-12">
                <div class="ms-3">
                    <div class="d-flex flex-column">
                        <label for="" class="ms-3">Brief description of Activity*</label>
                        <input class="research-input" id="brief-description-input" type="text" placeholder="Enter brief description of Activity">
                    </div>

                    <div class="d-flex flex-column mt-3">
                      <label for="" class="ms-3">Remarks*</label>
                      <input class="research-input" id="remarks-input" type="text" placeholder="Enter remarks">
                    </div>

                    <div class="d-flex justify-content-center items-center mt-4">
                        <button class="d-flex justify-content-center items-center create-research-btn" onclick="submitForm()">
                            Create
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div id="loading-overlay" class="loading-create-task" style="display: none; justify-content: center; align-items: center; border-radius: 25px; z-index: 99; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: white;">
            <div style="display: flex; flex-direction: column; align-items: center;">
                <div class="spinner-border text-dark" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <div id="loading-text" style="margin-top: 3px;">Creating function, this may take a few seconds.</div>
            </div>
        </div>
    </div>

    <div class="container-fluid task-list" style="position: relative;">
        <div class="row">
          <div class="col-5">
              <h5 class="my-3 column-name" style="z-index: 100; position: relative;">Brief description of Activity</h5>
          </div>
          <div class="col-4">
              <h5 class="my-3 column-name" style="z-index: 100; position: relative;">Remarks</h5>
          </div>
          <div class="col-3">
            <h5 class="my-3 column-name" style="z-index: 100; position: relative;">Created At</h5>
          </div>
        </div>

        <div class="task-container">
            @foreach ($items as $item)
            <div class="row task-row" onclick="getSelectedItemRow({{ $item }})">
                <div class="col-5">
                    <h5 class="task-row-content my-2 task-name-text" style="text-align:left; margin-left: 32%">{{ $item->brief_description }}</h5>
                </div>
                <div class="col-4">
                  <h5 class="task-row-content my-2 task-name-text" style="text-align:left; margin-left: 42.8%">{{ $item->remarks }}</h5>
              </div>
                <div class="col-3">
                    <h5 class="task-row-content my-2 date-created" style="text-align:left; margin-left: 39%">
                        {{ date('F j, Y', strtotime($item->created_at)) }}
                        <br>
                        {{ date('g:i A', strtotime($item->created_at)) }}
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

        // create task popup
        function createNewTask() {
            const popup = document.querySelector('.create-task-popup');
            popup.style.display = 'block';

            void popup.offsetWidth;
            popup.classList.add('create-task-popup-animate');

            const overlay = document.querySelector('.overlay');
            overlay.classList.add('blur');
        }

        function closeNewTask() {
            const popup = document.querySelector('.create-task-popup');
            popup.style.display = 'none';
            popup.classList.remove('create-task-popup-animate');

            const overlay = document.querySelector('.overlay');
            overlay.classList.remove('blur');
        }

        function resetForm() {
          document.getElementById('name-input').value = '';
          document.getElementById('venue-input').value = '';
          document.getElementById('host-input').value = '';

          selectedFiles = [];
          updatePreview();
        }

        function loadingMessage() {
            let div = document.getElementById("loading-text");
            let text = ["Creating function, this may take a few seconds.",
                "Creating function, this may take a few seconds..",
                "Creating function, this may take a few seconds..."
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

        /// File Upload ///

        /*document.getElementById("file-upload").onchange = function() {
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
        });*/

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

            const query = encodeURIComponent(event.target.value);
            fetch(`/admin-tasks/functions/search?query=${query}`)
                .then(response => response.json())
                .then(data => {
                    const items = Object.values(data.items)
                    const taskContainer = document.querySelector('.task-container');
                    taskContainer.innerHTML = '';

                    items.forEach(item => {
                        const row = `
                            <div class="row task-row" onclick="getSelectedItemRow(${JSON.stringify(item)})">
                                <div class="col-5">
                                    <h5 class="task-row-content my-2 task-name-text" style="text-align:left; margin-left: 32%">${item.brief_description}</h5>
                                </div>
                                <div class="col-4">
                                    <h5 class="task-row-content my-2 task-name-text" style="text-align:left; margin-left: 42.8%">${item.remarks}</h5>
                                </div>
                                <div class="col-3">
                                    <h5 class="task-row-content my-2 date-created" style="text-align:left; margin-left: 39%">
                                        ${item.date_created_formatted}
                                        <br>
                                        ${item.date_created_time}
                                    </h5>
                                </div>
                            </div>
                        `;

                        taskContainer.innerHTML += row;

                        // Add event listeners to search results
                        taskContainer.querySelectorAll('.task-row').forEach(row => {
                            row.addEventListener('click', () => {
                                getSelectedItemRow(item);
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
          const brief_description = document.getElementById('brief-description-input').value;
          const remarks = document.getElementById('remarks-input').value;

          if (brief_description.trim() === '') {
            showNotification('Please enter a brief description of the activity.', '#fe3232bc');
            return false;
          }

          if (remarks.trim() === '') {
            showNotification('Please enter remarks.', '#fe3232bc');
            return false;
          }

          return true;
        }

        function submitForm() {
            if (!validateForm()) {
                return;
            }
            
            const brief_description = document.getElementById('brief-description-input').value;
            const remarks = document.getElementById('remarks-input').value;

            const data = new FormData();
            data.append('brief_description', brief_description);
            data.append('remarks', remarks);

            const loadingOverlay = document.getElementById('loading-overlay');
            const loadingText = document.getElementById('loading-text');
            const createResearchBtn = document.querySelector('.create-research-btn');

            loadingOverlay.style.display = 'flex';
            createResearchBtn.disabled = true;
            loadingMessage();

            let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            fetch('/admin-tasks/functions/create', {
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
                .then(data => {
                    console.log(data);
                    window.location.reload();

                    /*if (data.newlyAddedAttendance) {
                        showNotification('Attendance created successfully', '#32fe32bc');
                        closeNewTask();
                        resetForm();

                        let tasks = data.allAttendance;
                        let newlyAdded = data.newlyAddedAttendance;
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

                if (newlyAdded && task.name_of_activity === newlyAdded.name_of_activity) {
                    row.classList.add('newly-added'); // Add the newly-added class to the task row element

                    // Remove the newly-added class after 3 seconds
                    setTimeout(() => {
                        row.classList.remove('newly-added');
                    }, 3000);
                }

                row.innerHTML = `
                    <div class="col-6">
                        <h5 class="task-row-content my-2 task-name-text" style="text-align:left; margin-left: 41%">${task.name_of_activity}</h5>
                    </div>
                    <div class="col-6">
                        <h5 class="task-row-content my-2 date-created" style="text-align:left; margin-left: 44.5%">
                            ${task.date_created_formatted}
                                <br>
                            ${task.date_created_time}
                        </h5>
                    </div>
                `;

                taskList.appendChild(row);

                // Add the event listener to the row
                row.addEventListener('click', () => {
                    getSelectedItemRow(task);
                });
            });

            // Show the table
            taskList.style.display = 'block';
        }

        // On row click
        function getSelectedItemRow(item) {
            window.location.href = `/admin-tasks/functions/view?id=${item.id}`;
        }

    </script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script>
@endsection
