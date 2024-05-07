@extends('layouts.default')

@section('title', 'PUPQC - Dashboard')

@section('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('admin/css/admin_dashboard_assigned_task_timeline.css') }}">
@endsection

@section('body')
<div class="overlay"></div>
@include('layouts.admin_navbar')
@include('layouts.admin_dashboard_sidebar')
@include('layouts.notification_side')

<div class="container-fluid margin">
    <div class="row">
        <div class="col-12" style="display: flex;">
            <h1 class="my-4 title">Statistics</h1>

            <div class="drop-down create-dropdown1">
                <div class="wrapper">
                    <img src="{{ asset('admin/images/home.svg') }}" alt=" " class="selectedImg selectedImg1">
                    <div class="selected selected1">Select Program</div>
                </div>
                <i class="fa fa-caret-down caret1"></i>

                <div class="list create-list1">
                    <div class="item item1">
                        <img src="{{ asset('admin/images/home.svg') }}" alt="">
                        <div class="text">All Departments</div>
                    </div>
                    @foreach ($departments as $department)
                    <div class="item item1">
                        <img src="{{ asset('admin/images/home.svg') }}" alt="">
                        <div class="text">{{ $department->department_name }}</div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="drop-down create-dropdown2">
                <div class="wrapper">
                    <img src="{{ asset('admin/images/home.svg') }}" alt=" " class="selectedImg selectedImg2">
                    <div class="selected selected2">Select Member</div>
                </div>
                <i class="fa fa-caret-down caret2"></i>

                <div class="list create-list2">
                    <div class="item item2">
                        <img src="{{ asset('admin/images/home.svg') }}" alt="">
                        <div class="text"></div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="container-fluid task-list" style="position: relative;">
        <div class="row">
            <div class="col-5" style="position: relative;">
                <div class="chart-info">
                    <h3 class="chart-label"><b>Total memo in 6 months:</b> <span class="total-task-data" style="font-weight: normal; color: #363636"></span></h3>
                </div>
            </div>
            <div class="col-7" style="display: flex; justify-content: center; align-items: center;">
                <canvas id="myChart" class="pie-chart" width="700" height="555"></canvas>
            </div>
        </div>

        <div class="no-selected" style="display: flex; justify-content: center; align-items: center; border-radius: 25px; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: white;">
            <div id="no-selected" style="margin-top: 3px; font-size: 2vh;">Select program and member to display chart data.</div>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    
    <script>
        const ctx = document.getElementById('myChart');
        Chart.register(ChartDataLabels);

        // Dummy data 
        var data = [0, 0, 0, 0, 0, 0];

        // Create an array of labels for each month
        var labels = [];
        for (var i = 5; i >= 0; i--) {
            var month = moment().subtract(i, 'months').format('MMMM');
            labels.push(month);
        }
        
        let myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: '6 Months Statistics',
                    data: data,
                    backgroundColor: [
                        '#FB3333',
                        '#FA972F',
                        '#FFEB34',
                        '#8AFB85',
                        '#66A2FA',
                        '#8051DF',
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
                    legend: {
                        labels: {
                            boxWidth: 0,
                            font: {
                                size: 20
                            }
                        }
                    },
                    datalabels: {
                        anchor: 'end',
                        align: 'top',
                        formatter: Math.round,
                        font: {
                            weight: 'bold'
                        }
                    }
                },
                scales: {
                    y: {
                        suggestedMin: 0,
                        suggestedMax: Math.round(Math.max(...data)) * (1 + 0.3),
                        ticks: {
                            beginAtZero: true,
                            callback: function(value) {if (value % 1 === 0) {return value;}}
                        }
                    },
                }
            },
        });
       
    var membersLoaded = false;
    var isDepartmentSelected = false;

    // Select faculty department dropdown scripts
    const dropdown1 = document.querySelector('.create-dropdown1');
    const list1 = document.querySelector('.create-list1');
    const selected1 = document.querySelector('.selected1');
    const selectedImg1 = document.querySelector('.selectedImg1');
    const caret1 = document.querySelector('.caret1');

    dropdown1.addEventListener('click', () => {
        list1.classList.toggle('show');
        caret1.classList.toggle('fa-rotate');
    });

    document.addEventListener('click', (e) => {
        if (!dropdown1.contains(e.target)) {
            list1.classList.remove('show');
            caret1.classList.remove('fa-rotate');
        }
    });

    list1.addEventListener('click', (e) => {
        const item = e.target.closest('.item1');

        if (item) {
            const img = item.querySelector('img');
            const text = item.querySelector('.text');
            
            selectedImg1.src = img.src;
            selected1.innerHTML = text.innerHTML;

            let selectedDepartment = selected1.innerHTML.trim();
            let list = document.querySelector('.list.create-list2');
            list.innerHTML = ''; // Clear the list of department members for faster rendering
            document.querySelector('.create-dropdown2').style.backgroundColor = 'grey';
            document.querySelector('.create-dropdown2').style.cursor = 'default';

            document.querySelector('.selected2').innerHTML = 'Select Member';

            if (selectedDepartment === 'All Departments') {
                let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');  
                fetch('/admin-dashboard/department-task/timeline/get-statistics', {
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json, text-plain, */*",
                        "X-Requested-With": "XMLHttpRequest",
                        "X-CSRF-TOKEN": token
                    },
                    method: 'POST',
                    credentials: "same-origin",
                    body: JSON.stringify({
                        department: selectedDepartment,
                        member: 'none'
                    })
                })
                .then(response => response.json())
                .then(result => {
                    console.log(result.data);

                    if (result.data !== 'null') {
                        document.querySelector('#loading-overlay').style.display = 'none';
                        document.querySelector('.no-selected').style.display = 'none';

                        // Parse the result.data property into an array
                        let data = JSON.parse(result.data);

                        // Update the chart data
                        myChart.options.scales.y.suggestedMax = Math.round(Math.max(...data)) * (1 + 0.3);
                        myChart.update();

                        myChart.data.datasets[0].data = data;
                        myChart.update();

                        document.querySelector('.total-task-data').innerHTML = result.total;
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
            else {
                membersLoaded = false;
                isDepartmentSelected = true;
                let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');  
                fetch('/admin-tasks/get-department-members', {
                        headers: {
                            "Content-Type": "application/json",
                            "Accept": "application/json, text-plain, */*",
                            "X-Requested-With": "XMLHttpRequest",
                            "X-CSRF-TOKEN": token
                        },
                        method: 'POST',
                        credentials: "same-origin",
                        body: JSON.stringify({
                            department: selectedDepartment
                        })
                    })
                    .then(response => response.json())
                    .then(result => {
                        if (result.members.length !== 0) {
                            let list = document.querySelector('.list.create-list2');
                            list.innerHTML = '';

                            // Create and append the "All" option
                            let allItem = document.createElement('div');
                            allItem.classList.add('item2');
                            allItem.innerHTML = `
                                <img src="{{ asset('admin/images/user.png') }}" alt="">
                                <div class="text">All Members</div>
                            `;
                            list.appendChild(allItem);

                            // Iterate over the members array
                            result.members.forEach((member, index) => {
                                let item = document.createElement('div');
                                item.classList.add('item2');
                                item.innerHTML = `
                                    <img src="{{ asset('admin/images/user.png') }}" alt="">
                                    <div class="text">
                                        ${member.first_name} ${member.middle_name ? member.middle_name + ' ' : ''}${member.last_name}
                                    </div>
                                `;
                                list.appendChild(item);
                            });

                        } 
                        else {
                            let list = document.querySelector('.list.create-list2');
                            list.innerHTML = '';
                            document.querySelector('.selected2').innerHTML = 'Select Member';
                            showNotification("There are no members in " + selectedDepartment + ' program.', '#fe3232bc');
                        }

                        // Department members list is ready
                        document.querySelector('.create-dropdown2').style.backgroundColor = '#fff';
                        membersLoaded = true;

                        /* Event listener to prevent from closing when clicking in member list
                        let items = document.querySelectorAll('.item1');
                        items.forEach(item => {
                            item.addEventListener('click', (event) => {
                                event.stopPropagation();
                            });
                        });*/

                        if (document.querySelector('.item2')) {

                        const item2 = document.querySelector('.item2');
                            item2.addEventListener('click', (e) => {
                                const item = e.target.closest('.item2');

                                // Check if the clicked element is an .item2 element
                                if (item) {
                                    const img = item.querySelector('img');
                                    const text = item.querySelector('.text');

                                    selectedImg2.src = img.src;
                                    selected2.innerHTML = text.innerHTML;
                                }
                            });
                        }
                    })
                    .catch(error => {
                        console.log(error);
                        membersLoaded = true;
                        isDepartmentSelected = true;
                        showNotification("Error occured in getting program members.", '#fe3232bc');
                    });
                }
            }
        });

         // Select department members dropdown scripts
        const dropdown2 = document.querySelector('.create-dropdown2');
        const list2 = document.querySelector('.create-list2');
        const selected2 = document.querySelector('.selected2');
        const selectedImg2 = document.querySelector('.selectedImg2');
        const caret2 = document.querySelector('.caret2');

        dropdown2.addEventListener('click', () => {
            if (selected1.innerHTML.trim() === 'All Departments') {
                return;
            } 

            if (isDepartmentSelected) {
                if (membersLoaded) {
                    list2.classList.toggle('show');
                    caret2.classList.toggle('fa-rotate');
                }
                else {
                    //showNotification("Loading department members please wait.", '#fe3232bc');
                }
            }
            else {
                showNotification("Select department first.", '#fe3232bc');
            }
        });

        document.addEventListener('click', (e) => {
            if (membersLoaded) {
                if (!dropdown2.contains(e.target)) {
                    list2.classList.remove('show');
                    caret2.classList.remove('fa-rotate');
                }
            }
        });

        list2.addEventListener('click', (e) => {
            const item = e.target.closest('.item2');

            // Check if the clicked element is an .item2 element
            if (item) {
                const img = item.querySelector('img');
                const text = item.querySelector('.text');

                selectedImg2.src = img.src;
                selected2.innerHTML = text.innerHTML;

                let selectedDepartment = selected1.innerHTML.trim();
                let selectedMember = selected2.innerHTML.trim();
                
                //document.querySelector('#loading-overlay').style.display = 'flex';
                let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');  
                fetch('/admin-dashboard/department-task/timeline/get-statistics', {
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json, text-plain, */*",
                        "X-Requested-With": "XMLHttpRequest",
                        "X-CSRF-TOKEN": token
                    },
                    method: 'POST',
                    credentials: "same-origin",
                    body: JSON.stringify({
                        department: selectedDepartment,
                        member: selectedMember
                    })
                })
                .then(response => response.json())
                .then(result => {
                    console.log(result.data);

                    if (result.data !== 'null') {
                        document.querySelector('#loading-overlay').style.display = 'none';
                        document.querySelector('.no-selected').style.display = 'none';

                        // Parse the result.data property into an array
                        let data = JSON.parse(result.data);

                        // Update the chart data
                        myChart.options.scales.y.suggestedMax = Math.round(Math.max(...data)) * (1 + 0.3);
                        myChart.update();

                        myChart.data.datasets[0].data = data;
                        myChart.update();

                        document.querySelector('.total-task-data').innerHTML = result.total;
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

    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script>
    @endsection

