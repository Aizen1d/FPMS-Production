@extends('layouts.default')

@section('title', 'PUPQC - View Seminar')

@section('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('faculty/css/faculty_tasks_seminars_view.css') }}">
@endsection

@section('body')
<div class="overlay"></div>
@include('layouts.faculty_navbar')
@include('layouts.faculty_selected_seminar_sidebar') 
@include('layouts.notification_side')

<div class="container-fluid margin">
    <div class="row">
        <div class="col-6">
            <h3 class="my-4 title">View Training & Seminar</h3>
        </div>
        <div class="col-6 drop-down-container">
            <button class="my-4 create-btn delete-task-btn" onclick="deleteResearch()">Delete</button>
            <button class="my-4 create-btn edit-task-btn" onclick="editResearch()">Enable Edit</button>
            <button class="my-4 create-btn save-task-btn" onclick="submitForm()">Save</button>
        </div>
    </div>

    <div class="container-fluid task-list" style="position: relative;">
      <div class="row mt-3">
        <div class="col-12">
          <div class="ms-3 mt-4">
            <div class="d-flex flex-column">
              <label for="" class="research-labels ms-3">Title*</label>
              <input class="research-input" id="title-input" type="text" placeholder="Enter title" value="{{ $item->title }}">
            </div>

            <div class="d-flex flex-column mt-4">
              <label for="" class="research-labels ms-3">Classification*</label>
              <div class="drop-down create-dropdown-classification ms-3" style="margin-left: 1.5% !important">
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

            <div class="d-flex flex-column mt-4">
              <label for="" class="research-labels ms-3">Nature*</label>
              <div class="drop-down create-dropdown-nature ms-3" style="margin-left: 1.5% !important">
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
            
            <div class="d-flex flex-column mt-4">
              <label for="" class="research-labels ms-3">Type*</label>
              <div class="drop-down create-dropdown-type ms-3" style="margin-left: 1.5% !important">
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

            <div class="d-flex flex-column mt-4">
              <label for="" class="research-labels ms-3">Source of Fund*</label>
              <div class="drop-down create-dropdown-sourcefund ms-3" style="margin-left: 1.5% !important">
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

            <div class="d-flex flex-column mt-4">
              <label for="" class="research-labels ms-3">Budget*</label>
              <input class="research-input" id="budget-input" type="number" placeholder="Enter budget" value="{{ $item->budget }}">
            </div>

            <div class="d-flex flex-column mt-4">
              <label for="" class="research-labels  ms-3">Organizer*</label>
              <input class="research-input" id="organizer-input" type="text" placeholder="Enter organizer" value="{{ $item->organizer }}">
            </div>

            <div class="d-flex flex-column mt-4">
                <label for="" class="research-labels  ms-3">Level*</label>
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
            
            <div class="d-flex flex-column mt-4">
              <label for="" class="research-labels ms-3">Venue*</label>
              <input class="research-input" id="venue-input" type="text" placeholder="Enter venue" value="{{ $item->venue }}">
            </div>

            <div class="d-flex flex-column mt-4" style="margin-left: .5% !important">
              <label for="" class="research-labels ms-3">From*</label>
              <input class="ms-2 date-picker-from" type="date" id="date-picker" min="1997-01-01" max="2030-01-01" value="{{ $item->from_date }}">
            </div>   

            <div class="d-flex flex-column mt-4" style="margin-left: .5% !important">
              <label for="" class="research-labels ms-3">To*</label>
              <input class="ms-2 date-picker-to" type="date" id="date-picker" min="1997-01-01" max="2030-01-01" value="{{ $item->to_date }}">
            </div>  

            <div class="d-flex flex-column mt-4">
              <label for="" class="research-labels ms-3">Total No. of Hours*</label>
              <input class="research-input" id="total-no-hours-input" type="number" placeholder="Enter total no. of hours" value="{{ $item->total_no_hours }}">
            </div>

            <div class="d-flex flex-column mt-4 ms-3">
                <label for="" class="research-labels">Special Order*</label>
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
                    <div id="preview" class="preview-no-items" style="text-align:center; z-index: 99; position: relative;">
                        <p class="preview-label">Uploaded files are displayed here</p>

                        <div id="loading-special-overlay-files" class="loading-special-uploaded-files" style="display: none; justify-content: center; align-items: center; border-radius: 25px; z-index: 99; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: white;">
                          <div style="display: flex; flex-direction: column; align-items: center;">
                              <div class="spinner-border text-dark" role="status">
                                  <span class="sr-only">Loading...</span>
                              </div>
                              <div id="loading-special-uploaded-text" style="margin-top: 3px;">Retrieving uploaded files..</div>
                          </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-column mt-4 ms-3">
              <label for="" class="research-labels">Certificate of Participating/Attendance/Completion *</label>
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
                  <div id="preview-cert" class="preview-no-items" style="text-align:center; z-index: 99; position: relative;">
                      <p class="preview-label">Uploaded files are displayed here</p>

                      <div id="loading-cert-overlay-files" class="loading-cert-uploaded-files" style="display: none; justify-content: center; align-items: center; border-radius: 25px; z-index: 99; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: white;">
                        <div style="display: flex; flex-direction: column; align-items: center;">
                            <div class="spinner-border text-dark" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                            <div id="loading-cert-uploaded-text" style="margin-top: 3px;">Retrieving uploaded files..</div>
                        </div>
                      </div>
                  </div>
              </div>
            </div>

            <div class="d-flex flex-column mt-4 ms-3">
              <label for="" class="research-labels">Compiled Photos in 1 PDF Document</label>
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
                  <div id="preview-compiled" class="preview-no-items" style="text-align:center; z-index: 99; position: relative;">
                      <p class="preview-label">Uploaded files are displayed here</p>

                      <div id="loading-compiled-overlay-files" class="loading-compiled-uploaded-files" style="display: none; justify-content: center; align-items: center; border-radius: 25px; z-index: 99; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: white;">
                        <div style="display: flex; flex-direction: column; align-items: center;">
                            <div class="spinner-border text-dark" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                            <div id="loading-compiled-uploaded-text" style="margin-top: 3px;">Retrieving uploaded files..</div>
                        </div>
                      </div>
                  </div>
              </div>
            </div>

            <div class="d-flex flex-column mt-4 ms-2">
              <label for="" class="ms-3 research-labels">Notes</label>
              <textarea class="ms-2 task-description-content" id="notes" name="description" rows="4" cols="50" placeholder="Add your personal notes here">{{ $item->notes }}</textarea>
            </div>  

            <div id="loading-overlay" class="loading-save-task" style="display: none; justify-content: center; align-items: center; height: 250vh; border-radius: 25px; z-index: 99; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: white;">
              <div style="display: flex; flex-direction: column; align-items: center;">
                  <div class="spinner-border text-dark" role="status">
                      <span class="sr-only">Loading...</span>
                  </div>
                  <div id="loading-text" style="margin-top: 3px;"></div>
              </div>
            </div>          
            
          </div>
        </div>
      </div>
    </div>

    <script>
      const itemId = "{{ $item->id }}";

      const deleteButton = document.querySelector('.delete-task-btn');
      const editButton = document.querySelector('.edit-task-btn');
      const saveButton = document.querySelector('.save-task-btn');

      var selectedFiles = [];
      var additionalFiles = [];

      var selectedCertificationFiles = []; 
      var selectedFilesCompiled = [];

      function disableButtons() {
        deleteButton.disabled = true;
        editButton.disabled = true;
        saveButton.disabled = true;
      }

      function enableButtons() {
        deleteButton.disabled = false;
        editButton.disabled = false;
        saveButton.disabled = false;
      }

      document.querySelectorAll('.research-input').forEach(input => {
        input.disabled = true;
      })

      function disableForm() {
        let title = document.getElementById('title-input');
        let classification = document.querySelectorAll('input[name="classification"]');
        let nature = document.querySelectorAll('input[name="nature"]');
        let type = document.querySelectorAll('input[name="type"]');
        let sourceFund = document.querySelectorAll('input[name="sourcefund"]');
        let budget = document.getElementById('budget-input');
        let organizer = document.getElementById('organizer-input');
        let level = document.querySelectorAll('input[name="level"]');
        let venue = document.getElementById('venue-input');
        let dateFrom = document.querySelector('.date-picker-from');
        let dateTo = document.querySelector('.date-picker-to');
        let totalNoHours = document.getElementById('total-no-hours-input');
        let notes = document.getElementById('notes');

        title.disabled = true;

        classification.forEach(radio => {
            radio.disabled = true;
        });

        nature.forEach(radio => {
            radio.disabled = true;
        });

        type.forEach(radio => {
            radio.disabled = true;
        });

        sourceFund.forEach(radio => {
            radio.disabled = true;
        });

        budget.disabled = true;
        organizer.disabled = true;

        level.forEach(radio => {
            radio.disabled = true;
        });

        venue.disabled = true;
        dateFrom.disabled = true;
        dateTo.disabled = true;
        totalNoHours.disabled = true;
        notes.disabled = true;

        // Disable the file upload
        let fileUpload = document.getElementById('file-upload');
        fileUpload.disabled = true;

        // Hide the x button
        let removeButtons = document.querySelectorAll('.remove-file');
        removeButtons.forEach(button => {
            button.style.display = 'none';
        });

        // Disable the file upload for the certificate of participation
        let fileUploadCert = document.getElementById('file-upload-cert');
        fileUploadCert.disabled = true;

        // Hide the x button for the certificate of participation
        let removeButtonsCert = document.querySelectorAll('.remove-file-cert');
        removeButtonsCert.forEach(button => {
            button.style.display = 'none';
        });

        // Disable the file upload for the compiled photos
        let fileUploadCompiled = document.getElementById('file-upload-compiled');
        fileUploadCompiled.disabled = true;

        // Hide the x button for the compiled photos
        let removeButtonsCompiled = document.querySelectorAll('.remove-file-compiled');
        removeButtonsCompiled.forEach(button => {
            button.style.display = 'none';
        });
      }

      disableForm();

      function enableForm() {
        let title = document.getElementById('title-input');
        let classification = document.querySelectorAll('input[name="classification"]');
        let nature = document.querySelectorAll('input[name="nature"]');
        let type = document.querySelectorAll('input[name="type"]');
        let sourceFund = document.querySelectorAll('input[name="sourcefund"]');
        let budget = document.getElementById('budget-input');
        let organizer = document.getElementById('organizer-input');
        let level = document.querySelectorAll('input[name="level"]');
        let venue = document.getElementById('venue-input');
        let dateFrom = document.querySelector('.date-picker-from');
        let dateTo = document.querySelector('.date-picker-to');
        let totalNoHours = document.getElementById('total-no-hours-input');
        let notes = document.getElementById('notes');

        title.disabled = false;

        classification.forEach(radio => {
            radio.disabled = false;
        });

        nature.forEach(radio => {
            radio.disabled = false;
        });

        type.forEach(radio => {
            radio.disabled = false;
        });

        sourceFund.forEach(radio => {
            radio.disabled = false;
        });

        budget.disabled = false;
        organizer.disabled = false;

        level.forEach(radio => {
            radio.disabled = false;
        });

        venue.disabled = false;
        dateFrom.disabled = false;
        dateTo.disabled = false;
        totalNoHours.disabled = false;
        notes.disabled = false;

        // Enable the file upload
        let fileUpload = document.getElementById('file-upload');
        fileUpload.disabled = false;

        // Show the x button
        let removeButtons = document.querySelectorAll('.remove-file');
        removeButtons.forEach(button => {
            button.style.display = 'block';
        });

        // Enable the file upload for the certificate of participation
        let fileUploadCert = document.getElementById('file-upload-cert');
        fileUploadCert.disabled = false;

        // Show the x button for the certificate of participation
        let removeButtonsCert = document.querySelectorAll('.remove-file-cert');
        removeButtonsCert.forEach(button => {
            button.style.display = 'block';
        });

        // Enable the file upload for the compiled photos
        let fileUploadCompiled = document.getElementById('file-upload-compiled');
        fileUploadCompiled.disabled = false;

        // Show the x button for the compiled photos
        let removeButtonsCompiled = document.querySelectorAll('.remove-file-compiled');
        removeButtonsCompiled.forEach(button => {
            button.style.display = 'block';
        });
      }

      function editResearch() {
        if (editButton.innerHTML === 'Enable Edit') {
            editButton.innerHTML = 'Disable Edit';
            enableForm();
        } 
        else {
            editButton.innerHTML = 'Enable Edit';
            disableForm();
        }
      }

      function loadingMessage() {
        let div = document.getElementById("loading-text");
        let text = ["Updating seminar, this may take a few seconds.",
            "Updating seminar, this may take a few seconds..",
            "Updating seminar, this may take a few seconds..."
        ];

        let i = 0;
        setInterval(function() {
            div.innerHTML = text[i];
            i = (i + 1) % text.length;
        }, 400);
      }

      function deletingMessage() {
        let div = document.getElementById("loading-text");
        let text = ["Deleting seminar, this may take a few seconds.",
            "Deleting seminar, this may take a few seconds..",
            "Deleting seminar, this may take a few seconds..."
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

      // Set the initial value of the level
      selectedLevelDisplay.textContent = "{{ $item->level }}";

      // Set the radio button to the selected level
      let selectedLevel = document.querySelector(`input[id="${selectedLevelDisplay.textContent.toLowerCase()}"]`);
      selectedLevel.checked = true;

      // Add event listener to level dropdown
      levelRadioButtons.forEach(radio => {
          radio.addEventListener('change', () => {
              selectedLevelDisplay.textContent = radio.nextElementSibling.textContent.trim();
          });
      });

      // Set the initial value of the classification
      selectedClassificationDisplay.textContent = "{{ $item->classification }}";

      // Set the radio button to the selected classification
      let selectedClassification = document.querySelector(`input[id="${selectedClassificationDisplay.textContent}"]`);
      selectedClassification.checked = true;

      // Add event listener to classification dropdown
      classificationRadioButtons.forEach(radio => {
          radio.addEventListener('change', () => {
              selectedClassificationDisplay.textContent = radio.nextElementSibling.textContent.trim();
          });
      });

      // Set the initial value of the nature
      selectedNatureDisplay.textContent = "{{ $item->nature }}";

      // Set the radio button to the selected nature
      let selectedNature = document.querySelector(`input[id="${selectedNatureDisplay.textContent}"]`);
      selectedNature.checked = true;

      // Add event listener to nature dropdown
      natureRadioButtons.forEach(radio => {
          radio.addEventListener('change', () => {
              selectedNatureDisplay.textContent = radio.nextElementSibling.textContent.trim();
          });
      });

      // Set the initial value of the type
      selectedTypeDisplay.textContent = "{{ $item->type }}";

      // Set the radio button to the selected type
      let selectedType = document.querySelector(`input[id="${selectedTypeDisplay.textContent}"]`);
      selectedType.checked = true;

      // Add event listener to type dropdown
      typeRadioButtons.forEach(radio => {
          radio.addEventListener('change', () => {
              selectedTypeDisplay.textContent = radio.nextElementSibling.textContent.trim();
          });
      });

      // Set the initial value of the source of fund
      selectedSourceFundDisplay.textContent = "{{ $item->source_of_fund }}";

      // Set the radio button to the selected source of fund
      let selectedSourceFund = document.querySelector(`input[id="${selectedSourceFundDisplay.textContent}"]`);
      selectedSourceFund.checked = true;
      
      // Add event listener to source of fund dropdown
      sourceFundRadioButtons.forEach(radio => {
          radio.addEventListener('change', () => {
              selectedSourceFundDisplay.textContent = radio.nextElementSibling.textContent.trim();
          });
      });

      /// File Upload ///

      document.getElementById("file-upload").onchange = function() {
            var files = document.getElementById("file-upload").files;
            if (files.length === 0) {
                // The user clicked the cancel button in the file upload dialog
                console.log('Upload cancelled');
            } else {
                handleFiles(files);
            }
        };

        var dropZone = document.getElementById("drop-zone");
        dropZone.addEventListener("dragover", function(evt) {
            if (editButton.innerHTML === 'Disable Edit') {
              evt.preventDefault();
            }
        }, false);

        dropZone.addEventListener("drop", function(evt) {
            if (editButton.innerHTML === 'Disable Edit') {
              evt.preventDefault();
              var files = evt.dataTransfer.files;
              handleFiles(files);
            }
        }, false);

        function handleFiles(files) {
          for (var i = 0; i < files.length; i++) {
              var file = files[i];
              var isDuplicate = selectedFiles.some(function(selectedFile) {
                  return selectedFile.name === file.name;
              });
              if (isDuplicate) {
                  continue;
              }
              selectedFiles.push(file);
              additionalFiles.push(file);
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
              removeButton.className = "remove-file";
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
                    } 
                    else { // File uploaded previously
                        let url = `/faculty-tasks/seminars/attachment/preview?category=special_order&id=${itemId}&fileName=${f.name}`;

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
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                showNotification('An error occurred while retrieving the uploaded file.', '#fe3232bc');
                            });
                        }
                    }
                );
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

        document.getElementById("preview").addEventListener("click", function(evt) {
          if (editButton.innerHTML === 'Disable Edit') {
            if (evt.target.classList.contains("remove-file")) {
              var index = parseInt(evt.target.dataset.index);
              selectedFiles.splice(index, 1);
              updatePreview();

              // Reset the value of the file input
              document.getElementById("file-upload").value = null;                
            }
          }
        })

      // Certificate of Participation

      document.getElementById("file-upload-cert").onchange = function() {
            var files = document.getElementById("file-upload-cert").files;
            if (files.length === 0) {
                // The user clicked the cancel button in the file upload dialog
                console.log('Upload cancelled');
            } else {
                handleFilesCert(files);
            }
        };

        var dropZoneCert = document.getElementById("drop-zone-cert");
        dropZoneCert.addEventListener("dragover", function(evt) {
            if (editButton.innerHTML === 'Disable Edit') {
              evt.preventDefault();
            }
        }, false);

        dropZoneCert.addEventListener("drop", function(evt) {
            if (editButton.innerHTML === 'Disable Edit') {
              evt.preventDefault();
              var files = evt.dataTransfer.files;
              handleFilesCert(files);
            }
        }, false);

        function handleFilesCert(files) {
          for (var i = 0; i < files.length; i++) {
              var file = files[i];
              var isDuplicate = selectedCertificationFiles.some(function(selectedFile) {
                  return selectedFile.name === file.name;
              });
              if (isDuplicate) {
                  continue;
              }
              selectedCertificationFiles.push(file);
          }
          updatePreviewCert();
        }

        function updatePreviewCert() {
          var preview = document.getElementById("preview-cert");
          preview.innerHTML = "";
          for (var i = 0; i < selectedCertificationFiles.length; i++) {
              var file = selectedCertificationFiles[i];
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
              var removeButton = document.createElement("button");
              removeButton.textContent = "x";
              removeButton.className = "remove-file-cert";
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
                    } 
                    else { // File uploaded previously
                        let url = `/faculty-tasks/seminars/attachment/preview?category=certificate&id=${itemId}&fileName=${f.name}`;

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
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                showNotification('An error occurred while retrieving the uploaded file.', '#fe3232bc');
                            });
                        }
                    }
                );
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

            if (selectedCertificationFiles.length <= 0) {
                preview.innerHTML = "Files uploaded are displayed here";
                preview.classList.add('preview-no-items');
            } else {
                preview.classList.remove('preview-no-items');
            }

        }

        document.getElementById("preview-cert").addEventListener("click", function(evt) {
          if (editButton.innerHTML === 'Disable Edit') {
            if (evt.target.classList.contains("remove-file-cert")) {
              var index = parseInt(evt.target.dataset.index);
              selectedCertificationFiles.splice(index, 1);
              updatePreviewCert();

              // Reset the value of the file input
              document.getElementById("file-upload-cert").value = null;                
            }
          }
        })

        // Compiled Photos

        document.getElementById("file-upload-compiled").onchange = function() {
            var files = document.getElementById("file-upload-compiled").files;
            if (files.length === 0) {
                // The user clicked the cancel button in the file upload dialog
                console.log('Upload cancelled');
            } else {
                handleFilesCompiled(files);
            }
        };

        var dropZoneCompiled = document.getElementById("drop-zone-compiled");
        dropZoneCompiled.addEventListener("dragover", function(evt) {
            if (editButton.innerHTML === 'Disable Edit') {
              evt.preventDefault();
            }
        }, false);

        dropZoneCompiled.addEventListener("drop", function(evt) {
            if (editButton.innerHTML === 'Disable Edit') {
              evt.preventDefault();
              var files = evt.dataTransfer.files;
              handleFilesCompiled(files);
            }
        }, false);

        function handleFilesCompiled(files) {
          for (var i = 0; i < files.length; i++) {
              var file = files[i];
              var isDuplicate = selectedFilesCompiled.some(function(selectedFile) {
                  return selectedFile.name === file.name;
              });
              if (isDuplicate) {
                  continue;
              }
              selectedFilesCompiled.push(file);
          }
          updatePreviewCompiled();
        }

        function updatePreviewCompiled() {
          var preview = document.getElementById("preview-compiled");
          preview.innerHTML = "";
          for (var i = 0; i < selectedFilesCompiled.length; i++) {
              var file = selectedFilesCompiled[i];
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
              var removeButton = document.createElement("button");
              removeButton.textContent = "x";
              removeButton.className = "remove-file-compiled";
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
                    } 
                    else { // File uploaded previously
                        let url = `/faculty-tasks/seminars/attachment/preview?category=compiled&id=${itemId}&fileName=${f.name}`;

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
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                showNotification('An error occurred while retrieving the uploaded file.', '#fe3232bc');
                            });
                        }
                    }
                );
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

            if (selectedFilesCompiled.length <= 0) {
                preview.innerHTML = "Files uploaded are displayed here";
                preview.classList.add('preview-no-items');
            } else {
                preview.classList.remove('preview-no-items');
            }

        }

        document.getElementById("preview-compiled").addEventListener("click", function(evt) {
          if (editButton.innerHTML === 'Disable Edit') {
            if (evt.target.classList.contains("remove-file-compiled")) {
              var index = parseInt(evt.target.dataset.index);
              selectedFilesCompiled.splice(index, 1);
              updatePreviewCompiled();

              // Reset the value of the file input
              document.getElementById("file-upload-compiled").value = null;                
            }
          }
        })
        
        const loadingAttachmentSpecial = document.getElementById('loading-special-overlay-files');
        const loadingAttachmentCert = document.getElementById('loading-cert-overlay-files');
        const loadingAttachmentCompiled = document.getElementById('loading-compiled-overlay-files');
        let isBothAttachmentedSettled = 0;

        // Get all the attachments for the special order
        loadingAttachmentSpecial.style.display = 'flex';
        disableButtons();
        
        fetch(`/faculty-tasks/seminars/special-order/getAttachments?id=${itemId}`)
          .then(response => response.json())
          .then(files => {
            // Get the files
            for (const file of files) {
              let f = new File([], file);
              selectedFiles.push(f);
            }

            updatePreview();
          })
          .catch(error => {
            console.error('Error:', error);
            showNotification('An error occurred while retrieving the uploaded files.', '#fe3232bc');
          })
          .finally(() => {
              let removeButtons = document.querySelectorAll('.remove-file');
              removeButtons.forEach(button => {
                  button.style.display = 'none';
              });

              loadingAttachmentSpecial.style.display = 'none';
              isBothAttachmentedSettled++;
          });

        // Get all the attachments for the certificate of participation
        loadingAttachmentCert.style.display = 'flex';
        disableButtons();
        
        fetch(`/faculty-tasks/seminars/certificates/getAttachments?id=${itemId}`)
          .then(response => response.json())
          .then(files => {
            // Get the files
            for (const file of files) {
              let f = new File([], file);
              selectedCertificationFiles.push(f);
            }

            updatePreviewCert();
          })
          .catch(error => {
            console.error('Error:', error);
            showNotification('An error occurred while retrieving the uploaded files.', '#fe3232bc');
          })
          .finally(() => {
              let removeButtons = document.querySelectorAll('.remove-file-cert');
              removeButtons.forEach(button => {
                  button.style.display = 'none';
              });

              loadingAttachmentCert.style.display = 'none';
              isBothAttachmentedSettled++;
          });

        // Get all the attachments for the compiled photos
        loadingAttachmentCompiled.style.display = 'flex';
        disableButtons();

        fetch(`/faculty-tasks/seminars/compiled/getAttachments?id=${itemId}`)
          .then(response => response.json())
          .then(files => {
            // Get the files
            for (const file of files) {
              let f = new File([], file);
              selectedFilesCompiled.push(f);
            }

            updatePreviewCompiled();
          })
          .catch(error => {
            console.error('Error:', error);
            showNotification('An error occurred while retrieving the uploaded files.', '#fe3232bc');
          })
          .finally(() => {
              let removeButtons = document.querySelectorAll('.remove-file-compiled');
              removeButtons.forEach(button => {
                  button.style.display = 'none';
              });

              loadingAttachmentCompiled.style.display = 'none';
              isBothAttachmentedSettled++;
          });
      
        let interval = setInterval(() => {
            if (isBothAttachmentedSettled === 3) {
                clearInterval(interval);
                enableButtons();
            }
        }, 1000);

      // Hydrate data

      /* Get all the checkboxes
      let checkboxes = document.querySelectorAll('.select-checkbox');
      for (let i = 0; i < checkboxes.length; i++) {
          let checkbox = checkboxes[i];
          let authorName = checkbox.nextElementSibling.textContent.trim();

          if (authors.includes(authorName)) {
              checkbox.checked = true;
          }
      }*/

      // Get all the radio buttons
      /*let radioButtons = document.querySelectorAll('.select-radio');
      for (let i = 0; i < radioButtons.length; i++) {
          let radioButton = radioButtons[i];
          let levelName = radioButton.nextElementSibling.textContent.trim();

          if (levelName.toLowerCase() === level.toLowerCase()) {
              radioButton.checked = true;
          }
      }*/

        // Form Handling

        function validateForm() {
            let title = document.getElementById('title-input').value;
            let classification = document.querySelector('input[name="classification"]:checked');
            let nature = document.querySelector('input[name="nature"]:checked');
            let type = document.querySelector('input[name="type"]:checked');
            let sourceFund = document.querySelector('input[name="sourcefund"]:checked');
            let budget = document.getElementById('budget-input').value;
            let organizer = document.getElementById('organizer-input').value;
            let level = document.querySelector('input[name="level"]:checked');
            let venue = document.getElementById('venue-input').value;
            let dateFrom = document.querySelector('.date-picker-from').value;
            let dateTo = document.querySelector('.date-picker-to').value;
            let totalNoHours = document.getElementById('total-no-hours-input').value;
            let notes = document.getElementById('notes').value;

            if (title.trim() === '') {
                showNotification('Please enter the title of the seminar.', '#fe3232bc');
                return false;
            }

            if (classification === null) {
                showNotification('Please select the classification of the seminar.', '#fe3232bc');
                return false;
            }

            if (nature === null) {
                showNotification('Please select the nature of the seminar.', '#fe3232bc');
                return false;
            }

            if (type === null) {
                showNotification('Please select the type of the seminar.', '#fe3232bc');
                return false;
            }

            if (sourceFund === null) {
                showNotification('Please select the source of fund of the seminar.', '#fe3232bc');
                return false;
            }

            if (budget.trim() === '') {
                showNotification('Please enter the budget of the seminar.', '#fe3232bc');
                return false;
            }

            if (organizer.trim() === '') {
                showNotification('Please enter the organizer of the seminar.', '#fe3232bc');
                return false;
            }

            if (level === null) {
                showNotification('Please select the level of the seminar.', '#fe3232bc');
                return false;
            }

            if (venue.trim() === '') {
                showNotification('Please enter the venue of the seminar.', '#fe3232bc');
                return false;
            }

            if (dateFrom.trim() === '') {
                showNotification('Please enter the date started of the seminar.', '#fe3232bc');
                return false;
            }

            if (dateTo.trim() === '') {
                showNotification('Please enter the date completed of the seminar.', '#fe3232bc');
                return false;
            }

            if (totalNoHours.trim() === '') {
                showNotification('Please enter the total number of hours of the seminar.', '#fe3232bc');
                return false;
            }

            if (selectedFiles.length <= 0) {
                showNotification('Please upload the attachments for the seminar.', '#fe3232bc');
                return false;
            }

            if (selectedCertificationFiles.length <= 0) {
                showNotification('Please upload the certificate of participation for the seminar.', '#fe3232bc');
                return false;
            }

            if (selectedFilesCompiled.length <= 0) {
                showNotification('Please upload the compiled photos for the seminar.', '#fe3232bc');
                return false;
            }

            if (notes.trim() === '') {
                showNotification('Please enter the notes of the seminar.', '#fe3232bc');
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
            const dateFrom = document.querySelector('.date-picker-from').value;
            const dateTo = document.querySelector('.date-picker-to').value;
            const totalNoHours = document.getElementById('total-no-hours-input').value;
            const notes = document.getElementById('notes').value;

            let formData = new FormData();
            formData.append('id', itemId);
            formData.append('title', title);
            formData.append('classification', classification);
            formData.append('nature', nature);
            formData.append('type', type);
            formData.append('source_of_fund', sourceFund);
            formData.append('budget', budget);
            formData.append('organizer', organizer);
            formData.append('level', level);
            formData.append('venue', venue);
            formData.append('from_date', dateFrom);
            formData.append('to_date', dateTo);
            formData.append('total_no_hours', totalNoHours);
            formData.append('notes', notes);

            for (const file of selectedFiles) {
                formData.append('special_order_files[]', file);
            }

            for (const file of selectedCertificationFiles) {
                formData.append('certifications_files[]', file);
            }

            for (const file of selectedFilesCompiled) {
                formData.append('compiled_files[]', file);
            }

            document.getElementById('loading-overlay').style.display = 'flex';
            loadingMessage();
            disableButtons();

            let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            fetch(`/faculty-tasks/seminars/update`, {
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
                if (data.success) {
                    showNotification('Seminar updated successfully.', '#28a745');
                } else {
                    showNotification('An error occurred while updating the seminar.', '#fe3232bc');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred while updating the seminar.', '#fe3232bc');
            })
            .finally(() => {
                document.getElementById('loading-overlay').style.display = 'none';
                enableButtons();
            });
        }

        function deleteResearch() {
            if (!confirm('Are you sure you want to delete this seminar?')) {
                return;
            }

            let formData = new FormData();
            formData.append('id', itemId);

            document.getElementById('loading-overlay').style.display = 'flex';
            deletingMessage();
            disableButtons();

            let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            fetch(`/faculty-tasks/seminars/delete`, {
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
                if (data.message) {
                    localStorage.setItem('notif_green', 'Seminar deleted successfully.');
                    window.location.href = '/faculty-tasks/seminars';
                } 
                else {
                    showNotification('An error occurred while deleting the seminar.', '#fe3232bc');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred while deleting the seminar.', '#fe3232bc');
            })
            .finally(() => {
                document.getElementById('loading-overlay').style.display = 'none';
                enableButtons();
            });
        }
    </script>


    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script>
    @endsection