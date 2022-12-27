@extends('layouts.app')
@section('content')
    <div class="card">
        <form method="GET" id="form-filter">
            <div class="card-header border-0 pt-6">
                <div class="card-toolbar">
                    <div class="d-flex">
                        <x-form.select name="filterEmployee" event="document.getElementById('form-filter').submit();" class="w-400px" :datas="$employees" value="{{ $filterEmployee }}" />
                    </div>
                </div>
                <div class="card-toolbar">
                    <div class="d-flex">
                        <x-views.filter-month-year name-month="filterMonth" value-month="{{ $filterMonth }}" name-year="filterYear" value-year="{{ $filterYear }}" event="changeFilterMonthYear();" range="5" class="me-3" />
                    </div>
                </div>
            </div>
        </form>
        <x-views.employee-detail :employee="$employee" />
        <div class="separator"></div>
        <div class="card-body">
            <div class="d-flex justify-content-end">
                @can('add '.$menu_path)
                    <x-views.add-button-modal route="{{ route(Str::replace('/', '.', $menu_path).'.sync') }}" text="Sync {{ $selected_menu->name }}" />
                @endcan
            </div>
            <div class="table-responsive py-4">
                <table id="datatables" class="table table-rounded table-row-bordered border gy-5 gs-7">
                    <thead>
                        <tr class="text-start text-muted bg-gray-100 fw-bold fs-7 text-uppercase gs-0 border-bottom border-gray-200">
                            <th width="100">Tanggal</th>
                            <th width="200">Shift</th>
                            <th width="100">Masuk</th>
                            <th width="100">Pulang</th>
                            <th width="150">Keterangan</th>
                            <th width="100" class="text-center">Kontrol</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 fw-semibold">
                        @foreach($schedules as $k => $schedule)
                            <tr @if($schedule['shift'] == "OFF") class="table-danger" @endif>
                                <td>{{ setDate($schedule['date'], 't') }}</td>
                                <td>{{ $schedule['shift'] }}</td>
                                <td>{{ $schedule['start'] }}</td>
                                <td>{{ $schedule['end'] }}</td>
                                <td>{{ $schedule['description'] }}</td>
                                <td class="text-center">
                                    @can('edit '.$menu_path)
                                    <a data-bs-toggle="modal" class="btn btn-icon btn-light-primary w-30px h-30px me-1 btn-modal" data-url="{{ route(str_replace('/', '.', $menu_path).'.edit', [$filterEmployee, $schedule['date']]) }}">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <x-modal-form/>
    <script>
        function changeFilterMonthYear() {
            $('#form-filter').submit();
        }
    </script>
@endsection
