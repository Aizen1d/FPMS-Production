@extends('layouts.default')

@section('title', 'PUPQC - View Extension')

@section('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('admin/css/admin_tasks_extensions_view.css') }}">
@endsection

@section('body')
<div class="overlay"></div>
@include('layouts.admin_navbar')
@include('layouts.admin_selected_extension_sidebar') 
@include('layouts.notification_side')

<div class="container-fluid margin">
    <div class="row">
        <div class="col-6">
            <h3 class="my-4 title">Extension</h3>
        </div>
        <div class="col-6 drop-down-container">
            <button class="my-4 create-btn delete-task-btn" onclick="deleteItem()">Delete</button>
            <button class="my-4 create-btn edit-task-btn" onclick="editItem()">Enable Edit</button>
            <button class="my-4 create-btn save-task-btn" onclick="submitForm()">Save</button>
        </div>
    </div>

    <div class="container-fluid task-list" style="position: relative;">
      <div class="row mt-3">
        <div class="col-12">
          <div class="ms-3 mt-4">
            <div class="d-flex flex-column">
                <label class="research-labels ms-3" for="">Title of Extension Program</label>
                <input class="research-input" id="title-program-input" type="text" placeholder="Enter title of extension program" value="{{ $item->title_of_extension_program }}">
            </div>

            <div class="d-flex flex-column mt-4">
                <label class="research-labels ms-3" for="">Title of Extension Project</label>
                <input class="research-input" id="title-project-input" type="text" placeholder="Enter title of extension project" value="{{ $item->title_of_extension_project }}">
            </div>

            <div class="d-flex flex-column mt-4">
                <label class="research-labels ms-3" for="">Title of Extension Activity</label>
                <input class="research-input" id="title-activity-input" type="text" placeholder="Enter title of extension activity" value="{{ $item->title_of_extension_activity }}">
            </div>
            
            <div class="d-flex flex-column mt-4">
                <label for="" class="research-labels ms-3">Place/Venue</label>
                <input class="research-input" id="place-input" type="text" value="{{ $item->place }}">
            </div>
            
            <div class="d-flex flex-column mt-4">
                <label for="" class="research-labels ms-3">Level*</label>
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

            <div class="d-flex flex-column mt-4">
                <label for="" class="research-labels ms-3">Classification*</label>
                <label for="" class="ms-3" style="font-size: 12px; margin-top: -2px">
                    Livelihood Development; Health; Educational and Cultural Exchange; Technology Transfer; Knowledge Transfer; Local Governance; if others, please specify
                </label>
                <input class="research-input" id="classification-input" type="text" value="{{ $item->classification }}">
            </div>

            <div class="d-flex flex-column mt-4">
                <label for="" class="research-labels ms-3">Type*</label>
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

            <div class="d-flex flex-column mt-4">
                <label for="" class="research-labels ms-3">Keywords (at least five (5) keywords)</label>
                <input class="research-input" id="keywords-input" type="text" value="{{ $item->keywords }}">
            </div>

            <div class="d-flex flex-column mt-4">
                <label for="" class="research-labels ms-3">Type of Funding*</label>
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

            <div class="d-flex flex-column mt-4">
                <label for="" class="research-labels ms-3">Funding Agency</label>
                <input class="research-input" id="funding-agency-input" type="text" value="{{ $item->funding_agency }}">
            </div>

            <div class="d-flex flex-column mt-4">
                <label for="" class="research-labels ms-3">Amount of Funding (PHP)</label>
                <input class="research-input" id="amount-funding-input" type="number" min="0" value="{{ $item->amount_of_funding }}">
            </div>
            
            <div class="d-flex flex-column mt-4">
                <label for="" class="research-labels ms-3">Total No. of Hours</label>
                <input class="research-input" id="total-hours-input" type="number" min="0" value="{{ $item->total_no_of_hours }}">
            </div>

            <div class="d-flex flex-column mt-4">
                <label for="" class="research-labels ms-3">No. of Trainees/Beneficiaries</label>
                <input class="research-input" id="number-of-trainees-input" type="number" min="0" value="{{ $item->no_of_trainees }}">
            </div>

            <div class="d-flex flex-column mt-4">
                <label for="" class="research-labels ms-3">Classification of Trainees/Beneficaries*</label>
                <label for="" class="ms-3" style="font-size: 12px; margin-top: -2px">
                    Faculty; Administrative Employee; Students; Community; If others, please specify.
                </label>
                <input class="research-input" id="classification-of-trainees-input" type="text" value="{{ $item->classification_of_trainees }}">
            </div>

            <div class="d-flex flex-column mt-4">
                <label for="" class="research-labels ms-3">Nature of Involvement*</label>
                <input class="research-input" id="nature-input" type="text" value="{{ $item->nature_of_involvement }}">
            </div>

            <div class="d-flex flex-column mt-4">
                <label for="" class="research-labels ms-3">Status*</label>
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
            
            <div class="d-flex flex-column mt-4">
                <label for="" class="research-labels ms-3">From</label>
                <input class="ms-2" type="date" id="date-picker-from" min="1997-01-01" max="2030-01-01" value="{{ $from_date }}">
            </div>

            <div class="d-flex flex-column mt-4">
                <label for="" class="research-labels ms-3">To</label>
                <input class="ms-2" type="date" id="date-picker-to" min="1997-01-01" max="2030-01-01" value="{{ $to_date }}">
            </div>
           
            <div id="loading-overlay" class="loading-save-task" style="display: none; justify-content: center; height: 210vh; align-items: center; border-radius: 25px; z-index: 99; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: white;">
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
      const initalTitle = "{{ $item->title }}";

      const deleteButton = document.querySelector('.delete-task-btn');
      const editButton = document.querySelector('.edit-task-btn');
      const saveButton = document.querySelector('.save-task-btn');

      const loadingAttachment = document.querySelector('.loading-uploaded-files')

      var selectedFiles = [];
      var additionalFiles = [];

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

      function disableForm() {
        let titleProgramInput = document.getElementById('title-program-input');
        let titleProjectInput = document.getElementById('title-project-input');
        let titleActivityInput = document.getElementById('title-activity-input');
        let placeInput = document.getElementById('place-input');
        let classificationInput = document.getElementById('classification-input');
        let keywordsInput = document.getElementById('keywords-input');
        let fundingAgencyInput = document.getElementById('funding-agency-input');
        let amountFundingInput = document.getElementById('amount-funding-input');
        let totalHoursInput = document.getElementById('total-hours-input');
        let numberOfTraineesInput = document.getElementById('number-of-trainees-input');
        let classificationOfTraineesInput = document.getElementById('classification-of-trainees-input');
        let natureInput = document.getElementById('nature-input');

        titleProgramInput.disabled = true;
        titleProjectInput.disabled = true;
        titleActivityInput.disabled = true;
        placeInput.disabled = true;
        classificationInput.disabled = true;
        keywordsInput.disabled = true;
        fundingAgencyInput.disabled = true;
        amountFundingInput.disabled = true;
        totalHoursInput.disabled = true;
        numberOfTraineesInput.disabled = true;
        classificationOfTraineesInput.disabled = true;
        natureInput.disabled = true;

        let levelRadios = document.querySelectorAll('input[name="level"]');
        levelRadios.forEach(radio => {
            radio.disabled = true;
        });

        let typeRadios = document.querySelectorAll('input[name="type"]');
        typeRadios.forEach(radio => {
            radio.disabled = true;
        });

        let typeFundingRadios = document.querySelectorAll('input[name="typefunding"]');
        typeFundingRadios.forEach(radio => {
            radio.disabled = true;
        });

        let statusRadios = document.querySelectorAll('input[name="status"]');
        statusRadios.forEach(radio => {
            radio.disabled = true;
        });

        let datePickerFrom = document.getElementById('date-picker-from');
        datePickerFrom.disabled = true;

        let datePickerTo = document.getElementById('date-picker-to');
        datePickerTo.disabled = true;
      }

      disableForm();

      function enableForm() {
        let titleProgramInput = document.getElementById('title-program-input');
        let titleProjectInput = document.getElementById('title-project-input');
        let titleActivityInput = document.getElementById('title-activity-input');
        let placeInput = document.getElementById('place-input');
        let classificationInput = document.getElementById('classification-input');
        let keywordsInput = document.getElementById('keywords-input');
        let fundingAgencyInput = document.getElementById('funding-agency-input');
        let amountFundingInput = document.getElementById('amount-funding-input');
        let totalHoursInput = document.getElementById('total-hours-input');
        let numberOfTraineesInput = document.getElementById('number-of-trainees-input');
        let classificationOfTraineesInput = document.getElementById('classification-of-trainees-input');
        let natureInput = document.getElementById('nature-input');

        titleProgramInput.disabled = false;
        titleProjectInput.disabled = false;
        titleActivityInput.disabled = false;
        placeInput.disabled = false;
        classificationInput.disabled = false;
        keywordsInput.disabled = false;
        fundingAgencyInput.disabled = false;
        amountFundingInput.disabled = false;
        totalHoursInput.disabled = false;
        numberOfTraineesInput.disabled = false;
        classificationOfTraineesInput.disabled = false;
        natureInput.disabled = false;

        let levelRadios = document.querySelectorAll('input[name="level"]');
        levelRadios.forEach(radio => {
            radio.disabled = false;
        });

        let typeRadios = document.querySelectorAll('input[name="type"]');
        typeRadios.forEach(radio => {
            radio.disabled = false;
        });

        let typeFundingRadios = document.querySelectorAll('input[name="typefunding"]');
        typeFundingRadios.forEach(radio => {
            radio.disabled = false;
        });

        let statusRadios = document.querySelectorAll('input[name="status"]');
        statusRadios.forEach(radio => {
            radio.disabled = false;
        });

        let datePickerFrom = document.getElementById('date-picker-from');
        datePickerFrom.disabled = false;

        let datePickerTo = document.getElementById('date-picker-to');
        datePickerTo.disabled = false;

        if (titleProgramInput.value === '') {
            titleProgramInput.disabled = true;
        }

        if (titleProjectInput.value === '') {
            titleProjectInput.disabled = true;
        }

        if (titleActivityInput.value === '') {
            titleActivityInput.disabled = true;
        }
      }

      function editItem() {
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
        let text = ["Updating extension, this may take a few seconds.",
            "Updating extension, this may take a few seconds..",
            "Updating extension, this may take a few seconds..."
        ];

        let i = 0;
        setInterval(function() {
            div.innerHTML = text[i];
            i = (i + 1) % text.length;
        }, 400);
      }

      function deletingMessage() {
        let div = document.getElementById("loading-text");
        let text = ["Deleting extension, this may take a few seconds.",
            "Deleting extension, this may take a few seconds..",
            "Deleting extension, this may take a few seconds..."
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

        // Initial disable the other inputs who is empty
        if (titleProgramInput.value !== '') {
            titleProjectInput.disabled = true;
            titleActivityInput.disabled = true;
        } else if (titleProjectInput.value !== '') {
            titleProgramInput.disabled = true;
            titleActivityInput.disabled = true;
        } else if (titleActivityInput.value !== '') {
            titleProgramInput.disabled = true;
            titleProjectInput.disabled = true;
        }

        // Add event listeners
        titleProgramInput.addEventListener('input', () => disableOtherInputs(titleProgramInput, titleProjectInput, titleActivityInput));
        titleProjectInput.addEventListener('input', () => disableOtherInputs(titleProjectInput, titleProgramInput, titleActivityInput));
        titleActivityInput.addEventListener('input', () => disableOtherInputs(titleActivityInput, titleProgramInput, titleProjectInput));

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

      /// File Upload ///

      /*document.getElementById("file-upload").onchange = function() {
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
                        let url = `/admin-tasks/researches/attachment/preview?category=${category}&id=${researchId}&fileName=${f.name}`;

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
        })*/

      // Hydrate data

      /* let authors = "{{ $item->authors }}".split(', ');
      let level = "{{ $item->level }}";

      // Get all the checkboxes
      let checkboxes = document.querySelectorAll('.select-checkbox');
      for (let i = 0; i < checkboxes.length; i++) {
          let checkbox = checkboxes[i];
          let authorName = checkbox.nextElementSibling.textContent.trim();

          if (authors.includes(authorName)) {
              checkbox.checked = true;
          }
      } */

      /* Get all the radio buttons
      let radioButtons = document.querySelectorAll('.select-radio');
      for (let i = 0; i < radioButtons.length; i++) {
          let radioButton = radioButtons[i];
          let levelName = radioButton.nextElementSibling.textContent.trim();

          if (levelName.toLowerCase() === level.toLowerCase()) {
              radioButton.checked = true;
          }
      }*/

      /* Get all the attachments
      loadingAttachment.style.display = 'flex';
      disableButtons();
      
      fetch(`/admin-tasks/researches/getAttachments?category=${category}&id=${researchId}`)
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

            loadingAttachment.style.display = 'none';
            enableButtons();
        });*/

        // Set initial value of level
        let setLevelRadios = document.querySelectorAll('input[name="level"]');
        setLevelRadios.forEach(radio => {
            if (radio.id === "{{ $item->level }}") {
                radio.checked = true;
                selectedLevelDisplay.textContent = radio.parentElement.querySelector('.text').textContent;
            }
        });

        // Set initial value of type
        let setTypeRadios = document.querySelectorAll('input[name="type"]');
        setTypeRadios.forEach(radio => {
            if (radio.id === "{{ $item->type }}") {
                radio.checked = true;
                selectedTypeDisplay.textContent = radio.parentElement.querySelector('.text').textContent;
            }
        });

        // Set initial value of type of funding
        let setTypeFundingRadios = document.querySelectorAll('input[name="typefunding"]');
        setTypeFundingRadios.forEach(radio => {
            if (radio.id === "{{ $item->type_of_funding }}") {
                radio.checked = true;
                selectedTypeFundingDisplay.textContent = radio.parentElement.querySelector('.text').textContent;
            }
        });

        // Set initial value of status
        let setStatusRadios = document.querySelectorAll('input[name="status"]');
        setStatusRadios.forEach(radio => {
            if (radio.id === "{{ $item->status }}") {
                radio.checked = true;
                selectedStatusDisplay.textContent = radio.parentElement.querySelector('.text').textContent;
            }
        });

        // Form Handling

        function validateForm() {
          let titleProgramInput = document.getElementById('title-program-input');
            let titleProjectInput = document.getElementById('title-project-input');
            let titleActivityInput = document.getElementById('title-activity-input');
            let levelRadios = document.querySelectorAll('input[name="level"]');
            let classificationInput = document.getElementById('classification-input');
            let typeRadios = document.querySelectorAll('input[name="type"]');
            let keywordsInput = document.getElementById('keywords-input');
            let typeFundingRadios = document.querySelectorAll('input[name="typefunding"]');
            let fundingAgencyInput = document.getElementById('funding-agency-input');
            let amountFundingInput = document.getElementById('amount-funding-input');
            let totalHoursInput = document.getElementById('total-hours-input');
            let numberOfTraineesInput = document.getElementById('number-of-trainees-input');
            let classificationOfTraineesInput = document.getElementById('classification-of-trainees-input');
            let natureInput = document.getElementById('nature-input');
            let statusRadios = document.querySelectorAll('input[name="status"]');
            let datePickerFrom = document.getElementById('date-picker-from');
            let datePickerTo = document.getElementById('date-picker-to');

            // Check if any of titleProgram, titleProject, titleActivity is empty
            if (titleProgramInput.value.trim() === '' && titleProjectInput.value.trim() === '' && titleActivityInput.value.trim() === '') {
                showNotification('Please enter the title of the extension program, project or activity.', '#fe3232bc');
                return false;
            }

            let levelChecked = false;
            levelRadios.forEach(radio => {
                if (radio.checked) {
                    levelChecked = true;
                }
            });

            if (!levelChecked) {
                showNotification('Level is required.', '#fe3232bc');
                return false;
            }

            if (classificationInput.value === '') {
                showNotification('Classification is required.', '#fe3232bc');
                return false;
            }

            let typeChecked = false;
            typeRadios.forEach(radio => {
                if (radio.checked) {
                    typeChecked = true;
                }
            });

            if (!typeChecked) {
                showNotification('Type is required.', '#fe3232bc');
                return false;
            }

            if (keywordsInput.value === '') {
                showNotification('Keywords is required.', '#fe3232bc');
                return false;
            }

            let typeFundingChecked = false;
            typeFundingRadios.forEach(radio => {
                if (radio.checked) {
                    typeFundingChecked = true;
                }
            });

            if (!typeFundingChecked) {
                showNotification('Type of Funding is required.', '#fe3232bc');
                return false;
            }

            if (fundingAgencyInput.value === '') {
                showNotification('Funding Agency is required.', '#fe3232bc');
                return false;
            }

            if (amountFundingInput.value === '') {
                showNotification('Amount of Funding is required.', '#fe3232bc');
                return false;
            }

            if (totalHoursInput.value === '') {
                showNotification('Total No. of Hours is required.', '#fe3232bc');
                return false;
            }

            if (numberOfTraineesInput.value === '') {
                showNotification('No. of Trainees/Beneficiaries is required.', '#fe3232bc');
                return false;
            }

            if (classificationOfTraineesInput.value === '') {
                showNotification('Classification of Trainees/Beneficiaries is required.', '#fe3232bc');
                return false;
            }

            if (natureInput.value === '') {
                showNotification('Nature of Involvement is required.', '#fe3232bc');
                return false;
            }

            let statusChecked = false;
            statusRadios.forEach(radio => {
                if (radio.checked) {
                    statusChecked = true;
                }
            });

            if (!statusChecked) {
                showNotification('Status is required.', '#fe3232bc');
                return false;
            }

            if (datePickerFrom.value === '') {
                showNotification('From is required.', '#fe3232bc');
                return false;
            }

            if (datePickerTo.value === '') {
                showNotification('To is required.', '#fe3232bc');
                return false;
            }

            return true;
        }

        function submitForm() {
            if (!validateForm()) {
                return;
            }

            let formData = new FormData();
            formData.append('id', itemId);
            formData.append('title_program', document.getElementById('title-program-input').value);
            formData.append('title_project', document.getElementById('title-project-input').value);
            formData.append('title_activity', document.getElementById('title-activity-input').value);
            formData.append('level', document.getElementById('selected-level-display').textContent);
            formData.append('place', document.getElementById('place-input').value);
            formData.append('classification', document.getElementById('classification-input').value);
            formData.append('type', document.getElementById('selected-type-display').textContent);
            formData.append('keywords', document.getElementById('keywords-input').value);
            formData.append('type_of_funding', document.getElementById('selected-typefunding-display').textContent);
            formData.append('funding_agency', document.getElementById('funding-agency-input').value);
            formData.append('amount_of_funding', document.getElementById('amount-funding-input').value);
            formData.append('total_no_of_hours', document.getElementById('total-hours-input').value);
            formData.append('no_of_trainees', document.getElementById('number-of-trainees-input').value);
            formData.append('classification_of_trainees', document.getElementById('classification-of-trainees-input').value);
            formData.append('nature_of_involvement', document.getElementById('nature-input').value);
            formData.append('status', document.getElementById('selected-status-display').textContent);
            formData.append('from_date', document.getElementById('date-picker-from').value);
            formData.append('to_date', document.getElementById('date-picker-to').value);

            document.getElementById('loading-overlay').style.display = 'flex';
            loadingMessage();
            disableButtons();

            let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            fetch(`/admin-tasks/extensions/update`, {
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
                    showNotification('Extension updated successfully.', '#28a745');
                } else {
                    showNotification('An error occurred while updating the extension.', '#fe3232bc');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred while updating the extension.', '#fe3232bc');
            })
            .finally(() => {
                document.getElementById('loading-overlay').style.display = 'none';
                enableButtons();
            });
        }

        function deleteItem() {
            if (!confirm('Are you sure you want to delete this extension?')) {
                return;
            }

            let formData = new FormData();
            formData.append('id', itemId);

            document.getElementById('loading-overlay').style.display = 'flex';
            deletingMessage();
            disableButtons();

            let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            fetch(`/admin-tasks/extensions/delete`, {
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
                    localStorage.setItem('notif_green', 'Extension deleted successfully.');
                    window.location.href = '/admin-tasks/extensions';
                } 
                else {
                    showNotification('An error occurred while deleting the extension.', '#fe3232bc');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred while deleting the extension.', '#fe3232bc');
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