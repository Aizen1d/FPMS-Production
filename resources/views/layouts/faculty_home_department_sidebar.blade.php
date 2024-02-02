<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" type="text/css" href="{{ asset('faculty/css/faculty_home_department_sidebar.css') }}">
    </head>
<body>
    <nav id="sidebarMenu" class="collapse d-lg-block sidebar">
        <div class="position-sticky">
            <div class="list-group list-group-flush mx-3">
                <a href="{{ route('faculty-home/department/assigned-tasks') }}" class="main list-group-item list-group-item-action py-2 ripple bg 
                        {{ (request()->is('faculty-home/department/assigned-tasks')) ? 'active' : '' }}">
                    <span><b>All Department Tasks</b></span>
                </a>
                <div id="sub-task">
                    <a href="{{ route('faculty-home/department/assigned-tasks/completed') }}?category=completed" class="list-group-item list-group-item-action py-2 sub ripple bg
                    {{ (request()->is('faculty-home/department/assigned-tasks/completed')) ? 'active' : '' }}">
                        <span>Completed</span>
                    </a>
                    <a href="{{ route('faculty-home/department/assigned-tasks/late-completed') }}?category=late-completed" class="list-group-item list-group-item-action py-2 sub ripple bg
                    {{ (request()->is('faculty-home/department/assigned-tasks/late-completed')) ? 'active' : '' }}">
                        <span>Late Completed</span>
                    </a>
                    <a href="{{ route('faculty-home/department/assigned-tasks/ongoing') }}?category=ongoing" class="list-group-item list-group-item-action py-2 sub ripple bg
                    {{ (request()->is('faculty-home/department/assigned-tasks/ongoing')) ? 'active' : '' }}">
                        <span>Ongoing</span>
                    </a>
                    <a href="{{ route('faculty-home/department/assigned-tasks/missing') }}?category=missing" class="list-group-item list-group-item-action py-2 sub ripple bg
                    {{ (request()->is('faculty-home/department/assigned-tasks/missing')) ? 'active' : '' }}">
                        <span>Missing</span>
                    </a>
                </div>
                <a href="{{ route('faculty-home/department/members') }}" class="main list-group-item list-group-item-action py-2 ripple bg 
                        {{ (request()->is('faculty-home/department/members')) ? 'active' : '' }}">
                    <span><b>Members</b></span>
                </a>
                <a href="#" data-bs-toggle="modal" data-bs-target="#leaveDepartmentModal" class="main list-group-item list-group-item-action py-2 ripple bg 
                        {{ (request()->is('faculty-home/department/leave')) ? 'active' : '' }}">
                    <span><b>Leave Program</b></span>
                </a>
            </div>
        </div>
    </nav>

    <div class="modal fade" id="leaveDepartmentModal" tabindex="-1" aria-labelledby="leaveDepartmentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #8f2121;">
                    <h5 class="modal-title" id="leaveDepartmentModalLabel" style="color:white">Leave Program</h5>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="password" class="col-form-label">Enter your password to confirm:</label>
                        <input type="password" class="form-control" id="password" name="password">
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary close-btn" style="border: none;" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary confirm-btn" type="button" style="background-color: #3cb546; border: none;" onclick="submitLeaveDepartmentForm()">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let isHotkeySubmitting = false;
        let passwordInput = document.querySelector('#password');

        // Add an event listener to the password input field
        passwordInput.addEventListener('keydown', function(event) {
            if (isHotkeySubmitting === false) {
                if (event.key === 'Enter') {
                    submitLeaveDepartmentForm();
                }
            }
        });

        function submitLeaveDepartmentForm() {
            isHotkeySubmitting = true;

            document.querySelector(".close-btn").style.backgroundColor = '#b5b5b5';
            document.querySelector(".confirm-btn").style.backgroundColor = '#b5b5b5';

            let password = document.querySelector('#password').value.trim();
            console.log(password);

            // Create a FormData object and append the password
            let formData = new FormData();
            formData.append('password', password);

            // Send an AJAX request to the server
            fetch('/faculty-home/department/leave', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
               if (data.status === 'error') {
                  // Check if the alert element already exists
                    let existingAlert = document.querySelector('.alert.alert-danger');

                    // If the alert element does not exist, create and add it
                    if (!existingAlert) {
                        let alert = document.createElement('div');
                        alert.className = 'alert alert-danger';
                        alert.style = 'margin-bottom: -3%; text-align: center;'
                        alert.textContent = data.message;

                        document.querySelector('.modal-header').insertAdjacentElement('afterend', alert);
                    }

                    document.querySelector(".close-btn").style.backgroundColor = '#6C757D';
                    document.querySelector(".confirm-btn").style.backgroundColor = '#3cb546';
                    isHotkeySubmitting = false;
                }
                else if (data.status === 'success') {
                    window.location.href = '/faculty-home';
                }
            });
        }
    </script>
</body>
</html>
