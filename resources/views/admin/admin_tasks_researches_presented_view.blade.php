@extends('layouts.default')

@section('title', 'PUPQC - View Research')

@section('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('admin/css/admin_tasks_researches_presented_view.css') }}">
@endsection

@section('body')
<div class="overlay"></div>
@include('layouts.admin_navbar')
@include('layouts.admin_selected_research_sidebar') 
@include('layouts.notification_side')

<div class="container-fluid margin">
    <div class="row">
        <div class="col-6">
            <h3 class="my-4 title">Research ({{ $category }})</h3>
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
                <label class="research-labels ms-3" for="">Title*</label>
                <input class="research-input" id="research-title-input" type="text" placeholder="Enter title" value="{{ $research->title }}">
            </div>

            <div class="d-flex flex-column mt-4">
                <label for="" class="research-labels ms-3">Authors*</label>
                <div class="drop-down create-dropdown-faculties">
                    <div class="wrapper">
                        <div class="selected">Select authors</div>
                    </div>
                    <i class="fa fa-caret-down caret2"></i>

                    <div class="list create-list-faculties">
                        @foreach ($faculties as $faculty)
                        <div class="item2">
                            <input type="checkbox" id="all" class="select-checkbox">
                            <div class="text">
                                {{ $faculty->first_name }} {{ $faculty->middle_name ? $faculty->middle_name . ' ' : '' }}{{ $faculty->last_name }}
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="d-flex flex-column mt-4">
                <label for="" class="research-labels ms-3">Conference Organizer / Host*</label>
                <input class="research-input" id="host-input" type="text" placeholder="Enter Conference Organizer / Host" 
                  value="{{ $research->host }}">
            </div>

            <div class="d-flex flex-column mt-4">
                <label for="" class="research-labels ms-3">Level*</label>
                <div class="drop-down create-dropdown-level">
                    <div class="wrapper">
                        <div class="selected">Select Level</div>
                    </div>
                    <i class="fa fa-caret-down caret-level"></i>
            
                    <div class="list create-list-level">
                        <div class="item2">
                            <input class="select-radio" type="radio" name="level" id="local">
                            <div class="text">
                                Local
                            </div>
                        </div>
                        <div class="item2">
                            <input class="select-radio" type="radio" name="level" id="national">
                            <div class="text">
                                National
                            </div>
                        </div>
                        <div class="item2">
                            <input class="select-radio" type="radio" name="level" id="international">
                            <div class="text">
                                International
                            </div>
                        </div>
                    </div>
                </div>
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
      const category = "{{ $category }}";
      const researchId = "{{ $research->id }}";
      const initalTitle = "{{ $research->title }}";

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
        let inputs = document.querySelectorAll('.research-input');
        let checkboxes = document.querySelectorAll('.select-checkbox');
        let radios = document.querySelectorAll('.select-radio');
        let fileUpload = document.getElementById('file-upload');

        inputs.forEach(input => {
            input.disabled = true;
        });

        checkboxes.forEach(checkbox => {
            checkbox.disabled = true;
        });

        radios.forEach(radio => {
            radio.disabled = true;
        });

        fileUpload.disabled = true;

        // Display none the x button
        let removeButtons = document.querySelectorAll('.remove-file');
        removeButtons.forEach(button => {
            button.style.display = 'none';
        });
      }

      disableForm();

      function enableForm() {
        let inputs = document.querySelectorAll('.research-input');
        let checkboxes = document.querySelectorAll('.select-checkbox');
        let radios = document.querySelectorAll('.select-radio');
        let fileUpload = document.getElementById('file-upload');

        inputs.forEach(input => {
            input.disabled = false;
        });

        checkboxes.forEach(checkbox => {
            checkbox.disabled = false;
        });

        radios.forEach(radio => {
            radio.disabled = false;
        });

        fileUpload.disabled = false;

        // Display the x button
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
        let text = ["Updating research, this may take a few seconds.",
            "Updating research, this may take a few seconds..",
            "Updating research, this may take a few seconds..."
        ];

        let i = 0;
        setInterval(function() {
            div.innerHTML = text[i];
            i = (i + 1) % text.length;
        }, 400);
      }

      function deletingMessage() {
        let div = document.getElementById("loading-text");
        let text = ["Deleting research, this may take a few seconds.",
            "Deleting research, this may take a few seconds..",
            "Deleting research, this may take a few seconds..."
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

      // Dropdown for level
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
        })

      // Hydrate data

      let authors = "{{ $research->authors }}".split(', ');
      let level = "{{ $research->level }}";

      // Get all the checkboxes
      let checkboxes = document.querySelectorAll('.select-checkbox');
      for (let i = 0; i < checkboxes.length; i++) {
          let checkbox = checkboxes[i];
          let authorName = checkbox.nextElementSibling.textContent.trim();

          if (authors.includes(authorName)) {
              checkbox.checked = true;
          }
      }

      // Get all the radio buttons
      let radioButtons = document.querySelectorAll('.select-radio');
      for (let i = 0; i < radioButtons.length; i++) {
          let radioButton = radioButtons[i];
          let levelName = radioButton.nextElementSibling.textContent.trim();

          if (levelName.toLowerCase() === level.toLowerCase()) {
              radioButton.checked = true;
          }
      }

      // Get all the attachments
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
        });

        // Form Handling

        function validateForm() {
            let title = document.getElementById('research-title-input').value;
            let authors = document.querySelectorAll('.select-checkbox:checked');
            let host = document.getElementById('host-input').value;
            let level = document.querySelector('.select-radio:checked');
    
            if (title === '') {
                showNotification('Please enter the title of the research.', '#fe3232bc');
                return false;
            }
    
            if (authors.length <= 0) {
                showNotification('Please select at least one author.', '#fe3232bc');
                return false;
            }
    
            if (host === '') {
                showNotification('Please enter the conference organizer / host.', '#fe3232bc');
                return false;
            }
    
            if (level === null) {
                showNotification('Please select the level of the research.', '#fe3232bc');
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

            let title = document.getElementById('research-title-input').value;
            let authors = document.querySelectorAll('.select-checkbox:checked');
            let host = document.getElementById('host-input').value;
            let level = document.querySelector('.select-radio:checked').nextElementSibling.textContent.trim();
            let files = selectedFiles;

            let authorNames = [];
            authors.forEach(author => {
                authorNames.push(author.nextElementSibling.textContent.trim());
            });

            let formData = new FormData();
            formData.append('category', category);
            formData.append('id', researchId);

            formData.append('title', title);
            formData.append('initialTitle', initalTitle); // For renaming the files
            formData.append('authors', authorNames.join(', '));
            formData.append('host', host);
            formData.append('level', level);
            
            for (const file of selectedFiles) {
                formData.append('files[]', file);
            }

            document.getElementById('loading-overlay').style.display = 'flex';
            loadingMessage();
            disableButtons();

            let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            fetch(`/admin-tasks/researches/update`, {
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
                    showNotification('Research updated successfully.', '#28a745');
                } else {
                    showNotification('An error occurred while updating the research.', '#fe3232bc');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred while updating the research.', '#fe3232bc');
            })
            .finally(() => {
                document.getElementById('loading-overlay').style.display = 'none';
                enableButtons();
            });
        }

        function deleteResearch() {
            if (!confirm('Are you sure you want to delete this research?')) {
                return;
            }

            let formData = new FormData();
            formData.append('category', category);
            formData.append('id', researchId);

            document.getElementById('loading-overlay').style.display = 'flex';
            deletingMessage();
            disableButtons();

            let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            fetch(`/admin-tasks/researches/delete`, {
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
                    window.location.href = '/admin-tasks/researches/presented';
                } 
                else {
                    showNotification('An error occurred while deleting the research.', '#fe3232bc');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred while deleting the research.', '#fe3232bc');
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