<div class="card">
    <form method="POST" id="form-edit" action="{{ empty($master) ? route(Str::replace('/', '.', $menu_path).'.store') : route(Str::replace('/', '.', $menu_path).'.update', $master->id) }}">
        @csrf
        @if(!empty($master))
            @method('PATCH')
        @endif
        <x-form.modal-header title="{{ empty($master) ? __('Tambah Master Gaji') : __('Edit Master Gaji') }}" />
        <div class="separator mt-2 mb-5 d-flex"></div>
        <div class="card-body pt-0">
            <x-form.input label="Kode" placeholder="Masukkan Kode" name="code" value="{{ $master->code ?? '' }}" required/>
            <x-form.input label="Nama" placeholder="Masukkan Nama" name="name" value="{{ $master->name ?? '' }}" required/>
            <x-form.textarea label="Keterangan" placeholder="Masukkan Keterangan" name="description" value="{{ $master->description ?? '' }}" />
            <x-form.radio label="Status" name="status" :datas="$statusOption" value="{{ $master->status ?? '' }}" />
        </div>
        <x-form.modal-footer />
    </form>
</div>
