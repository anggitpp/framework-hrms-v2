<div class="card">
    <form method="POST" id="form-edit" action="{{ empty($machine) ? route(str_replace('/', '.', $menu_path).'.store') : route(str_replace('/', '.', $menu_path).'.update', $machine->id) }}">
        @csrf
        @if(!empty($machine))
            @method('PATCH')
        @endif
        <x-form.modal-header title="{{ empty($machine) ? __('Tambah Mesin Absen') : __('Edit Mesin Absen') }}" />
        <div class="separator mt-2 mb-5 d-flex"></div>
        <div class="card-body pt-0">
            <x-form.select label="Lokasi Kerja" name="location_id" required :datas="$locations" value="{{ $machine->location_id ?? '' }}" option="- Pilih Lokasi Kerja -" />
            <x-form.input label="Nama" placeholder="Masukkan Nama" name="name" value="{{ $machine->name ?? '' }}" required/>
            <div class="row">
                <div class="col-md-6">
                    <x-form.input label="Serial Number" placeholder="Masukkan Serial Number" name="serial_number" value="{{ $machine->serial_number ?? '' }}"/>
                </div>
                <div class="col-md-6">
                    <x-form.input label="IP Address" placeholder="Masukkan IP Address" name="ip_address" value="{{ $machine->ip_address ?? '' }}" required/>
                </div>
            </div>
            <x-form.textarea label="Alamat" placeholder="Masukkan Alamat" name="address" value="{{ $machine->address ?? '' }}" />
            <x-form.textarea label="Keterangan" placeholder="Masukkan Keterangan" name="description" value="{{ $machine->description ?? '' }}" />
            <x-form.radio label="Status" name="status" :datas="$statusOption" value="{{ $machine->status ?? '' }}" />
        </div>
        <x-form.modal-footer />
    </form>
</div>
