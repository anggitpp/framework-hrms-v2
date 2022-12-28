<div class="card">
    <form method="POST" id="form-edit" action="{{ empty($family) ? route(Str::replace('/', '.', $menu_path).'.families.store', $id) : route(Str::replace('/', '.', $menu_path).'.families.update', $family->id) }}" enctype="multipart/form-data">
        @csrf
        @if(!empty($family))
            @method('PATCH')
        @endif
        <x-form.modal-header title="{{ empty($fmaily) ? __('Tambah Keluarga') : __('Edit Keluarga') }}" />
        <div class="separator mt-2 mb-5 d-flex"></div>
        <div class="card-body pt-0">
            <input type="hidden" name="employee_id" value="{{ $family->employee_id ?? $id }}"/>
            <x-form.input label="Nama" name="name" value="{{ $family->name ?? '' }}" required/>
            <x-form.select label="Hubungan" name="relationship_id" option="- Pilih Hubungan -" :datas="$relationships" value="{{ $family->relationship_id ?? '' }}" required />
            <div class="row">
                <div class="col-md-6">
                    <x-form.input label="Tempat Lahir" name="birth_place" value="{{ $family->birth_place ?? '' }}"/>
                    <x-form.input label="Nomor KTP" name="identity_number" value="{{ $family->identity_number ?? '' }}"/>
                </div>
                <div class="col-md-6">
                    <x-form.datepicker label="Tanggal Lahir" class="w-100" name="birth_date" value="{{ $family->birth_date ?? '' }}"/>
                    <x-form.radio name="gender" label="Jenis Kelamin" :datas="$genderOption" value="{{ $employee->gender ?? '' }}" />
                </div>
            </div>
            <x-form.textarea label="Keterangan" name="description" value="{{ $family->description ?? '' }}"/>
            <x-form.file label="File Pendukung" name="filename" value="{{ $family->filename ?? '' }}"/>
        </div>
        <x-form.modal-footer />
    </form>
</div>
