@extends('layouts.app')
@section('content')
    <div class="card">
        <form method="GET" id="form-filter">
            <div class="card-header border-0 pt-6">
                <div class="card-title">
                    <div class="d-flex">
                        <x-views.search />
                        <x-views.filter>
                            <x-views.filter-select label="Posisi" name="combo_1" :datas="$masters['EMP']" />
                            <x-views.filter-select label="Pangkat" name="combo_2" :datas="$masters['EP']" />
                            <x-views.filter-select label="Grade" name="combo_3" :datas="$masters['EG']" />
                            <x-views.filter-select label="Lokasi Kerja" name="combo_4" :datas="$masters['ELK']" />
                        </x-views.filter>
                    </div>
                </div>
                <div class="card-toolbar">
                    <div class="d-flex justify-content-end">
                        @can('add '.$menu_path)
                            <x-views.add-button route="{{ route(str_replace('/', '.', $menu_path).'.create') }}" text="Tambah Jabatan" />
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
                            <th width="10">No</th>
                            <th width="100">NIP</th>
                            <th width="*">Nama</th>
                            <th width="150">Jabatan</th>
                            <th width="150">Kelas Jabatan</th>
                            <th width="100">Grade</th>
                            <th width="50">Status</th>
                            <th width="100" class="text-center">Kontrol</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold">
                    </tbody>
                </table>
                @php
                    $route = route(str_replace('/', '.', $menu_path).'.index');
                    $datas = array("employee_number", "name", "position_id", "rank_id", "grade_id", "status", "action\ttrue\tfalse");
                @endphp
                <x-views.datatables :datas="$datas" :route="$route" def-order="2"/>
                <x-views.delete-form/>
            </div>
        </div>
    </div>
    <x-modal-form/>
@endsection
