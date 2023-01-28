<div class="card">
    <form method="POST" id="form-edit" action="{{ empty($fixed) ? route(Str::replace('/', '.', $menu_path).'.store') : route(Str::replace('/', '.', $menu_path).'.update', $fixed->id) }}">
        @csrf
        @if(!empty($fixed))
            @method('PATCH')
        @endif
        <x-form.modal-header title="{{ empty($fixed) ? __('Tambah Komponen Fix') : __('Edit Komponen Fix') }}" />
        <div class="separator mt-2 mb-5 d-flex"></div>
        <div class="card-body pt-0">
            <x-form.input label="Kode" placeholder="Masukkan Kode" name="code" value="{{ $fixed->code ?? '' }}" required/>
            <x-form.input label="Nama" placeholder="Masukkan Nama" name="name" value="{{ $fixed->name ?? '' }}" required/>
            <x-form.input label="Nilai" placeholder="Masukkan Nilai" name="amount" value="{{ $fixed->amount ?? 0 }}" required currency/>
            <x-form.textarea label="Keterangan" placeholder="Masukkan Keterangan" name="description" value="{{ $fixed->description ?? '' }}" />
            <x-form.radio label="Status" name="status" :datas="$statusOption" value="{{ $fixed->status ?? '' }}" />
{{--            {{ $fixed->status }}--}}
        </div>
        <x-form.modal-footer />
    </form>
</div>
