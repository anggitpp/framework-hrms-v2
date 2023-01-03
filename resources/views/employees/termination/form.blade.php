@extends('layouts.app')
@section('content')
    <div class="card">
        <form method="POST" id="form-edit" action="{{ empty($permission) ? route(str_replace('/', '.', $menu_path).'.store') : route(str_replace('/', '.', $menu_path).'.update', $permission->id) }}" enctype="multipart/form-data">
            @csrf
            @if(!empty($permission))
                @method('PATCH')
            @endif
            <x-form.header title="{{ empty($permission) ? __('Tambah Data '.$selected_menu->name) : __('Edit Data '.$selected_menu->name) }}" />
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
                        <x-form.textarea label="Keterangan" placeholder="Masukkan Keterangan" name="description" value="{{ $permission->description ?? '' }}" />
                    </div>
                </div>
                <div class="separator mt-2 mb-5 d-flex"></div>
                <div class="row">
                    <div class="col-md-6">
                        <x-form.select label="Alasan" name="reason_id" option="- Pilih Alasan -" :datas="$reasons" value="{{ $permission->reason_id ?? '' }}" required/>
                        <x-form.datepicker name="effective_date" label="Tanggal Efektif" value="{{ $permission->effective_date ?? '' }}" required />
                        <x-form.textarea label="Catatan" placeholder="Masukkan Keterangan" name="description" value="{{ $permission->description ?? '' }}" />
                    </div>
                    <div class="col-md-6">
                        <x-form.select label="Tipe" name="type_id" option="- Pilih Tipe -" :datas="$types" value="{{ $permission->type_id ?? '' }}" required/>
                        <x-form.file label="File Pendukung" name="filename" value="{{ $permission->filename ?? '' }}" />
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
                url: "{{ route( Str::replace('/', '.', $menu_path).'.employee') }}",
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

