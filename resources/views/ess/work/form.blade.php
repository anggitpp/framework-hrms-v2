<div class="card">
    <form method="POST" id="form-edit" action="{{ empty($work) ? route(Str::replace('/', '.', $menu_path).'.store') : route(Str::replace('/', '.', $menu_path).'.update', $work->id) }}" enctype="multipart/form-data">
        @csrf
        @if(!empty($work))
            @method('PATCH')
        @endif
        <x-form.modal-header title="{{ empty($work) ? __('Tambah Kerja') : __('Edit Kerja') }}" />
        <div class="separator mt-2 mb-5 d-flex"></div>
        <div class="card-body pt-0">
            <input type="hidden" name="employee_id" value="{{ Auth::user()->employee_id }}"/>
            <x-form.input label="Perusahaan" name="company" value="{{ $work->company ?? '' }}" required/>
            <x-form.input label="Posisi" name="position" value="{{ $work->position ?? '' }}" required/>
            <div class="row">
                <div class="col-md-6">
                    <x-form.datepicker label="Tanggal Mulai" class="w-100" name="start_date" value="{{ $work->start_date ?? '' }}" required/>
                </div>
                <div class="col-md-6">
                    <x-form.datepicker label="Tanggal Selesai" class="w-100" name="end_date" value="{{ $work->end_date ?? '' }}" />
                </div>
            </div>
            <x-form.input label="Kota" name="city" value="{{ $work->city ?? '' }}"/>
            <x-form.textarea label="Keterangan" name="description" value="{{ $work->description ?? '' }}"/>
            <x-form.file label="File Pendukung" name="filename" value="{{ $work->filename ?? '' }}"/>
        </div>
        <x-form.modal-footer />
    </form>
</div>
