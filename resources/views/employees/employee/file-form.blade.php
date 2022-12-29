<div class="card">
    <form method="POST" id="form-edit" action="{{ empty($file) ? route(Str::replace('/', '.', $menu_path).'.file.store', $id) : route(Str::replace('/', '.', $menu_path).'.file.update', $file->id) }}" enctype="multipart/form-data">
        @csrf
        @if(!empty($file))
            @method('PATCH')
        @endif
        <x-form.modal-header title="{{ empty($file) ? __('Tambah File') : __('Edit File') }}" />
        <div class="separator mt-2 mb-5 d-flex"></div>
        <div class="card-body pt-0">
            <input type="hidden" name="employee_id" value="{{ $file->employee_id ?? $id }}"/>
            <x-form.input label="Nama" name="name" value="{{ $file->name ?? '' }}" required/>
            <x-form.textarea label="Keterangan" name="description" value="{{ $file->description ?? '' }}"/>
            <x-form.file label="File Pendukung" name="filename" value="{{ $file->filename ?? '' }}"/>
        </div>
        <x-form.modal-footer />
    </form>
</div>
