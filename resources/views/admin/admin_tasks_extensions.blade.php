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
                Add Extension Program, Project and Activity (Ongoing and Completed)
                <br>
                <span style="font-size: 10px">
                    Please fill in the necessary details. No abbreviations. All inputs with symbol (*) are required. 
                </span>
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
                        <label for="" class="ms-3" style="font-size: 12px">Title of Extension Program</label>
                        <input class="research-input" id="title-program-input" type="text">
                    </div>

                    <div class="d-flex flex-column mt-3">
                        <label for="" class="ms-3" style="font-size: 12px">Title of Extension Project</label>
                        <input class="research-input" id="title-project-input" type="text">
                    </div>
                    
                    <div class="d-flex flex-column mt-3">
                        <label for="" class="ms-3" style="font-size: 12px">Title of Extension Activity</label>
                        <input class="research-input" id="title-activity-input" type="text">
                    </div>

                    <div class="d-flex flex-column mt-3">
                        <label for="" class="ms-3" style="font-size: 12px">Place/Venue</label>
                        <input class="research-input" id="place-input" type="text">
                    </div>
                    
                    <div class="d-flex flex-column mt-3">
                        <label for="" class="ms-3" style="font-size: 12px">Level*</label>
                        <div class="drop-down create-dropdown-level">
                            <div class="wrapper">
                                <div class="selected" id="selected-level-display">Select Level</div>
                            </div>
                            <i class="fa fa-caret-down caret-level"></i>
                    
                            <div class="list create-list-level">
                                <div class="item2">
                                    <input type="radio" name="level" id="International">
                                    <div class="text">
                                        International
                                    </div>
                                </div>
                                <div class="item2">
                                    <input type="radio" name="level" id="National">
                                    <div class="text">
                                        National
                                    </div>
                                </div>
                                <div class="item2">
                                    <input type="radio" name="level" id="Regional">
                                    <div class="text">
                                        Regional
                                    </div>
                                </div>
                                <div class="item2">
                                    <input type="radio" name="level" id="Provincial/City/Municipal">
                                    <div class="text">
                                        Provincial/City/Municipal
                                    </div>
                                </div>
                                <div class="item2">
                                    <input type="radio" name="level" id="Local-PUP">
                                    <div class="text">
                                        Local-PUP
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-column mt-3">
                        <label for="" class="ms-3" style="font-size: 12px">Classification*</label>
                        <label for="" class="ms-3" style="font-size: 10px; margin-top: -2px">
                            Livelihood Development; Health; Educational and Cultural Exchange; Technology Transfer; Knowledge Transfer; Local Governance; if others, please specify
                        </label>
                        <input class="research-input" id="classification-input" type="text">
                    </div>

                    <div class="d-flex flex-column mt-3">
                        <label for="" class="ms-3" style="font-size: 12px">Type*</label>
                        <div class="drop-down-type create-dropdown-type">
                            <div class="wrapper">
                                <div class="selected" id="selected-type-display">Select Type</div>
                            </div>
                            <i class="fa fa-caret-down caret-type"></i>
                    
                            <div class="type create-list-type">
                                <div class="item-type">
                                    <input type="radio" name="type" id="Training">
                                    <div class="text">
                                        Training
                                    </div>
                                </div>
                                <div class="item-type">
                                    <input type="radio" name="type" id="Technical/Advisory Services">
                                    <div class="text">
                                        Technical/Advisory Services
                                    </div>
                                </div>
                                <div class="item-type">
                                    <input type="radio" name="type" id="Outreach">
                                    <div class="text">
                                        Outreach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-column mt-3">
                        <label for="" class="ms-3" style="font-size: 12px">Keywords (at least five (5) keywords)</label>
                        <input class="research-input" id="keywords-input" type="text">
                    </div>

                    <div class="d-flex flex-column mt-3">
                        <label for="" class="ms-3" style="font-size: 12px">Type of Funding*</label>
                        <div class="drop-down-type create-dropdown-typefunding">
                            <div class="wrapper">
                                <div class="selected" id="selected-typefunding-display">Select Type of Funding</div>
                            </div>
                            <i class="fa fa-caret-down caret-typefunding"></i>
                    
                            <div class="type create-list-typefunding">
                                <div class="item-typefunding">
                                    <input type="radio" name="typefunding" id="University Funded">
                                    <div class="text">
                                        University Funded
                                    </div>
                                </div>
                                <div class="item-typefunding">
                                    <input type="radio" name="typefunding" id="Self Funded">
                                    <div class="text">
                                        Self Funded
                                    </div>
                                </div>
                                <div class="item-typefunding">
                                    <input type="radio" name="typefunding" id="Externally Funded">
                                    <div class="text">
                                        Externally Funded
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-column mt-3">
                        <label for="" class="ms-3" style="font-size: 12px">Funding Agency</label>
                        <input class="research-input" id="funding-agency-input" type="text">
                    </div>

                    <div class="d-flex flex-column mt-3">
                        <label for="" class="ms-3" style="font-size: 12px">Amount of Funding (PHP)</label>
                        <input class="research-input" id="amount-funding-input" type="number" min="0">
                    </div>
                    
                    <div class="d-flex flex-column mt-3">
                        <label for="" class="ms-3" style="font-size: 12px">Total No. of Hours</label>
                        <input class="research-input" id="total-hours-input" type="number" min="0">
                    </div>

                    <div class="d-flex flex-column mt-3">
                        <label for="" class="ms-3" style="font-size: 12px">No. of Trainees/Beneficiaries</label>
                        <input class="research-input" id="number-of-trainees-input" type="number" min="0">
                    </div>

                    <div class="d-flex flex-column mt-3">
                        <label for="" class="ms-3" style="font-size: 12px">Classification of Trainees/Beneficaries*</label>
                        <label for="" class="ms-3" style="font-size: 10px; margin-top: -2px">
                            Faculty; Administrative Employee; Students; Community; If others, please specify.
                        </label>
                        <input class="research-input" id="classification-of-trainees-input" type="text">
                    </div>

                    <div class="d-flex flex-column mt-3">
                        <label for="" class="ms-3" style="font-size: 12px">Nature of Involvement*</label>
                        <input class="research-input" id="nature-input" type="text">
                    </div>

                    <div class="d-flex flex-column mt-3">
                        <label for="" class="ms-3" style="font-size: 12px">Status*</label>
                        <div class="drop-down-type create-dropdown-status">
                            <div class="wrapper">
                                <div class="selected" id="selected-status-display">Select Status</div>
                            </div>
                            <i class="fa fa-caret-down caret-status"></i>
                    
                            <div class="type create-list-status">
                                <div class="item-status">
                                    <input type="radio" name="status" id="Ongoing">
                                    <div class="text">
                                        Ongoing
                                    </div>
                                </div>
                                <div class="item-status">
                                    <input type="radio" name="status" id="Completed">
                                    <div class="text">
                                        Completed
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex flex-column mt-3">
                        <label for="" class="ms-3" style="font-size: 12px;">From</label>
                        <input class="ms-2" type="date" id="date-picker-from" min="1997-01-01" max="2030-01-01">
                    </div>

                    <div class="d-flex flex-column mt-3">
                        <label for="" class="ms-3" style="font-size: 12px;">To</label>
                        <input class="ms-2" type="date" id="date-picker-to" min="1997-01-01" max="2030-01-01">
                    </div>

                    <div class="d-flex justify-content-center items-center mt-4 mb-3">
                        <button class="d-flex justify-content-center items-center create-research-btn" onclick="submitForm()">
                            Save
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
            <div class="col-4">
                <h5 class="my-3 column-name" style="z-index: 100; position: relative;">Title</h5>
            </div>
            <div class="col-3">
                <h5 class="my-3 column-name" style="z-index: 100; position: relative;">Type of Extension</h5>
            </div>
            <div class="col-2">
                <h5 class="my-3 column-name" style="z-index: 100; position: relative;">Level</h5>
            </div>
            <div class="col-3">
                <h5 class="my-3 column-name" style="z-index: 100; position: relative;">From Date & To Date</h5>
            </div>
        </div>

        <div class="task-container">
            @foreach ($items as $item)
            <div class="row task-row" onclick="getSelectedItemRow({{ $item }})">
                <div class="col-4">
                    <h5 class="task-row-content my-2 task-name-text" style="text-align:left; margin-left: 47%">
                        {{ $item->title_of_extension_program ? $item->title_of_extension_program : ($item->title_of_extension_project ? $item->title_of_extension_project : $item->title_of_extension_activity) }}
                    </h5>
                </div>
                <div class="col-3">
                    <h5 class="task-row-content my-2 task-name-text" style="text-align:left; margin-left: 30%; text-transform: capitalize;">
                        {{ $item->type_of_extension }}
                    </h5>
                </div>
                <div class="col-2">
                    <h5 class="task-row-content my-2 task-name-text" style="text-align:left; margin-left: 41%; text-transform: capitalize;">
                        {{ $item->level }}
                    </h5>
                </div>
                <div class="col-3">
                    <h5 class="task-row-content my-2 date-created" style="text-align:left; margin-left: 27%">
                        Fr: {{ date('F j, Y', strtotime($item->from_date)) }}
                        <br>
                        To: {{ date('F j, Y', strtotime($item->to_date)) }}
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
        
        // Dropdown for type
        const dropdownType = document.querySelector('.create-dropdown-type');
        const listType = document.querySelector('.create-list-type');
        const caretType = document.querySelector('.caret-type');
        const selectedTypeDisplay = document.getElementById('selected-type-display');

        dropdownType.addEventListener('click', () => {
            listType.classList.toggle('show-type');
            caretType.classList.toggle('fa-rotate');
        });

        document.addEventListener('click', (e) => {
            if (!dropdownType.contains(e.target)) {
                listType.classList.remove('show-type');
                caretType.classList.remove('fa-rotate');
            }
        });

        let itemsType = document.querySelectorAll('.item-type');
        itemsType.forEach(item => {
            item.addEventListener('click', (event) => {
                event.stopPropagation();
            });
        });

        let typeRadios = document.querySelectorAll('input[name="type"]');
        typeRadios.forEach(radio => {
            radio.addEventListener('change', () => {
                selectedTypeDisplay.textContent = radio.parentElement.querySelector('.text').textContent;
            });
        });
        
        // Dropdown for type of funding
        const dropdownTypeFunding = document.querySelector('.create-dropdown-typefunding');
        const listTypeFunding = document.querySelector('.create-list-typefunding');
        const caretTypeFunding = document.querySelector('.caret-typefunding');
        const selectedTypeFundingDisplay = document.getElementById('selected-typefunding-display');

        dropdownTypeFunding.addEventListener('click', () => {
            listTypeFunding.classList.toggle('show-typefunding');
            caretTypeFunding.classList.toggle('fa-rotate');
        });

        document.addEventListener('click', (e) => {
            if (!dropdownTypeFunding.contains(e.target)) {
                listTypeFunding.classList.remove('show-typefunding');
                caretTypeFunding.classList.remove('fa-rotate');
            }
        });

        let itemsTypeFunding = document.querySelectorAll('.item-typefunding');
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

        // Dropdown for status
        const dropdownStatus = document.querySelector('.create-dropdown-status');
        const listStatus = document.querySelector('.create-list-status');
        const caretStatus = document.querySelector('.caret-status');
        const selectedStatusDisplay = document.getElementById('selected-status-display');

        dropdownStatus.addEventListener('click', () => {
            listStatus.classList.toggle('show-status');
            caretStatus.classList.toggle('fa-rotate');
        });

        document.addEventListener('click', (e) => {
            if (!dropdownStatus.contains(e.target)) {
                listStatus.classList.remove('show-status');
                caretStatus.classList.remove('fa-rotate');
            }
        });

        let itemsStatus = document.querySelectorAll('.item-status');
        itemsStatus.forEach(item => {
            item.addEventListener('click', (event) => {
                event.stopPropagation();
            });
        });

        let statusRadios = document.querySelectorAll('input[name="status"]');
        statusRadios.forEach(radio => {
            radio.addEventListener('change', () => {
                selectedStatusDisplay.textContent = radio.parentElement.querySelector('.text').textContent;
            });
        });

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

                    console.log(items);

                    items.forEach(item => {
                        const row = `
                            <div class="row task-row">
                                <div class="col-4">
                                    <h5 class="task-row-content my-2 task-name-text" style="text-align:left; margin-left: 47%">
                                        ${item.title_program ? item.title_program : (item.title_project ? item.title_project : item.title_activity)}
                                    </h5>
                                </div>
                                <div class="col-3">
                                    <h5 class="task-row-content my-2 task-name-text" style="text-align:left; margin-left: 30%; text-transform: capitalize;">${item.type_of_extension}</h5>
                                </div>
                                <div class="col-2">
                                    <h5 class="task-row-content my-2 task-name-text" style="text-align:left; margin-left: 41%; text-transform: capitalize;">${item.level}</h5>
                                </div>
                                <div class="col-3">
                                    <h5 class="task-row-content my-2 date-created" style="text-align:left; margin-left: 27%">
                                        Fr: ${item.from_date}
                                        <br>
                                        To: ${item.to_date}
                                    </h5>
                                </div>
                            </div>
                        `;

                        taskContainer.innerHTML += row;

                        // Add the event listener to all the rows
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

        // Add event listener to titleProgram, Project and Activity
        const titleProgramInput = document.getElementById('title-program-input');
        const titleProjectInput = document.getElementById('title-project-input');
        const titleActivityInput = document.getElementById('title-activity-input');

        // Function to disable other inputs
        function disableOtherInputs(changedInput, input1, input2) {
            if (changedInput.value !== '') {
                input1.disabled = true;
                input2.disabled = true;
            } else {
                input1.disabled = false;
                input2.disabled = false;
            }
        }

        // Add event listeners
        titleProgramInput.addEventListener('input', () => disableOtherInputs(titleProgramInput, titleProjectInput, titleActivityInput));
        titleProjectInput.addEventListener('input', () => disableOtherInputs(titleProjectInput, titleProgramInput, titleActivityInput));
        titleActivityInput.addEventListener('input', () => disableOtherInputs(titleActivityInput, titleProgramInput, titleProjectInput));

        // Form handling

        function validateForm() {
            const titleProgram = document.getElementById('title-program-input').value;
            const titleProject = document.getElementById('title-project-input').value;
            const titleActivity = document.getElementById('title-activity-input').value;
            const level = document.querySelector('input[name="level"]:checked');
            const classification = document.getElementById('classification-input').value;
            const type = document.querySelector('input[name="type"]:checked');

            const typeFunding = document.querySelector('input[name="typefunding"]:checked');
            const classificationOfTrainees = document.getElementById('classification-of-trainees-input').value;
            const nature = document.getElementById('nature-input').value;
            const status = document.querySelector('input[name="status"]:checked');

            // Check if any of titleProgram, titleProject, titleActivity is empty
            if (titleProgram.trim() === '' && titleProject.trim() === '' && titleActivity.trim() === '') {
                showNotification('Please enter the title of the extension program, project or activity.', '#fe3232bc');
                return false;
            }

            if (!level) {
                showNotification('Please select the level.', '#fe3232bc');
                return false;
            }

            if (classification.trim() === '') {
                showNotification('Please enter the classification.', '#fe3232bc');
                return false;
            }

            if (!type) {
                showNotification('Please select the type.', '#fe3232bc');
                return false;
            }

            if (typeFunding === null) {
                showNotification('Please select the type of funding.', '#fe3232bc');
                return false;
            }

            if (classificationOfTrainees.trim() === '') {
                showNotification('Please enter the classification of trainees.', '#fe3232bc');
                return false;
            }

            if (nature.trim() === '') {
                showNotification('Please enter the nature of involvement.', '#fe3232bc');
                return false;
            }

            if (!status) {
                showNotification('Please select the status.', '#fe3232bc');
                return false;
            }

            return true;
        }

        function submitForm() {
            if (!validateForm()) {
                return;
            }
            
            const titleProgram = document.getElementById('title-program-input').value;
            const titleProject = document.getElementById('title-project-input').value;
            const titleActivity = document.getElementById('title-activity-input').value;
            const place = document.getElementById('place-input').value;
            const level = document.querySelector('input[name="level"]:checked').id;
            const classification = document.getElementById('classification-input').value;
            const type = document.querySelector('input[name="type"]:checked').id;
            const keywords = document.getElementById('keywords-input').value;
            const typeFunding = document.querySelector('input[name="typefunding"]:checked').id;
            const fundingAgency = document.getElementById('funding-agency-input').value;
            const amountFunding = document.getElementById('amount-funding-input').value;
            const totalHours = document.getElementById('total-hours-input').value;
            const numberOfTrainees = document.getElementById('number-of-trainees-input').value;
            const classificationOfTrainees = document.getElementById('classification-of-trainees-input').value;
            const nature = document.getElementById('nature-input').value;
            const status = document.querySelector('input[name="status"]:checked').id;
            const dateFrom = document.getElementById('date-picker-from').value;
            const dateTo = document.getElementById('date-picker-to').value;

            const data = new FormData();
            data.append('titleProgram', titleProgram.trim());
            data.append('titleProject', titleProject.trim());
            data.append('titleActivity', titleActivity.trim());
            data.append('place', place.trim());
            data.append('level', level.trim());
            data.append('classification', classification.trim());
            data.append('type', type.trim());
            data.append('keywords', keywords.trim());
            data.append('typeFunding', typeFunding.trim());
            data.append('fundingAgency', fundingAgency.trim());
            data.append('amountFunding', amountFunding.trim());
            data.append('totalHours', totalHours.trim());
            data.append('numberOfTrainees', numberOfTrainees.trim());
            data.append('classificationOfTrainees', classificationOfTrainees.trim());
            data.append('nature', nature.trim());
            data.append('status', status.trim());
            data.append('dateFrom', dateFrom.trim());
            data.append('dateTo', dateTo.trim());

            if (titleProgram.trim() !== '') {
                data.append('extensionType', 'program');
            }
            else if (titleProject.trim() !== '') {
                data.append('extensionType', 'project');
            }
            else if (titleActivity.trim() !== '') {
                data.append('extensionType', 'activity');
            }
                        
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
                    localStorage.setItem('notif_green', 'Extension created successfully')
                    location.reload();
                    /*if (data.newlyAddedExtension) {
                        showNotification('Extension created successfully', '#32fe32bc');
                        closeNewTask();
                        resetForm();

                        let tasks = data.allExtensions;
                        let newlyAdded = data.newlyAddedExtension;
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