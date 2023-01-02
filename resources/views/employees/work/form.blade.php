@extends('layouts.app')
@section('content')
    <div class="card">
        <form method="POST" id="form-edit" action="{{ empty($work) ? route(Str::replace('/', '.', $menu_path).'.store') : route(Str::replace('/', '.', $menu_path).'.update', $work->id) }}" enctype="multipart/form-data">
            @csrf
            @if(!empty($work))
                @method('PATCH')
            @endif
            <x-form.header title="{{ empty($work) ? __('Tambah '.$selected_menu->name) : __('Edit '.$selected_menu->name) }}" />
            <div class="separator mt-2 mb-5 d-flex"></div>
            <div class="card-body pt-0">
                <div class="row">
                    <div class="col-md-6">
                        <x-form.select name="employee_id" required label="Pegawai" option="- Pilih Pegawai -" :datas="$employees" value="{{ $work->employee_id ?? '' }}" />
                        <x-form.input label="Posisi" name="position" value="{{ $work->position ?? '' }}" required/>
                        <x-form.datepicker label="Tanggal Mulai" class="w-250px" name="start_date" value="{{ $work->start_date ?? '' }}" required/>
                        <x-form.textarea label="Keterangan" name="description" value="{{ $work->description ?? '' }}"/>
                    </div>
                    <div class="col-md-6">
                        <x-form.input label="Perusahaan" name="company" value="{{ $work->company ?? '' }}" required/>
                        <x-form.input label="Kota" name="city" value="{{ $work->city ?? '' }}"/>
                        <x-form.datepicker label="Tanggal Selesai" class="w-250px" name="end_date" value="{{ $work->end_date ?? '' }}"/>
                        <x-form.file label="File Pendukung" name="filename" value="{{ $work->filename ?? '' }}"/>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
