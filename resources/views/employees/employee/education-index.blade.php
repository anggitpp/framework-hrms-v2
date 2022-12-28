<div class="card-toolbar">
    <div class="d-flex justify-content-end">
        @can('add '.$menu_path)
            <x-views.add-button-modal route="{{ route(Str::replace('/', '.', $menu_path).'.education.create', $employee->id) }}" text="Tambah Data Pendidikan" />
        @endcan
    </div>
</div>
<div class="table-responsive pt-6">
    <table class="table table-rounded table-row-bordered border gy-5 gs-7">
        <thead>
        <tr class="text-start text-muted bg-gray-100 fw-bold fs-7 text-uppercase gs-0 border-bottom border-gray-200">
            <th width="10">No</th>
            <th width="100">Tingkatan</th>
            <th width="*">Nama</th>
            <th width="150">Jurusan</th>
            <th width="100">Mulai</th>
            <th width="100">Selesai</th>
            <th width="50">File</th>
            <th width="130" class="text-center">Kontrol</th>
        </tr>
        </thead>
        <tbody class="text-gray-600 fw-semibold">
        @foreach($educations as $k => $education)
            <tr>
                <td>{{ $k + 1 }}</td>
                <td>{{ $education->level->name }}</td>
                <td>{{ $education->name }}</td>
                <td>{{ $education->major }}</td>
                <td>{{ $education->start_year }}</td>
                <td>{{ $education->end_year }}</td>
                <td>
                    <x-views.download-button url="{{ $education->filename }}" />
                </td>
                <td class="text-center justify-content-between">
                    <x-views.action
                        menu_path="{{ $menu_path }}"
                        url_edit="{{ route(Str::replace('/', '.', $menu_path).'.education.edit', $education->id) }}"
                        url_destroy="{{ route(Str::replace('/', '.', $menu_path).'.education.destroy',  $education->id) }}" />
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <x-views.delete-form/>
</div>
