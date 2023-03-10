<div class="card">
    <form method="POST" id="form-edit"
          action="{{ empty($bank) ? route(Str::replace('/', '.', $menu_path).'.store') : route(Str::replace('/', '.', $menu_path).'.update', $bank->id) }}">
        @csrf
        @if(!empty($bank))
            @method('PATCH')
        @endif
        <x-form.modal-header title="{{ empty($bank) ? __('Tambah Data Bank') : __('Edit Data Bank') }}"/>
        <div class="separator mt-2 mb-5 d-flex"></div>
        <div class="card-body pt-0">
            <input type="hidden" name="employee_id" value="{{ Auth::user()->employee_id }}">
            <x-form.select name="bank_id" required label="Bank" option="- Pilih Bank -" :datas="$banks"
                           value="{{ $bank->bank_id ?? '' }}"/>
            <div class="row">
                <div class="col-md-6">
                    <x-form.input name="account_number" label="Nomor Rekening" value="{{ $bank->account_number ?? '' }}"
                                  required numeric/>
                    <x-form.textarea name="description" label="Keterangan" value="{{ $bank->description ?? '' }}"/>
                    <x-form.radio name="status" label="Status" :datas="$statusOption"
                                  value="{{ $bank->status ?? '' }}"/>
                </div>
                <div class="col-md-6">
                    <x-form.input name="account_name" label="Nama Pemilik Rekening"
                                  value="{{ $bank->account_name ?? '' }}" required/>
                    <x-form.input name="branch" label="Cabang" value="{{ $bank->branch ?? '' }}"/>
                </div>
            </div>
        </div>
        <x-form.modal-footer />
    </form>
</div>
