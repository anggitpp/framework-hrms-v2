<div class="card">
    <form method="POST" id="form-edit" action="{{ empty($holiday) ? route(str_replace('/', '.', $menu_path).'.store') : route(str_replace('/', '.', $menu_path).'.update', $holiday->id) }}">
        @csrf
        @if(!empty($holiday))
            @method('PATCH')
        @endif
        <x-form.modal-header title="{{ empty($holiday) ? __('Tambah Libur Resmi') : __('Edit Libur Resmi') }}" />
        <div class="separator mt-2 mb-5 d-flex"></div>
        <div class="card-body pt-0">
            <x-form.input label="Nama" placeholder="Masukkan Nama" name="name" value="{{ $holiday->name ?? '' }}" required/>
            <div class="row">
                <div class="col-md-6">
                    <x-form.datepicker label="Tanggal Mulai" class="w-100" name="start_date" value="{{ $holiday->start_date ?? '' }}" required/>
                </div>
                <div class="col-md-6">
                    <x-form.datepicker label="Tanggal Selesai" class="w-100" name="end_date" value="{{ $holiday->end_date ?? '' }}" required/>
                </div>
            </div>
            <x-form.textarea label="Keterangan" placeholder="Masukkan Keterangan" name="description" value="{{ $holiday->description ?? '' }}" />
            <x-form.radio label="Status" name="status" :datas="$statusOption" value="{{ $holiday->status ?? '' }}" />
        </div>
        <x-form.modal-footer />
    </form>
</div>
