@extends('layouts.app')
@section('content')
    <div class="card">
        <div class="card-header border-0 pt-6">
            <div class="card-toolbar">
                <form method="GET" id="form-filter">
                    <div class="d-flex">
                        <x-views.search />
                    </div>
                </form>
            </div>
            <div class="card-toolbar">
                <div class="d-flex justify-content-end">
                    <x-views.filter-month-year name-month="combo_1" name-year="combo_2" value-month="{{ date('m') }}" value-year="{{ date('Y') }}" class="me-2" />
                    @can('add '.$menu_path)
                        <x-views.add-button route="{{ route(str_replace('/', '.', $menu_path).'.create') }}" text="Tambah {{ $selected_menu->name }}" />
                    @endcan
                </div>
            </div>
        </div>
        <div class="card-body py-4">
            <div class="table-responsive">
                <table id="datatables" class="table table-rounded table-row-bordered border gy-5 gs-7">
                    <thead>
                        <tr class="text-start text-muted bg-gray-100 fw-bold fs-7 text-uppercase gs-0 border-bottom border-gray-200">
                            <th class="min-w-10px">No</th>
                            <th class="min-w-100px">Nomor</th>
                            <th class="min-w-300px">Nama</th>
                            <th class="min-w-100px">NIP</th>
                            <th class="min-w-100px">Tanggal</th>
                            <th class="min-w-100px">Mulai</th>
                            <th class="min-w-50px">Selesai</th>
                            <th class="min-w-50px">Status</th>
                            <th class="min-w-150px text-center">Kontrol</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold">
                    </tbody>
                </table>
                @php
                    $route = route(str_replace('/', '.', $menu_path).'.data');
                    $datas = array("number", "name", "employee_number", "attendance_date", "start_time", "end_time", "approved_status", "action\ttrue\tfalse");
                @endphp
                <x-views.datatables :datas="$datas" :route="$route" def-order="1"/>
                <x-views.delete-form/>
            </div>
        </div>
    </div>
    <x-modal-form/>
@endsection
