<div class="card">
    <form method="POST" id="form-edit" action="{{ route(Str::replace('/', '.', $menu_path).'.process-import') }}" enctype="multipart/form-data">
        @csrf
        @method('PATCH')
        <x-form.modal-header title="{{ $title }}" />
        <div class="separator mt-2 mb-5 d-flex"></div>
        <div class="card-body pt-0">
            <x-form.file label="File" name="filename" />
        </div>
        <x-form.modal-footer/>
    </form>
</div>
