<div class="card">
    <form method="POST" id="form-edit" action="{{ route(Str::replace('/', '.', $menu_path).'.update', $master->id) }}">
        @csrf
        @if(!empty($master))
            @method('PATCH')
        @endif
        <x-form.modal-header title="{{ __('Edit Kelas Jabatan') }}" />
        <div class="separator mt-2 mb-5 d-flex"></div>
        <div class="card-body pt-0">
            <x-form.input label="Nama" placeholder="Masukkan Nama" name="name" value="{{ $master->name }}" readonly/>
            <x-form.input label="Nilai" placeholder="Masukkan Nilai" name="amount" value="{{ $master->payrollSetting->amount ?? 0 }}" required currency nospacing=""/>
        </div>
        <x-form.modal-footer />
    </form>
</div>
