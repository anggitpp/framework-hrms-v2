@extends('layouts.app')
@section('content')
    <div class="card">
        <form method="POST" id="form-edit" action="{{ empty($education) ? route(Str::replace('/', '.', $menu_path).'.store') : route(Str::replace('/', '.', $menu_path).'.update', $education->id) }}">
            @csrf
            @if(!empty($education))
                @method('PATCH')
            @endif
            <x-form.header title="{{ empty($education) ? __('Tambah Data Pendidikan') : __('Edit Data Pendidikan') }}" />
            <div class="separator mt-2 mb-5 d-flex"></div>
            <div class="card-body pt-0">
                <div class="row">
                    <div class="col-md-6">
                        <x-form.select name="employee_id" required label="Pegawai" option="- Pilih Pegawai -" :datas="$employees" value="{{ $education->employee_id ?? '' }}" />
                        <x-form.input label="Nama Institusi" name="name" value="{{ $education->name ?? '' }}" required/>
                        <x-form.input label="Tahun Mulai" name="start_year" value="{{ $education->start_year ?? '' }}" class="w-25 text-end" numeric maxlength="4"/>
                        <x-form.input label="Kota/Lokasi" name="city" value="{{ $education->city ?? '' }}"/>
                        <x-form.textarea label="Keterangan" name="description" value="{{ $education->description ?? '' }}"/>
                    </div>
                    <div class="col-md-6">
                        <x-form.select label="Tingkatan" name="level_id" option="- Pilih Tingkatan -" :datas="$levels" value="{{ $education->level_id ?? '' }}" required />
                        <x-form.input label="Jurusan" name="major" value="{{ $education->major ?? '' }}"/>
                        <x-form.input label="Tahun Selesai" name="end_year" value="{{ $education->end_year ?? '' }}" class="w-25 text-end" numeric maxlength="4"/>
                        <x-form.input label="Nilai/IPK" name="score" value="{{ $education->score ?? '' }}" class="w-25 text-end" numeric/>
                        <x-form.file label="File Pendukung" name="filename" value="{{ $education->filename ?? '' }}"/>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
