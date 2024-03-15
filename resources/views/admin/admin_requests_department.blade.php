@extends('layouts.default')

@section('title', 'PUPQC - Admin Requests Department')

@section('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('admin/css/admin_requests_department.css') }}">
@endsection

@section('body')
<div class="overlay"></div>
@include('layouts.admin_navbar')
@include('layouts.admin_requests_sidebar')
@include('layouts.notification_side')
  
<div class="container-fluid margin">
    <div class="row">
        <div class="col-4">
            <h1 class="my-4 title">Requests - Program</h1>
        </div>
        <div class="col-2 pages">
            {{ $items->links()  }}
        </div>
        <div class="col-6 drop-down-container">
            <button class="accept-all-btn mx-2"><i class="fa fa-check mx-2"></i>ALL</button>
            <button class="reject-all-btn mx-2"><i class="fa fa-times mx-2"></i>ALL</button>
            <input type="text" class="search-input mx-5" id="search-input" placeholder="Search fullname...">
            <div id="search-results"></div>
        </div>
    </div>

    <div class="container-fluid item-list" style="position: relative;">
        <div class="row">
            <div class="col-2">
                <h5 class="my-3 column-name" style="z-index: 100; position: relative;">Username</h5>
            </div>
            <div class="col-3">
                <h5 class="my-3 column-name" style="z-index: 100; position: relative;">Fullname</h5>
            </div>
            <div class="col-2">
                <h5 class="my-3 column-name" style="z-index: 100; position: relative;">Program Request</h5>
            </div>
            <div class="col-2">
                <h5 class="my-3 column-name" style="z-index: 100; position: relative;">Request Date</h5>
            </div>
            <div class="col-3">
                <h5 class="my-3 column-name" style="z-index: 100; position: relative;">Actions</h5>
            </div>
        </div>

        @foreach ($items as $item)
            <div class="row item-row">
                <div class="col-2 column-1">
                    <h5 class="item-row-content my-2 column-0-text">
                        {{ $item->getFacultyForeign->username }}
                    </h5>
                </div>
                <div class="col-3 column-1">
                    <h5 class="item-row-content my-2 column-1-text">
                        {{ $item->getFacultyForeign->first_name }} {{ $item->getFacultyForeign->middle_name }} {{ $item->getFacultyForeign->last_name }}
                    </h5>
                </div>
                <div class="col-2 column-2">
                    <h5 class="item-row-content px-3 my-2 column-2-text">{{ $item->department_name}}</h5>
                </div>
                <div class="col-2 column-3">
                    <h5 class="item-row-content my-2 column-3-text">{{ date('F j, Y', strtotime($item->created_at)) }}<br>{{ date('g:i A', strtotime($item->created_at)) }}</h5>
                </div>
                <div class="col-3 column-4 actions-btn">
                    <button class="accept-btn mx-2" data-faculty-username="{{ $item->getFacultyForeign->username }}" 
                                                    data-department-name="{{ $item->department_name }}">
                        <i class="fa fa-check"></i>
                    </button>
                    <button class="reject-btn mx-2" data-faculty-username="{{ $item->getFacultyForeign->username }}" 
                                                    data-department-name="{{ $item->department_name }}">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
            </div>
        @endforeach

        <div id="loading-overlay" style="display: none; border-radius: 25px; z-index: 99; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: white; justify-content: center; align-items: center;">
            <!-- Add your loading spinner or other visual indicator here -->
            <div class="spinner-border text-dark" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
    </div>
        
    </div>

<script>
    const searchInput = document.querySelector('#search-input');
    const itemRows = document.querySelectorAll('.item-row');

    let items = [];

    function debounce(func, wait) {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), wait);
        }
    }

    const debouncedInputHandler = debounce(function(event) {
        // Set a timeout to show the loading overlay after a delay
        //loadingOverlayTimeout = setTimeout(() => {
            document.getElementById('loading-overlay').style.display = 'flex';
        //}, 500);

        const query = encodeURIComponent(event.target.value);
        fetch(`/admin-requests/department/search?query=${query}`)
            .then(response => response.json())
            .then(newItems => {
                items = newItems;
                requestAnimationFrame(renderItems);

                // Clear the timeout and hide the loading overlay
                //clearTimeout(loadingOverlayTimeout);
                document.getElementById('loading-overlay').style.display = 'none';
            });
    }, 50);

    searchInput.addEventListener('input', debouncedInputHandler);

    function renderItems() {
        //console.log('renderItems called');
        //console.log('items:', items);
        // Hide all rows
        itemRows.forEach(row => row.style.display = 'none');

        // Show only rows that match fetched data
        for (let i = 0; i < items.length; i++) {
            const item = items[i];
            const itemRow = itemRows[i];
            if (itemRow) {
                itemRow.style.display = '';
                const column0Text = itemRow.querySelector('.column-0-text');
                const column1Text = itemRow.querySelector('.column-1-text');
                const column2Text = itemRow.querySelector('.column-2-text');
                const column3Text = itemRow.querySelector('.column-3-text');

                column0Text.textContent = item.username;
                column1Text.textContent = item.fullname;
                column2Text.textContent = item.departmentRequest;
                column3Text.innerHTML = `${item.request_date_formatted}<br>${item.request_date_time}`;
            }
        }
    }

    // Action buttons
    let acceptAllBtn = document.querySelector('.accept-all-btn');
    let rejectAllBtn = document.querySelector('.reject-all-btn');
    function attachEventListeners() {
        const acceptButtons = document.querySelectorAll('.accept-btn');
        const rejectButtons = document.querySelectorAll('.reject-btn');

        acceptButtons.forEach(button => {
            button.addEventListener('click', () => {
                const row = button.closest('.row');
                const Username = row.querySelector('.column-0-text').innerHTML.trim();
                const DepartmentToJoin = row.querySelector('.column-2-text').innerHTML.trim();
                console.log(Username + " " + DepartmentToJoin);
                handleClick('/admin-requests/department/accept', 'accept', Username, DepartmentToJoin);
            });
        });

        rejectButtons.forEach(button => {
            button.addEventListener('click', () => {
                const row = button.closest('.row');
                const Username = row.querySelector('.column-0-text').innerHTML.trim();
                const DepartmentToJoin = row.querySelector('.column-2-text').innerHTML.trim();
                console.log(Username + " " + DepartmentToJoin);
                handleClick('/admin-requests/department/reject', 'reject', Username, DepartmentToJoin);
            });
        });

        // Check if there are any rows in the table
        let rows = document.querySelectorAll('.item-row');
        if (rows.length === 0) {
            // If there are no rows, disable the "Accept All" and "Reject All" buttons
            acceptAllBtn.disabled = true;
            rejectAllBtn.disabled = true;
        } else {
            // If there are rows, enable the "Accept All" and "Reject All" buttons
            acceptAllBtn.disabled = false;
            rejectAllBtn.disabled = false;

            acceptAllBtn.addEventListener('click', function() {
                let Username = 'acceptAll';
                let DepartmentToJoin = 'SpecificAll';
                let AllData = [];

                document.querySelectorAll('.item-row').forEach(function(row) {
                    let username = row.querySelector('.column-0-text').innerHTML.trim();
                    let departmentToJoin = row.querySelector('.column-2-text').innerHTML.trim();

                    AllData.push({
                        username: username,
                        departmentToJoin: departmentToJoin
                    });
                });

                handleClick('/admin-requests/department/acceptAll', 'acceptAll', Username, DepartmentToJoin, AllData);
            });

            rejectAllBtn.addEventListener('click', function() {
                let Username = 'rejectAll';
                let DepartmentToJoin = 'None';
                let AllData = [];

                document.querySelectorAll('.item-row').forEach(function(row) {
                    let username = row.querySelector('.column-0-text').innerHTML.trim();
                    let departmentToJoin = row.querySelector('.column-2-text').innerHTML.trim();

                    AllData.push({
                        username: username,
                        departmentToJoin: departmentToJoin
                    });
                });

                console.log(AllData);

                handleClick('/admin-requests/department/rejectAll', 'rejectAll', Username, DepartmentToJoin, AllData);
            });
        }
    }

    function handleClick(url, buttonClicked, Username, DepartmentToJoin, AllData) {
        if (!url || !buttonClicked || !Username || !DepartmentToJoin) { // Check if row is present
            return;
        }
        acceptAllBtn.disabled = false;
        rejectAllBtn.disabled = false;

        let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        fetch(url, {
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json, text-plain, */*",
                "X-Requested-With": "XMLHttpRequest",
                "X-CSRF-TOKEN": token
            },
            method: 'post',
            credentials: "same-origin",
            body: JSON.stringify({
                username: Username,
                departmentToJoin: DepartmentToJoin,
                allData: AllData
            })
        })
        .then(response => response.json())
        .then(newItems => {
            // Update UI table rows
            items = newItems;
            requestAnimationFrame(renderItems);

            // Notify the admin about the action made
            if (buttonClicked === 'accept') {
                showNotification(Username + "'s department request is accepted.", '#1dad3cbc');
            } 
            else if (buttonClicked === 'reject') {
                showNotification(Username + "'s department request is rejected.", '#fe3232bc');
            }
            if (items) {
                if (buttonClicked === 'acceptAll') {
                    showNotification("All requests have been accepted.", '#1dad3cbc');
                    acceptAllBtn.disabled = true;
                    rejectAllBtn.disabled = true;
                }
                else if (buttonClicked === 'rejectAll') {
                    showNotification("All requests have been rejected.", '#fe3232bc');
                    acceptAllBtn.disabled = true;
                    rejectAllBtn.disabled = true;
                }
            }
            
        })
        .catch(function(error) {
            console.log(error);
            showNotification("Error occured, try again later.", '#fe3232bc');
        });
    }

    // Re attach event listeners when table is refreshed: OPTION 2 could be have an onlick function directly on the button
    attachEventListeners();

</script>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script>
@endsection