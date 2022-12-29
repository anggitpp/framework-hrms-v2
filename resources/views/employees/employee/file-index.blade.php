<div class="card-toolbar">
    <div class="d-flex justify-content-end">
        @can('add '.$menu_path)
            <x-views.add-button-modal route="{{ route(Str::replace('/', '.', $menu_path).'.file.create', $employee->id) }}" text="Tambah Data File" />
        @endcan
    </div>
</div>
<div class="table-responsive pt-6">
    <table class="table table-rounded table-row-bordered border gy-5 gs-7">
        <thead>
        <tr class="text-start text-muted bg-gray-100 fw-bold fs-7 text-uppercase gs-0 border-bottom border-gray-200">
            <th width="10">No</th>
            <th width="200">Nama</th>
            <th width="*">Keterangan</th>
            <th width="100">File</th>
            <th width="130" class="text-center">Kontrol</th>
        </tr>
        </thead>
        <tbody class="text-gray-600 fw-semibold">
        @foreach($files as $k => $file)
            <tr>
                <td>{{ $k + 1 }}</td>
                <td>{{ $file->name }}</td>
                <td>{{ $file->description }}</td>
                <td>
                    <x-views.download-button url="{{ $file->filename }}" />
                </td>
                <td class="text-center justify-content-between">
                    <x-views.action
                        menu_path="{{ $menu_path }}"
                        url_edit="{{ route(Str::replace('/', '.', $menu_path).'.file.edit', $file->id) }}"
                        url_destroy="{{ route(Str::replace('/', '.', $menu_path).'.file.destroy',  $file->id) }}" />
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ generatePagination($files) }}
    <x-views.delete-form/>
</div>
