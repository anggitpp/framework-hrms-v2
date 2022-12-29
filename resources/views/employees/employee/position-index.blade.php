@php use Carbon\Carbon; @endphp
<div class="card-toolbar">
    <div class="d-flex justify-content-end">
        @can('add '.$menu_path)
            <x-views.add-button-modal route="{{ route(Str::replace('/', '.', $menu_path).'.position.create', $employee->id) }}" text="Tambah Data Jabatan" />
        @endcan
    </div>
</div>
<div class="table-responsive pt-6">
    <table class="table table-rounded table-row-bordered border gy-5 gs-7">
        <thead>
        <tr class="text-start text-muted bg-gray-100 fw-bold fs-7 text-uppercase gs-0 border-bottom border-gray-200">
            <th width="10">No</th>
            <th width="100">Nomor SK</th>
            <th width="*">Jabatan</th>
            <th width="150">Pangkat</th>
            <th width="150">Grade</th>
            <th width="200">Periode</th>
            <th width="80">Status</th>
            <th width="130" class="text-center">Kontrol</th>
        </tr>
        </thead>
        <tbody class="text-gray-600 fw-semibold">
        @foreach($positions as $k => $position)
            @php
                $endPeriod = $position->end_date ? Carbon::create($position->end_date)->format('M Y') : 'current';
                $position->periode = Carbon::create($position->start_date)->format('M Y')." - ".$endPeriod;
            @endphp
            <tr>
                <td>{{ $k + 1 }}</td>
                <td>{{ $position->sk_number }}</td>
                <td>{{ $masters[$position->position_id] }}</td>
                <td>{{ $masters[$position->rank_id] }}</td>
                <td>{{ isset($position->grade_id) ? $masters[$position->grade_id] : '' }}</td>
                <td>{{ $position->periode }}</td>
                <td>
                    <x-views.status :status="$position->status" />
                </td>
                <td class="text-center justify-content-between">
                    <x-views.action
                        menu_path="{{ $menu_path }}"
                        url_edit="{{ route(Str::replace('/', '.', $menu_path).'.position.edit', $position->id) }}"
                        url_destroy="{{ route(Str::replace('/', '.', $menu_path).'.position.destroy',  $position->id) }}" />
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ generatePagination($positions) }}
    <x-views.delete-form/>
</div>
