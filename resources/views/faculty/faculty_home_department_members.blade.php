@extends('layouts.default')

@section('title', 'PUPQC - Department Members')

@section('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('faculty/css/faculty_home_department_members.css') }}">
@endsection

@section('body')
<div class="overlay"></div>
@include('layouts.faculty_navbar')
@include('layouts.faculty_home_department_sidebar')
@include('layouts.notification_side')
  
<div class="container-fluid margin">
    <div class="row">
        <div class="col-6">
            <h1 class="my-4 title">{{ $departmentName }} Program ({{ $numberOfMembers->number_of_members }} {{ $numberOfMembers->number_of_members <= 1 ? 'Member' : 'Members' }})</h1>
        </div>
        <div class="col-2 pages">
            {{ $items->links()  }}
        </div>
        <div class="col-4 drop-down-container">
            <input type="text" class="search-input mx-5" id="search-input" placeholder="Search fullname...">
            <div id="search-results"></div>
        </div>
    </div>

    <div class="container-fluid item-list" style="position: relative;">
        <div class="row">
            <div class="col-6">
                <h5 class="my-3 column-name" style="z-index: 100; position: relative;">
                    Fullname
                </h5>
            </div>
            <div class="col-6">
                <h5 class="my-3 column-name" style="z-index: 100; position: relative;">Join Date</h5>
            </div>
        </div>

        @foreach ($items as $item)
        <div class="row item-row">
            <div class="col-6 column-1">
                <h5 class="item-row-content my-2 column-1-text" style="text-align: left; margin-left: 45%">
                    {{ $item->first_name }} {{ $item->middle_name }} {{ $item->last_name }}
                </h5>
            </div>
            <div class="col-6 column-2">
                <h5 class="item-row-content my-2 column-2-text">{{ date('F j, Y', strtotime($item->department_join_date)) }}<br>{{ date('g:i A', strtotime($item->department_join_date)) }}</h5>
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
        fetch(`/faculty-home/department/members/search?query=${query}`)
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
                const column1Text = itemRow.querySelector('.column-1-text');
                const column2Text = itemRow.querySelector('.column-2-text');

                column1Text.textContent = item.fullname;
                column2Text.innerHTML = `${item.join_date_formatted}<br>${item.join_date_time}`;
            }
        }
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script>
@endsection