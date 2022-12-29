<div class="card-toolbar">
    <div class="d-flex justify-content-end">
        @can('add '.$menu_path)
            <x-views.add-button-modal route="{{ route(Str::replace('/', '.', $menu_path).'.asset.create', $employee->id) }}" text="Tambah Data Aset" />
        @endcan
    </div>
</div>
<div class="table-responsive pt-6">
    <table class="table table-rounded table-row-bordered border gy-5 gs-7">
        <thead>
        <tr class="text-start text-muted bg-gray-100 fw-bold fs-7 text-uppercase gs-0 border-bottom border-gray-200">
            <th width="10">No</th>
            <th width="*">Nama</th>
            <th width="150">Nomor</th>
            <th width="150">Tipe</th>
            <th width="100">Mulai</th>
            <th width="100">Selesai</th>
            <th width="50">File</th>
            <th width="80">Status</th>
            <th width="130" class="text-center">Kontrol</th>
        </tr>
        </thead>
        <tbody class="text-gray-600 fw-semibold">
        @foreach($assets as $k => $asset)
            <tr>
                <td>{{ $k + 1 }}</td>
                <td>{{ $asset->name }}</td>
                <td>{{ $asset->number }}</td>
                <td>{{ $asset->type->name ?? '' }}</td>
                <td>{{ setDate($asset->start_date) ?? '' }}</td>
                <td>{{ setDate($asset->end_date) ?? '' }}</td>
                <td>
                    <x-views.download-button url="{{ $asset->filename }}" />
                </td>
                <td>
                    <x-views.status :status="$asset->status" />
                </td>
                <td class="text-center justify-content-between">
                    <x-views.action
                        menu_path="{{ $menu_path }}"
                        url_edit="{{ route(Str::replace('/', '.', $menu_path).'.asset.edit', $asset->id) }}"
                        url_destroy="{{ route(Str::replace('/', '.', $menu_path).'.asset.destroy',  $asset->id) }}" />
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ generatePagination($assets) }}
    <x-views.delete-form/>
</div>
