@extends('layouts.app')
@section('content')
    <div class="card">
        <div class="card-header border-0">
            <div class="card-toolbar">
                <form method="GET" id="form-filter">
                    <div class="d-flex">
                        <x-views.search />
                        <x-form.select name="combo_1" :datas="$masters" class="w-300px" />
                    </div>
                </form>
            </div>
            <div class="card-toolbar">
                <div class="d-flex justify-content-end">
                    @can('add '.$menu_path)
                        <x-views.add-button event="onClickButtonAdd();" text="Tambah {{ $selected_menu->name }}" />
                    @endcan
                </div>
            </div>
        </div>
        <div class="card-body py-4 pt-0">
            <div class="table-responsive">
                <table id="datatables" class="table table-rounded table-row-bordered border gy-5 gs-7">
                    <thead>
                        <tr class="text-start text-muted bg-gray-100 fw-bold fs-7 text-uppercase gs-0 border-bottom border-gray-200">
                            <th class="10">No</th>
                            <th class="50">Kode</th>
                            <th class="200">Nama</th>
                            <th class="100">Tipe Perhitungan</th>
                            <th class="*">Keterangan</th>
                            <th class="50">Status</th>
                            <th class="100 text-center">Kontrol</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold">
                    </tbody>
                </table>
                @php
                    $route = route(Str::replace('/', '.', $menu_path).'.data');
                    $datas = array("code", "name", "calculation_type", "description", "status", "action\ttrue\tfalse");
                @endphp
                <x-views.datatables :datas="$datas" :route="$route" def-order="1"/>
                <x-views.delete-form/>
            </div>
        </div>
    </div>
    <script>
        function onClickButtonAdd() {
            let masterId = $('#combo_1').val();
            window.location.href = "{{ $menu_path }}/create?master_id=" + masterId;
        }
    </script>
@endsection
