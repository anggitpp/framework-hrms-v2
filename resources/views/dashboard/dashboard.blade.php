@extends('layouts.app')
@section('content')
    <div class="row g-5 g-xl-10 mb-xl-10">
        <div class="col-lg-12 col-xl-4 mb-5 mb-xl-0">
            <div class="card">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-dark">Total Pegawai</span>
                        <span class="text-gray-400 mt-1 fw-semibold fs-6">{{ $totalEmployee }} per hari ini</span>
                    </h3>
                </div>
                <div class="card-body">
                    <div id="empCategory" style="height: 300px;"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-12 col-xl-4 mb-5 mb-xl-0">
            <div class="card">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold text-dark">Total Pengajuan</span>
                        <span class="text-gray-400 mt-1 fw-semibold fs-6">{{ $totalSubmission }} per hari ini</span>
                    </h3>
                </div>
                <div class="card-body">
                    <div id="submission" style="height: 300px;"></div>
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
                    <div id="empGender" style="height: 300px;"></div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="https://cdn.amcharts.com/lib/5/index.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/xy.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/percent.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/radar.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>
    <script>
        function initEmpCategory(){
            //START DASHBOARD TOTAL EMPLOYEE BY CATEGORY
            var root = am5.Root.new("empCategory");
            root.setThemes([
                am5themes_Animated.new(root)
            ]);
            var chart = root.container.children.push(am5percent.PieChart.new(root, {
                layout: root.verticalLayout,
            }));

            var series = chart.series.push(am5percent.PieSeries.new(root, {
                alignLabels: true,
                calculateAggregates: true,
                valueField: "value",
                categoryField: "category"
            }));

            series.slices.template.setAll({
                strokeWidth: 3,
                stroke: am5.color(0xffffff)
            });

            series.labelsContainer.set("paddingTop", 30)

            series.data.setAll([
                @php
                    foreach ($totalEmployeeByCategories as $category => $total){
                        echo "{value: $total, category: '$category'},";
                    }
                @endphp
            ]);

            var legend = chart.children.push(am5.Legend.new(root, {
                centerX: am5.p50,
                x: am5.p50,
                marginTop: 15,
                marginBottom: 15
            }));

            legend.data.setAll(series.dataItems);

            series.calculatePercent = true;
            series.slices.template.set('tooltipText', '{category}: {value}');
            series.labels.template.setAll({
                maxWidth: 80,
                oversizedBehavior: "wrap"
            });
            legend.valueLabels.template.set("forceHidden", true);
            series.appear(1000, 100);
            //END DASHBOARD TOTAL EMPLOYEE BY CATEGORY
        }

        function initEmpSubmission(){
            //START DASHBOARD SUBMISSION
            var root = am5.Root.new("submission");

            root.setThemes([
                am5themes_Animated.new(root)
            ]);

            var chart = root.container.children.push(am5xy.XYChart.new(root, {
                panX: true,
                panY: true,
                wheelX: "panX",
                wheelY: "zoomX",
                pinchZoomX:true
            }));

            chart.get("colors").set("colors", [
                am5.color('#EAC7C7'),
                am5.color('#A0C3D2'),
                am5.color('#EAE0DA'),
            ]);

            var cursor = chart.set("cursor", am5xy.XYCursor.new(root, {}));
            cursor.lineY.set("visible", false);

            var xRenderer = am5xy.AxisRendererX.new(root, { minGridDistance: 30 });
            xRenderer.labels.template.setAll({
                centerY: am5.p50,
                centerX: am5.p50,
                paddingTop: 15
            });

            var xAxis = chart.xAxes.push(am5xy.CategoryAxis.new(root, {
                maxDeviation: 0.3,
                categoryField: "country",
                renderer: xRenderer,
                tooltip: am5.Tooltip.new(root, {})
            }));

            var yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
                maxDeviation: 0.3,
                renderer: am5xy.AxisRendererY.new(root, {})
            }));

            var series = chart.series.push(am5xy.ColumnSeries.new(root, {
                name: "Series 1",
                xAxis: xAxis,
                yAxis: yAxis,
                valueYField: "value",
                sequencedInterpolation: true,
                categoryXField: "country",
                tooltip: am5.Tooltip.new(root, {
                    labelText:"{valueY}"
                })
            }));

            series.columns.template.setAll({ cornerRadiusTL: 5, cornerRadiusTR: 5 });
            series.columns.template.adapters.add("fill", function(fill, target) {
                return chart.get("colors").getIndex(series.columns.indexOf(target));
            });

            series.columns.template.adapters.add("stroke", function(stroke, target) {
                return chart.get("colors").getIndex(series.columns.indexOf(target));
            });

            var data = [{
                country: "Izin",
                value: {{ $totalPermissions }}
            }, {
                country: "Cuti",
                value: {{ $totalLeaves }}
            }, {
                country: "Koreksi",
                value: {{ $totalCorrections }}
            }];

            xAxis.data.setAll(data);
            series.data.setAll(data);

            series.appear(1000);
            chart.appear(1000, 100);
            //END DASHBOARD SUBMISSION
        }

        function initEmpGender(){
            //START DASHBOARD GENDER
            var root = am5.Root.new("empGender");
            root.setThemes([
                am5themes_Animated.new(root)
            ]);
            var chart = root.container.children.push(am5percent.PieChart.new(root, {
                layout: root.verticalLayout,
            }));

            var series = chart.series.push(am5percent.PieSeries.new(root, {
                alignLabels: true,
                calculateAggregates: true,
                valueField: "value",
                categoryField: "category"
            }));

            series.get("colors").set("colors", [
                am5.color('#FD8A8A'),
                am5.color('#F1F7B5'),
            ]);

            series.slices.template.setAll({
                strokeWidth: 3,
                stroke: am5.color(0xffffff)
            });

            series.labelsContainer.set("paddingTop", 30)

            series.data.setAll([
                @php
                    foreach ($totalEmployeeByGender as $category => $total){
                        $category = $category == 'm' ? 'Pria' : 'Wanita';
                        echo "{value: $total, category: '$category'},";
                    }
                @endphp
            ]);

            var legend = chart.children.push(am5.Legend.new(root, {
                centerX: am5.p50,
                x: am5.p50,
                marginTop: 15,
                marginBottom: 15
            }));

            legend.data.setAll(series.dataItems);

            series.calculatePercent = true;
            series.slices.template.set('tooltipText', '{category}: {value}');
            series.labels.template.setAll({
                maxWidth: 80,
                oversizedBehavior: "wrap"
            });
            legend.valueLabels.template.set("forceHidden", true);
            series.appear(1000, 100);
        }

        am5.ready(function () {
            initEmpCategory();
            initEmpSubmission();
            initEmpGender();
        });
    </script>
@endsection
