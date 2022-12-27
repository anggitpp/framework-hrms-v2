<div class="card">
    <form method="POST" id="form-edit" action="{{ empty($shift) ? route(str_replace('/', '.', $menu_path).'.store') : route(str_replace('/', '.', $menu_path).'.update', $shift->id) }}">
        @csrf
        @if(!empty($shift))
            @method('PATCH')
        @endif
        @php
            $arr = ['t' => 'Ya'];
        @endphp
        <x-form.modal-header title="{{ empty($shift) ? __('Tambah Shift') : __('Edit Shift') }}" />
        <div class="separator mt-2 mb-5 d-flex"></div>
        <div class="card-body pt-0">
            <x-form.input label="Nama" placeholder="Masukkan Nama" name="name" value="{{ $shift->name ?? '' }}" required/>
            <div class="row">
                <div class="col-md-6">
                    <x-form.select label="Lokasi Kerja" name="location_id" :datas="$locations" value="{{ $shift->location_id ?? '' }}" option="- Semua Lokasi Kerja -" />
                    <x-form.timepicker label="Jam Masuk" name="start" value="{{ $shift->start ?? '' }}" required/>
                </div>
                <div class="col-md-6">
                    <x-form.input label="Kode" placeholder="Masukkan Kode" name="code" value="{{ $shift->code ?? '' }}" required maxlength="10"/>
                    <x-form.timepicker label="Jam Keluar" name="end" value="{{ $shift->end ?? '' }}" required/>
                </div>
            </div>
            <x-form.textarea label="Keterangan" placeholder="Masukkan Keterangan" name="description" value="{{ $shift->description ?? '' }}" />
            <div class="row">
                <div class="col-md-6">
                    <x-form.radio label="Status" name="status" :datas="$statusOption" value="{{ $shift->status ?? '' }}" />
                </div>
                <div class="col-md-6">
                    <x-form.single-checkbox label="Night Shift" name="night_shift" value="{{ $shift->night_shift ?? '' }}" :arr="$arr" />
                </div>
            </div>
        </div>
        <x-form.modal-footer />
    </form>
</div>
