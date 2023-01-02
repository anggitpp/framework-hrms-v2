<div class="card">
    <form method="POST" id="form-edit" action="{{ empty($education) ? route(Str::replace('/', '.', $menu_path).'.education.store', $id) : route(Str::replace('/', '.', $menu_path).'.education.update', $education->id) }}" enctype="multipart/form-data">
        @csrf
        @if(!empty($education))
            @method('PATCH')
        @endif
        <x-form.modal-header title="{{ empty($education) ? __('Tambah Pendidikan') : __('Edit Pendidikan') }}" />
        <div class="separator mt-2 mb-5 d-flex"></div>
        <div class="card-body pt-0">
            <input type="hidden" name="employee_id" value="{{ $education->employee_id ?? $id }}"/>
            <x-form.select label="Tingkatan" name="level_id" option="- Pilih Tingkatan -" :datas="$levels" value="{{ $education->level_id ?? '' }}" required />
            <x-form.input label="Nama Institusi" name="name" value="{{ $education->name ?? '' }}" required/>
            <x-form.input label="Jurusan" name="major" value="{{ $education->major ?? '' }}"/>
            <div class="row">
                <div class="col-md-6">
                    <x-form.input label="Tahun Mulai" name="start_year" value="{{ $education->start_year ?? '' }}" class="w-50 text-end" numeric maxlength="4"/>
                    <x-form.input label="Kota/Lokasi" name="city" value="{{ $education->city ?? '' }}"/>
                </div>
                <div class="col-md-6">
                    <x-form.input label="Tahun Selesai" name="end_year" value="{{ $education->end_year ?? '' }}" class="w-50 text-end" numeric maxlength="4"/>
                    <x-form.input label="Nilai/IPK" name="score" value="{{ $education->score ?? '' }}" class="w-50 text-end" numeric/>
                </div>
            </div>
            <x-form.textarea label="Keterangan" name="description" value="{{ $education->description ?? '' }}"/>
            <x-form.file label="File Pendukung" name="filename" value="{{ $education->filename ?? '' }}"/>
        </div>
        <x-form.modal-footer />
    </form>
</div>
