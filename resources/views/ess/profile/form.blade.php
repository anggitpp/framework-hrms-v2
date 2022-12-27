@extends('layouts.app')
@section('content')
    <div class="card">
        <form method="POST" id="form-edit" action="{{ route(Str::replace('/', '.', $menu_path).'.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            <x-form.header title="{{ empty($employee) ? __('Tambah Pegawai') : __('Edit Pegawai') }}" />
            <div class="separator mt-2 mb-5 d-flex"></div>
            <div class="card-body pt-0">
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
        </form>
    </div>
@endsection
