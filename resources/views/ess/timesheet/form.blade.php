<div class="card">
    <form method="POST" id="form-edit" action="{{ empty($timesheet) ? route(str_replace('/', '.', $menu_path).'.store') : route(str_replace('/', '.', $menu_path).'.update', $timesheet->id) }}">
        @csrf
        @if(!empty($timesheet))
            @method('PATCH')
        @endif
        <x-form.modal-header title="{{ empty($timesheet) ? __('Tambah Data Kinerja') : __('Edit Data Kinerja') }}" />
        <div class="separator mt-2 mb-5 d-flex"></div>
        <div class="card-body pt-0">
            <x-form.datepicker name="date" label="Tanggal" value="{{ $timesheet->date ?? date('Y-m-d') }}" required />
            <x-form.input label="Kegiatan" placeholder="Masukkan Kegiatan" name="activity" value="{{ $timesheet->activity ?? '' }}" required/>
            <x-form.input label="Output" placeholder="Masukkan Output" name="output" value="{{ $timesheet->output ?? '' }}" required/>
            <div class="row">
                <div class="col-md-6">
                    <x-form.timepicker name="start_time" label="Mulai" value="{{ $timesheet->start_time ?? '' }}" required />
                    <x-form.input label="Volume" placeholder="Masukkan Volume" name="volume" value="{{ $timesheet->volume ?? '' }}" required/>
                </div>
                <div class="col-md-6">
                    <x-form.timepicker name="end_time" label="Selesai" value="{{ $timesheet->end_time ?? '' }}" required />
                    <x-form.input label="Satuan" placeholder="Masukkan Satuan" name="type" value="{{ $timesheet->type ?? '' }}" required/>
                </div>
            </div>
            <x-form.textarea label="Keterangan" placeholder="Masukkan Keterangan" name="description" value="{{ $timesheet->description ?? '' }}" />
        </div>
        <x-form.modal-footer />
    </form>
</div>
