<div class="card">
    <form method="POST" id="form-edit" action="{{ empty($asset) ? route(Str::replace('/', '.', $menu_path).'.asset.store', $id) : route(Str::replace('/', '.', $menu_path).'.asset.update', $asset->id) }}" enctype="multipart/form-data">
        @csrf
        @if(!empty($asset))
            @method('PATCH')
        @endif
        <x-form.modal-header title="{{ empty($asset) ? __('Tambah Asset') : __('Edit Asset') }}" />
        <div class="separator mt-2 mb-5 d-flex"></div>
        <div class="card-body pt-0">
            <input type="hidden" name="employee_id" value="{{ $asset->employee_id ?? $id }}"/>
            <x-form.input label="Nama Aset" name="name" value="{{ $asset->name ?? '' }}" required/>
            <div class="row">
                <div class="col-md-6">
                    <x-form.input label="Nomor Seri" name="number" value="{{ $asset->number ?? '' }}"/>
                    <x-form.select label="Kategori" name="category_id" option="- Pilih Kategori -" :datas="$categories" value="{{ $asset->category_id ?? '' }}" />
                    <x-form.datepicker label="Tanggal Mulai" class="w-100" name="start_date" value="{{ $asset->start_date ?? '' }}"/>
                </div>
                <div class="col-md-6">
                    <x-form.datepicker label="Tanggal" class="w-100" name="date" value="{{ $asset->date ?? '' }}" required/>
                    <x-form.select label="Tipe" name="type_id" option="- Pilih Tipe -" :datas="$types" value="{{ $asset->type_id ?? '' }}" />
                    <x-form.datepicker label="Tanggal Selesai" class="w-100" name="end_date" value="{{ $asset->end_date ?? '' }}"/>
                </div>
            </div>
            <x-form.textarea label="Keterangan" name="description" value="{{ $asset->description ?? '' }}"/>
            <x-form.file label="File Pendukung" name="filename" value="{{ $asset->filename ?? '' }}"/>
            <x-form.radio label="Status" name="status" :datas="$statusOption" value="{{ $asset->status ?? '' }}"/>
        </div>
        <x-form.modal-footer />
    </form>
</div>
