<div class="card">
    <form method="POST" id="form-edit" action="{{ empty($attendance) ? route(Str::replace('/', '.', $menu_path).'.store') : route(Str::replace('/', '.', $menu_path).'.update', $attendance->id) }}">
        @csrf
        @if(!empty($attendance))
            @method('PATCH')
        @endif
        <x-form.modal-header title="{{ empty($attendance) ? __('Tambah Absen') : __('Edit Absen') }}" />
        <div class="separator mt-2 mb-5 d-flex"></div>
        <div class="card-body pt-0">
            <div class="row">
                <div class="col-md-6">
                    <x-form.select label="Pegawai" name="employee_id" :datas="$employees" value="{{ $attendance->employee_id ?? '' }}" option="- Pilih Pegawai -" required />
                    <x-form.timepicker label="Jam Masuk" name="start_time" value="{{ $attendance->start_time ?? '' }}" required/>
                    <x-form.textarea label="Alamat Masuk" name="start_address" value="{{ $attendance->start_address ?? '' }}" required/>
                </div>
                <div class="col-md-6">
                    <x-form.datepicker label="Tanggal" class="w-100" name="start_date" value="{{ $attendance->start_date ?? date('Y-m-d') }}" required/>
                    <x-form.timepicker label="Jam Keluar" name="end_time" value="{{ $attendance->end_time ?? '' }}" required/>
                    <x-form.textarea label="Alamat Keluar" name="end_address" value="{{ $attendance->start_address ?? '' }}" required/>
                </div>
            </div>
            <x-form.textarea label="Keterangan" placeholder="Masukkan Keterangan" name="description" value="{{ $attendance->description ?? '' }}" />
        </div>
        <x-form.modal-footer />
    </form>
</div>
