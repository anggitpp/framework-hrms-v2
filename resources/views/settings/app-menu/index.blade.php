@extends('layouts.app')
@section('content')
    <div class="card">
        <div class="card-header border-0 pt-6">
            <div class="card-toolbar">
                <form method="GET" id="form-filter">
                    <div class="d-flex">
                        <x-views.search />
                        <x-form.select name="combo_1" :datas="$moduls" event="getSub(this.value, 'combo_2', 'sub-moduls')" class="w-200px" />
                        <x-form.select name="combo_2" :datas="$submoduls" class="w-200px" />
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
        <div class="card-body py-4">
            <div class="table-responsive">
                <table id="datatables" class="table table-rounded table-row-bordered border gy-5 gs-7">
                    <thead>
                        <tr class="text-start text-muted bg-gray-100 fw-bold fs-7 text-uppercase gs-0 border-bottom border-gray-200">
                            <th width="10">No</th>
                            <th width="*">Nama</th>
                            <th width="200">Target</th>
                            <th width="50">Urutan</th>
                            <th width="100">Status</th>
                            <th width="150" class="text-center">Kontrol</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                @php
                    $route = route(str_replace('/', '.', $menu_path).'.index');
                    $datas = array("name", "target", "order\ttrue\ttrue\ttext-center", "status", "action\ttrue\tfalse");
                @endphp
                <x-views.datatables :datas="$datas" :route="$route" def-order="3"/>
                <x-views.delete-form/>
            </div>
        </div>
    </div>
    <x-modal-form/>
@endsection
