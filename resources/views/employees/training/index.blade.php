@extends('layouts.app')
@section('content')
    <div class="card">
        <form method="GET" id="form-filter">
            <div class="card-header border-0 pt-6">
                <div class="card-title">
                    <div class="d-flex">
                        <x-views.search />
                        <x-views.filter>
                            <x-views.filter-select label="Posisi" name="combo_1" :datas="$masters['EMP']" />
                            <x-views.filter-select label="Pangkat" name="combo_2" :datas="$masters['EP']" />
                            <x-views.filter-select label="Grade" name="combo_3" :datas="$masters['EG']" />
                            <x-views.filter-select label="Lokasi Kerja" name="combo_4" :datas="$masters['ELK']" />
                        </x-views.filter>
                    </div>
                </div>
                <div class="card-toolbar">
                    <div class="d-flex justify-content-end">
                        <x-views.export-button id="btnExport" text="Export Data" class="me-3" url="{{ $menu_path }}" />
                        @can('lvl1 '.$menu_path)
                            <x-views.add-button-modal route="{{ route(Str::replace('/', '.', $menu_path).'.import') }}" class="me-3" text="Import Data" />
                        @endcan
                        @can('add '.$menu_path)
                            <x-views.add-button route="{{ route(str_replace('/', '.', $menu_path).'.create') }}" text="Tambah Data" />
                        @endcan
                    </div>
                </div>
            </div>
        </form>
        <div class="card-body py-4">
            <div class="table-responsive">
                <table id="datatables" class="table table-rounded table-row-bordered border gy-5 gs-7">
                    <thead>
                        <tr class="text-start text-muted bg-gray-100 fw-bold fs-7 text-uppercase gs-0 border-bottom border-gray-200">
                            <th class="min-w-20px">No</th>
                            <th class="min-w-150px">NIP</th>
                            <th class="min-w-250px">Nama</th>
                            <th class="min-w-150px">Perihal</th>
                            <th class="min-w-150px">Institusi</th>
                            <th class="min-w-200px">No. Sertifikat</th>
                            <th class="min-w-150px">Tipe</th>
                            <th class="min-w-100px">Mulai</th>
                            <th class="min-w-100px">Selesai</th>
                            <th class="min-w-100px">File</th>
                            <th class="min-w-100px text-center">Kontrol</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold">
                    </tbody>
                </table>
                @php
                    $route = route(Str::replace('/', '.', $menu_path).'.data');
                    $datas = array("employee_number", "employee_name", "subject", "institution", "certificate_number", "type_id", "start_date\ttext-center", "end_date\ttext-center", "filename\ttext-center", "action\ttrue\tfalse");
                @endphp
                <x-views.datatables :datas="$datas" :route="$route" def-order="2"/>
                <x-views.delete-form/>
            </div>
        </div>
    </div>
    <x-modal-form/>
@endsection
