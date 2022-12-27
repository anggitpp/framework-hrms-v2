@extends('layouts.app')
@section('content')
<div class="card">
    <form method="POST" id="form-edit" action="{{ empty($employee) ? route('employees.employees.store') : route('employees.employees.update', $employee->id) }}" enctype="multipart/form-data">
        @csrf
        @if(!empty($employee))
            @method('PATCH')
        @endif
        @php
            if($errors->hasAny(['name', 'employee_number'])){
                $tabActive = 'identity';
            }else if($errors->hasAny(['position_id', 'employee_type_id', 'rank_id', 'start_date', 'location_id', 'unit_id'])){
                $tabActive = 'position';
            }else if($errors->hasAny(['join_date', 'status_id'])){
                $tabActive = 'status';
            }else{
                $tabActive = 'identity';
            }
        @endphp
        <x-form.header title="{{ empty($employee) ? __('Tambah Pegawai') : __('Edit Pegawai') }}" />
        <div class="separator mt-2 mb-5 d-flex"></div>
        <div class="card-body pt-0">
            <ul class="nav nav-tabs nav-line-tabs nav-line-tabs-2x mb-5 fs-6">
                <li class="nav-item">
                    <a class="nav-link text-dark {{ $tabActive == 'identity' ? 'active' : '' }}" data-bs-toggle="tab" href="#identity">Identitas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark {{ $tabActive == 'position' ? 'active' : '' }}" data-bs-toggle="tab" href="#position">Posisi</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark {{ $tabActive == 'status' ? 'active' : '' }}" data-bs-toggle="tab" href="#status">Status</a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade {{ $tabActive == 'identity' ? 'show active' : '' }}" id="identity" role="tabpanel">
                    <div class="row">
                        <div class="col-md-6">
                            <x-form.input name="name" label="Nama" value="{{ $employee->name ?? '' }}" required />
                            <x-form.input name="place_of_birth" label="Tempat Lahir" value="{{ $employee->place_of_birth ?? '' }}" />
                            <x-form.input name="employee_number" label="Nomor Pegawai" value="{{ $employee->employee_number ?? '' }}" required />
                            <x-form.radio name="gender" label="Jenis Kelamin" :datas="$genderOption" value="{{ $employee->gender ?? '' }}" />
                        </div>
                        <div class="col-md-6">
                            <x-form.input name="nickname" label="Nama Panggilan" value="{{ $employee->nickname ?? '' }}" />
                            <x-form.datepicker name="date_of_birth" label="Tanggal Lahir" value="{{ $employee->date_of_birth ?? '' }}" />
                            <x-form.input name="identity_number" numeric label="Nomor Identitas" value="{{ $employee->identity_number ?? '' }}" />
                            <x-form.input name="email" label="Email" nospacing value="{{ $employee->email ?? '' }}" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <x-form.textarea name="identity_address" label="Alamat KTP" value="{{ $employee->identity_address ?? '' }}" />
                            <x-form.input name="mobile_phone_number" numeric label="Nomor Handphone" value="{{ $employee->mobile_phone_number ?? '' }}" />
                            <x-form.image-input name="identity_file" label="File KTP" value="{{ $employee->identity_file ?? '' }}" />
                        </div>
                        <div class="col-md-6">
                            <x-form.textarea name="address" label="Alamat" value="{{ $employee->address ?? '' }}" />
                            <x-form.input name="phone_number" numeric label="Nomor Telepon" value="{{ $employee->phone_number ?? '' }}" />
                            <x-form.image-input name="photo" label="Foto" value="{{ $employee->photo ?? '' }}" />
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade {{ $tabActive == 'position' ? 'show active' : '' }}" id="position" role="tabpanel">
                    <div class="row">
                        <div class="col-md-6">
                            <x-form.select name="position_id" required label="Jabatan" option="- Pilih Jabatan -" :datas="$positions" value="{{ $employee->position->position_id ?? '' }}" />
                            <x-form.select name="rank_id" required label="Kelas Jabatan" option="- Pilih Kelas Jabatan -" :datas="$ranks" value="{{ $employee->position->rank_id ?? '' }}" />
                            <x-form.input name="sk_number" label="Nomor SK" value="{{ $employee->position->sk_number ?? '' }}" />
                            <x-form.datepicker name="start_date" required label="Tanggal Mulai" value="{{ $employee->position->start_date ?? '' }}" />
                            <x-form.select name="location_id" required label="Lokasi Kerja" option="- Pilih Lokasi Kerja -" :datas="$locations" value="{{ $employee->position->location_id ?? '' }}" />
                            <x-form.select name="shift_id" label="Shift" option="- Pilih Shift -" :datas="$shifts" value="{{ $employee->position->shift_id ?? array_key_first($shifts) }}" />
                        </div>
                        <div class="col-md-6">
                            <x-form.select name="employee_type_id" required label="Tipe Pegawai" option="- Pilih Tipe Pegawai -" :datas="$types" value="{{ $employee->position->employee_type_id ?? '' }}" />
                            <x-form.select name="grade_id" label="Grade/Golongan" option="- Pilih Grade/Golongan -" :datas="$grades" value="{{ $employee->position->grade_id ?? '' }}" />
                            <x-form.datepicker name="sk_date" label="Tanggal SK" value="{{ $employee->position->sk_date ?? '' }}" />
                            <x-form.datepicker name="end_date" label="Tanggal Selesai" value="{{ $employee->position->end_date ?? '' }}" />
                            <x-form.select name="unit_id" required label="Unit" option="- Pilih Unit -" :datas="$units" value="{{ $employee->position->unit_id ?? '' }}" />
                            <x-form.select name="leader_id" label="Atasan Langsung" option="- Pilih Atasan -" :datas="$employees" value="{{ $employee->position->leader_id ?? '' }}"/>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade {{ $tabActive == 'status' ? 'show active' : '' }}" id="status" role="tabpanel">
                    <div class="row">
                        <div class="col-md-6">
                            <x-form.select name="status_id" required label="Status" option="- Pilih Status Pegawai -" :datas="$statuses" value="{{ $employee->status_id ?? '' }}" />
                            <x-form.datepicker name="join_date" required label="Tanggal Masuk" value="{{ $employee->join_date ?? '' }}" />
                            <x-form.select name="marital_status_id" label="Status Perkawinan" option="- Pilih Status Perkawinan -" :datas="$maritals" value="{{ $employee->marital_status_id ?? '' }}" />
                        </div>
                        <div class="col-md-6">
                            <x-form.datepicker name="leave_date" label="Tanggal Keluar" value="{{ $employee->leave_date ?? '' }}" />
                            <x-form.input name="attendance_pin" numeric label="PIN Mesin Absen" value="{{ $employee->attendance_pin ?? '' }}" maxlength="5" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
