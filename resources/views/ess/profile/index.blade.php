@extends('layouts.app')
@section('content')
    <div class="card">
        <x-views.employee-detail :employee="$employee" />
        <div class="card-header border-0 d-flex justify-content-between">
            <div class="card-toolbar">
                <h2 class="pt-5">Identitas</h2>
            </div>
            <div class="card-toolbar">
                @can('edit '.$menu_path)
                    <x-views.add-button route="{{ route(Str::replace('/', '.', $menu_path).'.edit') }}" text="Edit {{ $selected_menu->name }}" icon="fa-edit" />
                @endcan
            </div>
        </div>
        <div class="separator"></div>
        <div class="card-body pb-4 pt-0">
            <div class="row mt-2">
                <div class="col-md-6">
                    <x-views.span-inline text="Nama" :value="$employee->name" />
                    <x-views.span-inline text="Tempat Lahir" :value="$employee->place_of_birth" />
                    <x-views.span-inline text="Nomor Induk Pegawai" :value="$employee->employee_number" />
                    <x-views.span-inline text="Gender" :value="$employee->gender == 'm' ? 'Laki-Laki' : 'Perempuan'" />
                    <x-views.span-inline text="Alamat KTP" :value="$employee->identity_address" />
                    <x-views.span-inline text="Nomor Handphone" :value="$employee->mobile_phone_number" />
                    <x-views.span-inline text="Status" :value="$employee->status->name ?? ''" />
                    <x-views.span-inline text="Tanggal Masuk" :value="$employee->join_date ? setDate($employee->join_date, 't') : ''" />
                    <x-views.span-inline text="PIN Mesin Absen" :value="$employee->attendance_pin" />
                </div>
                <div class="col-md-6">
                    <x-views.span-inline text="Nama Panggilan" :value="$employee->nickname" />
                    <x-views.span-inline text="Tanggal Lahir" :value="$employee->date_of_birth ? setDate($employee->date_of_birth) : ''" />
                    <x-views.span-inline text="Nomor Identitas" :value="$employee->identity_number" />
                    <x-views.span-inline text="Email" :value="$employee->email" />
                    <x-views.span-inline text="Alamat Domisili" :value="$employee->address" />
                    <x-views.span-inline text="Nomor Telepon" :value="$employee->phone_number" />
                    <x-views.span-inline text="Status Perkawinan" :value="$employee->maritalStatus->name ?? ''" />
                    <x-views.span-inline text="Tanggal Keluar" :value="$employee->leave_date ? setDate($employee->leave_date) : ''" />
                    <x-views.span-inline text="Agama" :value="$employee->religion->name ?? ''" />
                </div>
            </div>
        </div>
    </div>
@endsection
