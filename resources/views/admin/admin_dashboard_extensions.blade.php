@extends('layouts.default')

@section('title', 'PUPQC - Extensions')

@section('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('admin/css/admin_dashboard_extensions.css') }}">
@endsection

@section('body')
<div class="overlay"></div>
@include('layouts.admin_navbar')
@include('layouts.admin_dashboard_sidebar')
@include('layouts.notification_side')

<div class="container-fluid margin">
    <div class="row">
        <div class="col-12" style="display: flex;">
            <h1 class="my-4 title">Extensions Analytics</h1>

            <button class="create-btn" style="margin-left: auto; margin-top: 2%" onclick="exportData()">Export Data</button>
        </div>
    </div>

    <div class="container-fluid task-list" style="position: relative;">
        <div class="row">
            <div class="col-6 d-flex flex-column justify-content-center align-items-center mt-4">
                <label for="" style="font-weight: 700; font-size: 30px;">Extensions Type</label>
                <div style="width: 80%; height: 100%">
                    <canvas id="extension-category-chart"></canvas>
                </div>
            </div>
            <div class="col-6 d-flex flex-column justify-content-center align-items-center mt-4">
                <label for="" style="font-weight: 700; font-size: 30px;">Level of Extension</label>
                <div style="width: 80%; height: 100%">
                    <canvas id="extension-level-chart"></canvas>
                </div>
            </div>
        </div>
        <div style="margin-top: 5%"></div>
        <div class="row">
            <div class="col-6 d-flex flex-column justify-content-center align-items-center mt-4" style="height: 500px;">
                <label for="" style="font-weight: 700; font-size: 30px;">Extensions</label>
                <div style="width: 50%; height: 100%">
                    <canvas id="extension-type-chart"></canvas>
                </div>
            </div>
            <div class="col-6 d-flex flex-column justify-content-center align-items-center mt-4" style="height: 500px;">
                <label for="" style="font-weight: 700; font-size: 30px;">Status</label>
                <div style="width: 50%; height: 100%">
                    <canvas id="extension-status-chart"></canvas>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-6 d-flex flex-column justify-content-center align-items-center mt-4">
                <label for="" style="font-weight: 700; font-size: 30px;">No. of Hours (per extension)</label>
                <div style="width: 80%; height: 100%">
                    <canvas id="extension-totalhours-chart"></canvas>
                </div>
            </div>
            <div class="col-6 d-flex flex-column justify-content-center align-items-center mt-4">
                <label for="" style="font-weight: 700; font-size: 30px;">Funding Type</label>
                <div style="width: 80%; height: 100%">
                    <canvas id="extension-funding-chart"></canvas>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    
    <script>
        Chart.register(ChartDataLabels);
        const getCategoryOfExtensionChart = document.getElementById('extension-category-chart');
        const getLevelOfExtensionChart = document.getElementById('extension-level-chart');
        const getTypeOfExtensionChart = document.getElementById('extension-type-chart');
        const getFundingTypeChart = document.getElementById('extension-funding-chart');
        const getTotalNoHoursChart = document.getElementById('extension-totalhours-chart');
        const getStatusChart = document.getElementById('extension-status-chart');

        const categoryOfExtensionData = {!! json_encode($extensionCount) !!};
        const levelData = {!! json_encode($extensionLevelCount) !!};
        const typeData = {!! json_encode($extensionTypeCount) !!};
        const fundingTypeData = {!! json_encode($extensionFundingTypeCount) !!};
        const totalNoHoursData = {!! json_encode($extensionTotalHours) !!};
        const statusData = {!! json_encode($extensionStatusCount) !!};

        let myChart = new Chart(getCategoryOfExtensionChart, {
        type: 'bar',
        data: {
            labels: ['Extension Program', 'Extension Project', 'Extension Activity'],
            datasets: [{
                label: 'Total',
                data: categoryOfExtensionData,
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
            //maintainAspectRatio: false,
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

    // Update the category chart data
    myChart.data.datasets[0].data = categoryOfExtensionData;
    myChart.update();
    
    let myLevelChart = new Chart(getLevelOfExtensionChart, {
        type: 'bar',
        data: {
            labels: ['International', 'National', 'Regional', 'Provincial/City', 'Local-PUP'],
            datasets: [{
                label: 'Total',
                data: levelData,
                backgroundColor: [
                    '#06A64B',
                    '#fa1635',
                    '#F6B000',
                    '#7499d4',
                    '#f093f5',
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
            //maintainAspectRatio: false,
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
                },
            },
        });

    // Update the level chart data
    myLevelChart.data.datasets[0].data = levelData;
    myLevelChart.update();

    let myTypeChart = new Chart(getTypeOfExtensionChart, {
        type: 'pie',
        data: {
            labels: ['Training', 'Technical/Advisory', 'Outreach'],
            datasets: [{
                label: 'Type of Extension',
                data: typeData,
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
                        size: 14,
                    }
                }
            }
        }
    });

    // Update the type chart data
    myTypeChart.data.datasets[0].data = typeData;
    myTypeChart.update();

    let myFundingChart = new Chart(getFundingTypeChart, {
        type: 'bar',
        data: {
            labels: ['University Funded', 'Self Funded', 'Externally Funded'],
            datasets: [{
                label: 'Total',
                data: fundingTypeData,
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
            //maintainAspectRatio: false,
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

    // Update the funding chart data
    myFundingChart.data.datasets[0].data = fundingTypeData;
    myFundingChart.update();

    let myTotalNoHoursChart = new Chart(getTotalNoHoursChart, {
        type: 'bar',
        data: {
            labels: ['1-10 hours', '11-20 hours', '21-30 hours', '31-40 hours', '41-50 hours', '51 and above hours'],
            datasets: [{
                label: 'Total',
                data: totalNoHoursData,
                backgroundColor: [
                    '#06A64B',
                    '#fa1635',
                    '#F6B000',
                    '#7499d4',
                    '#f093f5',
                    '#e87420',
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
            //maintainAspectRatio: false,
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

    // Update the total no hours chart data
    myTotalNoHoursChart.data.datasets[0].data = totalNoHoursData;
    myTotalNoHoursChart.update();

    let myStatusChart = new Chart(getStatusChart, {
        type: 'pie',
        data: {
            labels: ['Ongoing', 'Completed'],
            datasets: [{
                label: 'Type of Extension',
                data: statusData,
                backgroundColor: [
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
                        size: 14,
                    }
                }
            }
        }
    });

    // Update the status chart data
    myStatusChart.data.datasets[0].data = statusData;
    myStatusChart.update();

    function exportData(){
        showNotification("Downloading file in a moment.", '#278a51');

        const endpoint = `/admin-dashboard/extensions/export-data`;
        window.location.href = endpoint;
    }
            
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script>
    @endsection

