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
                            <th width="*">Nama</th>
                            <th width="150">Nomor</th>
                            <th width="150">Tipe</th>
                            <th width="100">Mulai</th>
                            <th width="100">Selesai</th>
                            <th width="50">File</th>
                            <th width="80">Status</th>
                            <th width="130" class="text-center">Kontrol</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                @php
                    $route = route(str_replace('/', '.', $menu_path).'.data');
                    $datas = array("name", "number", "type_id", "start_date", "end_date", "filename", "status", "action\ttrue\tfalse");
                @endphp
                <x-views.datatables :datas="$datas" :route="$route" def-order="2"/>
                <x-views.delete-form/>
            </div>
        </div>
    </div>
    <x-modal-form/>
@endsection
