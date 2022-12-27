@extends('layouts.app')
@section('content')
    <div class="card">
        <x-views.employee-detail :employee="$employee" />
        <div class="separator pt-5 mx-8"></div>
        <div class="d-flex justify-content-end">
            <form method="GET" id="form-filter">
                <div class="card-header border-0 pt-6">
                    <div class="card-toolbar">
                        <div class="d-flex">
                            <x-views.filter-month-year name-month="filterMonth" value-month="{{ $filterMonth }}" name-year="filterYear" value-year="{{ $filterYear }}" event="changeFilterMonthYear();" range="5" />
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-body pt-0">
            <table class="table table-row-bordered gy-5 border mt-5">
                <thead>
                <tr class="fw-bold text-uppercase border-bottom border-gray-200">
                    <th rowspan="2" width="180" class="text-center border-end">Tanggal</th>
                    <th colspan="2" width="200" class="text-center border-end">Jadwal</th>
                    <th colspan="2" width="200" class="text-center border-end">Aktual</th>
                    <th rowspan="2" width="100" class="text-center border-end">Durasi</th>
                    <th rowspan="2" width="*" class="text-center border-end">Keterangan</th>
                </tr>
                <tr class="fw-bold text-uppercase border-bottom border-gray-200">
                    <th width="100" class="text-center border-end">Masuk</th>
                    <th width="100" class="text-center border-end">Pulang</th>
                    <th width="100" class="text-center border-end">Masuk</th>
                    <th width="100" class="text-center border-end">Pulang</th>
                </tr>
                </thead>
                <tbody class="text-gray-600 fw-semibold">
                    @foreach($attendances as $attendance)
                        @php
                            $startShift = $schedules[$attendance->start_date]['start_time'] ?? $defaultShift->start;
                            $endShift = $schedules[$attendance->start_date]['end_time'] ?? $defaultShift->end;
                        @endphp
                        <tr>
                            <td class="text-center border-end">{{ setDate($attendance->start_date, 't') }}</td>
                            <td class="text-center border-end">{{ Str::substr($startShift,  0, 5) }}</td>
                            <td class="text-center border-end">{{ Str::substr($endShift,  0, 5) }}</td>
                            <td class="text-center border-end">{{ Str::substr($attendance->start_time,0,5) }}</td>
                            <td class="text-center border-end">{{ Str::substr($attendance->end_time,0,5) }}</td>
                            <td class="text-center border-end">{{ Str::substr($attendance->duration,0,5) }}</td>
                            <td class="text-center border-end">{{ $attendance->description }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <x-modal-form/>
    <script>
        function changeFilterMonthYear() {
            $('#form-filter').submit();
        }
    </script>
@endsection
