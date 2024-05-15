@extends('layouts.default')

@section('title', 'PUPQC - Researches Completed')

@section('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('admin/css/admin_tasks_researches_completed.css') }}">
@endsection

@section('body')
<div class="overlay"></div>
@include('layouts.faculty_navbar')
@include('layouts.faculty_tasks_sidebar')
@include('layouts.notification_side')

<div class="container-fluid margin">
    <div class="row">
        <div class="col-4">
            <h1 class="my-4 title">Researches (Completed)</h1>
        </div>
        <div class="col-2 pages">
            {{ $researches->links()  }}
        </div>
        <div class="col-6 drop-down-container">
            <input type="text" class="search-input mx-5" id="search-input" placeholder="Search research title...">
            <div id="search-results"></div>

            <button class="my-4 create-btn" onclick="createNewTask()">Add Research</button>
        </div>
    </div>

    <div class="create-task-popup">
        <div class="row">
          <div class="d-flex flex-col col-9">
            <h5 class="create-label">
                Add Research (Completed)
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
                        <label for="" class="ms-3" style="font-size: 11px" id="selected-authors-label"></label>
                        <div class="drop-down create-dropdown-faculties">
                            <div class="wrapper">
                                <div class="selected">Select authors</div>
                            </div>
                            <i class="fa fa-caret-down caret2"></i>
    
                            <div class="list create-list-faculties">
                                <input type="text" class="search-input-faculties" placeholder="Search faculty...">
                                <div class="item2">
                                    <input type="checkbox" id="all-checkbox">
                                    <div class="text select-all-checkbox">
                                        Select all
                                    </div>
                                </div>
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
                        <label for="" class="ms-3">Type of Funding*</label>
                        <div class="drop-down create-dropdown-typefunding">
                            <div class="wrapper">
                                <div class="selected" id="selected-typefunding-display">Type of Funding</div>
                            </div>
                            <i class="fa fa-caret-down caret-typefunding"></i>
                    
                            <div class="list create-list-typefunding">
                                <div class="typefunding">
                                    <input type="radio" name="typefunding" id="Internally-Funded">
                                    <div class="text">
                                        Internally Funded
                                    </div>
                                </div>
                                <div class="typefunding">
                                    <input type="radio" name="typefunding" id="Externally-Funded">
                                    <div class="text">
                                        Externally Funded
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>       

                    <div class="d-flex flex-column mt-3">
                        <label for="" class="ms-3">Date Completed*</label>
                        <input class="ms-2" type="date" id="date-picker" min="1997-01-01" max="2030-01-01">
                    </div>

                    <div class="d-flex flex-column mt-3">
                      <label for="" class="ms-3">Abstract / IMRaD*</label>
                      <textarea class="ms-2 task-description-content" id="abstract" name="description" rows="4" cols="50" placeholder="Enter Abstract / IMRaD here.."></textarea>
                    </div>                    

                    <div class="d-flex justify-content-center items-center mt-2">
                        <button class="d-flex justify-content-center items-center create-research-btn" onclick="submitForm()">
                            Create
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div id="loading-overlay" class="loading-create-task" style="display: none; justify-content: center; height: 70vh; align-items: center; border-radius: 25px; z-index: 9999; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: white;">
            <div style="display: flex; flex-direction: column; align-items: center;">
                <div class="spinner-border text-dark" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <div id="loading-text" style="margin-top: 3px;">Creating research, this may take a few seconds.</div>
            </div>
        </div>
    </div>

    <div class="mark-as-presented-popup">
        <div class="row">
          <div class="d-flex flex-col col-9">
            <h5 class="create-label mark-as-label-title" style="width: 100%;">
            </h5>
          </div>
          <div class="col-3">
            <button class="close-task-btn close-mark-as-presented" onclick="closeMarkAsPresented()"><i class="fa fa-times"></i></button>
          </div>
        </div>

        <div class="row mt-3">
            <div class="col-12">
                <div class="ms-3">
                    <div class="d-flex flex-column mt-1">
                        <label for="" class="ms-3">Conference Organizer / Host*</label>
                        <input class="research-input" id="host-input" type="text" placeholder="Enter Conference Organizer / Host">
                    </div>

                    <div class="d-flex flex-column mt-3">
                        <label for="" class="ms-3">Date Presented*</label>
                        <input class="ms-2 date-picker-presented" type="date" id="date-picker" min="1997-01-01" max="2030-01-01">
                    </div>   

                    <div class="d-flex flex-column mt-3">
                        <label for="" class="ms-3">Level*</label>
                        <div class="drop-down create-dropdown-level">
                            <div class="wrapper">
                                <div class="selected" id="selected-level-display">Select Level</div>
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
                        <label for="" style="margin-left: 1% !important">Special Order*</label>
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

                    <div class="d-flex flex-column mt-3 ms-2" style="margin-left: 2% !important">
                        <label for="" style="margin-left: 1% !important">Certificates*</label>
                        <div style="display: flex; flex-direction: row">
                            <div style="margin-right: 20px">
                                <label for="file-upload-cert" class="custom-file-upload">
                                    <i class="fa fa-cloud-upload px-1" style="color: #82ceff;"></i> Upload Files
                                </label>
                                <input id="file-upload-cert" type="file" multiple accept=".docx,.pdf,.xls,.xlsx,.png,.jpeg,.jpg,.ppt,.pptx" />
                                <div id="drop-zone-cert">
                                    <p>Drop your files here</p>
                                </div>
                            </div>
                            <div id="preview-cert" class="preview-no-items" style="text-align:center; z-index: 99;">
                                <p class="preview-label">Uploaded files are displayed here</p>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-center items-center mt-3">
                        <button class="d-flex justify-content-center items-center create-research-btn mark-as-presented-btn" onclick="submitMarkAsPresentedForm()">
                            Save
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div id="loading-overlay-mark-as-presented" class="loading-create-task" style="display: none; justify-content: center; align-items: center; border-radius: 25px; z-index: 99; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: white;">
            <div style="display: flex; flex-direction: column; align-items: center;">
                <div class="spinner-border text-dark" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <div id="loading-text-mark-as-presented" style="margin-top: 3px;">Processing, this may take a few seconds.</div>
            </div>
        </div>
    </div>

    <div class="mark-as-published-popup">
        <div class="row">
          <div class="d-flex flex-col col-9">
            <h5 class="create-label mark-as-published-label-title" style="width: 100%;">
            </h5>
          </div>
          <div class="col-3">
            <button class="close-task-btn close-mark-as-published" onclick="closeMarkAsPublished()"><i class="fa fa-times"></i></button>
          </div>
        </div>

        <div class="row mt-3">
            <div class="col-12">
                <div class="ms-3">
                    <div class="d-flex flex-column">
                        <label for="" class="ms-3">Name of Journal*</label>
                        <input class="research-input" id="journal-input" type="text" placeholder="Enter name of journal">
                    </div>

                    <div class="d-flex flex-column mt-3">
                        <label for="" class="ms-3">Published at*</label>
                        <input class="research-input" id="published-at-input" type="text" placeholder="Enter published at">
                    </div>

                    <div class="d-flex flex-column mt-3">
                        <label for="" class="ms-3">Date of Publication*</label>
                        <input class="ms-2 date-picker-published" type="date" id="date-picker" min="1997-01-01" max="2030-01-01">
                    </div>           

                    <div class="d-flex flex-column mt-3">
                        <label for="" class="ms-3">Research Link*</label>
                        <input class="research-input" id="link-input" type="link" placeholder="Enter research link">
                    </div>

                    <div class="d-flex justify-content-center items-center mt-3">
                        <button class="d-flex justify-content-center items-center create-research-btn mark-as-published-btn" onclick="submitMarkAsPublishedForm()">
                            Save
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div id="loading-overlay-mark-as-published" class="loading-create-task" style="display: none; justify-content: center; align-items: center; border-radius: 25px; z-index: 99; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: white;">
            <div style="display: flex; flex-direction: column; align-items: center;">
                <div class="spinner-border text-dark" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <div id="loading-text-mark-as-published" style="margin-top: 3px;">Processing, this may take a few seconds.</div>
            </div>
        </div>
    </div>

    <div class="container-fluid task-list" style="position: relative;">
        <div class="row">
            <div class="col-4">
                <h5 class="my-3 column-name" style="z-index: 100; position: relative;">Title</h5>
            </div>
            <div class="col-3">
                <h5 class="my-3 column-name" style="z-index: 100; position: relative;">Authors</h5>
            </div>
            <div class="col-2">
                <h5 class="my-3 column-name" style="z-index: 100; position: relative;">Created At</h5>
            </div>
            <div class="col-3">
                <h5 class="my-3 column-name" style="z-index: 100; position: relative;">Mark As</h5>
            </div>
        </div>

        <div class="task-container">
            @foreach ($researches as $research)
            <div class="row task-row" onclick="getSelectedResearchRow({{ $research }})">
                <div class="col-4">
                    <h5 class="task-row-content my-2 task-name-text" style="text-align:left; margin-left: 47%">{{ $research->title }}</h5>
                </div>
                <div class="col-3">
                    <h5 class="task-row-content my-2 task-name-text authors-truncate" style="text-align:left; margin-left: 40.5%">{{ $research->authors }}</h5>
                </div>
                <div class="col-2">
                    <h5 class="task-row-content my-2 date-created" style="text-align:left; margin-left: 32%">
                        {{ date('F j, Y', strtotime($research->created_at)) }}
                        <br>
                        {{ date('g:i A', strtotime($research->created_at)) }}
                    </h5>
                </div>
                <div class="col-3 d-flex justify-content-center">
                    <button class="mark-as-btn mark-pres mx-2" onclick="markAsPresented(event, {{ $research }})">
                        Presented
                    </button>
                    <button class="mark-as-btn mark-pubs mx-2" onclick="markAsPublished(event, {{ $research }})">
                        Published
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

        var selectedCertifications = []; // Files selected to be uploaded for certifications
        var selectedCompletedResearchId = 0;

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

        // open mark as presented popup
        function markAsPresented(event, research) {
            event.stopPropagation();
            event.preventDefault();

            const selectedResearchTitle = document.querySelector('.mark-as-label-title');
            selectedResearchTitle.textContent = 'Mark (' + research.title + ') as presented'

            selectedCompletedResearchId = research.id;

            fetch(`/faculty-tasks/researches/is-marked-as-presented?id=${research.id}`)
            .then(response => response.json())
            .then(data => {
                if (data.response) {
                    showNotification('This research has already been marked as presented.', '#fe3232bc');
                    return;
                }

                const popup = document.querySelector('.mark-as-presented-popup');
                popup.style.display = 'block';

                void popup.offsetWidth;
                popup.classList.add('mark-as-presented-animate');

                const overlay = document.querySelector('.overlay');
                overlay.classList.add('blur');
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }

        function closeMarkAsPresented() {
            resetMarkAsPresentedForm();

            selectedCompletedResearchId = 0;

            const popup = document.querySelector('.mark-as-presented-popup');
            popup.style.display = 'none';
            popup.classList.remove('create-task-popup-animate');

            const overlay = document.querySelector('.overlay');
            overlay.classList.remove('blur');
        }

        // open mark as published popup
        function markAsPublished(event, research) {
            event.stopPropagation();
            event.preventDefault();

            const selectedResearchTitle = document.querySelector('.mark-as-published-label-title');
            selectedResearchTitle.textContent = 'Mark (' + research.title + ') as published'

            selectedCompletedResearchId = research.id;

            fetch(`/faculty-tasks/researches/is-marked-as-published?id=${research.id}`)
            .then(response => response.json())
            .then(data => {
                if (data.response) {
                    showNotification('This research has already been marked as published.', '#fe3232bc');
                    return;
                }

                const popup = document.querySelector('.mark-as-published-popup');
                popup.style.display = 'block';

                void popup.offsetWidth;
                popup.classList.add('mark-as-published-animate');

                const overlay = document.querySelector('.overlay');
                overlay.classList.add('blur');
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }

        function closeMarkAsPublished() {
            selectedCompletedResearchId = 0;

            const popup = document.querySelector('.mark-as-published-popup');
            popup.style.display = 'none';
            popup.classList.remove('create-task-popup-animate');

            const overlay = document.querySelector('.overlay');
            overlay.classList.remove('blur');
        }

        function resetForm() {
            document.getElementById('research-title-input').value = '';
            document.getElementById('date-picker').value = '';
            document.getElementById('abstract').value = '';

            const checkboxes = document.querySelectorAll('.item2 input[type="checkbox"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
        }

        function resetMarkAsPresentedForm() {
            document.getElementById('host-input').value = '';
            document.getElementById('selected-level-display').textContent = 'Select Level';

            // Reset the radio buttons
            let levelRadios = document.querySelectorAll('input[name="level"]');
            levelRadios.forEach(radio => {
                radio.checked = false;
            });

            selectedFiles = [];
            updatePreview();

            selectedCertifications = [];
            updatePreviewCertifications();
        }

        function resetMarkAsPublishedForm() {
            document.getElementById('journal-input').value = '';
            document.getElementById('published-at-input').value = '';
            document.getElementById('date-picker').value = '';
            document.getElementById('link-input').value = '';
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

        // Don't close the dropdown when typing in the search input
        let searchInputFaculties2 = document.querySelector('.search-input-faculties');
        searchInputFaculties2.addEventListener('click', (event) => {
            event.stopPropagation();
        });

        // Dropdown for level
        const dropdownLevel = document.querySelector('.create-dropdown-level');
        const listLevel = document.querySelector('.create-list-level');
        const caretLevel = document.querySelector('.caret-level');
        const selectedLevelDisplay = document.getElementById('selected-level-display');

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

        let levelRadios = document.querySelectorAll('input[name="level"]');
        levelRadios.forEach(radio => {
            radio.addEventListener('change', () => {
                selectedLevelDisplay.textContent = radio.parentElement.querySelector('.text').textContent;
            });
        });

        // Dropdown for type of funding
        const dropdownTypeFunding = document.querySelector('.create-dropdown-typefunding');
        const listTypeFunding = document.querySelector('.create-list-typefunding');
        const caretTypeFunding = document.querySelector('.caret-typefunding');
        const selectedTypeFundingDisplay = document.getElementById('selected-typefunding-display');

        dropdownTypeFunding.addEventListener('click', () => {
            listTypeFunding.classList.toggle('show');
            caretTypeFunding.classList.toggle('fa-rotate');
        });

        document.addEventListener('click', (e) => {
            if (!dropdownTypeFunding.contains(e.target)) {
                listTypeFunding.classList.remove('show');
                caretTypeFunding.classList.remove('fa-rotate');
            }
        });

        let itemsTypeFunding = document.querySelectorAll('.typefunding');
        itemsTypeFunding.forEach(item => {
            item.addEventListener('click', (event) => {
                event.stopPropagation();
            });
        });

        let typeFundingRadios = document.querySelectorAll('input[name="typefunding"]');
        typeFundingRadios.forEach(radio => {
            radio.addEventListener('change', () => {
                selectedTypeFundingDisplay.textContent = radio.parentElement.querySelector('.text').textContent;
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

            const category = 'Completed'
            const query = encodeURIComponent(event.target.value);
            fetch(`/faculty-tasks/researches/category/search?category=${category}&query=${query}`)
                .then(response => response.json())
                .then(data => {
                    const researches = Object.values(data.researches)
                    const researchContainer = document.querySelector('.task-container');
                    researchContainer.innerHTML = '';

                    researches.forEach(research => {
                        const row = `
                            <div class="row task-row" onclick="getSelectedResearchRow(${JSON.stringify(research)})">
                                <div class="col-4">
                                    <h5 class="task-row-content my-2 task-name-text" style="text-align:left; margin-left: 47%">${research.title}</h5>
                                </div>
                                <div class="col-3">
                                    <h5 class="task-row-content my-2 task-name-text authors-truncate" style="text-align:left; margin-left: 40.5%">${research.authors}</h5>
                                </div>
                                <div class="col-2">
                                    <h5 class="task-row-content my-2 date-created" style="text-align:left; margin-left: 32%">
                                        ${research.date_created_formatted}
                                        <br>
                                        ${research.date_created_time}
                                    </h5>
                                </div>
                                <div class="col-3 d-flex justify-content-center">
                                    <button class="mark-as-btn mark-pres mx-2">
                                        Presented
                                    </button>
                                    <button class="mark-as-btn mark-pubs mx-2">
                                        Published
                                    </button>
                                </div>
                            </div>
                        `;

                        researchContainer.innerHTML += row;

                        // Add event listener to the buttons
                        const markAsPresentedBtn = document.querySelector('.mark-pres');
                        markAsPresentedBtn.addEventListener('click', (event) => {
                            markAsPresented(event, research);
                        });

                        const markAsPublishedBtn = document.querySelector('.mark-pubs');
                        markAsPublishedBtn.addEventListener('click', (event) => {
                            markAsPublished(event, research);
                        });

                        // Add event listener to the row
                        const taskRow = document.querySelector('.task-row');
                        taskRow.addEventListener('click', () => {
                            getSelectedResearchRow(research);
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

        // Set selected authors label
        const checkboxes = document.querySelectorAll('.item2 input[type="checkbox"]');
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', () => {
                let selectedAuthors = [];
                checkboxes.forEach(checkbox => {
                    if (checkbox.checked) {
                        const authorName = checkbox.parentElement.querySelector('.text').textContent.trim();
                        selectedAuthors.push(authorName);
                    }
                });

                const selectedAuthorsLabel = document.getElementById('selected-authors-label');
                selectedAuthorsLabel.textContent = `Selected authors: (${selectedAuthors.join(', ')})`

                if (selectedAuthors.length === 0) {
                    selectedAuthorsLabel.textContent = '';
                }
            });
        });

        // Add functionality to select all checkbox
        const selectAllCheckbox = document.getElementById('all-checkbox');
        selectAllCheckbox.addEventListener('change', () => {
            const checkboxes = document.querySelectorAll('.item2 input[id="all"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
            });

            let selectedAuthors = [];
            checkboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    const authorName = checkbox.parentElement.querySelector('.text').textContent.trim();
                    selectedAuthors.push(authorName);
                }
            });

            const selectedAuthorsLabel = document.getElementById('selected-authors-label');
            selectedAuthorsLabel.textContent = `Selected authors: (${selectedAuthors.join(', ')}`

            if (selectedAuthors.length === 0) {
                selectedAuthorsLabel.textContent = '';
            }
        });

        // Search functionality for faculties
        const searchInputFaculties = document.querySelector('.search-input-faculties');
        const faculties = document.querySelectorAll('.item2');

        searchInputFaculties.addEventListener('input', (event) => {
            const query = event.target.value.toLowerCase();

            faculties.forEach(faculty => {
                const text = faculty.querySelector('.text').textContent.toLowerCase();
                if (text.includes(query)) {
                    faculty.style.display = 'flex';
                } else {
                    faculty.style.display = 'none';
                }
            });
        });

        /// Special Order: File Upload ///

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

        /// Certifications: File Upload ///

        document.getElementById("file-upload-cert").onchange = function() {
            var files = document.getElementById("file-upload-cert").files;
            if (files.length === 0) {
                // The user clicked the cancel button in the file upload dialog
                console.log('Upload cancelled');
            } 
            else {
                handleFilesCertificate(files);
            }
        };

        var dropZone = document.getElementById("drop-zone-cert");
        dropZone.addEventListener("dragover", function(evt) {
            evt.preventDefault();
        }, false);
        dropZone.addEventListener("drop", function(evt) {
            evt.preventDefault();
            var files = evt.dataTransfer.files;
            handleFilesCertificate(files);
        }, false);

        function handleFilesCertificate(files) {
            for (var i = 0; i < files.length; i++) {
                var file = files[i];
                
                selectedCertifications.push(file);
            }
            updatePreviewCertifications();
        }

        function updatePreviewCertifications() {
            var preview = document.getElementById("preview-cert");
            preview.innerHTML = "";
            for (var i = 0; i < selectedCertifications.length; i++) {
                var file = selectedCertifications[i];
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

            if (selectedCertifications.length <= 0) {
                preview.innerHTML = "Files uploaded are displayed here";
                preview.classList.add('preview-no-items');
            } else {
                preview.classList.remove('preview-no-items');
            }
        }

        document.getElementById("preview-cert").addEventListener("click", function(evt) {
            if (evt.target.classList.contains("remove-file")) {
                var index = parseInt(evt.target.dataset.index);
                selectedCertifications.splice(index, 1);
                updatePreviewCertifications();

                // Reset the value of the file input
                document.getElementById("file-upload-cert").value = null;
            }
        });

        // Form handling

        function validateForm() {
            const title = document.getElementById('research-title-input').value;
            const authors = document.querySelectorAll('.item2 input[id="all"]:checked');
            const typeFunding = document.querySelectorAll('input[name="typefunding"]:checked');
            const dateCompleted = document.getElementById('date-picker').value;
            const abstract = document.getElementById('abstract').value;

            if (title.trim() === '') {
                showNotification('Please enter a title.', '#fe3232bc');
                return false;
            }

            if (authors.length === 0) {
                showNotification('Please select at least one author.', '#fe3232bc');
                return false;
            }

            if (typeFunding.length === 0) {
                showNotification('Please select a type of funding.', '#fe3232bc');
                return false;
            }

            if (dateCompleted.trim() === '') {
                showNotification('Please select a date completed.', '#fe3232bc');
                return false;
            }

            if (abstract.trim() === '') {
                showNotification('Please enter an abstract.', '#fe3232bc');
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
            const dateCompleted = document.getElementById('date-picker').value;
            const abstract = document.getElementById('abstract').value;

            const typeFunding = document.getElementById('selected-typefunding-display').textContent;

            const checkboxes = document.querySelectorAll('.item2 input[id="all"]');
            checkboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    const authorName = checkbox.parentElement.querySelector('.text').textContent.trim();
                    authors.push(authorName);
                }
            });

            const data = new FormData();
            data.append('title', title);
            data.append('authors', authors.join(', '));
            data.append('type_funding', typeFunding);
            data.append('date_completed', dateCompleted);
            data.append('abstract', abstract);
            data.append('type', 'Completed');

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

                    localStorage.setItem('notif_green', 'Research created successfully');
                    // reload the page
                    window.location.reload();

                    /*if (data.newlyAddedResearch) {
                        showNotification('Research created successfully', '#32fe32bc');
                        closeNewTask();
                        resetForm();

                        let tasks = data.allCompletedResearches;
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

        // Validate mark as presented form
        function validateMarkAsPresentedForm() {
            const host = document.getElementById('host-input').value;
            const datePresented = document.querySelector('.date-picker-presented').value;
            const levelRadios = document.querySelectorAll('input[name="level"]');
            const selectedLevel = document.getElementById('selected-level-display').textContent;

            if (host.trim() === '') {
                showNotification('Please enter the conference organizer / host.', '#fe3232bc');
                return false;
            }

            if (selectedLevel === 'Select Level') {
                showNotification('Please select the level.', '#fe3232bc');
                return false;
            }

            if (selectedFiles.length === 0) {
                showNotification('Please upload the special order.', '#fe3232bc');
                return false;
            }

            if (selectedCertifications.length === 0) {
                showNotification('Please upload the certificates.', '#fe3232bc');
                return false;
            }

            return true;
        }

        // Mark as presented form submission
        function submitMarkAsPresentedForm() {
            if (!validateMarkAsPresentedForm()) {
                return;
            }

            const host = document.getElementById('host-input').value;
            const datePresented = document.querySelector('.date-picker-presented').value;
            const levelRadios = document.querySelectorAll('input[name="level"]');
            let level = '';
            levelRadios.forEach(radio => {
                if (radio.checked) {
                    level = radio.parentElement.querySelector('.text').textContent;
                }
            });

            const data = new FormData();
            data.append('completed_research_id', selectedCompletedResearchId);
            data.append('host', host);
            data.append('date_presented', datePresented);
            data.append('level', level);
            
            for (const file of selectedFiles) {
                data.append('special_order_files[]', file);
            }

            for (const file of selectedCertifications) {
                data.append('certifications_files[]', file);
            }

            const markAsPresentedButton = document.querySelector('.mark-as-presented-btn');
            const closeMarkAsPresentedButton = document.querySelector('.close-mark-as-presented');
            
            markAsPresentedButton.disabled = true;
            closeMarkAsPresentedButton.disabled = true;
            markAsPresentedButton.innerHTML = 'Saving...';

            loadingMessage();

            let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            fetch('/faculty-tasks/researches/mark-as-presented', {
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

                    if (data.error) {
                        showNotification(data.error, '#fe3232bc');
                        return;
                    } 

                    localStorage.setItem('notif_green', 'Research marked as presented successfully');
                    window.location.href = '/faculty-tasks/researches/presented';
                })
                .catch((error) => {
                    //console.log(error)
                    showNotification('An error occurred, please try again.', '#fe3232bc');
                })
                .finally(() => {
                    markAsPresentedButton.innerHTML = 'Save';
                    markAsPresentedButton.disabled = false;
                    closeMarkAsPresentedButton.disabled = false;
                });
        }

        // validate mark as published form
        function validateMarkAsPublishedForm() {
            const journal = document.getElementById('journal-input').value;
            const publishedAt = document.getElementById('published-at-input').value;
            const datePublished = document.querySelector('.date-picker-published').value;
            const link = document.getElementById('link-input').value;

            if (journal.trim() === '') {
                showNotification('Please enter the name of the journal.', '#fe3232bc');
                return false;
            }

            if (publishedAt.trim() === '') {
                showNotification('Please enter published at.', '#fe3232bc');
                return false;
            }

            if (datePublished.trim() === '') {
                showNotification('Please select the date of publication.', '#fe3232bc');
                return false;
            }

            if (link.trim() === '') {
                showNotification('Please enter the research link.', '#fe3232bc');
                return false;
            }

            return true;
        }

        // mark as published form submission
        function submitMarkAsPublishedForm() {
            if (!validateMarkAsPublishedForm()) {
                return;
            }

            const journal = document.getElementById('journal-input').value;
            const publishedAt = document.getElementById('published-at-input').value;
            const datePublished = document.querySelector('.date-picker-published').value;
            const link = document.getElementById('link-input').value;

            const data = new FormData();
            data.append('completed_research_id', selectedCompletedResearchId);
            data.append('journal', journal);
            data.append('published_at', publishedAt);
            data.append('date_published', datePublished);
            data.append('link', link);

            const markAsPublishedButton = document.querySelector('.mark-as-published-btn');
            const closeMarkAsPublishedButton = document.querySelector('.close-mark-as-published');
            
            markAsPublishedButton.disabled = true;
            closeMarkAsPublishedButton.disabled = true;
            markAsPublishedButton.innerHTML = 'Saving...';

            loadingMessage();

            let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            fetch('/faculty-tasks/researches/mark-as-published', {
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

                    if (data.error) {
                        showNotification(data.error, '#fe3232bc');
                        return;
                    } 

                    localStorage.setItem('notif_green', 'Research marked as published successfully');
                    window.location.href = '/faculty-tasks/researches/published';
                })
                .catch((error) => {
                    //console.log(error)
                    showNotification('An error occurred, please try again.', '#fe3232bc');
                })
                .finally(() => {
                    markAsPublishedButton.innerHTML = 'Save';
                    markAsPublishedButton.disabled = false;
                    closeMarkAsPublishedButton.disabled = false;
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
                    <div class="col-3">
                        <h5 class="task-row-content my-2 task-name-text" style="text-align:left; margin-left: 43.5%">${task.authors}</h5>
                    </div>
                    <div class="col-2">
                        <h5 class="task-row-content my-2 date-created" style="text-align:left; margin-left: 41%">
                            ${task.date_created_formatted}
                            <br>
                            ${task.date_created_time}
                        </h5>
                    </div>
                    <div class="col-3 d-flex justify-content-center">
                        <button class="mark-as-btn mark-pres mx-2">
                            Presented
                        </button>
                        <button class="mark-as-btn mark-pubs mx-2">
                            Published
                        </button>
                    </div>
                `;

                taskList.appendChild(row);

                // Add the event listener to the row
                row.addEventListener('click', () => {
                    getSelectedResearchRow(task);
                });

                // Add the event listener to the buttons
                const markAsPresentedBtn = row.querySelector('.mark-pres');
                markAsPresentedBtn.addEventListener('click', (event) => {
                    markAsPresented(event, task);
                });

                const markAsPublishedBtn = row.querySelector('.mark-pubs');
                markAsPublishedBtn.addEventListener('click', (event) => {
                    markAsPublished(event, task);
                });
            });

            // Show the table
            taskList.style.display = 'block';
        }

        // On row click
        function getSelectedResearchRow(research) {
            window.location.href = `/faculty-tasks/researches/view?category=Completed&id=${research.id}`;
        }

    </script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script>
@endsection