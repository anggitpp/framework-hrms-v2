<div class="card">
    <form method="POST" id="form-edit" action="{{ route('attendances.work-schedule.update', [$schedule->employee_id, $schedule->date]) }}">
        @csrf
        @if(!empty($schedule))
            @method('PATCH')
        @endif
        <x-form.modal-header title="{{ __('Edit Jadwal') }}" />
        <div class="separator mt-2 mb-5 d-flex"></div>
        <div class="card-body pt-0">
            <div class="row">
                <div class="col-md-6">
                    <x-form.input label="Tanggal" value="{{ setDate($schedule->date, 't') }}" readonly/>
                    <x-form.timepicker label="Masuk" name="start_time" value="{{ substr($schedule->start_time, 0, 5) ?? '' }}" required/>
                </div>
                <div class="col-md-6">
                    <x-form.select label="Shift" name="shift_id" required :datas="$shifts" value="{{ $schedule->shift_id ?? '' }}" option="OFF" event="getDetailShift(this.value);" />
                    <x-form.timepicker label="Pulang" name="end_time" value="{{ substr($schedule->end_time, 0, 5) ?? '' }}" required/>
                </div>
            </div>
            <x-form.textarea label="Keterangan" placeholder="Masukkan Keterangan" name="description" value="{{ $schedule->description ?? '' }}" />
        </div>
        <x-form.modal-footer />
    </form>
</div>
<script>
    function getDetailShift(shift_id) {
        $.ajax({
            url: "{{ route('attendances.work-schedule.shift') }}",
            type: "GET",
            data: {
                shift_id: shift_id
            },
            success: function (data) {
                alert(data);
                alert(data.start);
                alert(data.end);
                $('#start_time').val(data.start);
                $('#end_time').val(data.end);
            },
            error: function (data) {
                $('#start_time').val('00:00');
                $('#end_time').val('00:00');
            }
        });
    }
</script>
