@extends('layouts.default')

@section('title', 'PUPQC - Faculty Profile')

@section('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('faculty/css/faculty_profile.css') }}">
@endsection

@section('body')
@include('layouts.faculty_navbar')
@include('layouts.notification_side')
<div class="container-fluid contain">
    <div class="profile-picture-section">
        <div class="img-container">
            <img class="img-profile"src="{{ asset('faculty/images/user-profile.png') }}" alt="">
        </div>
        <h1 class="faculty-name">{{ $data->first_name . ' ' . $data->middle_name . ' ' . $data->last_name }}</h1>
        <h1 class="department-name">{{ $data->department }}</h1> 

        <h1 class="joined-date-label">Join date:</h1> 
        <h1 class="joined-date">{{ date('F j, Y', strtotime($data->created_at)) }}</h1> 

        <h1 class="updated-date-label">Last profile update:</h1> 
        <h1 class="updated-date" style="text-align: center;">{{ date('F j, Y', strtotime($data->updated_at)) }}<br>{{ date('g:i A', strtotime($data->updated_at)) }}</h1> 
        
    </div>

    <div class="information-section">
        <button class="save-btn mx-2">Save</button>
        
        <h1 class="information-section-label">Personal Information</h1> 
        <div class="row">
            <div class="col-12 col-container">
                <div class="input-container">
                    <h1 class="input-field-label">Username</h1> 
                    <div class="input-wrapper">
                        <input type="text" class="input-field" placeholder="Enter your username"
                                data-label="username"
                                type="username"
                                id="username"
                                name="username"
                                value="{{ $data->username ? $data->username : '' }}">
                        <span class="loading-icon loading-icon-username"></span>
                    </div>
                    <span style="font-size: .7vw; display: block; margin-top: 5px;" 
                            class="error-username text-danger">@error('username') {{ $message }} @enderror</span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-container">
                <div class="input-container">
                    <h1 class="input-field-label">First Name</h1> 
                    <div class="input-wrapper">
                        <input type="text" class="input-field" placeholder="Enter your first name"
                                data-label="firstname"
                                type="firstname"
                                id="firstname"
                                name="firstname"
                                value="{{ $data->first_name ? $data->first_name : '' }}">
                    </div>
                    <span style="font-size: .7vw; display: block; margin-top: 5px;" 
                            class="error-firstname text-danger">@error('firstname') {{ $message }} @enderror</span>
                </div>

                <div class="input-container next-element">
                    <h1 class="input-field-label">Middle Name</h1> 
                    <div class="input-wrapper">
                        <input type="text" class="input-field" placeholder="Enter your middle name"
                                data-label="middlename"
                                type="middlename"
                                id="middlename"
                                name="middlename"
                                value="{{ $data->middle_name ? $data->middle_name : '' }}">
                    </div>
                    <span style="font-size: .7vw; display: block; margin-top: 5px;" 
                            class="error-middlename text-danger">@error('middlename') {{ $message }} @enderror</span>
                </div>

                <div class="input-container next-element">
                    <h1 class="input-field-label">Last Name</h1> 
                    <div class="input-wrapper">
                        <input type="text" class="input-field" placeholder="Enter your last name"
                                data-label="lastname"
                                type="lastname"
                                id="lastname"
                                name="lastname"
                                value="{{ $data->last_name ? $data->last_name : '' }}">
                    </div>
                    <span style="font-size: .7vw; display: block; margin-top: 5px;" 
                            class="error-lastname text-danger">@error('lastname') {{ $message }} @enderror</span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-container">
                <div class="input-container">
                    <h1 class="input-field-label">Email</h1> 
                    <div class="input-wrapper">
                        <input type="text" class="input-field" placeholder="Enter your email address"
                                data-label="email"
                                type="email"
                                id="email"
                                name="email"
                                value="{{ $data->email ? $data->email : '' }}">
                        <span class="loading-icon loading-icon-email"></span>
                    </div>
                    <span style="font-size: .7vw; display: block; margin-top: 5px;" 
                                class="error-email text-danger">@error('email') {{ $message }} @enderror</span>
                </div>

                <div class="input-container next-element">
                    <h1 class="input-field-label">Contact number</h1> 
                    <div class="input-wrapper">
                        <input type="text" class="input-field" placeholder="Enter your contact number"
                                data-label="contactnumber"
                                type="contactnumber"
                                id="contactnumber"
                                name="contactnumber"
                                value="{{ $data->contact_number ? $data->contact_number : '' }}">
                        <span class="loading-icon loading-icon-contactnumber"></span>
                    </div>
                    <span style="font-size: .7vw; display: block; margin-top: 5px;" 
                                class="error-contactnumber text-danger">@error('contactnumber') {{ $message }} @enderror</span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-container">
                <div class="input-container">
                    <h1 class="input-field-label">Gender</h1> 
                    <div class="drop-down mx-2">
                        <div class="wrapper">
                            <div class="selected" id="selected-gender">{{ $data->gender ? $data->gender : 'Choose gender' }}</div>
                        </div>
                        <i class="fa fa-caret-down"></i>

                        <div class="list">
                            <div class="item">
                                <div class="text">Male</div>
                            </div>
                            <div class="item">
                                <div class="text">Female</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-container change-password-container">
                <h1 class="change-password-label">Change password:</h1> 
                <h1 class="password-inform-label">You can ignore this if you don't want to change your password</h1> 
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-container">
                <div class="input-container">
                    <h1 class="input-field-label">Password</h1> 
                    <div class="input-wrapper">
                    <input class="input-field" placeholder="Enter your password"
                            data-label="password"
                            type="password"
                            id="password"
                            name="password">
                        <span class="loading-icon loading-icon-password"></span>
                    </div>
                    <span style="font-size: .7vw; display: block; margin-top: 5px;" 
                                class="error-password text-danger">@error('password') {{ $message }} @enderror</span>
                </div>
                <div class="input-container next-element">
                    <h1 class="input-field-label">Confirm Password</h1>
                    <div class="input-wrapper"> 
                    <input class="input-field" placeholder="Re-enter your password"
                            data-label="password_confirmation"
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation">
                        <span class="loading-icon loading-icon-password_confirmation"></span>
                    </div>
                    <span style="font-size: .7vw; display: block; margin-top: 5px;" 
                                class="error-password_confirmation text-danger">@error('password') {{ $message }} @enderror</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const savebtn = document.querySelector('.save-btn');
    let isValid = true;

    // When save button is hovered and not valid yet.
    savebtn.addEventListener('mouseover', (event) => {
        if (isValid == false) {
            showNotification("Please complete all validations first.", '#fe3232bc');
        }
    });

    // Save button is valid and clicked.
    savebtn.addEventListener('click', () => {
        if (savebtn.classList.contains('save-btn-disabled') || 
            savebtn.classList.contains('save-btn-saving')) { // if disabled or still saving
            return;
        }
        document.querySelector('.save-btn').classList.add('save-btn-saving');
        document.querySelector('.save-btn').innerHTML = "Saving.."

        let data = {
            username: document.querySelector('#username').value,
            firstname: document.querySelector('#firstname').value,
            middlename: document.querySelector('#middlename').value,
            lastname: document.querySelector('#lastname').value,
            email: document.querySelector('#email').value,
            contactnumber: document.querySelector('#contactnumber').value,
            gender: document.querySelector('#selected-gender').innerHTML,
            password: document.querySelector('#password').value
        };

        // Update data requests dynamically to server.
        let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        fetch('/faculty-profile/save', {
            method: 'POST',
            credentials: "same-origin",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json, text-plain, */*",
                "X-Requested-With": "XMLHttpRequest",
                "X-CSRF-TOKEN": token
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(items => {
            showNotification("Your profile has been saved.", '#1dad3cbc');

            let updatedAt = new Date(items.updated_at);

            // Format the date and time.
            let dateOptions = { year: 'numeric', month: 'long', day: 'numeric' };
            let timeOptions = { hour: 'numeric', minute: '2-digit' };
            let formattedDate = updatedAt.toLocaleDateString('en-US', dateOptions);
            let formattedTime = updatedAt.toLocaleTimeString('en-US', timeOptions);

            // Update profile datas that needs to be updated.
            if (items.middle_name) {
                document.querySelector('.faculty-name').innerHTML = items.first_name + " " + items.middle_name + " " + items.last_name;
            }
            else {
                document.querySelector('.faculty-name').innerHTML = items.first_name + " " + items.last_name;
            }

            if (items.first_name === null) {
                document.querySelector('.faculty-name').innerHTML = '';
            }
  
            document.querySelector('.updated-date').innerHTML = `${formattedDate}<br>${formattedTime}`;
            document.querySelector('.save-btn').classList.remove('save-btn-saving');
            document.querySelector('.save-btn').innerHTML = "Save"
        })
        .catch(error => {
            // Handle the error if occurs.
            console.error('An error occurred:', error);
            
            showNotification("An error occurred, try again later.", '#fe3232bc');
            document.querySelector('.save-btn').classList.remove('save-btn-saving');
            document.querySelector('.save-btn').innerHTML = "Save"
        });
    });

    // Gender dropdown scripts.
    const dropdown = document.querySelector('.drop-down');
    const list = document.querySelector('.list');
    const selected = document.querySelector('.selected');
    const caret = document.querySelector('.fa-caret-down');

    dropdown.addEventListener('click', () => {
        list.classList.toggle('show');
        caret.classList.toggle('fa-rotate');
    });

    list.addEventListener('click', (e) => {
        const text = e.target.querySelector('.text');

        selected.innerHTML = text.innerHTML;
    });

    // Old Full Name // To prevent faculty user to remove its name after setting it once.
    let oldFirstName = document.querySelector('#firstname').value.trim();
    let oldMiddleName = document.querySelector('#middlename').value.trim();
    let oldLastName = document.querySelector('#lastname').value.trim();

    // Handle input field click, type.
    function handleclick(Field, Data, inputField) {
        if (!Field || !inputField) { // Checking
            return;
        }

        Data = Data.trim();

        let Message = "";
        let currentFirstName = document.querySelector('#firstname').value.trim();
        let currentMiddleName = document.querySelector('#middlename').value.trim();
        let currentLastName = document.querySelector('#lastname').value.trim();

        let isAnyOldNameFieldFilledOut  = oldFirstName || oldMiddleName || oldLastName;
        let isAnyCurrentNameFieldFilledOut  = currentFirstName || currentMiddleName || currentLastName;

        if (isAnyOldNameFieldFilledOut) { // if fresh data e.g (no full name has ever set)
            Message = "activateFullNameValidation";
        }
        else { // if full name has been set once
            if (isAnyCurrentNameFieldFilledOut) { // check if any of them is filled out
                Message = "activateFullNameValidation";
            }
            else { // if any one them is not filled out, remove validation error since its optional to set fullname
                document.querySelector('.error-firstname').innerHTML = "";
                document.querySelector('.error-lastname').innerHTML = "";
            }
        }

        // Remove validation error if the faculty user starts typing his name and last name
        if (currentFirstName) {
            document.querySelector('.error-firstname').innerHTML = "";
        }
        if (currentLastName) {
            document.querySelector('.error-firstname').innerHTML = "";
        }
        
        let errorElement = document.querySelector(`.error-${Field}`);
        
        if (document.querySelector(`.loading-icon-${Field}`)) { // if loading icon is present in the input field
            let loadingIcon = document.querySelector(`.loading-icon-${Field}`);
            loadingIcon.style.display = 'inline-block';
        }
        
        /*if (Data == '' || Data == '{{ $data->username }}') { // if data is blank, then do not run the loading icon
            if (document.querySelector(`.loading-icon-${Field}`)) {
                let loadingIcon = document.querySelector(`.loading-icon-${Field}`);
                errorElement.innerHTML = '';
                loadingIcon.style.display = 'none';
            }
        }*/

        // If confirming password, pass the password field and its data to check in server side.
        if (Field == 'password_confirmation') {
            Field = 'password';
            Data = document.querySelector('#password').value
        }

        // Send a request to your server with the data from the input field for validation
        let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        fetch('/faculty-profile/validate-fields', {
            method: 'POST',
            credentials: "same-origin",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json, text-plain, */*",
                "X-Requested-With": "XMLHttpRequest",
                "X-CSRF-TOKEN": token
            },
            body: JSON.stringify({
                field: Field,
                data: Data,
                requestMessage: Message,
                firstname: currentFirstName,
                lastname: currentLastName,
                password_confirmation: document.querySelector('#password_confirmation').value
            })
        })
        .then(response => response.json())
        .then(data => {
            // Hide loading icon if loading icon is present in the input field
            if (document.querySelector(`.loading-icon-${Field}`)) {
                let loadingIcon = document.querySelector(`.loading-icon-${Field}`);
                loadingIcon.style.display = 'none';
            }

            // if there is validation errors
            if (data.errors.length != 0) {
                isValid = false;

                // Check all the fields if validation error have occured.
                let hasError = false;
                document.querySelectorAll('.text-danger').forEach(function (element) {
                    if (element.textContent.trim() !== '') {
                        hasError = true;
                    }
                });

                // If has error, then disable the button.
                if (hasError) {
                    document.querySelector('.save-btn').classList.add('save-btn-disabled');
                }
                
                // Display validation error for the field
                for (const [key, value] of Object.entries(data.errors)) {
                    let errorElement = document.querySelector(`.error-${key}`);
                    errorElement.innerHTML = value;
                }
            } 
            else {
                // Clear the error message for the specified field
                isValid = true;
                let errorElement = document.querySelector(`.error-${data.field}`);
                errorElement.innerHTML = '';
                
                if (document.querySelector('#password').value == document.querySelector('#password_confirmation').value) {
                    document.querySelector('.loading-icon-password_confirmation').style.display = 'none';
                    document.querySelector('.error-password_confirmation').innerHTML = '';
                }

                // Check all the fields if validation error have occured.
                let hasError = false;
                document.querySelectorAll('.text-danger').forEach(function (element) {
                    if (element.textContent.trim() !== '') {
                        hasError = true;
                    }
                });

                // If no errors, enable the save button.
                if (!hasError) { 
                    document.querySelector('.save-btn').classList.remove('save-btn-disabled');
                }
            }

        })
        .catch(error => {
            console.log(error);
            showNotification("Error occured, try again later.", '#fe3232bc');
        });
    }

    // Gender dropdown event listener.
    const items = document.querySelectorAll('.item');

    items.forEach(item => {
        item.addEventListener('click', () => {
            selected.textContent = item.querySelector('.text').textContent;
            console.log(selected.textContent);
        });
    });

    // Debounce function
    function debounce(func, wait) {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), wait);
        };
    }

    // Debounced handleclick function
    const debouncedHandleclick = debounce(handleclick, 300);

    // Input fields event listener.
    const inputs = document.querySelectorAll('.input-field');

    inputs.forEach(input => {
        input.addEventListener('input', event => {
            document.querySelector('.save-btn').classList.add('save-btn-disabled'); // disable first
            const field = event.target.getAttribute('data-label');
            const data = event.target.value;
            debouncedHandleclick(field, data, event.target);
        });

        /* NOT SURE IF WORKING: Listen for change event to trigger validation when user selects value from autocomplete suggestions
        input.addEventListener('change', event => {
            const field = event.target.getAttribute('data-label');
            const data = event.target.value;
            debouncedHandleclick(field, data, event.target);
        }); */
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script>
@endsection