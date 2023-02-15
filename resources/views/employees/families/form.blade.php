@extends('layouts.app')
@section('content')
    <div class="card">
        <form method="POST" id="form-edit" action="{{ empty($family) ? route(Str::replace('/', '.', $menu_path).'.store') : route(Str::replace('/', '.', $menu_path).'.update', $family->id) }}" enctype="multipart/form-data">
            @csrf
            @if(!empty($family))
                @method('PATCH')
            @endif
            <x-form.header title="{{ empty($family) ? __('Tambah Data Keluarga') : __('Edit Data Keluarga') }}" />
            <div class="separator mt-2 mb-5 d-flex"></div>
            <div class="card-body pt-0">
                <div class="row">
                    <div class="col-md-6">
                        <x-form.select name="employee_id" required label="Pegawai" option="- Pilih Pegawai -" :datas="$employees" value="{{ $family->employee_id ?? '' }}" />
                        <x-form.select name="relationship_id" required label="Hubungan" option="- Pilih Hubungan -" :datas="$relationships" value="{{ $family->relationship_id ?? '' }}" />
                        <x-form.input label="Tempat Lahir" name="birth_place" value="{{ $family->birth_place ?? '' }}"/>
                        <x-form.radio name="gender" label="Jenis Kelamin" :datas="$genderOption" value="{{ $employee->gender ?? '' }}" />
                        <x-form.textarea name="description" label="Keterangan" value="{{ $family->description ?? '' }}" />
                    </div>
                    <div class="col-md-6">
                        <x-form.input name="name" label="Nama" value="{{ $family->name ?? '' }}" required />
                        <x-form.input label="Nomor KTP" name="identity_number" value="{{ $family->identity_number ?? '' }}"/>
                        <x-form.datepicker label="Tanggal Lahir" class="w-100" name="birth_date" value="{{ $family->birth_date ?? '' }}"/>
                        <x-form.file label="File Pendukung" name="filename" value="{{ $family->filename ?? '' }}"/>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
