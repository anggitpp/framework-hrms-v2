@extends('layouts.app')
@section('content')
    <div class="card">
        <form method="POST" id="form-edit" action="{{ empty($permission) ? route(str_replace('/', '.', $menu_path).'.store') : route(str_replace('/', '.', $menu_path).'.update', $permission->id) }}" enctype="multipart/form-data">
            @csrf
            @if(!empty($permission))
                @method('PATCH')
            @endif
            <x-form.header title="{{ empty($permission) ? __('Tambah Data Izin') : __('Edit Data Izin') }}" />
            <div class="separator mt-2 mb-5 d-flex"></div>
            <div class="card-body pt-0">
                <div class="row">
                    <div class="col-md-6">
                        <x-form.input label="Nomor" name="number" value="{{ $permission->number ?? $lastNumber }}" readonly required />
                        <x-form.select label="Pegawai" name="employee_id" option="- Pilih Pegawai -" :datas="$employees" value="{{ $permission->employee_id ?? '' }}" event="getDetailEmployee();" required/>
                        <x-form.input label="Unit" name="unit" value="{{ $permission->employee->position->unit_id ?? '' }}" readonly />
                    </div>
                    <div class="col-md-6">
                        <x-form.datepicker label="Tanggal Pengajuan" name="date" value="{{ $permission->date ?? date('Y-m-d') }}" required />
                        <x-form.input label="Pangkat" name="rank" value="{{ $permission->employee->position->rank_id ?? '' }}" readonly />
                    </div>
                </div>
                <div class="separator mt-2 mb-5 d-flex"></div>
                <div class="row">
                    <div class="col-md-6">
                        <x-form.select label="Kategori Izin" name="category_id" option="- Pilih Kategori Izin -" :datas="$categories" value="{{ $permission->category_id ?? '' }}" required/>
                        <x-form.datepicker name="start_date" label="Tanggal Mulai" value="{{ $permission->start_date ?? '' }}" required />
                        <x-form.textarea label="Keterangan" placeholder="Masukkan Keterangan" name="description" value="{{ $permission->description ?? '' }}" />
                    </div>
                    <div class="col-md-6">
                        <x-form.file label="Bukti" name="filename" value="{{ $permission->filename ?? '' }}" />
                        <x-form.datepicker name="end_date" label="Tanggal Selesai" value="{{ $permission->end_date ?? '' }}" required />
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
@section('scripts')
    <script>
        function getDetailEmployee() {
            let employee_id = $('#employee_id').val();
            $.ajax({
                url: "{{ route('attendances.permissions.employee') }}",
                type: "GET",
                data: {
                    employee_id: employee_id
                },
                success: function (response) {
                    $('#rank').val(response['position']['rank_id']);
                    $('#unit').val(response['position']['unit_id']);
                }
            });
        }
    </script>
@endsection

