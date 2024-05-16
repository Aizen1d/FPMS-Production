@extends('layouts.default')

@section('title', 'PUPQC - Attendance')

@section('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('admin/css/admin_dashboard_researches.css') }}">
@endsection

@section('body')
<div class="overlay"></div>
@include('layouts.admin_navbar')
@include('layouts.admin_dashboard_sidebar')
@include('layouts.notification_side')

<div class="container-fluid margin">
    <div class="row">
        <div class="col-12" style="display: flex;">
            <h1 class="my-4 title">Attendance Analytics</h1>

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
        <div class="row">
            <div class="col-5" style="position: relative;">
                <div class="chart-info">
                    <h3 class="chart-label" style="font-size: 40px;"><b>Approved Attendance:</b> <span class="research-presented item-approved" style="font-weight: normal; color: #363636"></span></h3>
                    <h3 class="chart-label" style="font-size: 40px;"><b>Rejected Attendance:</b> <span class="research-completed item-rejected" style="font-weight: normal; color: #363636"></span></h3>
                    <h3 class="chart-label" style="font-size: 40px;"><b>Pending Attendance:</b> <span class="research-published item-pending" style="font-weight: normal; color: #363636"></span></h3>
                    <h3 class="chart-label" style="font-size: 40px;"><b>Total Attendance:</b> <span class="research-total item-total" style="font-weight: normal; color: #363636"></span></h3>
                </div>
            </div>
            <div class="col-7" style="display: flex; justify-content: center; align-items: center;">
                <canvas id="myChart" class="pie-chart" width="700" height="555"></canvas>
            </div>
        </div>

        <div class="no-selected" style="display: flex; justify-content: center; align-items: center; border-radius: 25px; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: white;">
          <div id="no-selected" style="margin-top: 3px; font-size: 2vh;">Select faculty member to display data.</div>
        </div>

        <div id="loading-overlay" class="loading-create-task" style="display: none; justify-content: center; align-items: center; border-radius: 25px; z-index: 199; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: white;">
            <div style="display: flex; flex-direction: column; align-items: center;">
                <div class="spinner-border text-dark" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <div id="loading-text" style="margin-top: 3px;">Loading chart data..</div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
    
    <script>
        const ctx = document.getElementById('myChart');
        Chart.register(ChartDataLabels);

        var data = [0, 0, 0]; // place holder

        let myChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Approved Attendance', 'Rejected Attendance', 'Pending Attendance'],
            datasets: [{
                label: 'Total',
                data: data,
                backgroundColor: [
                    '#06A64B',
                    '#fa1635',
                    '#F6B000',
                ],
                borderWidth: 2,
                hoverOffset: 5
            }]
        },
        options: {
            tooltips: {
                enabled: true
            },
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                datalabels: {
                    formatter: (value, ctx) => {
                        if (value >= 1) {
                            let sum = ctx.dataset.data.reduce((a, b) => a + b, 0);
                            let percentage = ctx.chart.data.labels[ctx.dataIndex] + '\n' + (value*100 / sum).toFixed(2)+"%";
                            return percentage;
                        } else {
                            return null;
                        }
                    },
                    color: '#fff',
                    font: {
                        size: 16,
                    }
                }
            }
        },
    });

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
            fetch('/admin-dashboard/attendance/get-analytics', {
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

                console.log(result);
                if (result.data) {
                    document.querySelector('#loading-overlay').style.display = 'none';
                    document.querySelector('.no-selected').style.display = 'none';

                    // Parse the result.data property into an array
                    let data = JSON.parse(result.data);

                    // Update the chart data
                    myChart.data.datasets[0].data = data;
                    myChart.update();

                    document.querySelector('.item-total').innerHTML = result.totalItems;
                    document.querySelector('.item-approved').innerHTML = result.approved;
                    document.querySelector('.item-rejected').innerHTML = result.rejected;
                    document.querySelector('.item-pending').innerHTML = result.pending;
                }
                else {
                    document.querySelector('#loading-overlay').style.display = 'none';
                    document.querySelector('.no-selected').style.display = 'flex';
                    document.querySelector('#no-selected').innerHTML = 'No data to display.'
                }
            })
            .catch(error => {
                console.log(error);

                document.querySelector('#loading-overlay').style.display = 'none';
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
        const endpoint = `/admin-dashboard/attendance/export-data?memberId=${getSelectedMemberId}&memberFullName=${getSelectedMemberName}`;
        window.location.href = endpoint;
    }

    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script>
    @endsection

