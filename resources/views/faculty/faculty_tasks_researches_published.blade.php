@extends('layouts.default')

@section('title', 'PUPQC - Researches Published')

@section('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('admin/css/admin_tasks_researches_published.css') }}">
@endsection

@section('body')
<div class="overlay"></div>
@include('layouts.faculty_navbar')
@include('layouts.faculty_tasks_sidebar')
@include('layouts.notification_side')

<div class="container-fluid margin">
    <div class="row">
        <div class="col-4">
            <h1 class="my-4 title">Researches (Published)</h1>
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
                Add Research (Published)
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
                      <label for="" class="ms-3">Name of Journal*</label>
                      <input class="research-input" id="journal-input" type="text" placeholder="Enter name of journal">
                    </div>

                    <div class="d-flex flex-column mt-3">
                        <label for="" class="ms-3">Date of Publication*</label>
                        <input class="ms-2" type="date" id="date-picker" min="1997-01-01" max="2030-01-01">
                    </div>           

                    <div class="d-flex flex-column mt-3">
                      <label for="" class="ms-3">Research Link*</label>
                      <input class="research-input" id="link-input" type="link" placeholder="Enter research link">
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
                <h5 class="my-3 column-name" style="z-index: 100; position: relative;">Name of Journal</h5>
            </div>
            <div class="col-2">
                <h5 class="my-3 column-name" style="z-index: 100; position: relative;">Published At</h5>
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
                    <h5 class="task-row-content my-2 task-name-text" style="text-align:left; margin-left: 21%">{{ $research->name_of_journal }}</h5>
                </div>
                <div class="col-2">
                    <h5 class="task-row-content my-2 task-name-text" style="text-align:left; margin-left: 28%">{{ $research->published_at }}</h5>
                </div>
                <div class="col-2">
                    <h5 class="task-row-content my-2 date-created" style="text-align:left; margin-left: 32%">
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
            document.getElementById('journal-input').value = '';
            document.getElementById('date-picker').value = '';
            document.getElementById('link-input').value = '';

            const checkboxes = document.querySelectorAll('.item2 input[type="checkbox"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
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
                                    <h5 class="task-row-content my-2 task-name-text" style="text-align:left; margin-left: 21%">${research.name_of_journal}</h5>
                                </div>
                                <div class="col-2">
                                    <h5 class="task-row-content my-2 task-name-text" style="text-align:left; margin-left: 28%">${research.published_at}</h5>
                                </div>
                                <div class="col-2">
                                    <h5 class="task-row-content my-2 date-created" style="text-align:left; margin-left: 32%">
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
            const journal = document.getElementById('journal-input').value;
            const date = document.getElementById('date-picker').value;
            const link = document.getElementById('link-input').value;

            if (title.trim() === '') {
                showNotification('Please enter a title.', '#fe3232bc');
                return false;
            }

            if (authors.length === 0) {
                showNotification('Please select at least one author.', '#fe3232bc');
                return false;
            }

            if (journal.trim() === '') {
                showNotification('Please enter the name of the journal.', '#fe3232bc');
                return false;
            }

            if (date.trim() === '') {
                showNotification('Please enter the date of publication.', '#fe3232bc');
                return false;
            }

            if (link.trim() === '') {
                showNotification('Please enter the link to the research.', '#fe3232bc');
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
            const journal = document.getElementById('journal-input').value;
            const date = document.getElementById('date-picker').value;

            const checkboxes = document.querySelectorAll('.item2 input[type="checkbox"]');
            checkboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    const authorName = checkbox.parentElement.querySelector('.text').textContent.trim();
                    authors.push(authorName);
                }
            });
         
            const link = document.getElementById('link-input').value;

            const data = new FormData();
            data.append('title', title);
            data.append('authors', authors.join(', '));
            data.append('journal', journal);
            data.append('date', date);
            data.append('link', link);
            data.append('type', 'Published');

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

                        let tasks = data.allPublishedResearches;
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
            window.location.href = `/faculty-tasks/researches/view?category=Published&id=${research.id}`;
        }

    </script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script>
@endsection