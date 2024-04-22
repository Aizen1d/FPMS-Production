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
            <h3 class="my-4 title">Attendance</h3>
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
                <label class="research-labels ms-3" for="">Name of Activity*</label>
                <input class="research-input" id="name-input" type="text" placeholder="Enter name of activity" value="{{ $item->name_of_activity }}">
            </div>

            <div class="d-flex flex-column mt-4">
              <label class="research-labels ms-3" for="">Venue*</label>
              <input class="research-input" id="venue-input" type="text" placeholder="Enter venue" value="{{ $item->venue }}">
            </div>

            <div class="d-flex flex-column mt-4">
              <label class="research-labels ms-3" for="">Host*</label>
              <input class="research-input" id="host-input" type="text" placeholder="Enter host" value="{{ $item->host }}">
            </div>

            <div class="d-flex flex-column mt-4">
              <label for="" class="research-labels ms-3">Date Conducted*</label>
              <input class="ms-3" type="date" id="date-picker" min="1997-01-01" max="2030-01-01" value="{{ $date_conducted }}">
            </div>

            <div class="d-flex flex-column mt-4 ms-2" style="margin-left: 1% !important">
              <label for="" class="research-labels">S.O and Certificates*</label>

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
              
                  <div id="loading-overlay-files" class="loading-uploaded-files" style="display: none; justify-content: center; align-items: center; border-radius: 25px; z-index: 99; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: white;">
                    <div style="display: flex; flex-direction: column; align-items: center;">
                      <div class="spinner-border text-dark" role="status">
                          <span class="sr-only">Loading...</span>
                      </div>
                      <div id="loading-uploaded-text" style="margin-top: 3px;">Retrieving uploaded files..</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div id="loading-overlay" class="loading-save-task" style="display: none; justify-content: center; align-items: center; border-radius: 25px; z-index: 99; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: white;">
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
        let name = document.getElementById('name-input');
        let venue = document.getElementById('venue-input');
        let host = document.getElementById('host-input');
        let date = document.getElementById('date-picker');
        let fileUpload = document.getElementById('file-upload');

        name.disabled = true;
        venue.disabled = true;
        host.disabled = true;
        date.disabled = true;
        fileUpload.disabled = true;

        let removeButtons = document.querySelectorAll('.remove-file');
        removeButtons.forEach(button => {
            button.style.display = 'none';
        });
      }

      disableForm();

      function enableForm() {
        let name = document.getElementById('name-input');
        let venue = document.getElementById('venue-input');
        let host = document.getElementById('host-input');
        let date = document.getElementById('date-picker');
        let fileUpload = document.getElementById('file-upload');

        name.disabled = false;
        venue.disabled = false;
        host.disabled = false;
        date.disabled = false;
        fileUpload.disabled = false;

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
                        let url = `/admin-tasks/attendance/attachment/preview?id=${itemId}&fileName=${f.name}`;

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
        })

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

      // Get all the attachments
      loadingAttachment.style.display = 'flex';
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

            loadingAttachment.style.display = 'none';
            enableButtons();
        });

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
    </script>


    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script>
    @endsection