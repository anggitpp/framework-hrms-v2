<div class="card">
    <form method="POST" id="form-edit" action="{{ empty($position) ? route(Str::replace('/', '.', $menu_path).'.position.store', $id) : route(Str::replace('/', '.', $menu_path).'.position.update', $position->id) }}" enctype="multipart/form-data">
        @csrf
        @if(!empty($position))
            @method('PATCH')
        @endif
        <x-form.modal-header title="{{ empty($position) ? __('Tambah Jabatan') : __('Edit Jabatan') }}" />
        <div class="separator mt-2 mb-5 d-flex"></div>
        <div class="card-body pt-0">
            <input type="hidden" name="employee_id" value="{{ $position->employee_id ?? $id }}"/>
            <div class="row">
                <div class="col-md-6">
                    <x-form.select name="position_id" required label="Jabatan" option="- Pilih Jabatan -" :datas="$positions" value="{{ $position->position_id ?? '' }}" />
                    <x-form.select name="rank_id" required label="Pangkat" option="- Pilih Pangkat -" :datas="$ranks" value="{{ $position->rank_id ?? '' }}" />
                    <x-form.input name="sk_number" label="Nomor SK" value="{{ $position->sk_number ?? '' }}" />
                    <x-form.datepicker name="start_date" required label="Tanggal Mulai" class="w-100" value="{{ $position->start_date ?? '' }}" />
                    <x-form.select name="unit_id" required label="Unit" option="- Pilih Unit -" :datas="$units" value="{{ $position->unit_id ?? '' }}" />
                    <x-form.select name="leader_id" label="Atasan Langsung" option="- Pilih Atasan -" :datas="$employees" value="{{ $position->leader_id ?? '' }}"/>
                    <x-form.radio name="status" label="Status" :datas="$statusOption" value="{{ $position->status ?? '' }}" />
                </div>
                <div class="col-md-6">
                    <x-form.select name="employee_type_id" required label="Tipe Pegawai" option="- Pilih Tipe Pegawai -" :datas="$types" value="{{ $position->employee_type_id ?? '' }}" />
                    <x-form.select name="grade_id" label="Grade/Golongan" option="- Pilih Grade/Golongan -" :datas="$grades" value="{{ $position->grade_id ?? '' }}" />
                    <x-form.datepicker name="sk_date" label="Tanggal SK" class="w-100" value="{{ $position->sk_date ?? '' }}" />
                    <x-form.datepicker name="end_date" label="Tanggal Selesai" class="w-100" value="{{ $position->end_date ?? '' }}" />
                    <x-form.select name="location_id" required label="Lokasi Kerja" option="- Pilih Lokasi Kerja -" :datas="$locations" value="{{ $position->location_id ?? '' }}" />
                    <x-form.select name="shift_id" label="Shift" option="- Pilih Shift -" :datas="$shifts" value="{{ $employee->position->shift_id ?? array_key_first($shifts) }}" />
                </div>
            </div>
        </div>
        <x-form.modal-footer />
    </form>
</div>
