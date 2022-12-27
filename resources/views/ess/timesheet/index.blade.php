@extends('layouts.app')
@section('content')
    <div class="card">
        <x-views.employee-detail :employee="$employee" />
        <div class="card-header border-0">
            <div class="card-toolbar">
                <form method="GET" id="form-filter">
                    <div class="d-flex">
                        <x-views.search />
                        <x-views.filter-datepicker name="filter_1" label="Tanggal" value="{{ date('Y-m-d') }}" class="me-2 w-150px" />
                    </div>
                </form>
            </div>
            <div class="card-toolbar">
                <div class="d-flex justify-content-end">
                    @can('add '.$menu_path)
                        <x-views.add-button-modal route="{{ route(str_replace('/', '.', $menu_path).'.create') }}" text="Tambah {{ $selected_menu->name }}" />
                    @endcan
                </div>
            </div>
        </div>
        <div class="card-body pb-4 pt-0">
            <div class="table-responsive">
                <table id="datatables" class="table table-rounded table-row-bordered border gy-5 gs-7">
                    <thead>
                        <tr class="text-start text-muted bg-gray-100 fw-bold fs-7 text-uppercase gs-0 border-bottom border-gray-200">
                            <th width="10">No</th>
                            <th width="150">Kegiatan</th>
                            <th width="*">Output</th>
                            <th width="80">Mulai</th>
                            <th width="80">Selesai</th>
                            <th width="80">Durasi</th>
                            <th width="100">Volume</th>
                            <th width="100">Satuan</th>
                            <th width="100" class="text-center">Kontrol</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                @php
                    $route = route(str_replace('/', '.', $menu_path).'.data');
                    $datas = array("activity", "output", "start_time", "end_time", "duration", "volume", "type", "action\ttrue\tfalse");
                @endphp
                <x-views.datatables :datas="$datas" :route="$route" def-order="4"/>
                <x-views.delete-form/>
            </div>
        </div>
    </div>
    <x-modal-form/>
@endsection
