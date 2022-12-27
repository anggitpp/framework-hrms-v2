@extends('layouts.app')
@section('content')
    <div class="card">
        <form method="POST" id="form-edit" action="{{ empty($correction) ? route(str_replace('/', '.', $menu_path).'.store') : route(str_replace('/', '.', $menu_path).'.update', $correction->id) }}" enctype="multipart/form-data">
            @csrf
            @if(!empty($correction))
                @method('PATCH')
            @endif
            <x-form.header title="{{ empty($correction) ? __('Tambah Data Koreksi') : __('Edit Data Koreksi') }}" :is-can-save="$isCanSave ?? true" />
            <input type="hidden" id="employee_id" name="employee_id" value="{{ $employee->id }}" />
            <div class="separator mt-2 mb-5 d-flex"></div>
            <div class="card-body pt-0">
                <div class="row">
                    <div class="col-md-6">
                        <x-form.input label="Nomor" name="number" value="{{ $correction->number ?? $lastNumber }}" readonly required />
                        <x-form.input label="Pegawai" value="{{ $employee->name ?? '' }}" readonly />
                        <x-form.input label="Unit" name="unit" value="{{ $employee->position->unit_id ?? '' }}" readonly />
                    </div>
                    <div class="col-md-6">
                        <x-form.datepicker label="Tanggal Pengajuan" name="date" value="{{ $correction->date ?? date('Y-m-d') }}" required />
                        <x-form.input label="Pangkat" name="rank" value="{{ $employee->position->rank_id ?? '' }}" readonly />
                    </div>
                </div>
                <div class="separator mt-2 mb-5 d-flex"></div>
                <div class="row">
                    <div class="col-md-6">
                        <x-form.datepicker name="attendance_date" label="Tanggal Absen" value="{{ $correction->attendance_date ?? '' }}" required event="getAttendance();" />
                        <x-form.input name="actual_start_time" class="w-50" label="Aktual Mulai" value="{{ $correction->start_time ?? '' }}" readonly />
                        <x-form.input name="actual_end_time" class="w-50" label="Aktual Selesai" value="{{ $correction->end_time ?? '' }}" readonly />
                    </div>
                    <div class="col-md-6">
                        <x-form.input label="Keterangan" placeholder="Masukkan Keterangan" name="description" value="{{ $correction->description ?? '' }}" />
                        <x-form.timepicker name="start_time" label="Koreksi Mulai" value="{{ $correction->start_time ?? '' }}" required />
                        <x-form.timepicker name="end_time" label="Koreksi Selesai" value="{{ $correction->end_time ?? '' }}" required />
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
@section('scripts')
    <script>
        function getAttendance() {
            let employee_id = $('#employee_id').val();
            let date = $('#attendance_date').val();
            if(date !== '' && employee_id !== '') {
                $.ajax({
                    url: "{{ route('ess.corrections.attendance') }}",
                    type: "GET",
                    data: {
                        employee_id: employee_id,
                        date: date
                    },
                    success: function (response) {
                        $('#actual_start_time').val(response['start_time']);
                        $('#actual_end_time').val(response['end_time']);
                    },
                    error: function () {
                        $('#actual_start_time').val('00:00');
                        $('#actual_end_time').val('00:00');
                    }
                });
            }
        }
    </script>
@endsection

