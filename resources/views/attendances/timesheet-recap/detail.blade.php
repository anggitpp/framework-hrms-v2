<div class="card">
    <x-form.modal-header title="Detail Kinerja" />
    <div class="separator mt-2 mb-5 d-flex"></div>
    <div class="card-body">
        <table class="table table-rounded table-row-bordered border gy-5 gs-7">
            <thead>
                <tr class="text-start text-muted bg-gray-100 fw-bold fs-7 text-uppercase gs-0 border-bottom border-gray-200">
                    <th width="20">No.</th>
                    <th width="100">Kegiatan</th>
                    <th width="*">Kegiatan</th>
                    <th width="100">Mulai</th>
                    <th width="100">Selesai</th>
                    <th width="100">Durasi</th>
                    <th width="100">Volume</th>
                    <th width="100">Satuan</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 fw-semibold">
                @foreach($timesheets as $k => $timesheet)
                 <tr>
                     <td class="text-center">{{ $k + 1 }}</td>
                     <td>{{ $timesheet->activity }}</td>
                     <td>{{ $timesheet->output }}</td>
                     <td class="text-center">{{ Str::substr($timesheet->start_time, 0, 5) }}</td>
                     <td class="text-center">{{ Str::substr($timesheet->end_time, 0, 5) }}</td>
                     <td class="text-center">{{ Str::substr($timesheet->duration, 0, 5) }}</td>
                     <td>{{ $timesheet->volume }}</td>
                     <td>{{ $timesheet->type }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
