@extends('layouts.default')

@section('title', 'PUPQC - View Attendance')

@section('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('admin/css/admin_tasks_attendance_view.css') }}">
@endsection

@section('body')
<div class="overlay"></div>
@include('layouts.admin_navbar')
@include('layouts.admin_selected_attendance_sidebar') 
@include('layouts.notification_side')

<div class="container-fluid margin">
    <div class="row">
        <div class="col-6">
            <h3 class="my-4 title attendance-title">Faculty Attendance ({{ $item->status }})</h3>
        </div>
        <div class="col-6 drop-down-container">
            <button class="my-4 create-btn approve-task-btn" style="background-color: rgba(54, 187, 41, 0.823)" onclick="Approve()">Approve</button>
            <button class="my-4 create-btn reject-task-btn" onclick="Reject()">Reject</button>
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
                  <label for="" class="research-labels" style="margin-left: 0% !important">Proof of Attendance*</label>
                  <label for="" class="mb-2" style="margin-left:0% !important; font-size: 13px; color:rgb(232, 79, 79);">Selfie photos are not allowed as supporting document.</label>
                  <div style="display: flex; flex-direction: row">
                      <div style="height: 200px;">
                          
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

            <div id="loading-overlay" class="loading-save-task" style="display: none; justify-content: center; height: 90vh; align-items: center; border-radius: 25px; z-index: 99; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: white;">
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
      const initialName = "{{ $item->name_of_activity }}";

        const approveButton = document.querySelector('.approve-task-btn');
        const rejectButton = document.querySelector('.reject-task-btn');
      const loadingAttachment = document.querySelector('.loading-uploaded-files')

      var selectedFiles = [];
      var additionalFiles = [];

      function disableButtons() {
        approveButton.disabled = true;
        rejectButton.disabled = true;
      }

      function enableButtons() {
        approveButton.disabled = false;
        rejectButton.disabled = false;
      }

      // Check if status of attendance is not on leave
        if ("{{ $item->status_of_attendace }}" !== 'On Leave') {
            // Disable the reason for absence input
            disableButtons();
        }

      document.querySelectorAll('.research-input').forEach(input => {
        input.disabled = true;
      })

      function disableForm() {
        let dateStarted = document.querySelector('.date-picker-started');
        let dateCompleted = document.querySelector('.date-picker-completed');
        let statusAttendance = document.querySelectorAll('input[name="status-attendance"]');
        let reasonForAbsence = document.getElementById('reason-for-absence-input');

        dateStarted.disabled = true;
        dateCompleted.disabled = true;

        statusAttendance.forEach(radio => {
            radio.disabled = true;
        });

        reasonForAbsence.disabled = true;

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

        dateStarted.disabled = false;
        dateCompleted.disabled = false;

        statusAttendance.forEach(radio => {
            radio.disabled = false;
        });

        reasonForAbsence.disabled = false;

        // Show the x button
        let removeButtons = document.querySelectorAll('.remove-file');
        removeButtons.forEach(button => {
            button.style.display = 'block';
        });
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
      });*/

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
                        let url = `/admin-tasks/attendance/attachment/preview?id=${itemId}&fileName=${f.name}`;

                        // Open a new tab with the loading page
                        var newTab = window.open('/admin-tasks/get-task/preview-file-selected/loading');

                        // Fetch the URL for the file
                        fetch(url)
                            .then(response => response.json())
                            .then(data => {
                                console.log(data);
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

      // Attachments variables
      if ("{{ $item->status_of_attendace }}" !== 'On Leave') {
        const loadingAttachmentSpecial = document.getElementById('loading-special-overlay-files');
        //const loadingAttachmentCert = document.getElementById('loading-cert-overlay-files');
        let isBothAttachmentedSettled = 0;

        // Get all the attachments for the special order
        loadingAttachmentSpecial.style.display = 'flex';
        disableButtons();
        
        fetch(`/admin-tasks/attendance/getAttachments?id=${itemId}`)
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

        // Form Handling

        function validateForm() {
          let name = document.getElementById('name-input').value;
          let venue = document.getElementById('venue-input').value;
          let host = document.getElementById('host-input').value;
          let date = document.getElementById('date-picker').value;

          if (name.trim() === '') {
              showNotification('Please enter the name of the activity.', '#fe3232bc');
              return false;
          }

          if (venue.trim() === '') {
              showNotification('Please enter the venue.', '#fe3232bc');
              return false;
          }

          if (host.trim() === '') {
              showNotification('Please enter the host.', '#fe3232bc');
              return false;
          }

          if (date.trim() === '') {
              showNotification('Please enter the date conducted.', '#fe3232bc');
              return false;
          }

          if (selectedFiles.length <= 0) {
              showNotification('Please upload the S.O and certificates.', '#fe3232bc');
              return false;
          }

          return true;
        }

        function submitForm() {
            if (!validateForm()) {
                return;
            }

            let name = document.getElementById('name-input').value;
            let venue = document.getElementById('venue-input').value;
            let host = document.getElementById('host-input').value;
            let date = document.getElementById('date-picker').value;

            let formData = new FormData();
            formData.append('id', itemId);

            formData.append('name', name);
            formData.append('initialName', initialName);

            formData.append('venue', venue);
            formData.append('host', host);
            formData.append('date', date);

            for (const file of selectedFiles) {
                formData.append('files[]', file);
            }

            document.getElementById('loading-overlay').style.display = 'flex';
            loadingMessage();
            disableButtons();

            let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            fetch(`/admin-tasks/attendance/update`, {
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

        function deleteItem() {
            if (!confirm('Are you sure you want to delete this attendance?')) {
                return;
            }

            let formData = new FormData();
            formData.append('id', itemId);

            document.getElementById('loading-overlay').style.display = 'flex';
            deletingMessage();
            disableButtons();

            let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            fetch(`/admin-tasks/attendance/delete`, {
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
                    window.location.href = '/admin-tasks/attendance';
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

        function Approve() {
            if (!confirm('Are you sure you want to approve this attendance?')) {
                return;
            }

            let formData = new FormData();
            formData.append('id', itemId);

            document.getElementById('loading-overlay').style.display = 'flex';
            loadingMessage();
            disableButtons();

            let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            fetch(`/admin-tasks/attendance/approve`, {
                headers: {
                    "Accept": "application/json, text-plain, */*",
                    "X-Requested-With": "XMLXMLHttpRequest",
                    "X-CSRF-TOKEN": token
                },
                method: 'POST',
                credentials: "same-origin",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.message) {
                    showNotification('Attendance approved successfully.', '#28a745');
                    document.querySelector('.attendance-title').textContent = 'Faculty Attendance (Approved)';
                } 
                else {
                    showNotification('An error occurred while approving the attendance.', '#fe3232bc');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred while approving the attendance.', '#fe3232bc');
            })
            .finally(() => {
                document.getElementById('loading-overlay').style.display = 'none';
                enableButtons();
            });
        }

        function Reject() {
            if (!confirm('Are you sure you want to reject this attendance?')) {
                return;
            }

            let formData = new FormData();
            formData.append('id', itemId);

            document.getElementById('loading-overlay').style.display = 'flex';
            loadingMessage();
            disableButtons();

            let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            fetch(`/admin-tasks/attendance/reject`, {
                headers: {
                    "Accept": "application/json, text-plain, */*",
                    "X-Requested-With": "XMLXMLHttpRequest",
                    "X-CSRF-TOKEN": token
                },
                method: 'POST',
                credentials: "same-origin",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.message) {
                    showNotification('Attendance rejected successfully.', '#28a745');
                    document.querySelector('.attendance-title').textContent = 'Faculty Attendance (Rejected)';
                } 
                else {
                    showNotification('An error occurred while rejecting the attendance.', '#fe3232bc');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred while rejecting the attendance.', '#fe3232bc');
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