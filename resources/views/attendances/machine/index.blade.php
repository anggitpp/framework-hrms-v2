@extends('layouts.app')
@section('content')
    <div class="card">
        <div class="card-header border-0 pt-6">
            <div class="card-toolbar">
                <form method="GET" id="form-filter">
                    <div class="d-flex">
                        <x-views.search />
                        <x-form.select name="combo_1" :datas="$locations" class="w-300px" option="- Semua Lokasi -" />
                    </div>
                </form>
            </div>
            <div class="card-toolbar">
                <div class="d-flex justify-content-end">
                    @can('add '.$menu_path)
                        <x-views.add-button-modal route="{{ route(Str::replace('/', '.', $menu_path).'.create') }}" text="Tambah {{ $selected_menu->name }}" />
                    @endcan
                </div>
            </div>
        </div>
        <div class="card-body py-4">
            <div class="table-responsive">
                <table id="datatables" class="table table-rounded table-row-bordered border gy-5 gs-7">
                    <thead>
                        <tr class="text-start text-muted bg-gray-100 fw-bold fs-7 text-uppercase gs-0 border-bottom border-gray-200">
                            <th width="10">No</th>
                            <th width="300">Lokasi</th>
                            <th width="100">SN</th>
                            <th width="*">Nama</th>
                            <th width="100">IP</th>
                            <th width="100">Status</th>
                            <th width="100" class="text-center">Kontrol</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                @php
                    $route = route(Str::replace('/', '.', $menu_path).'.index');
                    $datas = array("location_id", "serial_number", "name", "ip_address", "status", "action\ttrue\tfalse");
                @endphp
                <x-views.datatables :datas="$datas" :route="$route" def-order="3"/>
                <x-views.delete-form/>
            </div>
        </div>
    </div>
    <x-modal-form/>
@endsection
