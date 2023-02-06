@extends('layouts.app')
@section('content')
    <div class="card">
        <form method="GET" id="form-filter">
            <div class="card-header border-0 pt-6">
                <div class="card-title">
                    <div class="d-flex">
                        <x-views.search />
                    </div>
                    <span class="fs-5 text-gray-800 text-hover-primary fw-bold mb-1">
                        List Pegawai : {{ $month }} {{ $year }}
                    </span>
                </div>
            </div>
        </form>
        <div class="card-body py-4">
            <div class="table-responsive">
                <table id="datatables" class="table table-rounded table-row-bordered border gy-5 gs-7">
                    <thead>
                    <tr class="text-start text-muted bg-gray-100 fw-bold fs-7 text-uppercase gs-0 border-bottom border-gray-200">
                        <th width="20">No</th>
                        <th width="200">Nama</th>
                        <th width="150">Jabatan</th>
                        <th width="100">Nilai</th>
                        <th width="*">Keterangan</th>
                        <th width="100" class="text-center">Kontrol</th>
                    </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold">
                    </tbody>
                </table>
                @php
                    $route = route(Str::replace('/', '.', $menu_path).'.data-detail', ['month' => $month, 'year' => $year]);
                    $datas = array("name", "position_id", "amount", "description", "action\ttrue\tfalse");
                @endphp
                <x-views.datatables :datas="$datas" :route="$route" def-order="1"/>
                <x-views.delete-form/>
            </div>
        </div>
    </div>
    <x-modal-form/>
@endsection
