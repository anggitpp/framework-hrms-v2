@extends('layouts.app')
@section('content')
    <div class="card">
        <form method="POST" id="form-edit" action="{{ empty($position) ? route('employees.position-histories.store') : route('employees.position-histories.update', $position->id) }}">
            @csrf
            @if(!empty($position))
                @method('PATCH')
            @endif
            <div class="card-header border-0 pt-6 justify-content-between d-flex">
                <div class="card-title">
                    <h3 class="card-label">
                        {{ empty($position) ? __('Tambah Jabatan') : __('Edit Jabatan') }}
                    </h3>
                </div>
                <div>
                    <a href="{{ url()->previous() }}" class="btn btn-light-primary me-3">
                        <i class="fas fa-chart-pie"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-print"></i>Simpan
                    </button>
                </div>
            </div>
            <div class="separator mt-2 mb-5 d-flex"></div>
            <div class="card-body pt-0">
                <div class="row">
                    <div class="col-md-6">
                        <x-form.select name="employee_id" required label="Pegawai" option="- Pilih Pegawai -" :datas="$employees" value="{{ $position->employee_id ?? '' }}" />
                        <x-form.select name="position_id" required label="Jabatan" option="- Pilih Jabatan -" :datas="$positions" value="{{ $position->position_id ?? '' }}" />
                        <x-form.select name="rank_id" required label="Kelas Jabatan" option="- Pilih Kelas Jabatan -" :datas="$ranks" value="{{ $position->rank_id ?? '' }}" />
                        <x-form.input name="sk_number" label="Nomor SK" value="{{ $position->sk_number ?? '' }}" />
                        <x-form.datepicker name="start_date" required label="Tanggal Mulai" value="{{ $position->start_date ?? '' }}" />
                        <x-form.select name="unit_id" required label="Unit" option="- Pilih Unit -" :datas="$units" value="{{ $position->unit_id ?? '' }}" />
                        <x-form.select name="leader_id" label="Atasan Langsung" option="- Pilih Atasan -" :datas="$employees" value="{{ $position->leader_id ?? '' }}"/>
                    </div>
                    <div class="col-md-6">
                        <x-form.select name="employee_type_id" required label="Tipe Pegawai" option="- Pilih Tipe Pegawai -" :datas="$types" value="{{ $position->employee_type_id ?? '' }}" />
                        <x-form.select name="location_id" required label="Lokasi Kerja" option="- Pilih Lokasi Kerja -" :datas="$locations" value="{{ $position->location_id ?? '' }}" />
                        <x-form.select name="grade_id" label="Grade/Golongan" option="- Pilih Grade/Golongan -" :datas="$grades" value="{{ $position->grade_id ?? '' }}" />
                        <x-form.datepicker name="sk_date" label="Tanggal SK" value="{{ $position->sk_date ?? '' }}" />
                        <x-form.datepicker name="end_date" label="Tanggal Selesai" value="{{ $position->end_date ?? '' }}" />
                        <x-form.select name="shift_id" label="Shift" option="- Pilih Shift -" :datas="$shifts" value="{{ $employee->position->shift_id ?? array_key_first($shifts) }}" />
                        <x-form.radio name="status" label="Status" :datas="$statusOption" value="{{ $position->status ?? '' }}" />
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
