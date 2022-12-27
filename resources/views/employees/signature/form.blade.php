<div class="card">
    <form method="POST" id="form-edit" action="{{ empty($signature) ? route(Str::replace('/', '.', $menu_path).'.store') : route(Str::replace('/', '.', $menu_path).'.update', $signature->id) }}">
        @csrf
        @if(!empty($signature))
            @method('PATCH')
        @endif
        <x-form.modal-header title="{{ empty($signature) ? __('Tambah Tanda Tangan') : __('Edit Tanda Tangan') }}" />
        <div class="separator mt-2 mb-5 d-flex"></div>
        <div class="card-body pt-0">
            <x-form.select label="Lokasi Kerja" name="location_id" required :datas="$locations" value="{{ $signature->location_id ?? '' }}" option="- Pilih Lokasi Kerja -" />
            <x-form.select label="Pegawai" name="employee_id" required :datas="$employees" value="{{ $signature->employee_id ?? '' }}" option="- Pilih Pegawai -" />
            <x-form.textarea label="Keterangan" placeholder="Masukkan Keterangan" name="description" value="{{ $signature->description ?? '' }}" />
            <x-form.radio label="Status" name="status" :datas="$statusOption" value="{{ $signature->status ?? '' }}" />
        </div>
        <x-form.modal-footer />
    </form>
</div>
