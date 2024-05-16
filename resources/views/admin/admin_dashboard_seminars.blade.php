@extends('layouts.default')

@section('title', 'PUPQC - Training & Seminar')

@section('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('admin/css/admin_dashboard_seminars.css') }}">
@endsection

@section('body')
<div class="overlay"></div>
@include('layouts.admin_navbar')
@include('layouts.admin_dashboard_sidebar')
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
      <div class="row">
          <div class="col-6 d-flex flex-column justify-content-center align-items-center mt-4">
              <label for="" style="font-weight: 700; font-size: 30px;">Classification</label>
              <div style="width: 100%; height: 150%">
                  <canvas id="extension-classification-chart"></canvas>
              </div>
          </div>
          <div class="col-6 d-flex flex-column justify-content-center align-items-center mt-4">
              <label for="" style="font-weight: 700; font-size: 30px;">Nature</label>
              <div style="width: 80%; height: 100%">
                  <canvas id="extension-nature-chart"></canvas>
              </div>
          </div>
      </div>
      <div style="margin-top: 5%"></div>
      <div class="row">
          <div class="col-6 d-flex flex-column justify-content-center align-items-center mt-4">
              <label for="" style="font-weight: 700; font-size: 30px;">Type</label>
              <div style="width: 80%; height: 100%">
                  <canvas id="extension-type-chart"></canvas>
              </div>
          </div>
          <div class="col-6 d-flex flex-column justify-content-center align-items-center mt-4">
              <label for="" style="font-weight: 700; font-size: 30px;">Source of Fund</label>
              <div style="width: 80%; height: 100%">
                  <canvas id="extension-sourcefund-chart"></canvas>
              </div>
          </div>
      </div>
      
      <div style="margin-top: 5%"></div>
      <div class="row">
          <div class="col-6 d-flex flex-column justify-content-center align-items-center mt-4">
            <label for="" style="font-weight: 700; font-size: 30px;">Level</label>
            <div style="width: 80%; height: 100%">
                <canvas id="extension-level-chart"></canvas>
            </div>
          </div>
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
        Chart.register(ChartDataLabels);
        const getClassificationChart = document.getElementById('extension-classification-chart');
        const getNatureChart = document.getElementById('extension-nature-chart');
        const getTypeChart = document.getElementById('extension-type-chart');
        const getSourceFundChart = document.getElementById('extension-sourcefund-chart');
        const getLevelChart = document.getElementById('extension-level-chart');

        let classificationData = []
        let natureData = []
        let typeData = []
        let sourceFundData = []
        let levelData = []

        let myClassificationChart = new Chart(getClassificationChart, {
        type: 'bar',
        data: {
            labels: ['Seminar/Webinar', 'Fora', 'Conference', 'Planning', 'Workshop', 'Professional/Continuing Professional', 'Short Term Courses', 'Executive/Managerial'],
            datasets: [{
                label: 'Total',
                data: classificationData,
                backgroundColor: [
                    '#06A64B',
                    '#fa1635',
                    '#F6B000',
                    '#06A64B',
                    '#fa1635',
                    '#3cd4e8',
                    '#bd7757',
                    '#06A64B',
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
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                },
            },
            plugins: {
                datalabels: {
                    color: '#fff',
                    font: {
                        size: 20,
                    }
                }
            }
        },
    });

    let myNatureChart = new Chart(getNatureChart, {
        type: 'bar',
        data: {
            labels: ['GAD-Related', 'Inclusivity and Diversity', 'Professional', 'Skills/Technical'],
            datasets: [{
                label: 'Total',
                data: natureData,
                backgroundColor: [
                    '#06A64B',
                    '#fa1635',
                    '#F6B000',
                    '#06A64B',
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
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                },
            },
            plugins: {
                datalabels: {
                    color: '#fff',
                    font: {
                        size: 20,
                    }
                }
            }
        },
    });

    let myTypeChart = new Chart(getTypeChart, {
        type: 'bar',
        data: {
            labels: ['Executive/Managerial', 'Foundation', 'Supervisory', 'Technical'],
            datasets: [{
                label: 'Total',
                data: typeData,
                backgroundColor: [
                    '#06A64B',
                    '#fa1635',
                    '#F6B000',
                    '#06A64B',
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
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                },
            },
            plugins: {
                datalabels: {
                    color: '#fff',
                    font: {
                        size: 20,
                    }
                }
            }
        },
    });

    let mySourceFundChart = new Chart(getSourceFundChart, {
        type: 'bar',
        data: {
            labels: ['University Funded', 'Self-Funded', 'Externally-Funded', 'Not a Paid Seminar/Training'],
            datasets: [{
                label: 'Total',
                data: sourceFundData,
                backgroundColor: [
                    '#06A64B',
                    '#fa1635',
                    '#F6B000',
                    '#06A64B',
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
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                },
            },
            plugins: {
                datalabels: {
                    color: '#fff',
                    font: {
                        size: 20,
                    }
                }
            }
        },
    });

    let myLevelChart = new Chart(getLevelChart, {
        type: 'bar',
        data: {
            labels: ['International', 'National', 'Local'],
            datasets: [{
                label: 'Total',
                data: levelData,
                backgroundColor: [
                    '#06A64B',
                    '#fa1635',
                    '#F6B000',
                    '#06A64B',
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
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                },
            },
            plugins: {
                datalabels: {
                    color: '#fff',
                    font: {
                        size: 20,
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
                    // Update the classification chart data
                    myClassificationChart.data.datasets[0].data = result.seminarClassificationCount;
                    myClassificationChart.update();

                    // Update the nature chart data
                    myNatureChart.data.datasets[0].data = result.seminarNatureCount;
                    myNatureChart.update();

                    // Update the type chart data
                    myTypeChart.data.datasets[0].data = result.seminarTypeCount;
                    myTypeChart.update();

                    // Update the source fund chart data
                    mySourceFundChart.data.datasets[0].data = result.seminarFundCount;
                    mySourceFundChart.update();

                    // Update the level chart data
                    myLevelChart.data.datasets[0].data = result.seminarLevelCount;
                    myLevelChart.update();
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

