@extends('layouts.app')
@section('content')
    <div class="card">
        <div class="card-header border-0 pt-6">
            <div class="card-toolbar">
                <form method="GET" id="form-filter">
                    <div class="d-flex">
                        <x-views.filter-year name="filterYear" value="{{ $filterYear }}" event="document.getElementById('form-filter').submit();" />
                    </div>
                </form>
            </div>
        </div>
        <div class="card-body py-4">
            <div class="table-responsive">
                <table class="table table-rounded table-row-bordered border gy-5 gs-7">
                    <thead>
                        <tr class="text-start text-muted bg-gray-100 fw-bold fs-7 text-uppercase gs-0 border-bottom border-gray-200">
                            <th width="10">No</th>
                            <th width="100">Bulan</th>
                            <th width="200">Waktu Proses</th>
                            <th width="*">Diproses Oleh</th>
                            <th width="150" class="text-center">Total Pegawai</th>
                            <th width="150" class="text-center">Total Nilai</th>
                            <th width="100" class="text-center">Detail</th>
                            <th width="100" class="text-center">Upload</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($datas as $k => $data)
                        <tr class="align-middle">
                            <td>{{ $k }}</td>
                            <td>{{ numToMonth($data['month']) }}</td>
                            <td>{{ $data['time'] }}</td>
                            <td>{{ $data['by'] }}</td>
                            <td align="center">{{ $data['totalEmployee'] }}</td>
                            <td align="center">{{ setCurrency($data['totalAmount']) }}</td>
                            <td class="text-center justify-content-between">
                                <a class="btn btn-icon btn-light-info w-30px h-30px me-1" href="{{ route(Str::replace('/', '.', $menu_path).'.detail', [$k, $filterYear]) }}">
                                    <i class="fa-solid fa-list"></i>
                                </a>
                            </td>
                            <td class="text-center justify-content-between">
                                <a data-bs-toggle="modal" class="btn btn-icon btn-light-primary w-30px h-30px me-1 btn-modal" data-url="{{ route(Str::replace('/', '.', $menu_path).'.import', [$k, $filterYear]) }}">
                                    <i class="fa-solid fa-gear"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <x-views.delete-form/>
            </div>
        </div>
    </div>
    <x-modal-form/>
@endsection
