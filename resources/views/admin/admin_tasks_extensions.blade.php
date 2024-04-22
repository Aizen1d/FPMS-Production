@extends('layouts.default')

@section('title', 'PUPQC - Extensions')

@section('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('admin/css/admin_tasks_extensions.css') }}">
@endsection

@section('body')
<div class="overlay"></div>
@include('layouts.admin_navbar')
@include('layouts.admin_tasks_sidebar')
@include('layouts.notification_side')

<div class="container-fluid margin">
    <div class="row">
        <div class="col-4">
            <h1 class="my-4 title">Extensions</h1>
        </div>
        <div class="col-2 pages">
            {{ $items->links()  }}
        </div>
        <div class="col-6 drop-down-container">
            <input type="text" class="search-input mx-5" id="search-input" placeholder="Search extension title...">
            <div id="search-results"></div>

            <button class="my-4 create-btn" onclick="createNewTask()">Add Extension</button>
        </div>
    </div>

    <div class="create-task-popup">
        <div class="row">
          <div class="d-flex flex-col col-9">
            <h5 class="create-label">
                Add Extension Project
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
                        <input class="research-input" id="title-input" type="text" placeholder="Enter title">
                    </div>

                    <div class="d-flex flex-column mt-3">
                        <label for="" class="ms-3">Date Conducted*</label>
                        <input class="ms-2" type="date" id="date-picker" min="1997-01-01" max="2030-01-01">
                    </div>     
                    
                    <div class="d-flex flex-column mt-3">
                      <label for="" class="ms-3">Partner / Linkage*</label>
                      <input class="research-input" id="partner-input" type="text" placeholder="Enter partner / linkage">
                    </div>

                    <div class="d-flex flex-column mt-3">
                      <label for="" class="ms-3">Beneficiaries*</label>
                      <input class="research-input" id="beneficiaries-input" type="text" placeholder="Enter beneficiaries">
                    </div>

                    <div class="d-flex flex-column mt-3">
                      <label for="" class="ms-3">Evaluation*</label>
                      <input class="research-input" id="evaluation-input" type="text" placeholder="Enter evaluation">
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
                <div id="loading-text" style="margin-top: 3px;">Creating extension, this may take a few seconds.</div>
            </div>
        </div>
    </div>

    <div class="container-fluid task-list" style="position: relative;">
        <div class="row">
            <div class="col-6">
                <h5 class="my-3 column-name" style="z-index: 100; position: relative;">Title</h5>
            </div>
            <div class="col-6">
                <h5 class="my-3 column-name" style="z-index: 100; position: relative;">Created At</h5>
            </div>
        </div>

        <div class="task-container">
            @foreach ($items as $item)
            <div class="row task-row" onclick="getSelectedItemRow({{ $item }})">
                <div class="col-6">
                    <h5 class="task-row-content my-2 task-name-text" style="text-align:left; margin-left: 47.8%">{{ $item->title }}</h5>
                </div>
                <div class="col-6">
                    <h5 class="task-row-content my-2 date-created" style="text-align:left; margin-left: 44.5%">
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
            document.getElementById('title-input').value = '';
            document.getElementById('date-picker').value = '';
            document.getElementById('partner-input').value = '';
            document.getElementById('beneficiaries-input').value = '';
            document.getElementById('evaluation-input').value = '';

            const checkboxes = document.querySelectorAll('.item2 input[type="checkbox"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
        }

        function loadingMessage() {
            let div = document.getElementById("loading-text");
            let text = ["Creating extension, this may take a few seconds.",
                "Creating extension, this may take a few seconds..",
                "Creating extension, this may take a few seconds..."
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
            fetch(`/admin-tasks/extensions/search?query=${query}`)
                .then(response => response.json())
                .then(data => {
                    const items = Object.values(data.items)
                    const taskContainer = document.querySelector('.task-container');
                    taskContainer.innerHTML = '';

                    items.forEach(item => {
                        const row = `
                            <div class="row task-row" onclick="getSelectedItemRow(${JSON.stringify(item)})">
                                <div class="col-6">
                                    <h5 class="task-row-content my-2 task-name-text" style="text-align:left; margin-left: 47.8%">${item.title}</h5>
                                </div>
                                <div class="col-6">
                                    <h5 class="task-row-content my-2 date-created" style="text-align:left; margin-left: 44.5%">
                                        ${item.date_created_formatted}
                                        <br>
                                        ${item.date_created_time}
                                    </h5>
                                </div>
                            </div>
                        `;

                        taskContainer.innerHTML += row;
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
          const title = document.getElementById('title-input').value;
          const date = document.getElementById('date-picker').value;
          const partner = document.getElementById('partner-input').value;
          const beneficiaries = document.getElementById('beneficiaries-input').value;
          const evaluation = document.getElementById('evaluation-input').value;

          if (title.trim() === '') {
              showNotification('Please enter a title.', '#fe3232bc');
              return false;
          }
          if (date.trim() === '') {
              showNotification('Please enter a date.', '#fe3232bc');
              return false;
          }
          if (partner.trim() === '') {
              showNotification('Please enter a partner.', '#fe3232bc');
              return false;
          }
          if (beneficiaries.trim() === '') {
              showNotification('Please enter beneficiaries.', '#fe3232bc');
              return false;
          }
          if (evaluation.trim() === '') {
              showNotification('Please enter an evaluation.', '#fe3232bc');
              return false;
          }

          return true;
        }

        function submitForm() {
            if (!validateForm()) {
                return;
            }
            
            const title = document.getElementById('title-input').value;
            const date = document.getElementById('date-picker').value;
            const partner = document.getElementById('partner-input').value;
            const beneficiaries = document.getElementById('beneficiaries-input').value;
            const evaluation = document.getElementById('evaluation-input').value;

            const data = new FormData();
            data.append('title', title);
            data.append('date', date);
            data.append('partner', partner);
            data.append('beneficiaries', beneficiaries);
            data.append('evaluation', evaluation);

            const loadingOverlay = document.getElementById('loading-overlay');
            const loadingText = document.getElementById('loading-text');
            const createResearchBtn = document.querySelector('.create-research-btn');

            loadingOverlay.style.display = 'flex';
            createResearchBtn.disabled = true;
            loadingMessage();

            let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            fetch('/admin-tasks/extensions/create', {
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
                    if (data.newlyAddedExtension) {
                        showNotification('Extension created successfully', '#32fe32bc');
                        closeNewTask();
                        resetForm();

                        let tasks = data.allExtensions;
                        let newlyAdded = data.newlyAddedExtension;
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
                    <div class="col-6">
                        <h5 class="task-row-content my-2 task-name-text" style="text-align:left; margin-left: 47.8%">${task.title}</h5>
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
            window.location.href = `/admin-tasks/extensions/view?id=${item.id}`;
        }

    </script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script>
@endsection