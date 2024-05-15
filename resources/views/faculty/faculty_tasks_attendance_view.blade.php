@extends('layouts.default')

@section('title', 'PUPQC - View Attendance')

@section('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('faculty/css/faculty_tasks_attendance_view.css') }}">
@endsection

@section('body')
<div class="overlay"></div>
@include('layouts.faculty_navbar')
@include('layouts.faculty_selected_attendance_sidebar') 
@include('layouts.notification_side')

<div class="container-fluid margin">
    <div class="row">
        <div class="col-6">
            <h3 class="my-4 title">View Attendance ({{ $item->status }})</h3>
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
            <div class="d-flex flex-column mt-4">
                <label for="" class="research-labels ms-3">Brief Description of Activity</label>
                <input class="research-input" id="brief-description-input" type="text" 
                value="{{ $item->getFunction?->brief_description }}" readonly>
            </div>

            <div class="d-flex flex-column mt-4">
                <label for="" class="research-labels ms-3">Remarks</label>
                <input class="research-input" id="remarks-input" type="text" 
                value="{{ $item->getFunction?->remarks }}" readonly>
            </div>             

            <hr class="mt-4"></hr>

            <div class="d-flex flex-column mt-4 ms-3">
                <label for="" class="research-labels ms-1">Date Started*</label>
                <input class="ms-2 date-picker-started" type="date" id="date-picker" min="1997-01-01" max="2030-01-01"
                    value="{{ $item->date_started }}">
            </div>   

            <div class="d-flex flex-column mt-4 ms-3">
              <label for="" class="research-labels ms-1">Date Completed*</label>
              <input class="ms-2 date-picker-completed" type="date" id="date-picker" min="1997-01-01" max="2030-01-01"
                  value="{{ $item->date_completed }}">
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
                          <input type="radio" name="status-attendance" id="On Leave">
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
              <div class="d-flex flex-column mt-4">
                <label for="" class="research-labels ms-3">Reason for absence*</label>
                <input class="research-input research-input-presented" id="reason-for-absence-input" type="text" placeholder="Enter reason for absence"
                value="{{ $item->reason_for_absence }}">
              </div>
            </div>

            <div class="attendance-proof-container" style="display: none">
              <div class="d-flex flex-column mt-4 ms-1" style="margin-left: 1.5% !important">
                  <label for="" class="research-labels" style="margin-left: 0% !important">Upload proof of Attendance*</label>
                  <label for="" class="mb-2" style="margin-left:0% !important; font-size: 13px; color:rgb(232, 79, 79);">Selfie photos are not allowed as supporting document.</label>
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
            </div>

            <div id="loading-overlay" class="loading-save-task" style="display: none; justify-content: center; align-items: center; height: 90vh; border-radius: 25px; z-index: 99; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: white;">
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

      var selectedFilesCert = [];
      var additionalFilesCert = [];

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
        let dateStarted = document.querySelector('.date-picker-started');
        let dateCompleted = document.querySelector('.date-picker-completed');
        let statusAttendance = document.querySelectorAll('input[name="status-attendance"]');
        let reasonForAbsence = document.getElementById('reason-for-absence-input');
        let fileUpload = document.getElementById('file-upload');

        dateStarted.disabled = true;
        dateCompleted.disabled = true;

        statusAttendance.forEach(radio => {
            radio.disabled = true;
        });

        reasonForAbsence.disabled = true;

        fileUpload.disabled = true;

        // Hide the x button
        let removeButtons = document.querySelectorAll('.remove-file');
        removeButtons.forEach(button => {
            button.style.display = 'none';
        });
      }

      disableForm();

      function enableForm() {
        let dateStarted = document.querySelector('.date-picker-started');
        let dateCompleted = document.querySelector('.date-picker-completed');
        let statusAttendance = document.querySelectorAll('input[name="status-attendance"]');
        let reasonForAbsence = document.getElementById('reason-for-absence-input');
        let fileUpload = document.getElementById('file-upload');

        dateStarted.disabled = false;
        dateCompleted.disabled = false;

        statusAttendance.forEach(radio => {
            radio.disabled = false;
        });

        reasonForAbsence.disabled = false;

        fileUpload.disabled = false;

        // Show the x button
        let removeButtons = document.querySelectorAll('.remove-file');
        removeButtons.forEach(button => {
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
        let text = ["Updating attendance, this may take a few seconds.",
            "Updating attendance, this may take a few seconds..",
            "Updating attendance, this may take a few seconds..."
        ];

        let i = 0;
        setInterval(function() {
            div.innerHTML = text[i];
            i = (i + 1) % text.length;
        }, 400);
      }

      function deletingMessage() {
        let div = document.getElementById("loading-text");
        let text = ["Deleting attendance, this may take a few seconds.",
            "Deleting attendance, this may take a few seconds..",
            "Deleting attendance, this may take a few seconds..."
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

      /* Dropdown for level
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
        }); */

      // Dropdown for status of attendance
      const dropdownStatusAttendance = document.querySelector('.create-dropdown-status-attendance');
      const listStatusAttendance = document.querySelector('.create-list-status-attendance');
      const caretStatusAttendance = document.querySelector('.caret-status-attendance');
      const selectedStatusAttendanceDisplay = document.getElementById('selected-status-attendance-display');

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
              selectedStatusAttendanceDisplay.textContent = radio.parentElement.querySelector('.text').textContent;
          });
      });

      // Set the initial value of the status of attendance
      selectedStatusAttendanceDisplay.textContent = "{{ $item->status_of_attendace }}";

      // Set the radio button to the selected status of attendance
      let selectedStatusAttendance = document.querySelector(`input[id="${selectedStatusAttendanceDisplay.textContent}"]`);
      selectedStatusAttendance.checked = true;

      // Add event listener to status of attendance dropdown, if selected on leave, show reason for absence input
      statusAttendanceRadios.forEach(radio => {
          radio.addEventListener('change', () => {
              let reasonForAbsenceInput = document.querySelector('.reason-for-absence-container');
              let attendanceProofContainer = document.querySelector('.attendance-proof-container');
              if (radio.id === 'On Leave') {
                  reasonForAbsenceInput.style.display = 'block';
                  attendanceProofContainer.style.display = 'none';
              } 
              else {
                  reasonForAbsenceInput.style.display = 'none';
                  attendanceProofContainer.style.display = 'block';
              }
          });
      });

      // Set the initial view if the status of attendance is on leave or not
      let reasonForAbsenceInput = document.querySelector('.reason-for-absence-container');
      let attendanceProofContainer = document.querySelector('.attendance-proof-container');
      if ("{{ $item->status_of_attendace }}" === 'On Leave') {
          reasonForAbsenceInput.style.display = 'block';
          attendanceProofContainer.style.display = 'none';
      } 
      else {
          reasonForAbsenceInput.style.display = 'none';
          attendanceProofContainer.style.display = 'block';
      }

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
                        let url = `/faculty-tasks/attendance/attachment/preview?id=${itemId}&fileName=${f.name}`;

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

      // Attachments variables
      if ("{{ $item->status_of_attendace }}" !== 'On Leave') {
      
        const loadingAttachmentSpecial = document.getElementById('loading-special-overlay-files');
        //const loadingAttachmentCert = document.getElementById('loading-cert-overlay-files');
        let isBothAttachmentedSettled = 0;

        // Get all the attachments for the special order
        loadingAttachmentSpecial.style.display = 'flex';
        disableButtons();
        
        fetch(`/faculty-tasks/attendance/getAttachments?id=${itemId}`)
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

              enableButtons();
          });
       }

        /* Watch for isBothAttachmentedSettled to be 2
        let interval = setInterval(() => {
            if (isBothAttachmentedSettled === 2) {
                clearInterval(interval);
                enableButtons();
            }
        }, 1000);*/

        // Form Handling

        function validateForm() {
            let dateStarted = document.querySelector('.date-picker-started').value;
            let dateCompleted = document.querySelector('.date-picker-completed').value;
            const statusAttendanceRadios = document.querySelectorAll('input[name="status-attendance"]');
            let reasonForAbsence = document.getElementById('reason-for-absence-input').value;

            if (dateStarted === '') {
                showNotification('Please enter the date started.', '#fe3232bc');
                return false;
            }

            if (dateCompleted === '') {
                showNotification('Please enter the date completed.', '#fe3232bc');
                return false;
            }

            let selectedStatusAttendance = '';
            statusAttendanceRadios.forEach(radio => {
                if (radio.checked) {
                    selectedStatusAttendance = radio.id;
                }
            });

            if (selectedStatusAttendance === '') {
                showNotification('Please select the status of attendance.', '#fe3232bc');
                return false;
            }

            if (selectedStatusAttendance === 'On Leave' && reasonForAbsence === '') {
                showNotification('Please enter the reason for absence.', '#fe3232bc');
                return false;
            }
            else if (selectedStatusAttendance !== 'On Leave' && selectedFiles.length === 0) {
                showNotification('Please upload proof of attendance.', '#fe3232bc');
                return false;
            }

            return true;
        }

        function submitForm() {
            if (!validateForm()) {
                return;
            }

            const dateStarted = document.querySelector('.date-picker-started').value;
            const dateCompleted = document.querySelector('.date-picker-completed').value;
            const statusAttendanceRadios = document.querySelectorAll('input[name="status-attendance"]');
            let selectedStatusAttendance = '';
            statusAttendanceRadios.forEach(radio => {
                if (radio.checked) {
                    selectedStatusAttendance = radio.id;
                }
            });

            const reasonForAbsence = document.getElementById('reason-for-absence-input').value;

            let formData = new FormData();
            formData.append('id', itemId);
            formData.append('date_started', dateStarted);
            formData.append('date_completed', dateCompleted);
            formData.append('status_attendance', selectedStatusAttendance.trim());
            formData.append('reason_absence', reasonForAbsence);

            // Append the files
            for (const file of selectedFiles) {
                formData.append('files[]', file);
            }

            document.getElementById('loading-overlay').style.display = 'flex';
            loadingMessage();
            disableButtons();

            let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            fetch(`/faculty-tasks/attendance/update`, {
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
                    showNotification('Attendance updated successfully.', '#28a745');
                } else {
                    showNotification('An error occurred while updating the attendance.', '#fe3232bc');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred while updating the attendance.', '#fe3232bc');
            })
            .finally(() => {
                document.getElementById('loading-overlay').style.display = 'none';
                enableButtons();
            });
        }

        function deleteResearch() {
            if (!confirm('Are you sure you want to delete this attendance?')) {
                return;
            }

            let formData = new FormData();
            formData.append('id', itemId);

            document.getElementById('loading-overlay').style.display = 'flex';
            deletingMessage();
            disableButtons();

            let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            fetch(`/faculty-tasks/attendance/delete`, {
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
                    localStorage.setItem('notif_green', 'Attendance deleted successfully.');
                    window.location.href = '/faculty-tasks/attendance';
                } 
                else {
                    showNotification('An error occurred while deleting the attendance.', '#fe3232bc');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred while deleting the attendance.', '#fe3232bc');
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