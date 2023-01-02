@extends('layouts.app')
@section('content')
    <div class="card">
        <form method="POST" id="form-edit" action="{{ empty($training) ? route(Str::replace('/', '.', $menu_path).'.store') : route(Str::replace('/', '.', $menu_path).'.update', $training->id) }}" enctype="multipart/form-data">
            @csrf
            @if(!empty($training))
                @method('PATCH')
            @endif
            <x-form.header title="{{ empty($training) ? __('Tambah '.$selected_menu->name) : __('Edit '.$selected_menu->name) }}" />
            <div class="separator mt-2 mb-5 d-flex"></div>
            <div class="card-body pt-0">
                <div class="row">
                    <div class="col-md-6">
                        <x-form.select name="employee_id" required label="Pegawai" option="- Pilih Pegawai -" :datas="$employees" value="{{ $training->employee_id ?? '' }}" />
                        <x-form.input label="Perihal" name="subject" value="{{ $training->subject ?? '' }}" required/>
                        <x-form.select label="Kategori" name="category_id" option="- Pilih Kategori -" :datas="$categories" value="{{ $training->category_id ?? '' }}" />
                        <x-form.datepicker label="Tanggal Mulai" class="w-100" name="start_date" value="{{ $training->start_date ?? '' }}" required/>
                        <x-form.textarea label="Keterangan" name="description" value="{{ $training->description ?? '' }}"/>
                    </div>
                    <div class="col-md-6">
                        <x-form.input label="No. Sertifikat" name="certificate_number" value="{{ $training->certificate_number ?? '' }}"/>
                        <x-form.input label="Nama Institusi" name="institution" value="{{ $training->institution ?? '' }}" required/>
                        <x-form.select label="Tipe" name="type_id" option="- Pilih Tipe -" :datas="$types" value="{{ $training->type_id ?? '' }}" />
                        <x-form.datepicker label="Tanggal Selesai" class="w-100" name="end_date" value="{{ $training->end_date ?? '' }}" required/>
                        <x-form.file label="File Pendukung" name="filename" value="{{ $training->filename ?? '' }}"/>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
