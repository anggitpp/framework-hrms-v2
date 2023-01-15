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
                    <div class="col-lg-12 col-xl-4 mb-5 mb-xl-0">
                        <div class="card">
                            <div class="card-header border-0 pt-5">
                                <h3 class="card-title align-items-start flex-column">
                                    <span class="card-label fw-bold text-dark">Total Pegawai Aktif</span>
                                    <span class="text-gray-400 mt-1 fw-semibold fs-6">{{ $totalEmployee }} per hari ini</span>
                                </h3>
                            </div>
                            <div class="card-body">
                                <div id="empCategory" style="height: 200px;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12 col-xl-4 mb-5 mb-xl-0">
                        <div class="card">
                            <div class="card-header border-0 pt-5">
                                <h3 class="card-title align-items-start flex-column">
                                    <span class="card-label fw-bold text-dark">Total Pengajuan</span>
                                    <span class="text-gray-400 mt-1 fw-semibold fs-6">{{ $totalSubmission }} per {{ numToMonth($filterMonth)." ".$filterYear }}</span>
                                </h3>
                            </div>
                            <div class="card-body">
                                <div id="submission" style="height: 200px;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12 col-xl-4 mb-5 mb-xl-0">
                        <div class="card">
                            <div class="card-header border-0 pt-5">
                                <h3 class="card-title align-items-start flex-column">
                                    <span class="card-label fw-bold text-dark">Data Gender</span>
                                    <span class="text-gray-400 mt-1 fw-semibold fs-6">{{ $totalEmployee }} per hari ini</span>
                                </h3>
                            </div>
                            <div class="card-body">
                                <div id="empGender" style="height: 200px;"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row g-5 g-xl-10 mb-xl-10">
                    <div class="col-lg-12 col-xl-12 mb-5 mb-xl-0">
                        <div class="card">
                            <div class="card-header border-0 pt-5">
                                <h3 class="card-title align-items-start flex-column">
                                    <span class="card-label fw-bold text-dark">Rekap Absensi</span>
                                    <span class="text-gray-400 mt-1 fw-semibold fs-6">{{ numToMonth($filterMonth)." ".$filterYear }}</span>
                                </h3>
                            </div>
                            <div class="card-body">
                                <div id="attendance" style="height: 300px;"></div>
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

        function initEmpCategory(){
            var element = document.getElementById('empCategory');

            var successColor = KTUtil.getCssVariableValue('--kt-success');
            var primaryColor = KTUtil.getCssVariableValue('--kt-primary');
            var dangerColor = KTUtil.getCssVariableValue('--kt-danger');
            var infoColor = KTUtil.getCssVariableValue('--kt-info');
            var warningColor = KTUtil.getCssVariableValue('--kt-warning');

            if (!element) {
                return;
            }

            var options = {
                series: [
                    @foreach($totalEmployeeByCategories as $key => $value)
                        {{ $value }},
                    @endforeach
                ],
                chart: {
                    type: 'pie',
                },
                plotOptions: {
                    pie: {
                        dataLabels: {
                            offset: -20,
                            minAngleToShowLabel: 10
                        },
                    }
                },
                labels: [
                    @foreach($totalEmployeeByCategories as $key => $value)
                        '{{ $key }}',
                    @endforeach
                ],
                colors: [successColor, primaryColor, dangerColor, infoColor, warningColor],
            };

            var chart = new ApexCharts(element, options);
            chart.render();

            //END DASHBOARD TOTAL EMPLOYEE BY CATEGORY
        }

        function initEmpSubmission(){
            var element = document.getElementById('submission');

            var successColor = KTUtil.getCssVariableValue('--kt-success');
            var primaryColor = KTUtil.getCssVariableValue('--kt-primary');
            var dangerColor = KTUtil.getCssVariableValue('--kt-danger');

            if (!element) {
                return;
            }

            var options = {
                series: [{
                    name: 'Sakit',
                    data: [{{ $totalSicks ?? 0 }}]
                }, {
                    name: 'Cuti',
                    data: [{{ $totalLeaves ?? 0 }}]
                }, {
                    name: 'Koreksi',
                    data: [{{ $totalCorrections ?? 0 }}]
                }],
                chart: {
                    type: 'bar',
                    height: 180
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
                        text: 'Jumlah Pengajuan'
                    }
                },
                fill: {
                    opacity: 1
                },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return val + " pengajuan"
                        }
                    }
                },
                colors: [successColor, primaryColor, dangerColor],

            };

            var chart = new ApexCharts(element, options);
            chart.render();
            //END DASHBOARD SUBMISSION
        }

        function initEmpGender(){
            var element = document.getElementById('empGender');

            var successColor = KTUtil.getCssVariableValue('--kt-success');
            var primaryColor = KTUtil.getCssVariableValue('--kt-primary');

            if (!element) {
                return;
            }

            var options = {
                series: [
                    @foreach($totalEmployeeByGender as $key => $value)
                        {{ $value }},
                    @endforeach
                ],
                chart: {
                    type: 'pie',
                    height:180,
                },
                plotOptions: {
                    pie: {
                        dataLabels: {
                            offset: -20,
                            minAngleToShowLabel: 10
                        },
                    }
                },
                labels: [
                    @foreach($totalEmployeeByGender as $key => $value)
                        '{{ $key == 'm' ? 'Pria' : 'Wanita' }}',
                    @endforeach
                ],
                colors: [successColor, primaryColor],
            };

            var chart = new ApexCharts(element, options);
            chart.render();
        }

        function initAttendance(){
            var element = document.getElementById('attendance');

            var height = parseInt(KTUtil.css(element, 'height'));
            var labelColor = KTUtil.getCssVariableValue('--kt-gray-500');
            var borderColor = KTUtil.getCssVariableValue('--kt-border-dashed-color');
            var basePresentColor = KTUtil.getCssVariableValue('--kt-success');
            var lightPresentColor = KTUtil.getCssVariableValue('--kt-success');
            var basePermissionColor = KTUtil.getCssVariableValue('--kt-primary');
            var lightPermissionColor = KTUtil.getCssVariableValue('--kt-primary');
            var baseAlphaColor = KTUtil.getCssVariableValue('--kt-danger');
            var lightAlphaColor = KTUtil.getCssVariableValue('--kt-danger');
            var baseLeaveColor = KTUtil.getCssVariableValue('--kt-info');
            var lightLeaveColor = KTUtil.getCssVariableValue('--kt-info');

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
                        name: 'Hadir',
                        data: [
                            @foreach($days as $key => $day)
                                '{{ $totalAttendanceByDay['P'][$key] ?? 0 }}',
                            @endforeach
                        ]
                    },
                    {
                        name: 'Alpha',
                        data: [
                            @foreach($days as $key => $day)
                                '{{ $totalAttendanceByDay['A'][$key] ?? 0 }}',
                            @endforeach
                        ]
                    },
                    {
                        name: 'Sakit',
                        data: [
                            @foreach($days as $key => $day)
                                '{{ $totalAttendanceByDay['S'][$key] ?? 0 }}',
                            @endforeach
                        ]
                    },
                    {
                        name: 'Cuti',
                        data: [
                            @foreach($days as $key => $day)
                                '{{ $totalAttendanceByDay['C'][$key] ?? 0 }}',
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
                plotOptions: {

                },
                legend: {
                    show: true,
                    formatter: function(seriesName) {
                        let total = 0;
                        if(seriesName === 'Hadir'){
                            total = {{ $totalRecapByMonth['P'] ?? 0 }};
                        }else if(seriesName === 'Alpha'){
                            total = {{ $totalRecapByMonth['A'] ?? 0 }};
                        }else if(seriesName === 'Sakit'){
                            total = {{ $totalRecapByMonth['S'] ?? 0 }};
                        }else if(seriesName === 'Cuti'){
                            total = {{ $totalRecapByMonth['C'] ?? 0 }};
                        }
                        return [seriesName, '('+total+')'];
                    },
                },
                dataLabels: {
                    enabled: false
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
                    colors: [basePresentColor, baseAlphaColor, basePermissionColor, baseLeaveColor]
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
                            color: [basePresentColor, baseAlphaColor, basePermissionColor, baseLeaveColor],
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
                colors: [lightPresentColor, lightAlphaColor, lightPermissionColor, lightLeaveColor],
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
                    strokeColor: [basePresentColor, baseAlphaColor, basePermissionColor, baseLeaveColor],
                    strokeWidth: 3
                }
            };

            var chart = new ApexCharts(element, options);
            chart.render();
        }

        am5.ready(function () {
            initEmpCategory();
            initEmpSubmission();
            initEmpGender();
            initAttendance();
        });
    </script>
@endsection
