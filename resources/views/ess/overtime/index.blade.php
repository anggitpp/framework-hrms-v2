@extends('layouts.app')
@section('content')
    <div class="card">
        <x-views.employee-detail :employee="$employee" />
        <div class="card-header border-0">
            <div class="card-toolbar">
                <form method="GET" id="form-filter">
                    <div class="d-flex">
                        <x-views.search />
                    </div>
                </form>
            </div>
            <div class="card-toolbar">
                <div class="d-flex justify-content-end">
                    <x-views.filter-year name="combo_1" value="{{ date('Y') }}" />
                    @can('add '.$menu_path)
                        <x-views.add-button route="{{ route(str_replace('/', '.', $menu_path).'.create') }}" text="Tambah {{ $selected_menu->name }}" />
                    @endcan
                </div>
            </div>
        </div>
        <div class="card-body py-4 pt-0">
            <div class="table-responsive">
                <table id="datatables" class="table table-rounded table-row-bordered border gy-5 gs-7">
                    <thead>
                        <tr class="text-start text-muted bg-gray-100 fw-bold fs-7 text-uppercase gs-0 border-bottom border-gray-200">
                            <th class="min-w-10px">No</th>
                            <th class="min-w-100px">Nomor</th>
                            <th class="min-w-100px">Tanggal</th>
                            <th class="min-w-100px">Mulai</th>
                            <th class="min-w-100px">Selesai</th>
                            <th class="min-w-100px">Durasi</th>
                            <th class="min-w-100px">Bukti</th>
                            <th class="min-w-100px">Status</th>
                            <th class="min-w-100px text-center">Kontrol</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold">
                    </tbody>
                </table>
                @php
                    $route = route(str_replace('/', '.', $menu_path).'.data');
                    $datas = array("number", "start_date", "start_time", "end_time", "duration", "filename", "approved_status", "action\ttrue\tfalse");
                @endphp
                <x-views.datatables :datas="$datas" :route="$route" def-order="1"/>
                <x-views.delete-form/>
            </div>
        </div>
    </div>
    <x-modal-form/>
@endsection
