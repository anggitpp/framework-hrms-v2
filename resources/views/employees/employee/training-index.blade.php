<div class="card-toolbar">
    <div class="d-flex justify-content-end">
        @can('add '.$menu_path)
            <x-views.add-button-modal route="{{ route(Str::replace('/', '.', $menu_path).'.training.create', $employee->id) }}" text="Tambah Data Pelatihan" />
        @endcan
    </div>
</div>
<div class="table-responsive pt-6">
    <table class="table table-rounded table-row-bordered border gy-5 gs-7">
        <thead>
        <tr class="text-start text-muted bg-gray-100 fw-bold fs-7 text-uppercase gs-0 border-bottom border-gray-200">
            <th width="10">No</th>
            <th width="*">Perihal</th>
            <th width="150">Institusi</th>
            <th width="120">No. Sertifikat</th>
            <th width="150">Tipe</th>
            <th width="100">Mulai</th>
            <th width="100">Selesai</th>
            <th width="50">File</th>
            <th width="130" class="text-center">Kontrol</th>
        </tr>
        </thead>
        <tbody class="text-gray-600 fw-semibold">
        @foreach($trainings as $k => $training)
            <tr>
                <td>{{ $k + 1 }}</td>
                <td>{{ $training->subject }}</td>
                <td>{{ $training->institution }}</td>
                <td>{{ $training->certificate_number }}</td>
                <td>{{ $training->type->name ?? '' }}</td>
                <td>{{ setDate($training->start_date) }}</td>
                <td>{{ setDate($training->end_date) }}</td>
                <td>
                    <x-views.download-button url="{{ $training->filename }}" />
                </td>
                <td class="text-center justify-content-between">
                    <x-views.action
                        menu_path="{{ $menu_path }}"
                        url_edit="{{ route(Str::replace('/', '.', $menu_path).'.training.edit', $training->id) }}"
                        url_destroy="{{ route(Str::replace('/', '.', $menu_path).'.training.destroy',  $training->id) }}" />
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ generatePagination($trainings) }}
    <x-views.delete-form/>
</div>
