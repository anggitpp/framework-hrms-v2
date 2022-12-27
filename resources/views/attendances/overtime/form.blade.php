@extends('layouts.app')
@section('content')
    <div class="card">
        <form method="POST" id="form-edit" action="{{ empty($overtime) ? route(str_replace('/', '.', $menu_path).'.store') : route(str_replace('/', '.', $menu_path).'.update', $overtime->id) }}" enctype="multipart/form-data">
            @csrf
            @if(!empty($overtime))
                @method('PATCH')
            @endif
            <x-form.header title="{{ empty($overtime) ? __('Tambah Data Lembur') : __('Edit Data Lembur') }}" />
            <div class="separator mt-2 mb-5 d-flex"></div>
            <div class="card-body pt-0">
                <div class="row">
                    <div class="col-md-6">
                        <x-form.input label="Nomor" name="number" value="{{ $overtime->number ?? $lastNumber }}" readonly required />
                        <x-form.select label="Pegawai" name="employee_id" option="- Pilih Pegawai -" :datas="$employees" value="{{ $overtime->employee_id ?? '' }}" event="getDetailEmployee();" required/>
                        <x-form.input label="Unit" name="unit" value="{{ $overtime->employee->position->unit_id ?? '' }}" readonly />
                    </div>
                    <div class="col-md-6">
                        <x-form.datepicker label="Tanggal Pengajuan" name="date" value="{{ $overtime->date ?? date('Y-m-d') }}" required />
                        <x-form.input label="Pangkat" name="rank" value="{{ $overtime->employee->position->rank_id ?? '' }}" readonly />
                    </div>
                </div>
                <div class="separator mt-2 mb-5 d-flex"></div>
                <div class="row">
                    <div class="col-md-6">
                        <x-form.datepicker name="start_date" label="Tanggal Lembur" value="{{ $overtime->start_date ?? '' }}" required />
                        <x-form.timepicker name="start_time" label="Jam Mulai" value="{{ $overtime->start_time ?? '' }}" required />
                        <x-form.textarea label="Keterangan" placeholder="Masukkan Keterangan" name="description" value="{{ $overtime->description ?? '' }}" />
                    </div>
                    <div class="col-md-6">
                        <x-form.file label="File Pendukung" name="filename" value="{{ $overtime->filename ?? '' }}" />
                        <x-form.timepicker name="end_time" label="Jam Selesai" value="{{ $overtime->end_time ?? '' }}" required />
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

