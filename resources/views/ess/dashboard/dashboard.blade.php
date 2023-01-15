@extends('layouts.app')
@section('content')
<div class="d-flex flex-column flex-column-fluid">
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">{{ $selected_menu->name }}</h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="../../demo1/dist/index.html" class="text-muted text-hover-primary">{{ $selected_modul->name }}</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">{{ $selected_sub_modul->name }}</li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">{{ $selected_menu->name}}</li>
                </ul>
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <form method="GET" id="form-filter">
                    <x-views.filter-month-year name-month="filterMonth" value-month="{{ $filterMonth }}" name-year="filterYear" class="me-5" value-year="{{ $filterYear }}" range="5" />
                </form>
            </div>
        </div>
    </div>
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
                <div class="col-lg-12 col-xl-6 mb-5 mb-xl-0">
                    <div class="card">
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold text-dark">Rekap Absen</span>
                                <span class="text-gray-400 mt-1 fw-semibold fs-6"> per {{ numToMonth($filterMonth)." ".$filterYear }}</span>
                            </h3>
                        </div>
                        <div class="card-body">
                            <div id="recap-attendance" style="height: 300px;"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 col-xl-6 mb-5 mb-xl-0">
                    <div class="card">
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold text-dark">Data Terlambat & Tidak Absen</span>
                                <span class="text-gray-400 mt-1 fw-semibold fs-6"> per {{ numToMonth($filterMonth)." ".$filterYear }}</span>
                            </h3>
                        </div>
                        <div class="card-body">
                            <div id="attendance" style="height: 300px;"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row g-5 g-xl-10 mb-xl-10">
                <div class="col-lg-12 col-xl-12 mb-5 mb-xl-0">
                    <div class="card">
                        <div class="card-header border-0 pt-5">
                            <h3 class="card-title align-items-start flex-column">
                                <span class="card-label fw-bold text-dark">Rekap Timesheet</span>
                                <span class="text-gray-400 mt-1 fw-semibold fs-6">{{ convertMinutesToTime($arrTotalDurationInMonthTimesheet / 60, '%02d Jam %02d Menit') }} per {{ numToMonth($filterMonth)." ".$filterYear }}</span>
                            </h3>
                        </div>
                        <div class="card-body">
                            <div id="timesheet" style="height: 300px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end::Content-->
</div>
@endsection
@section('scripts')
    <script src="https://cdn.amcharts.com/lib/5/index.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/xy.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/percent.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/radar.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>
    <script>
        //trigger onchange filter month
        $('#filterMonth').on('change', function() {
            $('#form-filter').submit();
        });

        $('#filterYear').on('change', function() {
            $('#form-filter').submit();
        });

        function initRecapAttendance(){
            var element = document.getElementById('recap-attendance');

            if (!element) {
                return;
            }

            var options = {
                series: [
                {
                    name: 'Hadir',
                    data: [{{ $totalAttendance['P'] ?? 0 }}]
                },
                {
                    name: 'Alpha',
                    data: [{{ $totalAttendance['A'] ?? 0 }}]
                },
                {
                    name: 'Sakit',
                    data: [{{ $totalAttendance['S'] ?? 0 }}]
                },
                {
                    name: 'Cuti',
                    data: [{{ $totalAttendance['C'] ?? 0 }}]
                }],
                chart: {
                    type: 'bar',
                    height: 280,
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '80%',
                        endingShape: 'rounded'
                    },
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                xaxis: {
                    categories: ['{{ numToMonth($filterMonth)." ".$filterYear }}'],
                    labels: {
                        show: false
                    },
                },
                yaxis: {
                    title: {
                        text: 'Jumlah'
                    }
                },
                fill: {
                    opacity: 1
                },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return val + " kali"
                        }
                    }
                },
            };

            var chart = new ApexCharts(element, options);
            chart.render();
        }

        function initAttendance(){
            var element = document.getElementById('attendance');

            if (!element) {
                return;
            }

            var options = {
                series: [
                    {
                        name: 'Terlambat',
                        data: [{{ $arrLateNotAttendance['late'] ?? 0 }}]
                    },
                    {
                        name: 'Tidak Absen Masuk',
                        data: [{{ $arrLateNotAttendance['notAttendanceStart'] ?? 0 }}]
                    },
                    {
                        name: 'Tidak Absen Pulang',
                        data: [{{ $arrLateNotAttendance['notAttendanceEnd'] ?? 0 }}]
                    }],
                chart: {
                    type: 'bar',
                    height: 280,
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '80%',
                        endingShape: 'rounded'
                    },
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                xaxis: {
                    categories: ['{{ numToMonth($filterMonth)." ".$filterYear }}'],
                    labels: {
                        show: false
                    },
                },
                yaxis: {
                    title: {
                        text: 'Jumlah'
                    }
                },
                fill: {
                    opacity: 1
                },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return val + " kali"
                        }
                    }
                },
            };

            var chart = new ApexCharts(element, options);
            chart.render();
        }

        function initTimesheet(){
            var element = document.getElementById('timesheet');

            var height = parseInt(KTUtil.css(element, 'height'));
            var labelColor = KTUtil.getCssVariableValue('--kt-gray-500');
            var borderColor = KTUtil.getCssVariableValue('--kt-border-dashed-color');

            if (!element) {
                return;
            }

            @php
                $currentMonth = $filterMonth;
                $currentYear = $filterYear;

                $start = Carbon\Carbon::create($currentYear, $currentMonth, 1)->startOfMonth();
                $end = Carbon\Carbon::create($currentYear, $currentMonth, 1)->endOfMonth();
                $range = Carbon\CarbonPeriod::create($start, $end);
                $days = [];
                foreach ($range as $date) {
                    $days[] = $date->format('d');
                }
            @endphp

            var options = {
                series: [
                    {
                        name: 'Timesheet',
                        data: [
                            @foreach($days as $key => $day)
                                '{{ isset($arrTotalDurationTimesheet[$day]) ? convertMinutesToTime($arrTotalDurationTimesheet[$day] / 60) : '00:00' }}',
                            @endforeach
                        ]
                    },
                ],
                chart: {
                    fontFamily: 'inherit',
                    type: 'area',
                    height: height,
                    toolbar: {
                        show: false
                    }
                },
                dataLabels: {
                    enabled: false
                },
                legend: {
                    show: true,
                    formatter: function(seriesName) {
                        return [seriesName, '(test)'];
                    },
                },
                fill: {
                    type: "gradient",
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.4,
                        opacityTo: 0.2,
                        stops: [15, 120, 100]
                    }
                },
                stroke: {
                    curve: 'smooth',
                    show: true,
                    width: 3,
                },
                xaxis: {
                    categories: [
                        @foreach($days as $key => $day)
                            '{{ $day }}',
                        @endforeach
                    ],
                    axisBorder: {
                        show: false,
                    },
                    axisTicks: {
                        show: false
                    },
                    tickAmount: 30,
                    labels: {
                        rotate: 0,
                        rotateAlways: true,
                        style: {
                            colors: labelColor,
                            fontSize: '12px'
                        }
                    },
                    crosshairs: {
                        position: 'front',
                        stroke: {
                            width: 1,
                            dashArray: 3
                        }
                    },
                    tooltip: {
                        enabled: true,
                        formatter: undefined,
                        offsetY: 0,
                        style: {
                            fontSize: '12px'
                        }
                    }
                },
                yaxis: {
                    tickAmount: 6,
                    title: {
                        text: 'Jam',
                        style: {
                            color: undefined,
                            fontSize: '12px',
                            fontFamily: 'Helvetica, Arial, sans-serif',
                            fontWeight: 600,
                            cssClass: 'apexcharts-yaxis-title',
                        },
                    },
                    labels: {
                        style: {
                            colors: labelColor,
                            fontSize: '12px'
                        }
                    }
                },
                states: {
                    normal: {
                        filter: {
                            type: 'none',
                            value: 0
                        }
                    },
                    hover: {
                        filter: {
                            type: 'none',
                            value: 0
                        }
                    },
                    active: {
                        allowMultipleDataPointsSelection: false,
                        filter: {
                            type: 'none',
                            value: 0
                        }
                    }
                },
                tooltip: {
                    style: {
                        fontSize: '12px'
                    }
                },
                grid: {
                    borderColor: borderColor,
                    strokeDashArray: 4,
                    yaxis: {
                        lines: {
                            show: true
                        }
                    }
                },
                markers: {
                    strokeWidth: 3
                }
            };

            var chart = new ApexCharts(element, options);
            chart.render();
        }

        am5.ready(function () {
            initRecapAttendance();
            initAttendance();
            initTimesheet();
        });
    </script>
@endsection
