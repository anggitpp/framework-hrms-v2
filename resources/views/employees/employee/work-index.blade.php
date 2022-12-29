<div class="card-toolbar">
    <div class="d-flex justify-content-end">
        @can('add '.$menu_path)
            <x-views.add-button-modal route="{{ route(Str::replace('/', '.', $menu_path).'.work.create', $employee->id) }}" text="Tambah Data Pekerjaan" />
        @endcan
    </div>
</div>
<div class="table-responsive pt-6">
    <table class="table table-rounded table-row-bordered border gy-5 gs-7">
        <thead>
        <tr class="text-start text-muted bg-gray-100 fw-bold fs-7 text-uppercase gs-0 border-bottom border-gray-200">
            <th width="10">No</th>
            <th width="*">Perusahaan</th>
            <th width="150">Posisi</th>
            <th width="100">Mulai</th>
            <th width="100">Selesai</th>
            <th width="50">File</th>
            <th width="130" class="text-center">Kontrol</th>
        </tr>
        </thead>
        <tbody class="text-gray-600 fw-semibold">
        @foreach($works as $k => $work)
            <tr>
                <td>{{ $k + 1 }}</td>
                <td>{{ $work->company }}</td>
                <td>{{ $work->position }}</td>
                <td>{{ setDate($work->start_date) }}</td>
                <td>{{ $work->end_date != '0000-00-00' ? setDate($work->end_date) : '' }}</td>
                <td>
                    <x-views.download-button url="{{ $work->filename }}" />
                </td>
                <td class="text-center justify-content-between">
                    <x-views.action
                        menu_path="{{ $menu_path }}"
                        url_edit="{{ route(Str::replace('/', '.', $menu_path).'.work.edit', $work->id) }}"
                        url_destroy="{{ route(Str::replace('/', '.', $menu_path).'.work.destroy',  $work->id) }}" />
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ generatePagination($works) }}
    <x-views.delete-form/>
</div>
