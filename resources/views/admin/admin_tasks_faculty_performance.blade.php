@extends('layouts.default')

@section('title', 'PUPQC - Training & Seminar')

@section('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('admin/css/admin_dashboard_seminars.css') }}">
@endsection

@section('body')
<div class="overlay"></div>
@include('layouts.admin_navbar')
@include('layouts.admin_tasks_sidebar')
@include('layouts.notification_side')

<div class="container-fluid margin">
    <div class="row">
        <div class="col-12" style="display: flex;">
            <h1 class="my-4 title">Training & Seminar Analytics</h1>

            <div class="drop-down create-dropdown2">
                <div class="wrapper">
                    <div class="selected selected2">Select Member</div>
                </div>
                <i class="fa fa-caret-down caret2"></i>

                <div class="list create-list2">
                    <input type="text" placeholder="Search.." class="search2">
                  @foreach($faculties as $faculty)
                    <div class="item item2">
                        <div class="text" id="{{ $faculty->id }}">
                          {{ $faculty->first_name }} {{ $faculty->middle_name ? $faculty->middle_name . ' ' : '' }}{{ $faculty->last_name }}
                        </div>
                    </div>
                  @endforeach
                </div>
            </div>

            <button class="create-btn" style="margin-left: auto; margin-top: 2%" onclick="exportData()">Export Data</button>
        </div>
    </div>

    <div class="container-fluid task-list" style="position: relative;">
     
    </div>
        
    <script>
    // Select department members dropdown scripts
    const dropdown2 = document.querySelector('.create-dropdown2');
    const list2 = document.querySelector('.create-list2');
    const selected2 = document.querySelector('.selected2');
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

    // Don't close the dropdown on search
    const search2 = document.querySelector('.search2');
    search2.addEventListener('click', (e) => {
        e.stopPropagation();
    });

    // Add functionality for search bar
    const getsearch2 = document.querySelector('.search2');
    const getitems2 = document.querySelectorAll('.item2');

    getsearch2.addEventListener('keyup', (e) => {
        const term = e.target.value.toLowerCase();
        getitems2.forEach(item => {
            let text = item.querySelector('.text').textContent.toLowerCase();
            if (text.includes(term)) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    });

    list2.addEventListener('click', (e) => {
        const item = e.target.closest('.item2');

        // Check if the clicked element is an .item2 element
        if (item) {
            const img = item.querySelector('img');
            const text = item.querySelector('.text');

            selected2.innerHTML = text.innerHTML;

            let selectedMember = selected2.innerHTML.trim();
            getSelectedMemberName = selectedMember;

            // Get the selected id
            selectedMemberId = item.querySelector('.text').id;
            getSelectedMemberId = selectedMemberId;
            
            //document.querySelector('#loading-overlay').style.display = 'flex';
            let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');  
            fetch('/admin-dashboard/seminars/get-analytics', {
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json, text-plain, */*",
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": token
                },
                method: 'POST',
                credentials: "same-origin",
                body: JSON.stringify({
                    id: selectedMemberId
                })
            })
            .then(response => response.json())
            .then(result => {
                if (result) {
                    
                }
            })
            .catch(error => {
                console.log(error);
                showNotification("Error occured in getting chart data.", '#fe3232bc');
            });
        }

    });

    let getSelectedMemberName = '';
    let getSelectedMemberId = '';
    function exportData() {
        if (getSelectedMemberId === '') {
            showNotification("Select a member to export data.", '#fe3232bc');
            return;
        }

        showNotification("Downloading file in a moment.", '#278a51');
        const endpoint = `/admin-dashboard/seminars/export-data?memberId=${getSelectedMemberId}&memberFullName=${getSelectedMemberName}`;
        window.location.href = endpoint;
    }

    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script>
    @endsection

