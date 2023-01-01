@extends('layouts.app')
@section('content')
    <div class="card">
        <form method="POST" id="form-edit" action="{{ empty($contact) ? route(Str::replace('/', '.', $menu_path).'.store') : route(Str::replace('/', '.', $menu_path).'.update', $contact->id) }}">
            @csrf
            @if(!empty($contact))
                @method('PATCH')
            @endif
            <x-form.header title="{{ empty($contact) ? __('Tambah Data Kontak') : __('Edit Data Kontak') }}" />
            <div class="separator mt-2 mb-5 d-flex"></div>
            <div class="card-body pt-0">
                <div class="row">
                    <div class="col-md-6">
                        <x-form.select name="employee_id" required label="Pegawai" option="- Pilih Pegawai -" :datas="$employees" value="{{ $contact->employee_id ?? '' }}" />
                        <x-form.select name="relationship_id" required label="Hubungan" option="- Pilih Hubungan -" :datas="$relationships" value="{{ $contact->relationship_id ?? '' }}" />
                        <x-form.textarea name="description" label="Keterangan" value="{{ $contact->description ?? '' }}" />
                    </div>
                    <div class="col-md-6">
                        <x-form.input name="name" label="Nama" value="{{ $contact->name ?? '' }}" required />
                        <x-form.input name="phone_number" label="No. HP" value="{{ $contact->phone_number ?? '' }}" numeric required />
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
