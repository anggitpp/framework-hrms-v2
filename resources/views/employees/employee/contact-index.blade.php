<div class="card-toolbar">
    <div class="d-flex justify-content-end">
        @can('add '.$menu_path)
            <x-views.add-button-modal route="{{ route(Str::replace('/', '.', $menu_path).'.contact.create', $employee->id) }}" text="Tambah Data Kontak" />
        @endcan
    </div>
</div>
<div class="table-responsive pt-6">
    <table class="table table-rounded table-row-bordered border gy-5 gs-7">
        <thead>
        <tr class="text-start text-muted bg-gray-100 fw-bold fs-7 text-uppercase gs-0 border-bottom border-gray-200">
            <th width="10">No</th>
            <th width="*">Nama</th>
            <th width="200">Hubungan</th>
            <th width="200">No HP</th>
            <th width="130" class="text-center">Kontrol</th>
        </tr>
        </thead>
        <tbody class="text-gray-600 fw-semibold">
        @foreach($contacts as $k => $contact)
            <tr>
                <td>{{ $k + 1 }}</td>
                <td>{{ $contact->name }}</td>
                <td>{{ $contact->relationship->name }}</td>
                <td>{{ $contact->phone_number }}</td>
                <td class="text-center justify-content-between">
                    <x-views.action
                        menu_path="{{ $menu_path }}"
                        url_edit="{{ route(Str::replace('/', '.', $menu_path).'.contact.edit', $contact->id) }}"
                        url_destroy="{{ route(Str::replace('/', '.', $menu_path).'.contact.destroy',  $contact->id) }}" />
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <x-views.delete-form/>
</div>
