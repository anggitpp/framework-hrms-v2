@extends('layouts.app')
@section('content')
    <div class="card">
        <div class="card-header border-0 pt-6">
            <div class="card-toolbar">
                <form method="GET" id="form-filter">
                    <div class="d-flex">
                        <x-views.search />
                        <x-views.filter-daterangepicker value="{{ date('Y-m-d') }}" name="filter_1" />
                    </div>
                </form>
            </div>
            <div class="card-toolbar">
                <div class="d-flex justify-content-end">
                    <x-views.export-button id="btnExport" text="Export Data" class="me-3" url="{{ $menu_path }}" />
                    <x-views.add-button-modal route="{{ route(Str::replace('/', '.', $menu_path).'.create') }}" text="Tambah Data" />
                </div>
            </div>
        </div>
        <div class="card-body py-4">
            <div class="table-responsive">
                <table id="datatables" class="table table-row-bordered gy-5 border">
                    <thead>
                        <tr class="fw-bold text-uppercase border-bottom border-gray-200">
                            <th rowspan="2" class="min-w-30px text-center border-end">No</th>
                            <th rowspan="2" class="text-center border-end" style="min-width: 180px;">NIP</th>
                            <th rowspan="2" class="min-w-250px text-center border-end">Nama</th>
                            <th rowspan="2" class="min-w-150px text-center border-end">Tanggal</th>
                            <th colspan="2" class="min-w-160px text-center border-end">Jadwal</th>
                            <th colspan="2" class="min-w-160px text-center border-end">Aktual</th>
                            <th rowspan="2" class="min-w-80px text-center border-end">Durasi</th>
                            <th rowspan="2" class="min-w-100px text-center border-end">Kehadiran</th>
                            <th rowspan="2" class="min-w-200px text-center border-end">Keterangan</th>
                            <th rowspan="2" class="min-w-100px text-center border-end">Kontrol</th>
                        </tr>
                        <tr class="fw-bold text-uppercase border-bottom border-gray-200">
                            <th class="min-w-80px text-center border-end">Masuk</th>
                            <th class="min-w-80px text-center border-end">Pulang</th>
                            <th class="min-w-80px text-center border-end">Masuk</th>
                            <th class="min-w-80px text-center border-end">Pulang</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold">
                    </tbody>
                </table>
            </div>
            @php
                $route = route(Str::replace('/', '.', $menu_path).'.data');
                $datas = array("employee_number\ttext-center", "name", "start_date\ttext-center",  "start_shift\ttext-center", "end_shift\ttext-center", "start_time\ttext-center", "end_time\ttext-center", "duration\ttext-center", "category", "description", "action\ttrue\tfalse");
            @endphp
            <x-views.datatables :datas="$datas" :route="$route" def-order="2" class-default="border-end" class-first-column="ps-2" />
            <x-views.delete-form/>
        </div>
    </div>
    <x-modal-form/>
@endsection
