@extends('layouts.app')
@section('content')
    <div class="card">
        <form method="POST" id="form-edit" action="{{ empty($termination) ? route(str_replace('/', '.', $menu_path).'.store') : route(str_replace('/', '.', $menu_path).'.update', $termination->id) }}" enctype="multipart/form-data">
            @csrf
            @if(!empty($termination))
                @method('PATCH')
            @endif
            <x-form.header title="{{ empty($termination) ? __('Tambah Data '.$selected_menu->name) : __('Edit Data '.$selected_menu->name) }}" />
            <div class="separator mt-2 mb-5 d-flex"></div>
            <div class="card-body pt-0">
                <div class="row">
                    <div class="col-md-6">
                        <x-form.input label="Nomor" name="number" value="{{ $termination->number ?? $lastNumber }}" readonly required />
                        <x-form.select label="Pegawai" name="employee_id" option="- Pilih Pegawai -" :datas="$employees" value="{{ $termination->employee_id ?? '' }}" event="getDetailEmployee();" required/>
                        <x-form.input label="Unit" name="unit" value="{{ $termination->employee->position->unit_id ?? '' }}" readonly />
                    </div>
                    <div class="col-md-6">
                        <x-form.datepicker label="Tanggal Pengajuan" name="date" value="{{ $termination->date ?? date('Y-m-d') }}" required />
                        <x-form.input label="Pangkat" name="rank" value="{{ $termination->employee->position->rank_id ?? '' }}" readonly />
                        <x-form.textarea label="Keterangan" placeholder="Masukkan Keterangan" name="description" value="{{ $termination->description ?? '' }}" />
                    </div>
                </div>
                <div class="separator mt-2 mb-5 d-flex"></div>
                <div class="row">
                    <div class="col-md-6">
                        <x-form.select label="Alasan" name="reason_id" option="- Pilih Alasan -" :datas="$reasons" value="{{ $termination->reason_id ?? '' }}" required/>
                        <x-form.datepicker name="effective_date" label="Tanggal Efektif" value="{{ $termination->effective_date ?? '' }}" required />
                        <x-form.textarea label="Catatan" placeholder="Masukkan Keterangan" name="note" value="{{ $termination->note ?? '' }}" />
                    </div>
                    <div class="col-md-6">
                        <x-form.select label="Tipe" name="type_id" option="- Pilih Tipe -" :datas="$types" value="{{ $termination->type_id ?? '' }}" required/>
                        <x-form.file label="File Pendukung" name="filename" value="{{ $termination->filename ?? '' }}" />
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

