@extends('layouts.default')

@section('title', 'PUPQC - Dashboard My Tasks')

@section('styles')
<link rel="stylesheet" type="text/css" href="{{ asset('faculty/css/faculty_dashboard_assigned_task_timeline.css') }}">
@endsection

@section('body')
<div class="overlay"></div>
@include('layouts.faculty_navbar')
@include('layouts.faculty_dashboard_sidebar')
@include('layouts.notification_side')

<div class="container-fluid margin">
    <div class="row">
        <div class="col-12">
            <h1 class="my-4 title">Memo Timeline</h1>
        </div>
    </div>

    <div class="container-fluid task-list" style="position: relative;">
        <div class="row">
            <div class="col-5" style="position: relative;">
                <div class="chart-info">
                    <h3 class="chart-label"><b>Total memo in 6 months:</b> <span style="font-weight: normal; color: #363636">{{ $total }}</span></h3>
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
</div>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    
    <script>
        var status = '{{ $status }}';

        if (!status) {
            document.querySelector('.no-selected').style.display = 'flex';
        }

        const ctx = document.getElementById('myChart');
        Chart.register(ChartDataLabels);

        // Data retrieved from server side
        var data = {{ $assigned }};

        console.log(data);

        // Create an array of labels for each month
        var labels = [];
        for (var i = 5; i >= 0; i--) {
            var month = moment().subtract(i, 'months').format('MMMM');
            labels.push(month);
        }
        
        new Chart(ctx, {
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

    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script>
    @endsection

