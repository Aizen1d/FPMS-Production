@extends('layouts.default')

@section('title', 'PUPQC - Researches Presented')

@section('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('admin/css/admin_tasks_researches_presented.css') }}">
@endsection

@section('body')
<div class="overlay"></div>
@include('layouts.faculty_navbar')
@include('layouts.faculty_tasks_sidebar')
@include('layouts.notification_side')

<div class="container-fluid margin">
    <div class="row">
        <div class="col-4">
            <h1 class="my-4 title">Researches (Presented)</h1>
        </div>
        <div class="col-2 pages">
            {{ $researches->links()  }}
        </div>
        <div class="col-6 drop-down-container">
            <input type="text" class="search-input mx-5" id="search-input" placeholder="Search research title...">
            <div id="search-results"></div>

        </div>
    </div>

    <div class="create-task-popup">
        <div class="row">
          <div class="d-flex flex-col col-9">
            <h5 class="create-label">
                Add Research (Presented)
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
                        <label for="" class="ms-3">Title*</label>
                        <input class="research-input" id="research-title-input" type="text" placeholder="Enter title">
                    </div>

                    <div class="d-flex flex-column mt-3">
                        <label for="" class="ms-3">Authors*</label>
                        <div class="drop-down create-dropdown-faculties">
                            <div class="wrapper">
                                <div class="selected">Select authors</div>
                            </div>
                            <i class="fa fa-caret-down caret2"></i>
    
                            <div class="list create-list-faculties">
                                @foreach ($faculties as $faculty)
                                <div class="item2">
                                    <input type="checkbox" id="all">
                                    <div class="text">
                                        {{ $faculty->first_name }} {{ $faculty->middle_name ? $faculty->middle_name . ' ' : '' }}{{ $faculty->last_name }}
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-column mt-3">
                        <label for="" class="ms-3">Conference Organizer / Host*</label>
                        <input class="research-input" id="host-input" type="text" placeholder="Enter Conference Organizer / Host">
                    </div>

                    <div class="d-flex flex-column mt-3">
                        <label for="" class="ms-3">Level*</label>
                        <div class="drop-down create-dropdown-level">
                            <div class="wrapper">
                                <div class="selected">Select Level</div>
                            </div>
                            <i class="fa fa-caret-down caret-level"></i>
                    
                            <div class="list create-list-level">
                                <div class="item2">
                                    <input type="radio" name="level" id="local">
                                    <div class="text">
                                        Local
                                    </div>
                                </div>
                                <div class="item2">
                                    <input type="radio" name="level" id="national">
                                    <div class="text">
                                        National
                                    </div>
                                </div>
                                <div class="item2">
                                    <input type="radio" name="level" id="international">
                                    <div class="text">
                                        International
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>                    

                    <div class="d-flex flex-column mt-3 ms-2" style="margin-left: 2% !important">
                        <label for="" style="margin-left: 1% !important">S.O and Certificates*</label>
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

                    <div class="d-flex justify-content-center items-center mt-3">
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
                <div id="loading-text" style="margin-top: 3px;">Creating research, this may take a few seconds.</div>
            </div>
        </div>
    </div>

    <div class="container-fluid task-list" style="position: relative;">
        <div class="row">
            <div class="col-3">
                <h5 class="my-3 column-name" style="z-index: 100; position: relative;">Title</h5>
            </div>
            <div class="col-3">
                <h5 class="my-3 column-name" style="z-index: 100; position: relative;">Authors</h5>
            </div>
            <div class="col-2">
                <h5 class="my-3 column-name" style="z-index: 100; position: relative;">Hosts</h5>
            </div>
            <div class="col-2">
                <h5 class="my-3 column-name" style="z-index: 100; position: relative;">Level</h5>
            </div>
            <div class="col-2">
                <h5 class="my-3 column-name" style="z-index: 100; position: relative;">Created At</h5>
            </div>
        </div>

        <div class="task-container">
            @foreach ($researches as $research)
            <div class="row task-row" onclick="getSelectedResearchRow({{ $research }})">
                <div class="col-3">
                    <h5 class="task-row-content my-2 task-name-text" style="text-align:left; margin-left: 47%">{{ $research->title }}</h5>
                </div>
                <div class="col-3">
                    <h5 class="task-row-content my-2 task-name-text authors-truncate" style="text-align:left; margin-left: 41%">{{ $research->authors }}</h5>
                </div>
                <div class="col-2">
                    <h5 class="task-row-content my-2 task-name-text" style="text-align:left; margin-left: 40.5%">{{ $research->host }}</h5>
                </div>
                <div class="col-2">
                    <h5 class="task-row-content my-2 task-name-text" style="text-align:left; margin-left: 41.5%">{{ $research->level }}</h5>
                </div>
                <div class="col-2">
                    <h5 class="task-row-content my-2 date-created" style="text-align:left; margin-left: 33%">
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
            document.getElementById('research-title-input').value = '';
            document.getElementById('host-input').value = '';

            const checkboxes = document.querySelectorAll('.item2 input[type="checkbox"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
            });

            const radioButtons = document.querySelectorAll('.create-list-level input[type="radio"]');
            radioButtons.forEach(radio => {
                radio.checked = false;
            });

            selectedFiles = [];
            updatePreview();
        }

        function loadingMessage() {
            let div = document.getElementById("loading-text");
            let text = ["Creating research, this may take a few seconds.",
                "Creating research, this may take a few seconds..",
                "Creating research, this may take a few seconds..."
            ];

            let i = 0;
            setInterval(function() {
                div.innerHTML = text[i];
                i = (i + 1) % text.length;
            }, 400);
        }

        // Dropdown for faculties
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
        });

        // Dropdown for level
        const dropdownLevel = document.querySelector('.create-dropdown-level');
        const listLevel = document.querySelector('.create-list-level');
        const caretLevel = document.querySelector('.caret-level');

        dropdownLevel.addEventListener('click', () => {
            listLevel.classList.toggle('show');
            caretLevel.classList.toggle('fa-rotate');
        });

        document.addEventListener('click', (e) => {
            if (!dropdownLevel.contains(e.target)) {
                listLevel.classList.remove('show');
                caretLevel.classList.remove('fa-rotate');
            }
        });

        let itemsLevel = document.querySelectorAll('.item2');
        itemsLevel.forEach(item => {
            item.addEventListener('click', (event) => {
                event.stopPropagation();
            });
        });

        /// File Upload ///

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

            const category = 'Presented'
            const query = encodeURIComponent(event.target.value);
            fetch(`/faculty-tasks/researches/category/search?category=${category}&query=${query}`)
                .then(response => response.json())
                .then(data => {
                    const researches = Object.values(data.researches)
                    const researchContainer = document.querySelector('.task-container');
                    researchContainer.innerHTML = '';

                    researches.forEach(research => {
                        const row = `
                            <div class="row task-row">
                                <div class="col-3">
                                    <h5 class="task-row-content my-2 task-name-text" style="text-align:left; margin-left: 47%">${research.title}</h5>
                                </div>
                                <div class="col-3">
                                    <h5 class="task-row-content my-2 task-name-text authors-truncate" style="text-align:left; margin-left: 41%">${research.authors}</h5>
                                </div>
                                <div class="col-2">
                                    <h5 class="task-row-content my-2 task-name-text" style="text-align:left; margin-left: 40.5%">${research.host}</h5>
                                </div>
                                <div class="col-2">
                                    <h5 class="task-row-content my-2 task-name-text" style="text-align:left; margin-left: 41.5%">${research.level}</h5>
                                </div>
                                <div class="col-2">
                                    <h5 class="task-row-content my-2 date-created" style="text-align:left; margin-left: 33%">
                                        ${research.date_created_formatted}
                                        <br>
                                        ${research.date_created_time}
                                    </h5>
                                </div>
                            </div>
                        `;

                        researchContainer.innerHTML += row;

                        // Add event listeners to search results
                        document.querySelectorAll('.task-row').forEach(row => {
                            row.addEventListener('click', () => {
                                getSelectedResearchRow(research);
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
            const title = document.getElementById('research-title-input').value;
            const authors = document.querySelectorAll('.item2 input[type="checkbox"]:checked');
            const host = document.getElementById('host-input').value;
            const level = document.querySelector('.create-list-level input[type="radio"]:checked');

            if (title === '') {
                showNotification('Title is required', '#fe3232bc');
                return false;
            }

            if (authors.length <= 0) {
                showNotification('Authors are required', '#fe3232bc');
                return false;
            }

            if (host === '') {
                showNotification('Conference Organizer / Host is required', '#fe3232bc');
                return false;
            }

            if (!level) {
                showNotification('Level is required', '#fe3232bc');
                return false;
            }

            if (selectedFiles.length <= 0) {
                showNotification('Files are required', '#fe3232bc');
                return false;
            }

            return true;
        }

        function submitForm() {
            if (!validateForm()) {
                return;
            }
            
            const title = document.getElementById('research-title-input').value;
            let authors = [];

            const checkboxes = document.querySelectorAll('.item2 input[type="checkbox"]');
            checkboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    const authorName = checkbox.parentElement.querySelector('.text').textContent.trim();
                    authors.push(authorName);
                }
            });

            const host = document.getElementById('host-input').value;

            // Get the selected level
            const selectedLevelElement = document.querySelector('.create-list-level input[type="radio"]:checked');
            let level = '';
            if (selectedLevelElement) {
                level = selectedLevelElement.nextElementSibling.textContent.trim();
            }

            const data = new FormData();
            data.append('title', title);
            data.append('authors', authors.join(', '));
            data.append('host', host);
            data.append('level', level);
            data.append('type', 'Presented');

            for (const file of selectedFiles) {
                data.append('files[]', file);
            }

            const loadingOverlay = document.getElementById('loading-overlay');
            const loadingText = document.getElementById('loading-text');
            const createResearchBtn = document.querySelector('.create-research-btn');

            loadingOverlay.style.display = 'flex';
            createResearchBtn.disabled = true;
            loadingMessage();

            let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            fetch('/faculty-tasks/researches/create-research', {
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
                    if (data.newlyAddedResearch) {
                        showNotification('Research created successfully', '#32fe32bc');
                        closeNewTask();
                        resetForm();

                        let tasks = data.allPresentedResearches;
                        let newlyAdded = data.newlyAddedResearch;
                        refreshTable(tasks, newlyAdded);
                    } 
                    else {
                        showNotification('An error occurred, please try again.', '#fe3232bc');
                    }
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

        if (localStorage.getItem('notif_green')) {
            showNotification(localStorage.getItem('notif_green'), '#278a51');
            localStorage.removeItem('notif_green');
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

                // Add the event listener to the row
                row.addEventListener('click', () => {
                    getSelectedResearchRow(task);
                });
            });

            // Show the table
            taskList.style.display = 'block';
        }

        // On row click
        function getSelectedResearchRow(research) {
            window.location.href = `/faculty-tasks/researches/view?category=Presented&id=${research.id}`;
        }

    </script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script>
@endsection