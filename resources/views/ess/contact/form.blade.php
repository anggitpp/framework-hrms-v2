<div class="card">
    <form method="POST" id="form-edit" action="{{ empty($contact) ? route(Str::replace('/', '.', $menu_path).'.store') : route(Str::replace('/', '.', $menu_path).'.update', $contact->id) }}" enctype="multipart/form-data">
        @csrf
        @if(!empty($contact))
            @method('PATCH')
        @endif
        <x-form.modal-header title="{{ empty($contact) ? __('Tambah Kontak') : __('Edit Kontak') }}" />
        <div class="separator mt-2 mb-5 d-flex"></div>
        <div class="card-body pt-0">
            <input type="hidden" name="employee_id" value="{{ Auth::user()->employee_id }}"/>
            <x-form.input label="Nama" name="name" value="{{ $contact->name ?? '' }}" required/>
            <x-form.select label="Hubungan" name="relationship_id" option="- Pilih Hubungan -" :datas="$relationships" value="{{ $contact->relationship_id ?? '' }}" required />
            <x-form.input label="Nomor HP" name="phone_number" value="{{ $contact->phone_number ?? '' }}" numeric required/>
            <x-form.textarea label="Keterangan" name="description" value="{{ $contact->description ?? '' }}"/>
        </div>
        <x-form.modal-footer />
    </form>
</div>
