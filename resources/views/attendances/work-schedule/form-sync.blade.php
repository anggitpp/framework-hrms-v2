<div class="card">
    <form method="POST" id="form-edit" action="{{ route('attendances.work-schedule.process-sync') }}">
        <x-form.modal-header title="{{ __('Sync Jadwal') }}" />
        @csrf
        <div class="separator mt-2 mb-5 d-flex"></div>
        <div class="card-body pt-0">
            <div class="row">
                <x-form.select name="shift_id" label="Shift" :datas="$shifts" required />
                <div class="col-md-6">
                    <x-form.datepicker name="start_date" class="w-100" label="Tanggal Mulai" required />
                </div>
                <div class="col-md-6">
                    <x-form.datepicker name="end_date" class="w-100" label="Tanggal Akhir" required />
                </div>
                <x-form.select name="location_id" option="- Semua Lokasi -" label="Lokasi Kerja" :datas="$locations" />
                <x-form.select name="employee_id" option="- Semua Pegawai -" label="Pegawai" :datas="$employees" />
            </div>
        </div>
        <x-form.modal-footer />
    </form>
</div>
