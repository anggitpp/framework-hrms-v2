<div class="card">
    <form method="POST" id="form-edit" action="{{ route(Str::replace('/', '.', $menu_path).'.update', $setting->id) }}">
        @csrf
        @if(!empty($setting))
            @method('PATCH')
        @endif
        <x-form.modal-header title="{{ __('Edit Kelas Jabatan') }}" />
        <div class="separator mt-2 mb-5 d-flex"></div>
        <div class="card-body pt-0">
            <x-form.input label="Nama" placeholder="Masukkan Nama" name="name" value="{{ $setting->name }}" readonly/>
            <x-form.input label="Nilai" placeholder="Masukkan Nilai" name="amount" value="{{ $setting->amount ?? 0 }}" required currency nospacing=""/>
        </div>
        <x-form.modal-footer />
    </form>
</div>
