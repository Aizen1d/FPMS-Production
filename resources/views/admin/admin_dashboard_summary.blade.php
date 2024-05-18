@extends('layouts.default')

@section('title', 'PUPQC - Summary')

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
      <h1 class="my-4 title">Accomplishments Summary</h1>

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

  <div class="container-fluid task-list2" style="position: relative;">
    <div class="d-flex justify-content-center align-items-center mt-3">
      <label class="mt-3" for="" style="font-weight: 700; font-size: 30px;">Memo</label>
    </div>
    <div class="row">
      <div class="col-6" style="padding-left: 5%">
        <div class="memo-table voter-list ml-3 mt-3">
          <table class="table table-striped">
            <thead>
              <tr>
                <th scope="col"><span class="table-padding1">Title</span></th>
                <th scope="col"><span class="table-padding1 center">Status</span></th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><span class="table-padding"></span></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="col-6" style="display: flex; justify-content: center; align-items: center;">
        <div class="row" style="display: flex; justify-content: center; align-items: center;">
          <div class="col-12" style="position: relative;">
            <div class="chart-info" style="display: flex; justify-content: center; align-items: center;">
              <h3 class="chart-label-memo assigned-label"><b>Assigned Memo:</b> <span class="assigned-data" style="font-weight: normal; color: #363636"></span></h3>
              <h3 class="chart-label-memo"><b>Completed Memo:</b> <span class="assigned-completed" style="font-weight: normal; color: #363636"></span></h3>
              <h3 class="chart-label-memo "><b>Late Completed Memo:</b> <span class="assigned-late-completed" style="font-weight: normal; color: #363636"></span></h3>
              <h3 class="chart-label-memo "><b>Ongoing Memo:</b> <span class="assigned-ongoing" style="font-weight: normal; color: #363636"></span></h3>
              <h3 class="chart-label-memo "><b>Missing Memo:</b> <span class="assigned-missing" style="font-weight: normal; color: #363636"></span></h3>
            </div>
          </div>
          <div class="col-12" style="display: flex; justify-content: center; align-items: center; height: 400px;">
            <canvas id="myMemoChart" class="pie-chart" width="300" height="400"></canvas>
          </div>
        </div>
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

  <div class="mt-3"></div>

  <div class="container-fluid task-list" style="position: relative;">
    <div class="d-flex justify-content-center align-items-center mt-3">
      <label for="" style="font-weight: 700; font-size: 30px;">Researches</label>
    </div>
    <div class="row">
      <div class="col-6" style="padding-left: 5%">
        <div class="research-table voter-list ml-3 mt-3">
          <table class="table table-striped">
            <thead>
              <tr>
                <th scope="col"><span class="table-padding1">Title</span></th>
                <th scope="col"><span class="table-padding1 center">Status</span></th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><span class="table-padding"></span></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="col-6" style="display: flex; justify-content: center; align-items: center;">
        <div class="row" style="display: flex; justify-content: center; align-items: center;">
          <div class="col-12" style="position: relative;">
            <div class="chart-info" style="display: flex; justify-content: space-between; align-items: center;">
              <h3 class="chart-label-memo"><b>Presented:</b><span class="research-presented" style="font-weight: normal; color: #363636"></span></h3>
              <h3 class="chart-label-memo"><b>Completed:</b><span class="research-completed" style="font-weight: normal; color: #363636"></span></h3>
              <h3 class="chart-label-memo"><b>Published:</b><span class="research-published" style="font-weight: normal; color: #363636"></span></h3>
              <h3 class="chart-label-memo"><b>Total:</b><span class="research-total" style="font-weight: normal; color: #363636"></span></h3>
            </div>
          </div>
          <div class="col-12" style="display: flex; justify-content: center; align-items: center;">
            <div style="width: 300px; height: 400px;">
              <canvas id="myResearchChart" class="pie-chart" width="300" height="400"></canvas>
            </div>
          </div>
        </div>
      </div>

    </div>

    <div class="no-selected research-cover" style="display: flex; justify-content: center; align-items: center; border-radius: 25px; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: white;">
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

  <div class="mt-3"></div>

  <div class="container-fluid task-list" style="position: relative;">
    <div class="d-flex justify-content-center align-items-center mt-3">
      <label for="" style="font-weight: 700; font-size: 30px;">Attendance</label>
    </div>
    <div class="row">
      <div class="col-6" style="padding-left: 5%">
        <div class="attendance-table voter-list ml-3 mt-3">
          <table class="table table-striped">
            <thead>
              <tr>
                <th scope="col"><span class="table-padding1">Brief Description</span></th>
                <th scope="col"><span class="table-padding1 center">Status</span></th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><span class="table-padding"></span></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="col-6" style="display: flex; justify-content: center; align-items: center;">
        <div class="row" style="display: flex; justify-content: center; align-items: center;">
          <div class="col-12" style="position: relative;">
            <div class="chart-info" style="display: flex; justify-content: space-between; align-items: center;">
              <h3 class="chart-label-memo"><b>Approved:</b> <span class="item-approved" style="font-weight: normal; color: #363636"></span></h3>
              <h3 class="chart-label-memo"><b>Rejected:</b> <span class="item-rejected" style="font-weight: normal; color: #363636"></span></h3>
              <h3 class="chart-label-memo"><b>Pending:</b> <span class="item-pending" style="font-weight: normal; color: #363636"></span></h3>
              <h3 class="chart-label-memo"><b>Total:</b> <span class="item-total" style="font-weight: normal; color: #363636"></span></h3>
            </div>
          </div>
          <div class="col-12" style="display: flex; justify-content: center; align-items: center;">
            <canvas id="myAttendanceChart" class="pie-chart" width="300" height="400"></canvas>
          </div>
        </div>
      </div>

    </div>

    <div class="no-selected attendance-cover" style="display: flex; justify-content: center; align-items: center; border-radius: 25px; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: white;">
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

  <div class="mt-3"></div>

  <div class="container-fluid task-list" style="position: relative;">
    <div class="d-flex justify-content-center align-items-center mt-3">
      <label for="" style="font-weight: 700; font-size: 30px;">Trainings & Seminars</label>
    </div>
    <div class="row">
      <div class="col-12 d-flex flex-column justify-content-center align-items-center mt-4">
        <div class="seminars-table voter-list">
          <table class="table table-striped">
            <thead>
              <tr>
                <th scope="col"><span class="table-padding1">Title</span></th>
                <th scope="col"><span class="table-padding1 center">Classification</span></th>
                <th scope="col"><span class="table-padding1 center">No. of Hours</span></th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><span class="table-padding"></span></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
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

  <div class="mb-4"></div>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

  <script>
    Chart.register(ChartDataLabels);

    const ctx = document.getElementById('myMemoChart');
    var data = [0, 0, 0, 0]; // place holder

    let myMemoChart = new Chart(ctx, {
      type: 'pie',
      data: {
        labels: ['Completed Memo', 'Late Completed Memo', 'Ongoing Memo', 'Missing Memo'],
        datasets: [{
          label: 'Total',
          data: data,
          backgroundColor: [
            '#06A64B',
            '#69BB37',
            '#F6B000',
            '#FE432A'
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
        plugins: {
          datalabels: {
            formatter: (value, ctx) => {
              if (value >= 1) {
                let sum = ctx.dataset.data.reduce((a, b) => a + b, 0);
                let percentage = ctx.chart.data.labels[ctx.dataIndex] + '\n' + (value * 100 / sum).toFixed(2) + "%";
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

    // Research Chart
    const researchChart = document.getElementById('myResearchChart');
    var data = [0, 0, 0]; // place holder

    let myResearchChart = new Chart(researchChart, {
      type: 'pie',
      data: {
        labels: ['Presented', 'Completed', 'Published'],
        datasets: [{
          label: 'Total',
          data: data,
          backgroundColor: [
            '#06A64B',
            '#69BB37',
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
                let percentage = ctx.chart.data.labels[ctx.dataIndex] + '\n' + (value * 100 / sum).toFixed(2) + "%";
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

    const attendanceChart = document.getElementById('myAttendanceChart');
    Chart.register(ChartDataLabels);

    var data = [0, 0, 0]; // place holder

    let myAttendanceChart = new Chart(attendanceChart, {
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
                let percentage = ctx.chart.data.labels[ctx.dataIndex] + '\n' + (value * 100 / sum).toFixed(2) + "%";
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
        fetch('/admin-dashboard/summary/get-analytics', {
            headers: {
              "Content-Type": "application/json",
              "Accept": "application/json, text-plain, */*",
              "X-Requested-With": "XMLHttpRequest",
              "X-CSRF-TOKEN": token
            },
            method: 'POST',
            credentials: "same-origin",
            body: JSON.stringify({
              department: 'BSIT',
              member: selectedMember,
              memberId: selectedMemberId
            })
          })
          .then(response => response.json())
          .then(result => {
            if (result) {
              console.log(result);

              // Memo data

              // Hydrate the table
              let memotable = document.querySelector('.memo-table');
              let memotableBody = memotable.querySelector('tbody');
              memotableBody.innerHTML = '';

              result.allMemo.forEach(memo => {
                let row = document.createElement('tr');

                let title = document.createElement('td');
                title.innerHTML = memo.task_name;
                row.appendChild(title);

                let status = document.createElement('td');
                status.innerHTML = memo.status;
                row.appendChild(status);

                memotableBody.appendChild(row);
              });

              document.querySelector('.no-selected').style.display = 'none';
              myMemoChart.data.datasets[0].data = result.data;
              myMemoChart.update();

              document.querySelector('.assigned-data').innerHTML = result.assigned;
              document.querySelector('.assigned-completed').innerHTML = result.completed;
              document.querySelector('.assigned-late-completed').innerHTML = result.late_completed;
              document.querySelector('.assigned-ongoing').innerHTML = result.ongoing;
              document.querySelector('.assigned-missing').innerHTML = result.missing;

              // Research data

              // Hydrate the table
              let researchtable = document.querySelector('.research-table');
              let researchtableBody = researchtable.querySelector('tbody');
              researchtableBody.innerHTML = '';

              result.researchesCompleted.forEach(research => {
                let row = document.createElement('tr');

                let title = document.createElement('td');
                title.innerHTML = research.title;
                row.appendChild(title);

                let status = document.createElement('td');
                status.innerHTML = 'Completed';
                row.appendChild(status);

                researchtableBody.appendChild(row);
              });

              result.researchesPresented.forEach(research => {
                let row = document.createElement('tr');

                let title = document.createElement('td');
                title.innerHTML = research.completed_research.title;
                row.appendChild(title);

                let status = document.createElement('td');
                status.innerHTML = 'Presented';
                row.appendChild(status);

                researchtableBody.appendChild(row);
              });

              result.researchesPublished.forEach(research => {
                let row = document.createElement('tr');

                let title = document.createElement('td');
                title.innerHTML = research.completed_research.title;
                row.appendChild(title);

                let status = document.createElement('td');
                status.innerHTML = 'Published';
                row.appendChild(status);

                researchtableBody.appendChild(row);
              });

              document.querySelector('.research-cover').style.display = 'none';
              myResearchChart.data.datasets[0].data = result.researchesData;
              myResearchChart.update();

              document.querySelector('.research-presented').innerHTML = result.researchesPresented.length;
              document.querySelector('.research-completed').innerHTML = result.researchesCompleted.length;
              document.querySelector('.research-published').innerHTML = result.researchesPublished.length;
              document.querySelector('.research-total').innerHTML = result.researchesPresented.length + result.researchesCompleted.length + result.researchesPublished.length;

              // Attendance data

              // Hydrate the table
              let attendancetable = document.querySelector('.attendance-table');
              let attendancetableBody = attendancetable.querySelector('tbody');
              attendancetableBody.innerHTML = '';

              result.allAttendance.forEach(attendance => {
                let row = document.createElement('tr');

                let title = document.createElement('td');
                title.innerHTML = attendance.get_function.brief_description;
                row.appendChild(title);

                let status = document.createElement('td');
                status.innerHTML = attendance.status;
                row.appendChild(status);

                attendancetableBody.appendChild(row);
              });

              document.querySelector('.attendance-cover').style.display = 'none';
              myAttendanceChart.data.datasets[0].data = result.attendanceData;
              myAttendanceChart.update();

              document.querySelector('.item-approved').innerHTML = result.attendanceApproved;
              document.querySelector('.item-rejected').innerHTML = result.attendanceRejected;
              document.querySelector('.item-pending').innerHTML = result.attendancePending;
              document.querySelector('.item-total').innerHTML = result.totalAttendances;

              // Seminars Content

              // Hydrate the table
              let seminartable = document.querySelector('.seminars-table');
              let seminartableBody = seminartable.querySelector('tbody');
              seminartableBody.innerHTML = '';

              result.allSeminars.forEach(seminar => {
                let row = document.createElement('tr');

                let title = document.createElement('td');
                title.innerHTML = seminar.title;
                row.appendChild(title);

                let classification = document.createElement('td');
                classification.innerHTML = seminar.classification;
                row.appendChild(classification);

                let hours = document.createElement('td');
                hours.innerHTML = seminar.total_no_hours;
                row.appendChild(hours);

                seminartableBody.appendChild(row);
              });

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
      const endpoint = `/admin-dashboard/summary/export-data?memberId=${getSelectedMemberId}&memberFullName=${getSelectedMemberName}`;
      window.location.href = endpoint;
    }
  </script>

  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script>
  @endsection