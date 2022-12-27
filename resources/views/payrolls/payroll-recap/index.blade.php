@extends('layouts.app')
@section('content')
    <div class="card">
        <form method="GET" id="form-filter">
            <div class="card-header border-0 pt-6">
                <div class="card-toolbar">
                    <div class="d-flex">
                        <x-views.search />
                        @can('lvl3 '.$menu_path)
                            <x-views.filter>
                                <x-views.filter-select label="Unit" name="combo_3" :datas="$units" />
                                <x-views.filter-select label="Kelas Jabatan" name="combo_4" :datas="$ranks" />
                            </x-views.filter>
                        @endcan
                    </div>
                </div>
                <div class="card-toolbar">
                    <div class="d-flex justify-content-end">
                        <x-views.filter-month-year name-month="filter_1" value-month="{{ date('m') }}" name-year="filter_2" class="me-5" value-year="{{ date('Y') }}" range="5" />
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
            <table id="datatables" class="table table-row-bordered gy-5 border">
                <thead>
                    <tr class="fw-bold text-uppercase border-bottom border-gray-200 text-center">
                        <th rowspan="2" class="min-w-50px text-center border-end">No</th>
                        <th rowspan="2" class="min-w-150px text-center border-end">NIP</th>
                        <th rowspan="2" class="min-w-300px text-center border-end">Nama</th>
                        <th rowspan="2" class="min-w-100px text-center  border-end">Golongan</th>
                        <th rowspan="2" class="min-w-150px text-center border-end">Kelas Jabatan</th>
                        <th rowspan="2" class="min-w-100px text-center border-end">Nilai</th>
                        <th rowspan="2" class="min-w-100px text-center border-end">Status</th>
                        <th rowspan="2" class="min-w-100px text-center border-end">PT</th>
                        <th rowspan="2" class="min-w-100px text-center border-end">PC</th>
                        <th rowspan="2" class="min-w-100px text-center border-end">TPTC</th>
                        <th colspan="3" class="min-w-240px text-center border-end">HUKUMAN DISIPLIN</th>
                        <th rowspan="2" class="min-w-100px text-center border-end">SAKIT > 3 Hari Tanpa Keterangan</th>
                        <th rowspan="2" class="min-w-100px text-center border-end">Cuti Besar/Diluar Tanggungan Negara</th>
                        <th rowspan="2" class="min-w-100px text-center border-end">Izin Belajar</th>
                        <th colspan="2" class="min-w-160px text-center border-end">Total Pemotongan</th>
                        <th rowspan="2" class="min-w-100px text-center border-end">Jumlah Diterima</th>
                    </tr>
                    <tr class="fw-bold text-uppercase border-bottom border-gray-200 text-center">
                        <th class="min-w-80px text-center border-end">Ringan</th>
                        <th class="min-w-80px text-center border-end">Sedang</th>
                        <th class="min-w-80px text-center border-end">Berat</th>
                        <th class="min-w-50px text-center border-end">%</th>
                        <th class="min-w-50px text-center border-end">Rp</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 fw-semibold">
                </tbody>
            </table>
            </div>
            @php
                $route = route(Str::replace('/', '.', $menu_path).'.data');
                $datas = array("employee_number", "name", "grade_id", "rank_id", "rankAmount\ttext-end", "employee_type_id", "pt\ttext-end", "pc\ttext-end", "tptc\ttext-end","null\ttext-end","null\ttext-end","null\ttext-end","null\ttext-end","null\ttext-end","null\ttext-end","percent\ttext-end","deduction\ttext-end","total\ttext-end pe-3");
            @endphp
            <x-views.datatables :datas="$datas" :route="$route" def-order="2" class-default="border-end" class-first-column="ps-2" />
        </div>
    </div>
    <x-modal-form/>
@endsection
