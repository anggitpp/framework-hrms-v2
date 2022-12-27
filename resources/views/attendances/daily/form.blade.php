<div class="card">
    <form method="POST" id="form-edit" action="{{ empty($attendance) ? route(Str::replace('/', '.', $menu_path).'.store') : route(Str::replace('/', '.', $menu_path).'.update', $attendance->id) }}">
        @csrf
        @php
            $divWFHActive = 'd-none';
            $divWFHNonActive = 'd-block';
        @endphp
        @if(!empty($attendance))
            @method('PATCH')
            @php
                if($attendance->type == '2'){
                   $divWFHActive = 'd-block';
                   $divWFHNonActive = 'd-none';
                }
            @endphp
        @endif
        @foreach ($errors->all() as $error)
            {{ $error }}<br/>
        @endforeach
        <x-form.modal-header title="{{ empty($attendance) ? __('Tambah Absen') : __('Edit Absen') }}" />
        <div class="separator mt-2 mb-5 d-flex"></div>
        <div class="card-body pt-0">
            <x-form.select label="Pegawai" name="employee_id" :datas="$employees" value="{{ $attendance->employee_id ?? '' }}" option="- Pilih Pegawai -" required />
            <div class="row">
                <div class="col-md-6">
                    <x-form.datepicker label="Tanggal" class="w-100" name="start_date" value="{{ $attendance->start_date ?? date('Y-m-d') }}" required/>
                </div>
                <div class="col-md-6">
                    <div id="wfh_active" class="{{ $divWFHActive }}">
                        <x-form.radio label="Tipe Absen" name="type" :datas="$types" value="{{ $attendance->type ?? '' }}" />
                    </div>
                    <div id="wfh_non_active" class="{{ $divWFHNonActive }}">
                        <x-form.radio label="Tipe Absen" name="type" :datas="array('1' => 'WFO', '3' => 'Dinas')" value="{{ $attendance->type ?? '' }}" />
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <x-form.timepicker label="Jam Masuk" name="start_time" value="{{ $attendance->start_time ?? '' }}" required/>
                    <x-form.textarea label="Alamat Masuk" name="start_address" value="{{ $attendance->start_address ?? '' }}" required/>
                </div>
                <div class="col-md-6">
                    <x-form.timepicker label="Jam Keluar" name="end_time" value="{{ $attendance->end_time ?? '' }}" required/>
                    <x-form.textarea label="Alamat Keluar" name="end_address" value="{{ $attendance->start_address ?? '' }}" required/>
                </div>
            </div>
            <x-form.textarea label="Keterangan" placeholder="Masukkan Keterangan" name="description" value="{{ $attendance->description ?? '' }}" />
        </div>
        <x-form.modal-footer />
    </form>
</div>
<script>
    $(document).ready(function() {
        $('#employee_id').on('change', function() {
            let employee_id = $('#employee_id').val();
            $.ajax({
                url: "{{ route('attendances.dailies.checkWFH') }}",
                type: "GET",
                data: {
                    employee_id: employee_id
                },
                success: function(data) {
                    if (data === '1') {
                        $('#wfh_active').removeClass('d-none');
                        $('#wfh_non_active').addClass('d-none');
                    } else {
                        $('#wfh_active').addClass('d-none');
                        $('#wfh_non_active').removeClass('d-none');
                    }
                }
            });
        });
    });
</script>
