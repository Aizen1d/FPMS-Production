@extends('layouts.default')

@section('title', 'PUPQC - My Seminars')

@section('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('faculty/css/faculty_tasks_seminars.css') }}">
@endsection

@section('body')
<div class="overlay"></div>
@include('layouts.faculty_navbar')
@include('layouts.faculty_tasks_sidebar')
@include('layouts.notification_side')

<div class="container-fluid margin">
    <div class="row">
        <div class="col-4">
            <h1 class="my-4 title">My Trainings & Seminars</h1>
        </div>
        <div class="col-2 pages">
            {{ $items->links()  }}
        </div>
        <div class="col-6 drop-down-container">
            <input type="text" class="search-input mx-5" id="search-input" placeholder="Search title...">
            <div id="search-results"></div>

            <button class="my-4 create-btn" onclick="createNewTask()">Add Training & Seminar</button>
        </div>
    </div>

    <div class="create-task-popup">
        <div class="row">
          <div class="d-flex flex-col col-9">
            <h5 class="create-label">
                Add Training & Seminar
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
                      <label for="" class="ms-3">Classification*</label>
                      <div class="drop-down create-dropdown-classification">
                          <div class="wrapper">
                              <div class="selected" id="selected-classification">Select Classification</div>
                          </div>
                          <i class="fa fa-caret-down caret-classification"></i>
                  
                          <div class="list create-list-classification">
                              <div class="classification">
                                  <input type="radio" name="classification" id="Seminar/Webinar">
                                  <div class="text">
                                      Seminar/Webinar
                                  </div>
                              </div>
                              <div class="classification">
                                  <input type="radio" name="classification" id="Fora">
                                  <div class="text">
                                      Fora
                                  </div>
                              </div>
                              <div class="classification">
                                  <input type="radio" name="classification" id="Conference">
                                  <div class="text">
                                    Conference
                                  </div>
                              </div>
                              <div class="classification">
                                <input type="radio" name="classification" id="Planning">
                                <div class="text">
                                  Planning
                                </div>
                              </div>
                              <div class="classification">
                                <input type="radio" name="classification" id="Workshop">
                                <div class="text">
                                  Workshop
                                </div>
                              </div>
                              <div class="classification">
                                <input type="radio" name="classification" id="Professional/Continuing Professional Development">
                                <div class="text">
                                  Professional/Continuing Professional Development
                                </div>
                              </div>
                              <div class="classification">
                                <input type="radio" name="classification" id="Short Term Courses">
                                <div class="text">
                                  Short Term Courses
                                </div>
                              </div>
                              <div class="classification">
                                <input type="radio" name="classification" id="Executive/Managerial">
                                <div class="text">
                                  Executive/Managerial
                                </div>
                              </div>
                          </div>
                      </div>
                    </div>       

                    <div class="d-flex flex-column mt-3">
                      <label for="" class="ms-3">Nature*</label>
                      <div class="drop-down create-dropdown-nature">
                          <div class="wrapper">
                              <div class="selected" id="selected-nature">Select Nature</div>
                          </div>
                          <i class="fa fa-caret-down caret-nature"></i>
                  
                          <div class="list create-list-nature">
                              <div class="nature">
                                  <input type="radio" name="nature" id="GAD-Related">
                                  <div class="text">GAD-Related</div>
                              </div>
                              <div class="nature">
                                  <input type="radio" name="nature" id="Inclusivity and Diversity">
                                  <div class="text">Inclusivity and Diversity</div>
                              </div>
                              <div class="nature">
                                  <input type="radio" name="nature" id="Professional">
                                  <div class="text">Professional</div>
                              </div>
                              <div class="nature">
                                <input type="radio" name="nature" id="Skills/Technical">
                                <div class="text">Skills/Technical</div>
                              </div>
                          </div>
                      </div>
                    </div>  
                    
                    <div class="d-flex flex-column mt-3">
                      <label for="" class="ms-3">Type*</label>
                      <div class="drop-down create-dropdown-type">
                          <div class="wrapper">
                              <div class="selected" id="selected-type">Select Type</div>
                          </div>
                          <i class="fa fa-caret-down caret-type"></i>
                  
                          <div class="list create-list-type">
                              <div class="type">
                                  <input type="radio" name="type" id="Executive/Managerial">
                                  <div class="text">Executive/Managerial</div>
                              </div>
                              <div class="type">
                                  <input type="radio" name="type" id="Foundation">
                                  <div class="text">Foundation</div>
                              </div>
                              <div class="type">
                                  <input type="radio" name="type" id="Supervisory">
                                  <div class="text">Supervisory</div>
                              </div>
                              <div class="type">
                                <input type="radio" name="type" id="Technical">
                                <div class="text">Technical</div>
                              </div>
                          </div>
                      </div>
                    </div>   

                    <div class="d-flex flex-column mt-3">
                      <label for="" class="ms-3">Source of Fund*</label>
                      <div class="drop-down create-dropdown-sourcefund">
                          <div class="wrapper">
                              <div class="selected" id="selected-sourcefund">Select source of fund</div>
                          </div>
                          <i class="fa fa-caret-down caret-sourcefund"></i>
                  
                          <div class="list create-list-sourcefund">
                              <div class="sourcefund">
                                  <input type="radio" name="sourcefund" id="University Funded">
                                  <div class="text">University Funded</div>
                              </div>
                              <div class="sourcefund">
                                  <input type="radio" name="sourcefund" id="Self-Funded">
                                  <div class="text">Self-Funded</div>
                              </div>
                              <div class="sourcefund">
                                  <input type="radio" name="sourcefund" id="Externally-Funded">
                                  <div class="text">Externally-Funded</div>
                              </div>
                              <div class="sourcefund">
                                <input type="radio" name="sourcefund" id="Not a Paid Seminar/Training">
                                <div class="text">Not a Paid Seminar/Training</div>
                              </div>
                          </div>
                      </div>
                    </div>

                    <div class="d-flex flex-column mt-3">
                      <label for="" class="ms-3">Budget*</label>
                      <input class="research-input" id="budget-input" type="number" placeholder="Enter budget">
                    </div>

                    <div class="d-flex flex-column mt-3">
                      <label for="" class="ms-3">Organizer*</label>
                      <input class="research-input" id="organizer-input" type="text" placeholder="Enter organizer">
                    </div>

                    <div class="d-flex flex-column mt-3">
                        <label for="" class="ms-3">Level*</label>
                        <div class="drop-down create-dropdown-level">
                            <div class="wrapper">
                                <div class="selected" id="selected-level">Select Level</div>
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
                    
                    <div class="d-flex flex-column mt-3">
                      <label for="" class="ms-3">Venue*</label>
                      <input class="research-input" id="venue-input" type="text" placeholder="Enter venue">
                    </div>

                    <div class="d-flex flex-column mt-3">
                      <label for="" class="research-labels ms-3">From*</label>
                      <input class="ms-2 date-picker-from" type="date" id="date-picker" min="1997-01-01" max="2030-01-01">
                    </div>   
        
                    <div class="d-flex flex-column mt-3">
                      <label for="" class="research-labels ms-3">To*</label>
                      <input class="ms-2 date-picker-to" type="date" id="date-picker" min="1997-01-01" max="2030-01-01">
                    </div>  

                    <div class="d-flex flex-column mt-3">
                      <label for="" class="ms-3">Total No. of Hours*</label>
                      <input class="research-input" id="total-no-hours-input" type="number" placeholder="Enter total no of hours">
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

                    <div class="d-flex flex-column mt-4 ms-2" style="margin-left: 2% !important;">
                      <label for="" style="margin-left: 1% !important">Certificate of Participating/Attendance/Completion *</label>
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

                    <div class="d-flex flex-column mt-4 ms-2" style="margin-left: 2% !important">
                      <label for="" style="margin-left: 1% !important">Compiled Photos in 1 PDF Document</label>
                      <div style="display: flex; flex-direction: row">
                          <div style="margin-right: 20px">
                              <label for="file-upload-compiled" class="custom-file-upload">
                                  <i class="fa fa-cloud-upload px-1" style="color: #82ceff;"></i> Upload Files
                              </label>
                              <input id="file-upload-compiled" type="file" multiple accept=".docx,.pdf,.xls,.xlsx,.png,.jpeg,.jpg,.ppt,.pptx" />
                              <div id="drop-zone-compiled">
                                  <p>Drop your files here</p>
                              </div>
                          </div>
                          <div id="preview-compiled" class="preview-no-items" style="text-align:center; z-index: 99;">
                              <p class="preview-label">Uploaded files are displayed here</p>
                          </div>
                      </div>
                    </div>

                    <div class="d-flex flex-column mt-3">
                      <label for="" class="ms-3">Notes</label>
                      <textarea class="ms-2 task-description-content" id="notes" name="description" rows="4" cols="50" placeholder="Add your personal notes here"></textarea>
                    </div>  

                    <div class="d-flex justify-content-center items-center mt-3">
                        <button class="d-flex justify-content-center items-center create-research-btn" onclick="submitForm()">
                            Create
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div id="loading-overlay" class="loading-create-task" style="display: none; justify-content: center; height: 190vh; align-items: center; border-radius: 25px; z-index: 99; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: white;">
            <div style="display: flex; flex-direction: column; align-items: center;">
                <div class="spinner-border text-dark" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <div id="loading-text" style="margin-top: 3px;">Creating seminar, this may take a few seconds.</div>
            </div>
        </div>
    </div>

    <div class="container-fluid task-list" style="position: relative;">
        <div class="row">
            <div class="col-3">
                <h5 class="my-3 column-name" style="z-index: 100; position: relative;">Title</h5>
            </div>
            <div class="col-3">
                <h5 class="my-3 column-name" style="z-index: 100; position: relative;">Inclusive Date</h5>
            </div>
            <div class="col-2">
                <h5 class="my-3 column-name" style="z-index: 100; position: relative;">No. of Hours</h5>
            </div>
            <div class="col-2">
                <h5 class="my-3 column-name" style="z-index: 100; position: relative;">Notes</h5>
            </div>
            <div class="col-2">
                <h5 class="my-3 column-name" style="z-index: 100; position: relative;">Date Submitted</h5>
            </div>
        </div>

        <div class="task-container">
            @foreach ($items as $item)
            <div class="row task-row" onclick="getSelectedItemRow({{ $item }})">
                <div class="col-3">
                    <h5 class="task-row-content my-2 task-name-text" style="text-align:left; margin-left: 46%">{{ $item->title }}</h5>
                </div>
                <div class="col-3">
                  <h5 class="task-row-content my-2 date-created" style="text-align:left; margin-left: 34%">
                      Fr: {{ date('F j, Y', strtotime($item->from_date)) }}
                      <br>
                      To: {{ date('F j, Y', strtotime($item->to_date)) }}
                  </h5>
                </div>
                <div class="col-2">
                    <h5 class="task-row-content my-2 task-name-text" style="text-align:left; margin-left: 45.5%">{{ $item->total_no_hours }}</h5>
                </div>
                <div class="col-2">
                  <h5 class="task-row-content my-2 task-name-text" style="text-align:left; margin-left: 41%">{{ $item->notes }}</h5>
                </div>
                <div class="col-2">
                    <h5 class="task-row-content my-2 date-created" style="text-align:left; margin-left: 24%">
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

        var selectedCertificationFiles = []; 
        var selectedCompiledFiles = [];

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
          
        }

        function loadingMessage() {
            let div = document.getElementById("loading-text");
            let text = ["Creating seminar, this may take a few seconds.",
                "Creating seminar, this may take a few seconds..",
                "Creating seminar, this may take a few seconds..."
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

        // Dropdown for classification
        const dropdownClassification = document.querySelector('.create-dropdown-classification');
        const listClassification = document.querySelector('.create-list-classification');
        const caretClassification = document.querySelector('.caret-classification');
        const selectedClassificationDisplay = document.querySelector('#selected-classification');

        dropdownClassification.addEventListener('click', () => {
            listClassification.classList.toggle('show');
            caretClassification.classList.toggle('fa-rotate');
        });

        document.addEventListener('click', (e) => {
            if (!dropdownClassification.contains(e.target)) {
                listClassification.classList.remove('show');
                caretClassification.classList.remove('fa-rotate');
            }
        });

        let itemsClassification = document.querySelectorAll('.classification');
        itemsClassification.forEach(item => {
            item.addEventListener('click', (event) => {
                event.stopPropagation();
            });
        });

        let classificationRadioButtons = document.querySelectorAll('.create-list-classification input[type="radio"]');
        classificationRadioButtons.forEach(radio => {
            radio.addEventListener('change', () => {
                selectedClassificationDisplay.textContent = radio.nextElementSibling.textContent;
            });
        });

        // Dropdown for nature
        const dropdownNature = document.querySelector('.create-dropdown-nature');
        const listNature = document.querySelector('.create-list-nature');
        const caretNature = document.querySelector('.caret-nature');
        const selectedNatureDisplay = document.querySelector('#selected-nature');

        dropdownNature.addEventListener('click', () => {
            listNature.classList.toggle('show');
            caretNature.classList.toggle('fa-rotate');
        });

        document.addEventListener('click', (e) => {
            if (!dropdownNature.contains(e.target)) {
                listNature.classList.remove('show');
                caretNature.classList.remove('fa-rotate');
            }
        });

        let itemsNature = document.querySelectorAll('.nature');
        itemsNature.forEach(item => {
            item.addEventListener('click', (event) => {
                event.stopPropagation();
            });
        });

        let natureRadioButtons = document.querySelectorAll('.create-list-nature input[type="radio"]');
        natureRadioButtons.forEach(radio => {
            radio.addEventListener('change', () => {
                selectedNatureDisplay.textContent = radio.nextElementSibling.textContent;
            });
        });

        // Dropdown for type
        const dropdownType = document.querySelector('.create-dropdown-type');
        const listType = document.querySelector('.create-list-type');
        const caretType = document.querySelector('.caret-type');
        const selectedTypeDisplay = document.querySelector('#selected-type');

        dropdownType.addEventListener('click', () => {
            listType.classList.toggle('show');
            caretType.classList.toggle('fa-rotate');
        });

        document.addEventListener('click', (e) => {
            if (!dropdownType.contains(e.target)) {
                listType.classList.remove('show');
                caretType.classList.remove('fa-rotate');
            }
        });

        let itemsType = document.querySelectorAll('.type');
        itemsType.forEach(item => {
            item.addEventListener('click', (event) => {
                event.stopPropagation();
            });
        });

        let typeRadioButtons = document.querySelectorAll('.create-list-type input[type="radio"]');
        typeRadioButtons.forEach(radio => {
            radio.addEventListener('change', () => {
                selectedTypeDisplay.textContent = radio.nextElementSibling.textContent;
            });
        });

        // Dropdown for source of fund
        const dropdownSourceFund = document.querySelector('.create-dropdown-sourcefund');
        const listSourceFund = document.querySelector('.create-list-sourcefund');
        const caretSourceFund = document.querySelector('.caret-sourcefund');
        const selectedSourceFundDisplay = document.querySelector('#selected-sourcefund');

        dropdownSourceFund.addEventListener('click', () => {
            listSourceFund.classList.toggle('show');
            caretSourceFund.classList.toggle('fa-rotate');
        });

        document.addEventListener('click', (e) => {
            if (!dropdownSourceFund.contains(e.target)) {
                listSourceFund.classList.remove('show');
                caretSourceFund.classList.remove('fa-rotate');
            }
        });

        let itemsSourceFund = document.querySelectorAll('.sourcefund');
        itemsSourceFund.forEach(item => {
            item.addEventListener('click', (event) => {
                event.stopPropagation();
            });
        });

        let sourceFundRadioButtons = document.querySelectorAll('.create-list-sourcefund input[type="radio"]');
        sourceFundRadioButtons.forEach(radio => {
            radio.addEventListener('change', () => {
                selectedSourceFundDisplay.textContent = radio.nextElementSibling.textContent;
            });
        });

        // Dropdown for level
        const dropdownLevel = document.querySelector('.create-dropdown-level');
        const listLevel = document.querySelector('.create-list-level');
        const caretLevel = document.querySelector('.caret-level');
        const selectedLevelDisplay = document.querySelector('#selected-level');

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

        let levelRadioButtons = document.querySelectorAll('.create-list-level input[type="radio"]');
        levelRadioButtons.forEach(radio => {
            radio.addEventListener('change', () => {
                selectedLevelDisplay.textContent = radio.nextElementSibling.textContent;
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

        // File Upload for Certificate of Participation
        document.getElementById("file-upload-cert").onchange = function() {
            var files = document.getElementById("file-upload-cert").files;
            if (files.length === 0) {
                // The user clicked the cancel button in the file upload dialog
                console.log('Upload cancelled');
            } 
            else {
                handleCertFiles(files);
            }
        };

        var dropZoneCert = document.getElementById("drop-zone-cert");
        dropZoneCert.addEventListener("dragover", function(evt) {
            evt.preventDefault();
        }, false);
        dropZoneCert.addEventListener("drop", function(evt) {
            evt.preventDefault();
            var files = evt.dataTransfer.files;
            handleCertFiles(files);
        }, false);

        function handleCertFiles(files) {
            for (var i = 0; i < files.length; i++) {
                var file = files[i];
                
                selectedCertificationFiles.push(file);
            }
            updateCertPreview();
        }

        function updateCertPreview() {
            var preview = document.getElementById("preview-cert");
            preview.innerHTML = "";
            for (var i = 0; i < selectedCertificationFiles.length; i++) {
                var file = selectedCertificationFiles[i];
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

            if (selectedCertificationFiles.length <= 0) {
                preview.innerHTML = "Files uploaded are displayed here";
                preview.classList.add('preview-no-items');
            } else {
                preview.classList.remove('preview-no-items');
            }
        }

        document.getElementById("preview-cert").addEventListener("click", function(evt) {
            if (evt.target.classList.contains("remove-file")) {
                var index = parseInt(evt.target.dataset.index);
                selectedCertificationFiles.splice(index, 1);
                updateCertPreview();

                // Reset the value of the file input
                document.getElementById("file-upload-cert").value = null;
            }
        });

        // File Upload for Compiled Photos
        document.getElementById("file-upload-compiled").onchange = function() {
            var files = document.getElementById("file-upload-compiled").files;
            if (files.length === 0) {
                // The user clicked the cancel button in the file upload dialog
                console.log('Upload cancelled');
            } 
            else {
                handleCompiledFiles(files);
            }
        };

        var dropZoneCompiled = document.getElementById("drop-zone-compiled");
        dropZoneCompiled.addEventListener("dragover", function(evt) {
            evt.preventDefault();
        }, false);
        dropZoneCompiled.addEventListener("drop", function(evt) {
            evt.preventDefault();
            var files = evt.dataTransfer.files;
            handleCompiledFiles(files);
        }, false);

        function handleCompiledFiles(files) {
            for (var i = 0; i < files.length; i++) {
                var file = files[i];
                
                selectedCompiledFiles.push(file);
            }
            updateCompiledPreview();
        }

        function updateCompiledPreview() {
            var preview = document.getElementById("preview-compiled");
            preview.innerHTML = "";
            for (var i = 0; i < selectedCompiledFiles.length; i++) {
                var file = selectedCompiledFiles[i];
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

            if (selectedCompiledFiles.length <= 0) {
                preview.innerHTML = "Files uploaded are displayed here";
                preview.classList.add('preview-no-items');
            } else {
                preview.classList.remove('preview-no-items');
            }
        }

        document.getElementById("preview-compiled").addEventListener("click", function(evt) {
            if (evt.target.classList.contains("remove-file")) {
                var index = parseInt(evt.target.dataset.index);
                selectedCompiledFiles.splice(index, 1);
                updateCompiledPreview();

                // Reset the value of the file input
                document.getElementById("file-upload-compiled").value = null;
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
            fetch(`/faculty-tasks/seminars/search?query=${query}`)
                .then(response => response.json())
                .then(data => {
                    const items = Object.values(data.items)
                    const researchContainer = document.querySelector('.task-container');
                    researchContainer.innerHTML = '';

                    items.forEach(research => {
                        const row = `
                            <div class="row task-row">
                                <div class="col-3">
                                    <h5 class="task-row-content my-2 task-name-text" style="text-align:left; margin-left: 46%">${research.title}</h5>
                                </div>
                                <div class="col-3">
                                    <h5 class="task-row-content my-2 date-created" style="text-align:left; margin-left: 34%">
                                        Fr: ${research.from_date}
                                        <br>
                                        To: ${research.to_date}
                                    </h5>
                                </div>
                                <div class="col-2">
                                    <h5 class="task-row-content my-2 task-name-text" style="text-align:left; margin-left: 45.5%">${research.total_no_hours}</h5>
                                </div>
                                <div class="col-2">
                                    <h5 class="task-row-content my-2 task-name-text" style="text-align:left; margin-left: 41%">${research.notes}</h5>
                                </div>
                                <div class="col-2">
                                    <h5 class="task-row-content my-2 date-created" style="text-align:left; margin-left: 24%">
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
                                getSelectedItemRow(research);
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
            const title = document.getElementById('title-input').value;

            const classification = document.querySelectorAll('input[name="classification"]:checked');
            const nature = document.querySelectorAll('input[name="nature"]:checked');
            const type = document.querySelectorAll('input[name="type"]:checked');
            const sourceFund = document.querySelectorAll('input[name="sourcefund"]:checked');
            const budget = document.getElementById('budget-input').value;
            const organizer = document.getElementById('organizer-input').value;
            const level = document.querySelectorAll('input[name="level"]:checked');
            const venue = document.getElementById('venue-input').value;
            const fromDate = document.querySelector('.date-picker-from').value;
            const toDate = document.querySelector('.date-picker-to').value;
            const totalNoHours = document.getElementById('total-no-hours-input').value;
            const notes = document.getElementById('notes').value;

            if (title.trim() === '') {
                showNotification('Please enter a title.', '#fe3232bc');
                return false;
            }

            if (classification.length <= 0) {
                showNotification('Please select a classification.', '#fe3232bc');
                return false;
            }

            if (nature.length <= 0) {
                showNotification('Please select a nature.', '#fe3232bc');
                return false;
            }

            if (type.length <= 0) {
                showNotification('Please select a type.', '#fe3232bc');
                return false;
            }

            if (sourceFund.length <= 0) {
                showNotification('Please select a source of fund.', '#fe3232bc');
                return false;
            }

            if (budget.trim() === '') {
                showNotification('Please enter a budget.', '#fe3232bc');
                return false;
            }

            if (organizer.trim() === '') {
                showNotification('Please enter an organizer.', '#fe3232bc');
                return false;
            }

            if (level.length <= 0) {
                showNotification('Please select a level.', '#fe3232bc');
                return false;
            }

            if (venue.trim() === '') {
                showNotification('Please enter a venue.', '#fe3232bc');
                return false;
            }

            if (fromDate.trim() === '') {
                showNotification('Please enter a from date.', '#fe3232bc');
                return false;
            }

            if (toDate.trim() === '') {
                showNotification('Please enter a to date.', '#fe3232bc');
                return false;
            }

            if (totalNoHours.trim() === '') {
                showNotification('Please enter the total number of hours.', '#fe3232bc');
                return false;
            }

            if (selectedFiles.length <= 0) {
                showNotification('Please upload special order file.', '#fe3232bc');
                return false;
            }

            if (selectedCertificationFiles.length <= 0) {
                showNotification('Please upload certificate of participation file.', '#fe3232bc');
                return false;
            }

            if (selectedCompiledFiles.length <= 0) {
                showNotification('Please upload compiled photos file.', '#fe3232bc');
                return false;
            }

            if (notes.trim() === '') {
                showNotification('Please enter notes.', '#fe3232bc');
                return false;
            }

            return true;
        }

        function submitForm() {
            if (!validateForm()) {
                return;
            }
            
            const title = document.getElementById('title-input').value;
            const classification = document.getElementById('selected-classification').textContent;
            const nature = document.getElementById('selected-nature').textContent;
            const type = document.getElementById('selected-type').textContent;
            const sourceFund = document.getElementById('selected-sourcefund').textContent;
            const budget = document.getElementById('budget-input').value;
            const organizer = document.getElementById('organizer-input').value;
            const level = document.getElementById('selected-level').textContent;
            const venue = document.getElementById('venue-input').value;
            const fromDate = document.querySelector('.date-picker-from').value;
            const toDate = document.querySelector('.date-picker-to').value;
            const totalNoHours = document.getElementById('total-no-hours-input').value;
            const notes = document.getElementById('notes').value;

            const data = new FormData();
            data.append('title', title.trim());
            data.append('classification', classification.trim());
            data.append('nature', nature.trim());
            data.append('type', type.trim());
            data.append('source_of_fund', sourceFund.trim());
            data.append('budget', budget.trim());
            data.append('organizer', organizer.trim());
            data.append('level', level.trim());
            data.append('venue', venue.trim());
            data.append('from_date', fromDate.trim());
            data.append('to_date', toDate.trim());
            data.append('total_no_hours', totalNoHours.trim());
            data.append('notes', notes.trim());

            for (const file of selectedFiles) {
                data.append('special_order_files[]', file);
            }

            for (const file of selectedCertificationFiles) {
                data.append('certifications_files[]', file);
            }

            for (const file of selectedCompiledFiles) {
                data.append('compiled_files[]', file);
            }

            const loadingOverlay = document.getElementById('loading-overlay');
            const loadingText = document.getElementById('loading-text');
            const createResearchBtn = document.querySelector('.create-research-btn');

            loadingOverlay.style.display = 'flex';
            createResearchBtn.disabled = true;
            loadingMessage();

            let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            fetch('/faculty-tasks/seminars/create', {
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
                    localStorage.setItem('notif_green', 'Seminar created successfully');
                    window.location.reload();
                    /*if (data.newlyAddedResearch) {
                        showNotification('Research created successfully', '#32fe32bc');
                        closeNewTask();
                        resetForm();

                        let tasks = data.allPresentedResearches;
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
                    getSelectedItemRow(task);
                });
            });

            // Show the table
            taskList.style.display = 'block';
        }

        // On row click
        function getSelectedItemRow(research) {
            window.location.href = `/faculty-tasks/seminars/view?id=${research.id}`;
        }

    </script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script>
@endsection