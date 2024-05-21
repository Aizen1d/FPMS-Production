@extends('layouts.default')

@section('title', 'PUPQC - Faculty Performance')

@section('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('admin/css/admin_tasks_faculty_performance.css') }}">
@endsection

@section('body')
<div class="overlay"></div>
@include('layouts.admin_navbar')
@include('layouts.admin_tasks_sidebar')
@include('layouts.notification_side')

<div class="container-fluid margin">
    <div class="row">
        <div class="col-12" style="display: flex;">
            <h1 class="my-4 title">Faculty Performance</h1>

            <div class="drop-down create-dropdown2">
                <div class="wrapper">
                    <div class="selected selected2">Select Member</div>
                </div>
                <i class="fa fa-caret-down caret2"></i>

                <div class="list create-list2">
                    <input type="text" placeholder="Search.." class="search2">
                    <div class="item item2">
                        <div class="text" id="All Faculty">
                          All Faculty
                        </div>
                    </div>
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

    <div class="container-fluid task-list" style="position: relative; display: none">
        <div class="d-flex justify-content-center align-items-center mt-3">
            <label for="" style="font-weight: 700; font-size: 30px;">Memo</label>
        </div>
        <div class="memo-legend-table ml-5 mt-1">
            <table class="table table-bordered">
                <thead class="memo-legend-thead">
                    <tr>
                        <th scope="col" class="legend-column"><span class="table-padding1">Legend</span></th>
                        <th scope="col" ><span class="table-padding1">Status</span></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="completed-cell"><span class="table-padding1"></span></td>
                        <td><span class="table-padding1">Completed</span></td>
                    </tr>
                    <tr>
                        <td class="late-completed-cell"><span class="table-padding1"></span></td>
                        <td><span class="table-padding1">Late Completed</span></td>
                    </tr>
                    <tr>
                        <td class="ongoing-cell"><span class="table-padding1"></span></td>
                        <td><span class="table-padding1">Ongoing</span></td>
                    </tr>
                    <tr>
                        <td class="missing-cell"><span class="table-padding1"></span></td>
                        <td><span class="table-padding1">Missing</span></td>
                    </tr>
                    <tr>
                        <td class="not-assigned-cell"><span class="table-padding1"></span></td>
                        <td><span class="table-padding1">Not Assigned</span></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <label for="" style="font-weight: 700; font-size: 15px;">
            Note:
            <span style="font-weight: 300; font-size: 15px;">Hover over the column number to view the title.</span>
        </label>
        <div class="memo-table data-table ml-3 mt-1">
            <table class="table table-bordered">
                <thead class="memo-thead">
                    <tr>
                        <th scope="col"><span class="table-padding1"></span></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="container-fluid task-list" style="position: relative; display: none; margin-top: 2.5% !important;">
        <div class="d-flex justify-content-center align-items-center mt-3">
            <label for="" style="font-weight: 700; font-size: 30px;">Functions</label>
        </div>
        <div class="memo-legend-table ml-5 mt-1">
            <table class="table table-bordered">
                <thead class="memo-legend-thead">
                    <tr>
                        <th scope="col" class="legend-column"><span class="table-padding1">Legend</span></th>
                        <th scope="col" ><span class="table-padding1">Status</span></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="attended-cell"><span class="table-padding1"></span></td>
                        <td><span class="table-padding1">Attended</span></td>
                    </tr>
                    <tr>
                        <td class="on-leave-cell"><span class="table-padding1"></span></td>
                        <td><span class="table-padding1">On Leave</span></td>
                    </tr>
                    <tr>
                        <td class="pending-cell"><span class="table-padding1"></span></td>
                        <td><span class="table-padding1">Pending</span></td>
                    </tr>
                    <tr>
                        <td class="not-attended-cell"><span class="table-padding1"></span></td>
                        <td><span class="table-padding1">Not Attended</span></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <label for="" style="font-weight: 700; font-size: 15px;">
            Note:
            <span style="font-weight: 300; font-size: 15px;">Hover over the column number to view the title.</span>
        </label>
        <div class="function-table data-table ml-3 mt-1">
            <table class="table table-bordered">
                <thead class="function-thead">
                    <tr>
                        <th scope="col"><span class="table-padding1"></span></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="container-fluid task-list" style="position: relative; display: none; margin-top: 2.5% !important;">
        <div class="d-flex justify-content-center align-items-center mt-3">
            <label for="" style="font-weight: 700; font-size: 30px;">Researches</label>
        </div>
        <div class="d-flex flex-row mt-4">
            <div class="research-completed-table data-table ml-3 mt-1 mx-3">
                <div class="d-flex justify-content-center align-items-center">
                    <label for="" style="font-weight: 700; font-size: 20px;">Completed</label>
                </div>
                <table class="table table-bordered">
                    <thead class="function-thead">
                        <tr>
                            <th scope="col"><span class="table-padding1">Title</span></th>
                            <th scope="col"><span class="table-padding1">Author</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="research-presented-table data-table ml-3 mt-1 mx-3">
                <div class="d-flex justify-content-center align-items-center">
                    <label for="" style="font-weight: 700; font-size: 20px;">Presented</label>
                </div>
                <table class="table table-bordered">
                    <thead class="function-thead">
                        <tr>
                            <th scope="col"><span class="table-padding1">Title</span></th>
                            <th scope="col" ><span class="table-padding1">Author</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="research-published-table data-table ml-3 mt-1 mx-3">
                <div class="d-flex justify-content-center align-items-center">
                    <label for="" style="font-weight: 700; font-size: 20px;">Published</label>
                </div>
                <table class="table table-bordered">
                    <thead class="function-thead">
                        <tr>
                            <th scope="col"><span class="table-padding1">Title</span></th>
                            <th scope="col"><span class="table-padding1">Author</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="d-flex flex-row mt-5">
            <div class="research-completed-total-table data-table ml-3 mt-1 mx-3">
                <div class="d-flex justify-content-center align-items-center">
                    <label for="" style="font-weight: 700; font-size: 20px;">Completed Tally</label>
                </div>
                <table class="table table-bordered">
                    <thead class="function-thead">
                        <tr>
                            <th scope="col"><span class="table-padding1">Faculty</span></th>
                            <th scope="col"><span class="table-padding1">Total Completed Research</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="research-presented-total-table data-table ml-3 mt-1 mx-3">
                <div class="d-flex justify-content-center align-items-center">
                    <label for="" style="font-weight: 700; font-size: 20px;">Presented Tally</label>
                </div>
                <table class="table table-bordered">
                    <thead class="function-thead">
                        <tr>
                            <th scope="col"><span class="table-padding1">Faculty</span></th>
                            <th scope="col"><span class="table-padding1">Total Presented Research</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="research-published-total-table data-table ml-3 mt-1 mx-3">
                <div class="d-flex justify-content-center align-items-center">
                    <label for="" style="font-weight: 700; font-size: 20px;">Published Tally</label>
                </div>
                <table class="table table-bordered">
                    <thead class="function-thead">
                        <tr>
                            <th scope="col"><span class="table-padding1">Faculty</span></th>
                            <th scope="col"><span class="table-padding1">Total Published Research</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="container-fluid task-list" style="position: relative; display: none; margin-top: 2.5% !important;">
        <div class="d-flex justify-content-center align-items-center mt-3">
            <label for="" style="font-weight: 700; font-size: 30px;">Extensions</label>
        </div>
        <div class="d-flex flex-row">
            <div class="extensions-table data-table ml-3 mt-1 mx-3">
                <table class="table table-bordered">
                    <thead class="extensions-thead">
                        <tr>
                            <th scope="col"><span class="table-padding1">Title</span></th>
                            <th scope="col"><span class="table-padding1">Type</span></th>
                            <th scope="col"><span class="table-padding1">Total no. of hours</span></th>
                            <th scope="col"><span class="table-padding1">Created By</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="d-flex flex-row">
            <div class="extensions-total-table data-table ml-3 mt-4 mx-3">
                <div class="d-flex justify-content-center align-items-center">
                    <label for="" style="font-weight: 700; font-size: 20px;">Extensions Tally</label>
                </div>
                <table class="table table-bordered">
                    <thead class="extensions-total-thead">
                        <tr>
                            <th scope="col"><span class="table-padding1">Faculty</span></th>
                            <th scope="col" ><span class="table-padding1">Total Extension Created</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="container-fluid task-list" style="position: relative; display: none; margin-top: 2.5% !important;">
        <div class="d-flex justify-content-center align-items-center mt-3">
            <label for="" style="font-weight: 700; font-size: 30px;">Trainings & Seminars</label>
        </div>
        <div class="d-flex flex-row">
            <div class="seminars-table data-table ml-3 mt-1 mx-3">
                <table class="table table-bordered">
                    <thead class="seminars-thead">
                        <tr>
                            <th scope="col"><span class="table-padding1">Title</span></th>
                            <th scope="col"><span class="table-padding1">Classification</span></th>
                            <th scope="col"><span class="table-padding1">Total no. of hours</span></th>
                            <th scope="col"><span class="table-padding1">Created By</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="d-flex flex-row">
            <div class="seminars-total-table data-table ml-3 mt-4 mx-3">
                <div class="d-flex justify-content-center align-items-center">
                    <label for="" style="font-weight: 700; font-size: 20px;">Training & Seminars Tally</label>
                </div>
                <table class="table table-bordered">
                    <thead class="seminars-total-thead">
                        <tr>
                            <th scope="col"><span class="table-padding1">Faculty</span></th>
                            <th scope="col" ><span class="table-padding1">Total Training & Seminars</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
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

            document.querySelectorAll('.task-list').forEach((task) => {
                task.style.display = 'block';
            });
            
            //document.querySelector('#loading-overlay').style.display = 'flex';
            let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');  
            fetch('/admin-tasks/faculty-performance/getAnalytics', {
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json, text-plain, */*",
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": token
                },
                method: 'POST',
                credentials: "same-origin",
                body: JSON.stringify({
                    id: selectedMemberId,
                    member: selectedMember
                })
            })
            .then(response => response.json())
            .then(result => {
                console.log(result);

                if (selectedMember === 'All Faculty') {
                    /*******************************************
                     * SECTION: Memo table stuff
                     *******************************************/

                    // Clear the table first before appending new data
                    document.querySelector('.memo-thead tr').innerHTML = '<th scope="col"><span class="table-padding1"></span></th>';
                    document.querySelector('.memo-table tbody').innerHTML = '<tr></tr>';                    

                    result.all_memo.forEach((memo, index) => {
                        // Add new column per memo.task_name

                        let memoColumn = document.createElement('th');
                        memoColumn.innerHTML = index + 1;
                        memoColumn.classList.add('memo-column');

                        memoColumn.setAttribute('data-bs-toggle', 'tooltip');
                        memoColumn.setAttribute('data-bs-placement', 'top');
                        memoColumn.setAttribute('title', memo.task_name);

                        document.querySelector('.memo-thead tr').appendChild(memoColumn);
                    });

                    // Add totals column after all memos
                    let headers = ['Completed', 'Late Completed', 'Ongoing', 'Missing', 'Overall'];
                    headers.forEach((header) => {
                        let th = document.createElement('th');
                        th.innerHTML = header;
                        document.querySelector('.memo-thead tr').appendChild(th);
                    });

                    result.faculty_memo.forEach((faculty, index) => {
                        // Add new row per faculty, then first column is the faculty name

                        let row = document.createElement('tr');
                        row.classList.add('faculty-row');
                        document.querySelector('.memo-table tbody').appendChild(row);

                        let facultyName = document.createElement('td');
                        facultyName.innerHTML = faculty.faculty.full_name;
                        row.appendChild(facultyName);

                        // Change the cell color based on the memo status
                        result.all_memo.forEach((memo) => { // Loop over all memos
                            let memoStatus = document.createElement('td');

                            // Find the faculty's memo that matches the current memo
                            let facultyMemo = faculty.faculty_memo.find(fm => fm.task_name === memo.task_name);

                            if (facultyMemo) {
                                // If the faculty has a memo that matches the current memo, set the cell color based on its status
                                if (facultyMemo.status === 'Late Completed') {
                                    memoStatus.innerHTML = '✔';
                                    memoStatus.classList.add('late-completed-cell');
                                } 
                                else if (facultyMemo.status === 'Completed') {
                                    memoStatus.innerHTML = '✔'; 
                                    memoStatus.classList.add('completed-cell');
                                }
                                else if (facultyMemo.status === 'Ongoing') {
                                    memoStatus.innerHTML = '✔';
                                    memoStatus.classList.add('ongoing-cell');
                                }
                                else if (facultyMemo.status === 'Missing') {
                                    memoStatus.innerHTML = '✖';
                                    memoStatus.classList.add('missing-cell');
                                }
                            }
                            // If the faculty doesn't have a memo that matches the current memo, set the cell color to gray
                            else {
                                //memoStatus.innerHTML = '✖';
                                memoStatus.classList.add('not-assigned-cell');
                            }

                            row.appendChild(memoStatus);
                        });

                        // Calculate totals
                        let totalCompleted = faculty.faculty_memo.filter(memo => memo.status === 'Completed').length;
                        let totalLateCompleted = faculty.faculty_memo.filter(memo => memo.status === 'Late Completed').length;
                        let totalOngoing = faculty.faculty_memo.filter(memo => memo.status === 'Ongoing').length;
                        let totalMissing = faculty.faculty_memo.filter(memo => memo.status === 'Missing').length;
                        let overallTotal = faculty.faculty_memo.length;

                        // Add totals to row
                        [totalCompleted, totalLateCompleted, totalOngoing, totalMissing, overallTotal].forEach((total) => {
                            let td = document.createElement('td');
                            td.innerHTML = total;
                            row.appendChild(td);
                        });
                    });

                    /*******************************************
                     * SECTION: Function table stuff
                     *******************************************/

                    // Clear the table first before appending new data
                    document.querySelector('.function-thead tr').innerHTML = '<th scope="col"><span class="table-padding1"></span></th>';
                    document.querySelector('.function-table tbody').innerHTML = '<tr></tr>';

                    result.all_functions.forEach((func, index) => {
                        // Add new column per function.name

                        let functionColumn = document.createElement('th');
                        functionColumn.innerHTML = index + 1;
                        functionColumn.classList.add('function-column');

                        functionColumn.setAttribute('data-bs-toggle', 'tooltip');
                        functionColumn.setAttribute('data-bs-placement', 'top');
                        functionColumn.setAttribute('title', func.brief_description);

                        document.querySelector('.function-thead tr').appendChild(functionColumn);
                    });

                    // Add totals column after all functions
                    let headers2 = ['Attended', 'On Leave', 'Pending', 'Not Attended', 'Overall'];
                    headers2.forEach((header) => {
                        let th = document.createElement('th');
                        th.innerHTML = header;
                        document.querySelector('.function-thead tr').appendChild(th);
                    });

                    result.faculty_function.forEach((faculty, index) => {
                        // Add new row per faculty, then first column is the faculty name

                        let row = document.createElement('tr');
                        row.classList.add('faculty-row');
                        document.querySelector('.function-table tbody').appendChild(row);

                        let facultyName = document.createElement('td');
                        facultyName.innerHTML = faculty.faculty.full_name;
                        row.appendChild(facultyName);

                        // Change the cell color based on the function status
                        result.all_functions.forEach((func) => { // Loop over all functions
                            let functionStatus = document.createElement('td');

                            // Find the faculty's function that matches the current function
                            let facultyFunction = faculty.faculty_function.find(ff => ff.brief_description === func.brief_description);

                            if (facultyFunction) {
                                // If the faculty has a function that matches the current function, set the cell color based on its status
                                if (facultyFunction.status_of_attendace === 'Attended' && facultyFunction.status === 'Approved') {
                                    functionStatus.innerHTML = '✔';
                                    functionStatus.classList.add('attended-cell');
                                } 
                                else if (facultyFunction.status_of_attendace === 'On Leave' && facultyFunction.status === 'Approved') {
                                    //functionStatus.innerHTML = '✔'; 
                                    functionStatus.classList.add('on-leave-cell');
                                }
                                else if (facultyFunction.status === 'Pending') {
                                    functionStatus.classList.add('pending-cell');
                                }
                            }
                            // If the faculty doesn't have a function that matches the current function
                            else {
                                functionStatus.innerHTML = '✖';
                                functionStatus.classList.add('not-attended-cell');
                            }

                            row.appendChild(functionStatus);
                        });

                        // Calculate totals
                        let totalAttended = faculty.faculty_function.filter(func => func.status_of_attendace === 'Attended' && func.status === 'Approved').length;
                        let totalOnLeave = faculty.faculty_function.filter(func => func.status_of_attendace === 'On Leave' && func.status === 'Approved').length;
                        let totalPending = faculty.faculty_function.filter(func => func.status === 'Pending').length;
                        let overallTotal = result.all_functions.length;

                        let totalNotAttended = result.all_functions.length - (totalAttended + totalOnLeave + totalPending);

                        // Add totals to row
                        [totalAttended, totalOnLeave, totalPending, totalNotAttended, overallTotal].forEach((total) => {
                            let td = document.createElement('td');
                            td.innerHTML = total;
                            row.appendChild(td);
                        });
                    });

                    /*******************************************
                     * SECTION: Researches table stuff
                     *******************************************/

                    // Clear the table first before appending new data
                    document.querySelector('.research-completed-table tbody').innerHTML = '<tr></tr>';
                    document.querySelector('.research-presented-table tbody').innerHTML = '<tr></tr>';
                    document.querySelector('.research-published-table tbody').innerHTML = '<tr></tr>';

                    // Completed researches hydration
                    result.all_completed_researches.forEach((research) => {
                        let row = document.createElement('tr');
                        document.querySelector('.research-completed-table tbody').appendChild(row);

                        let title = document.createElement('td');
                        title.innerHTML = research.title;
                        row.appendChild(title);

                        let author = document.createElement('td');
                        author.innerHTML = research.authors;
                        row.appendChild(author);
                    });

                    if (result.all_completed_researches.length === 0) {
                        let row = document.createElement('tr');
                        document.querySelector('.research-completed-table tbody').appendChild(row);

                        let noData = document.createElement('td');
                        noData.innerHTML = 'No data';
                        row.appendChild(noData);

                        let noData2 = document.createElement('td');
                        noData2.innerHTML = 'No data';
                        row.appendChild(noData2);
                    }

                    // Presented researches hydration
                    result.all_presented_researches.forEach((research) => {
                        let row = document.createElement('tr');
                        document.querySelector('.research-presented-table tbody').appendChild(row);

                        let title = document.createElement('td');
                        title.innerHTML = research.title;
                        row.appendChild(title);

                        let author = document.createElement('td');
                        author.innerHTML = research.authors;
                        row.appendChild(author);
                    });

                    if (result.all_presented_researches.length === 0) {
                        let row = document.createElement('tr');
                        document.querySelector('.research-presented-table tbody').appendChild(row);

                        let noData = document.createElement('td');
                        noData.innerHTML = 'No data';
                        row.appendChild(noData);

                        let noData2 = document.createElement('td');
                        noData2.innerHTML = 'No data';
                        row.appendChild(noData2);
                    }

                    // Published researches hydration
                    result.all_published_researches.forEach((research) => {
                        let row = document.createElement('tr');
                        document.querySelector('.research-published-table tbody').appendChild(row);

                        let title = document.createElement('td');
                        title.innerHTML = research.title;
                        row.appendChild(title);

                        let author = document.createElement('td');
                        author.innerHTML = research.authors;
                        row.appendChild(author);
                    });

                    if (result.all_published_researches.length === 0) {
                        let row = document.createElement('tr');
                        document.querySelector('.research-published-table tbody').appendChild(row);

                        let noData = document.createElement('td');
                        noData.innerHTML = 'No data';
                        row.appendChild(noData);

                        let noData2 = document.createElement('td');
                        noData2.innerHTML = 'No data';
                        row.appendChild(noData2);
                    }

                    /*******************************************
                     * SECTION: Researches total table stuff
                     *******************************************/

                    // Clear the table first before appending new data
                    document.querySelector('.research-completed-total-table tbody').innerHTML = '<tr></tr>';
                    document.querySelector('.research-presented-total-table tbody').innerHTML = '<tr></tr>';
                    document.querySelector('.research-published-total-table tbody').innerHTML = '<tr></tr>';

                    // Completed researches total hydration
                    result.faculty_researches.forEach((faculty) => {
                        let row = document.createElement('tr');
                        document.querySelector('.research-completed-total-table tbody').appendChild(row);

                        let facultyName = document.createElement('td');
                        facultyName.innerHTML = faculty.faculty.full_name;
                        row.appendChild(facultyName);

                        let total = document.createElement('td');
                        total.innerHTML = faculty.completed_researches.length;
                        row.appendChild(total);
                    });

                    // Presented researches total hydration
                    result.faculty_researches.forEach((faculty) => {
                        let row = document.createElement('tr');
                        document.querySelector('.research-presented-total-table tbody').appendChild(row);

                        let facultyName = document.createElement('td');
                        facultyName.innerHTML = faculty.faculty.full_name;
                        row.appendChild(facultyName);

                        let total = document.createElement('td');
                        total.innerHTML = faculty.presented_researches.length;
                        row.appendChild(total);
                    });

                    // Published researches total hydration
                    result.faculty_researches.forEach((faculty) => {
                        let row = document.createElement('tr');
                        document.querySelector('.research-published-total-table tbody').appendChild(row);

                        let facultyName = document.createElement('td');
                        facultyName.innerHTML = faculty.faculty.full_name;
                        row.appendChild(facultyName);

                        let total = document.createElement('td');
                        total.innerHTML = faculty.published_researches.length;
                        row.appendChild(total);
                    });

                    /*******************************************
                     * SECTION: Extensions table stuff
                     *******************************************/

                    // Clear the table first before appending new data
                    document.querySelector('.extensions-table tbody').innerHTML = '<tr></tr>';

                    result.all_extensions.forEach((extension) => {
                        let row = document.createElement('tr');
                        document.querySelector('.extensions-table tbody').appendChild(row);

                        let title = document.createElement('td');
                        let type = document.createElement('td');
                        let total_no_of_hours = document.createElement('td');
                        total_no_of_hours.innerHTML = extension.total_no_of_hours;

                        if (extension.title_of_extension_activity) {
                            title.innerHTML = extension.title_of_extension_activity;
                            type.innerHTML = 'Activity';
                        }
                        else if (extension.title_of_extension_program) {
                            title.innerHTML = extension.title_of_extension_program;
                            type.innerHTML = 'Program';
                        }
                        else if (extension.title_of_extension_project) {
                            title.innerHTML = extension.title_of_extension_project;
                            type.innerHTML = 'Project';
                        }

                        let createdBy = document.createElement('td');
                        createdBy.innerHTML = extension.faculty_fullname;

                        row.appendChild(title);
                        row.appendChild(type);
                        row.appendChild(total_no_of_hours);
                        row.appendChild(createdBy);
                    });

                    if (result.all_extensions.length === 0) {
                        let row = document.createElement('tr');
                        document.querySelector('.extensions-table tbody').appendChild(row);

                        let noData = document.createElement('td');
                        noData.innerHTML = 'No data';
                        row.appendChild(noData);

                        let noData2 = document.createElement('td');
                        noData2.innerHTML = 'No data';
                        row.appendChild(noData2);

                        let noData3 = document.createElement('td');
                        noData3.innerHTML = 'No data';
                        row.appendChild(noData3);

                        let noData4 = document.createElement('td');
                        noData4.innerHTML = 'No data';
                        row.appendChild(noData4);
                    }

                    /*******************************************
                     * SECTION: Extensions total table stuff
                     *******************************************/

                    // Clear the table first before appending new data
                    document.querySelector('.extensions-total-table tbody').innerHTML = '<tr></tr>';

                    result.faculty_extensions.forEach((faculty) => {
                        let row = document.createElement('tr');
                        document.querySelector('.extensions-total-table tbody').appendChild(row);

                        let facultyName = document.createElement('td');
                        facultyName.innerHTML = faculty.faculty.full_name;
                        row.appendChild(facultyName);

                        let total = document.createElement('td');
                        total.innerHTML = faculty.extensions.length;
                        row.appendChild(total);
                    });

                    /*******************************************
                     * SECTION: Trainings & Seminars table stuff
                     *******************************************/

                    // Clear the table first before appending new data
                    document.querySelector('.seminars-table tbody').innerHTML = '<tr></tr>';

                    result.all_seminars.forEach((seminar) => {
                        let row = document.createElement('tr');
                        document.querySelector('.seminars-table tbody').appendChild(row);

                        let title = document.createElement('td');
                        title.innerHTML = seminar.title;
                        row.appendChild(title);

                        let classification = document.createElement('td');
                        classification.innerHTML = seminar.classification;
                        row.appendChild(classification);

                        let total_no_hours = document.createElement('td');
                        total_no_hours.innerHTML = seminar.total_no_hours;
                        row.appendChild(total_no_hours);

                        let createdBy = document.createElement('td');
                        createdBy.innerHTML = seminar.faculty_fullname;
                        row.appendChild(createdBy);
                    });
                    
                    if (result.all_seminars.length === 0) {
                        let row = document.createElement('tr');
                        document.querySelector('.seminars-table tbody').appendChild(row);

                        let noData = document.createElement('td');
                        noData.innerHTML = 'No data';
                        row.appendChild(noData);

                        let noData2 = document.createElement('td');
                        noData2.innerHTML = 'No data';
                        row.appendChild(noData2);

                        let noData3 = document.createElement('td');
                        noData3.innerHTML = 'No data';
                        row.appendChild(noData3);

                        let noData4 = document.createElement('td');
                        noData4.innerHTML = 'No data';
                        row.appendChild(noData4);
                    }

                    /*******************************************
                     * SECTION: Trainings & Seminars total table stuff
                     *******************************************/

                    // Clear the table first before appending new data
                    document.querySelector('.seminars-total-table tbody').innerHTML = '<tr></tr>';

                    result.faculty_seminars.forEach((faculty) => {
                        let row = document.createElement('tr');
                        document.querySelector('.seminars-total-table tbody').appendChild(row);

                        let facultyName = document.createElement('td');
                        facultyName.innerHTML = faculty.faculty.full_name;
                        row.appendChild(facultyName);

                        let total = document.createElement('td');
                        total.innerHTML = faculty.seminars.length;
                        row.appendChild(total);
                    });
                }
                // If a specific faculty is selected
                else {
                    /*******************************************
                     * SECTION: Memo table stuff
                     *******************************************/

                    // Clear the table first before appending new data
                    document.querySelector('.memo-thead tr').innerHTML = '<th scope="col"><span class="table-padding1"></span></th>';
                    document.querySelector('.memo-table tbody').innerHTML = '<tr></tr>'; 

                    result.all_memo.forEach((memo, index) => {
                        // Add new column per memo.task_name

                        let memoColumn = document.createElement('th');
                        memoColumn.innerHTML = index + 1;
                        memoColumn.classList.add('memo-column');

                        memoColumn.setAttribute('data-bs-toggle', 'tooltip');
                        memoColumn.setAttribute('data-bs-placement', 'top');
                        memoColumn.setAttribute('title', memo.task_name);

                        document.querySelector('.memo-thead tr').appendChild(memoColumn);
                    });

                    // Add totals column after all memos
                    let headers = ['Completed', 'Late Completed', 'Ongoing', 'Missing', 'Overall'];
                    headers.forEach((header) => {
                        let th = document.createElement('th');
                        th.innerHTML = header;
                        document.querySelector('.memo-thead tr').appendChild(th);
                    });
                    
                    // Add the row for the selected faculty
                    let row = document.createElement('tr');
                    row.classList.add('faculty-row');
                    document.querySelector('.memo-table tbody').appendChild(row);

                    let facultyName = document.createElement('td');
                    facultyName.innerHTML = selectedMember;

                    row.appendChild(facultyName);

                    if (result.faculty_memo.length === 0) {
                        // If the faculty has no memos, set all cells to gray
                        result.all_memo.forEach((memo) => {
                            let memoStatus = document.createElement('td');
                            memoStatus.classList.add('not-assigned-cell');
                            row.appendChild(memoStatus);
                        });

                        // Add totals to row
                        [0, 0, 0, 0, 0].forEach((total) => {
                            let td = document.createElement('td');
                            td.innerHTML = total;
                            row.appendChild(td);
                        });
                    }
                    else {
                        // Change the cell color based on the memo status
                        result.all_memo.forEach((memo) => { // Loop over all memos
                            let memoStatus = document.createElement('td');

                            // Find the faculty's memo that matches the current memo
                            let facultyMemo = result.faculty_memo.find(fm => fm.task_name === memo.task_name);

                            if (facultyMemo) {
                                // If the faculty has a memo that matches the current memo, set the cell color based on its status
                                if (facultyMemo.status === 'Late Completed') {
                                    memoStatus.innerHTML = '✔';
                                    memoStatus.classList.add('late-completed-cell');
                                } 
                                else if (facultyMemo.status === 'Completed') {
                                    memoStatus.innerHTML = '✔'; 
                                    memoStatus.classList.add('completed-cell');
                                }
                                else if (facultyMemo.status === 'Ongoing') {
                                    memoStatus.innerHTML = '✔';
                                    memoStatus.classList.add('ongoing-cell');
                                }
                                else if (facultyMemo.status === 'Missing') {
                                    memoStatus.innerHTML = '✖';
                                    memoStatus.classList.add('missing-cell');
                                }
                            }
                            // If the faculty doesn't have a memo that matches the current memo, set the cell color to gray
                            else {
                                //memoStatus.innerHTML = '✖';
                                memoStatus.classList.add('not-assigned-cell');
                            }

                            row.appendChild(memoStatus);
                        });

                        // Calculate totals
                        let totalCompleted = result.faculty_memo.filter(memo => memo.status === 'Completed').length;
                        let totalLateCompleted = result.faculty_memo.filter(memo => memo.status === 'Late Completed').length;
                        let totalOngoing = result.faculty_memo.filter(memo => memo.status === 'Ongoing').length;
                        let totalMissing = result.faculty_memo.filter(memo => memo.status === 'Missing').length;
                        let overallTotal = result.faculty_memo.length;

                        // Add totals to row
                        [totalCompleted, totalLateCompleted, totalOngoing, totalMissing, overallTotal].forEach((total) => {
                            let td = document.createElement('td');
                            td.innerHTML = total;
                            row.appendChild(td);
                        });
                    }

                    /*******************************************
                     * SECTION: Function table stuff
                     *******************************************/

                    // Clear the table first before appending new data
                    document.querySelector('.function-thead tr').innerHTML = '<th scope="col"><span class="table-padding1"></span></th>';
                    document.querySelector('.function-table tbody').innerHTML = '<tr></tr>';

                    result.all_functions.forEach((func, index) => {
                        // Add new column per function.name

                        let functionColumn = document.createElement('th');
                        functionColumn.innerHTML = index + 1;
                        functionColumn.classList.add('function-column');

                        functionColumn.setAttribute('data-bs-toggle', 'tooltip');
                        functionColumn.setAttribute('data-bs-placement', 'top');
                        functionColumn.setAttribute('title', func.brief_description);

                        document.querySelector('.function-thead tr').appendChild(functionColumn);
                    });

                    // Add totals column after all functions
                    let headers2 = ['Attended', 'On Leave', 'Pending', 'Not Attended', 'Overall'];
                    headers2.forEach((header) => {
                        let th = document.createElement('th');
                        th.innerHTML = header;
                        document.querySelector('.function-thead tr').appendChild(th);
                    });

                    // Add the row for the selected faculty
                    let row2 = document.createElement('tr');
                    row2.classList.add('faculty-row');
                    document.querySelector('.function-table tbody').appendChild(row2);

                    let facultyName2 = document.createElement('td');
                    facultyName2.innerHTML = selectedMember;

                    row2.appendChild(facultyName2);

                    // Change the cell color based on the function status
                    result.all_functions.forEach((func) => { // Loop over all functions
                        let functionStatus = document.createElement('td');

                        // Find the faculty's function that matches the current function
                        let facultyFunction = result.faculty_function.find(ff => ff.brief_description === func.brief_description);

                        if (facultyFunction) {
                            // If the faculty has a function that matches the current function, set the cell color based on its status
                            if (facultyFunction.status_of_attendace === 'Attended' && facultyFunction.status === 'Approved') {
                                functionStatus.innerHTML = '✔';
                                functionStatus.classList.add('attended-cell');
                            } 
                            else if (facultyFunction.status_of_attendace === 'On Leave' && facultyFunction.status === 'Approved') {
                                //functionStatus.innerHTML = '✔'; 
                                functionStatus.classList.add('on-leave-cell');
                            }
                            else if (facultyFunction.status === 'Pending') {
                                functionStatus.classList.add('pending-cell');
                            }
                        }
                        // If the faculty doesn't have a function that matches the current function,
                        else {
                            functionStatus.innerHTML = '✖';
                            functionStatus.classList.add('not-attended-cell');
                        }

                        row2.appendChild(functionStatus);
                    });

                    // Calculate totals
                    let totalAttended = result.faculty_function.filter(func => func.status_of_attendace === 'Attended' && func.status === 'Approved').length;
                    let totalOnLeave = result.faculty_function.filter(func => func.status_of_attendace === 'On Leave' && func.status === 'Approved').length;
                    let totalPending = result.faculty_function.filter(func => func.status === 'Pending').length;
                    let totalNotAttended = result.all_functions.length - (totalAttended + totalOnLeave + totalPending);
                    let overallTotal = result.all_functions.length;

                    // Add totals to row
                    [totalAttended, totalOnLeave, totalPending, totalNotAttended, overallTotal].forEach((total) => {
                        let td = document.createElement('td');
                        td.innerHTML = total;
                        row2.appendChild(td);
                    });

                    /*******************************************
                     * SECTION: Researches table stuff
                     *******************************************/

                    // Clear the table first before appending new data
                    document.querySelector('.research-completed-table tbody').innerHTML = '<tr></tr>';
                    document.querySelector('.research-presented-table tbody').innerHTML = '<tr></tr>';
                    document.querySelector('.research-published-table tbody').innerHTML = '<tr></tr>';

                    // Hydrate the completed researches table if there are any completed researches for the selected faculty
                    if (result.faculty_researches.completed_researches.length > 0) {
                        result.faculty_researches.completed_researches.forEach((research) => {
                            let row = document.createElement('tr');
                            document.querySelector('.research-completed-table tbody').appendChild(row);

                            let title = document.createElement('td');
                            title.innerHTML = research.title;
                            row.appendChild(title);

                            let author = document.createElement('td');
                            author.innerHTML = research.authors;
                            row.appendChild(author);
                        });
                    } 
                    else {
                        let row = document.createElement('tr');
                        document.querySelector('.research-completed-table tbody').appendChild(row);

                        let noData = document.createElement('td');
                        noData.innerHTML = 'No data';
                        row.appendChild(noData);

                        let noData2 = document.createElement('td');
                        noData2.innerHTML = 'No data';
                        row.appendChild(noData2);
                    }

                    // Hydrate the presented researches table if there are any presented researches for the selected faculty
                    if (result.faculty_researches.presented_researches.length > 0) {
                        result.faculty_researches.presented_researches.forEach((research) => {
                            let row = document.createElement('tr');
                            document.querySelector('.research-presented-table tbody').appendChild(row);

                            let title = document.createElement('td');
                            title.innerHTML = research.completed_research.title;
                            row.appendChild(title);

                            let author = document.createElement('td');
                            author.innerHTML = research.completed_research.authors;
                            row.appendChild(author);
                        });
                    }
                    else {
                        let row = document.createElement('tr');
                        document.querySelector('.research-presented-table tbody').appendChild(row);

                        let noData = document.createElement('td');
                        noData.innerHTML = 'No data';
                        row.appendChild(noData);

                        let noData2 = document.createElement('td');
                        noData2.innerHTML = 'No data';
                        row.appendChild(noData2);
                    }

                    // Hydrate the published researches table if there are any published researches for the selected faculty
                    if (result.faculty_researches.published_researches.length > 0) {
                        result.faculty_researches.published_researches.forEach((research) => {
                            let row = document.createElement('tr');
                            document.querySelector('.research-published-table tbody').appendChild(row);

                            let title = document.createElement('td');
                            title.innerHTML = research.completed_research.title;
                            row.appendChild(title);

                            let author = document.createElement('td');
                            author.innerHTML = research.completed_research.authors;
                            row.appendChild(author);
                        });
                    }
                    else {
                        let row = document.createElement('tr');
                        document.querySelector('.research-published-table tbody').appendChild(row);

                        let noData = document.createElement('td');
                        noData.innerHTML = 'No data';
                        row.appendChild(noData);

                        let noData2 = document.createElement('td');
                        noData2.innerHTML = 'No data';
                        row.appendChild(noData2);
                    }

                    /*******************************************
                     * SECTION: Researches total table stuff
                     *******************************************/

                    // Clear the table first before appending new data
                    document.querySelector('.research-completed-total-table tbody').innerHTML = '<tr></tr>';
                    document.querySelector('.research-presented-total-table tbody').innerHTML = '<tr></tr>';
                    document.querySelector('.research-published-total-table tbody').innerHTML = '<tr></tr>';

                    // Hydrate the completed researches total table if there are any completed researches for the selected faculty
                    const hydrateCompletedResearchesTotal = () => {
                        let row = document.createElement('tr');
                        document.querySelector('.research-completed-total-table tbody').appendChild(row);

                        let facultyName = document.createElement('td');
                        facultyName.innerHTML = selectedMember;
                        row.appendChild(facultyName);

                        let total = document.createElement('td');
                        total.innerHTML = result.faculty_researches.completed_researches.length;
                        row.appendChild(total);
                    }
                    
                    // Hydrate the presented researches total table if there are any presented researches for the selected faculty
                    const hydratePresentedResearchesTotal = () => {
                        let row = document.createElement('tr');
                        document.querySelector('.research-presented-total-table tbody').appendChild(row);

                        let facultyName = document.createElement('td');
                        facultyName.innerHTML = selectedMember;
                        row.appendChild(facultyName);

                        let total = document.createElement('td');
                        total.innerHTML = result.faculty_researches.presented_researches.length;
                        row.appendChild(total);
                    }

                    // Hydrate the published researches total table if there are any published researches for the selected faculty
                    const hydratePublishedResearchesTotal = () => {
                        let row = document.createElement('tr');
                        document.querySelector('.research-published-total-table tbody').appendChild(row);

                        let facultyName = document.createElement('td');
                        facultyName.innerHTML = selectedMember;
                        row.appendChild(facultyName);

                        let total = document.createElement('td');
                        total.innerHTML = result.faculty_researches.published_researches.length;
                        row.appendChild(total);
                    }

                    hydrateCompletedResearchesTotal();
                    hydratePresentedResearchesTotal();
                    hydratePublishedResearchesTotal();

                    /*******************************************
                     * SECTION: Extensions table stuff
                     *******************************************/

                    // Clear the table first before appending new data
                    document.querySelector('.extensions-table tbody').innerHTML = '<tr></tr>';

                    result.faculty_extensions.forEach((extension) => {
                        let row = document.createElement('tr');
                        document.querySelector('.extensions-table tbody').appendChild(row);

                        let title = document.createElement('td');
                        let type = document.createElement('td');
                        let total_no_of_hours = document.createElement('td');
                        total_no_of_hours.innerHTML = extension.total_no_of_hours;

                        if (extension.title_of_extension_activity) {
                            title.innerHTML = extension.title_of_extension_activity;
                            type.innerHTML = 'Activity';
                        }
                        else if (extension.title_of_extension_program) {
                            title.innerHTML = extension.title_of_extension_program;
                            type.innerHTML = 'Program';
                        }
                        else if (extension.title_of_extension_project) {
                            title.innerHTML = extension.title_of_extension_project;
                            type.innerHTML = 'Project';
                        }

                        let createdBy = document.createElement('td');
                        createdBy.innerHTML = selectedMember;

                        row.appendChild(title);
                        row.appendChild(type);
                        row.appendChild(total_no_of_hours);
                        row.appendChild(createdBy);
                    });

                    if (result.faculty_extensions.length === 0) {
                        let row = document.createElement('tr');
                        document.querySelector('.extensions-table tbody').appendChild(row);

                        let noData = document.createElement('td');
                        noData.innerHTML = 'No data';
                        row.appendChild(noData);

                        let noData2 = document.createElement('td');
                        noData2.innerHTML = 'No data';
                        row.appendChild(noData2);

                        let noData3 = document.createElement('td');
                        noData3.innerHTML = 'No data';
                        row.appendChild(noData3);

                        let noData4 = document.createElement('td');
                        noData4.innerHTML = 'No data';
                        row.appendChild(noData4);
                    }
                   
                    /*******************************************
                     * SECTION: Extensions total table stuff
                     *******************************************/

                    // Clear the table first before appending new data
                    document.querySelector('.extensions-total-table tbody').innerHTML = '<tr></tr>';

                    const hydrateFacultyExtensions = () => {
                        let row = document.createElement('tr');
                        document.querySelector('.extensions-total-table tbody').appendChild(row);

                        let facultyName = document.createElement('td');
                        facultyName.innerHTML = selectedMember;
                        row.appendChild(facultyName);

                        let total = document.createElement('td');
                        total.innerHTML = result.faculty_extensions.length;
                        row.appendChild(total);

                    };

                    hydrateFacultyExtensions();

                    /*******************************************
                     * SECTION: Trainings & Seminars table stuff
                     *******************************************/

                    // Clear the table first before appending new data
                    document.querySelector('.seminars-table tbody').innerHTML = '<tr></tr>';

                    result.faculty_seminars.forEach((seminar) => {
                        let row = document.createElement('tr');
                        document.querySelector('.seminars-table tbody').appendChild(row);

                        let title = document.createElement('td');
                        title.innerHTML = seminar.title;
                        row.appendChild(title);

                        let classification = document.createElement('td');
                        classification.innerHTML = seminar.classification;
                        row.appendChild(classification);

                        let total_no_of_hours = document.createElement('td');
                        total_no_of_hours.innerHTML = seminar.total_no_hours;
                        row.appendChild(total_no_of_hours);

                        let createdBy = document.createElement('td');
                        createdBy.innerHTML = selectedMember;
                        row.appendChild(createdBy);
                    });

                    if (result.faculty_seminars.length === 0) {
                        let row = document.createElement('tr');
                        document.querySelector('.seminars-table tbody').appendChild(row);

                        let noData = document.createElement('td');
                        noData.innerHTML = 'No data';
                        row.appendChild(noData);

                        let noData2 = document.createElement('td');
                        noData2.innerHTML = 'No data';
                        row.appendChild(noData2);

                        let noData3 = document.createElement('td');
                        noData3.innerHTML = 'No data';
                        row.appendChild(noData3);

                        let noData4 = document.createElement('td');
                        noData4.innerHTML = 'No data';
                        row.appendChild(noData4);
                    }

                    /*******************************************
                     * SECTION: Trainings & Seminars total table stuff
                     *******************************************/

                    // Clear the table first before appending new data
                    document.querySelector('.seminars-total-table tbody').innerHTML = '<tr></tr>';

                    const hydrateFacultySeminars = () => {
                        let row = document.createElement('tr');
                        document.querySelector('.seminars-total-table tbody').appendChild(row);

                        let facultyName = document.createElement('td');
                        facultyName.innerHTML = selectedMember;
                        row.appendChild(facultyName);

                        let total = document.createElement('td');
                        total.innerHTML = result.faculty_seminars.length;
                        row.appendChild(total);
                    };

                    hydrateFacultySeminars();
                }

                // Initialize tooltips of bootstrap
                const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
                const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
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
        const endpoint = `/admin-tasks/faculty-performance/export-data?memberId=${getSelectedMemberId}&memberFullName=${getSelectedMemberName}`;
        window.location.href = endpoint;
    }

    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script>
    @endsection

