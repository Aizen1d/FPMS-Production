@extends('layouts.default')

@section('title', 'PUPQC - Department Overview')

@section('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('admin/css/admin_show_department_overview.css') }}">
@endsection

@section('body')
<div class="overlay"></div>
@include('layouts.admin_navbar')
@include('layouts.admin_show_department_sidebar')
@include('layouts.notification_side')

<div class="container-fluid margin">
    <div class="row">
        <div class="col-6">
            <h1 class="my-4 title">{{ $departmentName }} Program</h1>
        </div>
        <div class="col-6 drop-down-container">
            
        </div>
    </div>

    <div class="container-fluid task-list" style="position: relative;">
        <div class="row">
            <div class="col-5" style="position: relative;">
                <div class="chart-info">
                    <h3 class="chart-label"><b>Assigned Memo:</b> <span style="font-weight: normal; color: #363636">{{ $assigned }}</span></h3>
                    <h3 class="chart-label"><b>Completed Memo:</b> <span style="font-weight: normal; color: #363636">{{ $completed }}</span></h3>
                    <h3 class="chart-label"><b>Late Completed Memo:</b> <span style="font-weight: normal; color: #363636">{{ $late_completed }}</span></h3>
                    <h3 class="chart-label"><b>Ongoing Memo:</b> <span style="font-weight: normal; color: #363636">{{ $ongoing }}</span></h3>
                    <h3 class="chart-label"><b>Missing Memo:</b> <span style="font-weight: normal; color: #363636">{{ $missing }}</span></h3>
                </div>
            </div>
            <div class="col-7" style="display: flex; justify-content: center; align-items: center;">
                <canvas id="myChart" class="pie-chart" width="700" height="555"></canvas>
            </div>
        </div>

        <div class="no-selected" style="display: none; z-index: 100; justify-content: center; align-items: center; border-radius: 25px; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-color: white;">
            <div id="no-selected" style="margin-top: 3px; font-size: 2vh;">No data to display.</div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
    
    <script>
        var status = '{{ $status }}';

        if (!status) {
            document.querySelector('.no-selected').style.display = 'flex';
        }

        const ctx = document.getElementById('myChart');
        Chart.register(ChartDataLabels);

        var data = {{ $data }}; // retrieved from server side

        new Chart(ctx, {
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

    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script>
    @endsection

