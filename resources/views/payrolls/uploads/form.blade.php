<div class="card">
    <form method="POST" id="form-edit" action="{{ route(Str::replace('/', '.', $menu_path).'.update', $upload->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PATCH')
        <x-form.modal-header title="Edit Data" />
        <div class="separator mt-2 mb-5 d-flex"></div>
        <div class="card-body pt-0">
            <x-form.input label="Nama" placeholder="Masukkan Nama" name="name" value="{{ $upload->employee->name ?? '' }}" readonly/>
            <x-form.input label="Nilai" placeholder="Masukkan Nilai" name="amount" value="{{ $upload->amount ?? 0 }}" required currency/>
            <x-form.textarea label="Keterangan" placeholder="Masukkan Keterangan" name="description" value="{{ $upload->description ?? '' }}" />
        </div>
        <x-form.modal-footer />
    </form>
</div>
