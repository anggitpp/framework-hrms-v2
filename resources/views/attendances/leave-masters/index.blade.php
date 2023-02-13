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
                    @can('add '.$menu_path)
                        <x-views.add-button-modal size="mw-800px" route="{{ route(str_replace('/', '.', $menu_path).'.create') }}" text="Tambah {{ $selected_menu->name }}" />
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
                        <th width="*">Tipe Cuti</th>
                        <th width="50">Jumlah</th>
                        <th width="200">Masa Berlaku</th>
                        <th width="150">Lokasi</th>
                        <th width="100">Masa Kerja</th>
                        <th width="100">Status</th>
                        <th width="100" class="text-center">Kontrol</th>
                    </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold">
                    </tbody>
                </table>
                @php
                    $route = route(str_replace('/', '.', $menu_path).'.data');
                    $datas = array("name", "balance", "start_date", "location_id", "work_period", "status", "action\ttrue\tfalse");
                @endphp
                <x-views.datatables :datas="$datas" :route="$route" def-order="3"/>
                <x-views.delete-form/>
            </div>
        </div>
    </div>
    <x-modal-form/>
@endsection
