@extends('layouts.app')
@section('content')
    <div class="card">
        <form method="GET" id="form-filter">
            <div class="card-header border-0 pt-6">
                <div class="card-toolbar">
                    <div class="d-flex">
                        <x-views.search />
                        <x-views.filter>
                            <x-views.filter-select label="Unit" name="combo_3" :datas="$units" />
                            <x-views.filter-select label="Kelas Jabatan" name="combo_4" :datas="$ranks" />
                        </x-views.filter>
                    </div>
                </div>
                <div class="card-toolbar">
                    <div class="d-flex">
                        <x-views.filter-month-year name-month="filterMonth" value-month="{{ $filterMonth }}" name-year="filterYear" value-year="{{ $filterYear }}" event="changeFilterMonthYear();" range="5" class="me-3" />
                        <x-views.export>
                            <x-views.export-button id="btnExport" text="Export XLS" url="{{ $menu_path }}" class="w-100 mb-5" />
                            <x-views.export-button id="btnExport" text="Export PDF" url="{{ $menu_path }}" class="w-100" type="pdf" />
                        </x-views.export>
                    </div>
                </div>
            </div>
        </form>
        <div class="card-body py-4">
            <div class="table-responsive">
                <table
                    id="datatables"
                    class="table table-row-bordered gy-5 border">
                    <thead>
                        <tr class="fw-bold text-uppercase border-bottom border-gray-200">
                            <th rowspan="2" class="min-w-50px text-center border-end">No</th>
                            <th rowspan="2" class="min-w-150px text-center border-end">NIP</th>
                            <th rowspan="2" class="min-w-300px text-center border-end">Nama</th>
                            <th colspan="{{ $totalDays }}" class="min-w-{{ $totalDays * 50 }}px text-center border-end">Tanggal</th>
                        </tr>
                        <tr class="fw-bold text-uppercase border-bottom border-gray-200">
                            @for ($i = 1; $i <= $totalDays; $i++)
                                <th class="min-w-50px text-center border-end">{{ $i }}</th>
                            @endfor
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold">
                    </tbody>
                </table>
                @php
                    $route = route(str_replace('/', '.', $menu_path).'.data', [$filterMonth, $filterYear]);
                    $datas = array("employee_number\ttext-center", "name");
                    for($i = 1; $i <= $totalDays; $i++) $datas[] = "day_$i\ttext-center\tfalse";

                @endphp
                <x-views.datatables :datas="$datas" :route="$route" def-order="2" class-default="border-end" class-first-column="ps-2" />
                <x-views.delete-form/>
            </div>
        </div>
    </div>
    <x-modal-form/>
    <script>
        function changeFilterMonthYear() {
            $('#form-filter').submit();
        }
    </script>
@endsection
