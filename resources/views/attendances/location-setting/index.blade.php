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
        </div>
        <div class="card-body py-4">
            <div class="table-responsive">
                <table id="datatables" class="table table-rounded table-row-bordered border gy-5 gs-7">
                    <thead>
                        <tr class="text-start text-muted bg-gray-100 fw-bold fs-7 text-uppercase gs-0 border-bottom border-gray-200">
                            <th width="10">No</th>
                            <th width="150">Nama</th>
                            <th width="*">Alamat</th>
                            <th width="80">WFH</th>
                            <th width="150">Latitude</th>
                            <th width="150">Longitude</th>
                            <th width="80">Radius (M)</th>
                            <th width="50" class="text-center">Kontrol</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold">
                    </tbody>
                </table>
                @php
                    $route = route(str_replace('/', '.', $menu_path).'.index');
                    $datas = array("name", "address", "wfh", "latitude", "longitude", "radius", "action\ttrue\tfalse");
                @endphp
                <x-views.datatables :datas="$datas" :route="$route" def-order="1"/>
                <x-views.delete-form/>
            </div>
        </div>
    </div>
    <x-modal-form/>
@endsection
