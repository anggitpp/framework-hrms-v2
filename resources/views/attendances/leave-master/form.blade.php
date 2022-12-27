<div class="card">
    <form method="POST" id="form-edit" action="{{ empty($master) ? route(str_replace('/', '.', $menu_path).'.store') : route(str_replace('/', '.', $menu_path).'.update', $master->id) }}">
        @csrf
        @if(!empty($master))
            @method('PATCH')
        @endif
        <x-form.modal-header title="{{ empty($master) ? __('Tambah Master Cuti') : __('Edit Master Cuti') }}" />
        <div class="separator mt-2 mb-5 d-flex"></div>
        <div class="card-body pt-0">
            <x-form.input label="Tipe Cuti" placeholder="Masukkan Tipe Cuti" name="name" value="{{ $master->name ?? '' }}" required/>
            <div class="row">
                <div class="col-md-6">
                    <x-form.select label="Lokasi" name="location_id" option="- Semua Lokasi Kerja -" :datas="$locations" value="{{ $master->location_id ?? '' }}"/>
                    <x-form.datepicker name="start_date" class="w-100" label="Tanggal Mulai" value="{{ $master->start_date ?? '' }}" required />
                    <x-form.input name="work_period" class="text-end" label="Minimal Masa Kerja (Bulan)" value="{{ $master->work_period ?? 0 }}" numeric/>
                </div>
                <div class="col-md-6">
                    <x-form.input label="Jatah Cuti (Hari)" placeholder="Masukkan Jatah Cuti" class="text-end" name="balance" value="{{ $master->balance ?? '' }}" numeric required/>
                    <x-form.datepicker name="end_date" class="w-100" label="Tanggal Selesai" value="{{ $master->end_date ?? '' }}" required />
                    <x-form.radio label="Gender" name="gender" :datas="$genderOption" value="{{ $master->gender ?? '' }}" />
                </div>
            </div>
            <x-form.textarea label="Keterangan" placeholder="Masukkan Keterangan" name="description" value="{{ $master->description ?? '' }}" />
            <x-form.radio label="Status" name="status" :datas="$statusOption" value="{{ $master->status ?? '' }}" />
        </div>
        <x-form.modal-footer />
    </form>
</div>
