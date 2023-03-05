@extends('layouts.app')
@section('content')
    <div class="card">
        <x-views.employee-detail :employee="$employee" />
        <form method="GET" id="form-filter">
            <div class="card-header border-0 pt-6">
                <div class="card-title">
                    <div class="d-flex">
                        <x-views.search/>
                    </div>
                </div>
                <div class="card-toolbar">
                    <div class="d-flex justify-content-end">
                        @can('add '.$menu_path)
                            <x-views.add-button route="{{ route(str_replace('/', '.', $menu_path).'.create') }}"
                                                text="Tambah Data"/>
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
                        <th class="min-w-150px">Bank</th>
                        <th class="min-w-150px">Nomor Rekening</th>
                        <th class="min-w-200px">Nama Pemilik</th>
                        <th class="min-w-150px">Cabang</th>
                        <th class="min-w-100px">Status</th>
                        <th class="min-w-100px text-center">Kontrol</th>
                    </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold">
                    </tbody>
                </table>
                @php
                    $route = route(Str::replace('/', '.', $menu_path).'.data');
                    $datas = array("bank_id", "account_number", "account_name", "branch", "status", "action\ttrue\tfalse");
                @endphp
                <x-views.datatables :datas="$datas" :route="$route" def-order="2"/>
                <x-views.delete-form/>
            </div>
        </div>
    </div>
    <x-modal-form/>
@endsection
