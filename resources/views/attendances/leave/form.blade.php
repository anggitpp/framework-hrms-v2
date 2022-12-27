@extends('layouts.app')
@section('content')
    <div class="card">
        <form method="POST" id="form-edit" action="{{ empty($leave) ? route(str_replace('/', '.', $menu_path).'.store') : route(str_replace('/', '.', $menu_path).'.update', $leave->id) }}" enctype="multipart/form-data">
            @csrf
            @php
                $classCanHidden = 'd-block';
            @endphp
            @if(!empty($leave))
                @method('PATCH')
            @endif
            @php
                $master_balance = $leave->leaveMaster->balance ?? 0;
                if(!empty($leave)){
                    if($master_balance == 0) $classCanHidden = 'd-none';
                }
            @endphp
            <x-form.header title="{{ empty($leave) ? __('Tambah Data Cuti') : __('Edit Data Cuti') }}" />
            <div class="separator mt-2 mb-5 d-flex"></div>
            <div class="card-body pt-0">
                <div class="row">
                    <div class="col-md-6">
                        <x-form.input label="Nomor" name="number" value="{{ $leave->number ?? $lastNumber }}" readonly required />
                        <x-form.select label="Pegawai" name="employee_id" option="- Pilih Pegawai -" :datas="$employees" value="{{ $leave->employee_id ?? '' }}" event="getDetailEmployee();getDetailLeave();" required/>
                        <x-form.input label="Unit" name="unit" value="{{ $leave->employee->position->unit_id ?? '' }}" readonly />
                    </div>
                    <div class="col-md-6">
                        <x-form.datepicker label="Tanggal Pengajuan" name="date" value="{{ $leave->date ?? date('Y-m-d') }}" required />
                        <x-form.input label="Pangkat" name="rank" value="{{ $leave->employee->position->rank_id ?? '' }}" readonly />
                    </div>
                </div>
                <div class="separator mt-2 mb-5 d-flex"></div>
                <div class="row">
                    <div class="col-md-6">
                        <x-form.select label="Tipe Cuti" name="leave_master_id" option="- Pilih Tipe Cuti -" :datas="$masters" value="{{ $leave->leave_master_id ?? '' }}" required event="getDetailLeave();"/>
                    </div>
                    <div class="col-md-6">
                        <div class="canHidden {{ $classCanHidden  }}">
                            <x-form.input label="Jatah Cuti" name="balance" value="{{ $leave->balance ?? '' }}" class="w-50 text-end" readonly required />
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <x-form.datepicker name="start_date" label="Tanggal Mulai" value="{{ $leave->start_date ?? '' }}" required event="getTotalLeave();" />
                        <div class="canHidden {{ $classCanHidden  }}">
                            <x-form.input label="Jumlah Cuti" name="amount" value="{{ $leave->amount ?? '' }}" class="w-50 text-end" readonly />
                        </div>
                        <x-form.file label="Bukti" name="filename" value="{{ $leave->filename ?? '' }}" />
                    </div>
                    <div class="col-md-6">
                        <x-form.datepicker name="end_date" label="Tanggal Selesai" value="{{ $leave->end_date ?? '' }}" required event="getTotalLeave();" />
                        <div class="canHidden {{ $classCanHidden  }}">
                            <x-form.input label="Sisa Cuti" name="remaining" value="{{ $leave->remaining ?? '' }}" class="w-50 text-end" readonly />
                        </div>
                        <x-form.input label="Keterangan" placeholder="Masukkan Keterangan" name="description" value="{{ $leave->description ?? '' }}" />
                    </div>
                </div>
            </div>
            <input type="hidden" id="isUnlimited" name="isUnlimited" value="{{ $master_balance == 0 ? 'true' : 'false' }}">
        </form>
    </div>
@endsection
@section('scripts')
    <script>
        function getDetailEmployee() {
            let employee_id = $('#employee_id').val();
            $.ajax({
                url: "{{ route('attendances.leaves.employee') }}",
                type: "GET",
                data: {
                    employee_id: employee_id
                },
                success: function (response) {
                    $('#rank').val(response['position']['rank_id']);
                    $('#unit').val(response['position']['unit_id']);
                }
            });
        }

        function getDetailLeave(){
            let leave_master_id = $('#leave_master_id').val();
            let employee_id = $('#employee_id').val();
            let isUnlimited = $('#isUnlimited');
            $.ajax({
                url: "{{ route('attendances.leaves.leave') }}",
                type: "GET",
                data: {
                    leave_master_id: leave_master_id,
                    employee_id: employee_id
                },
                success: function (response) {
                    if(response['isUnlimited']) {
                        isUnlimited.val(true);
                        $('.canHidden').removeClass('d-block').addClass('d-none');
                    }else{
                        isUnlimited.val(false);
                        $('#balance').val(response['balance']);
                        $('.canHidden').removeClass('d-none').addClass('d-block');
                    }
                }
            });
        }

        function getTotalLeave() {
            let start_date = $('#start_date').val();
            let end_date = $('#end_date').val();
            let balance = $('#balance').val();
            let isUnlimited = $('#isUnlimited').val();
            if (start_date !== '' && end_date !== '') {
                $.ajax({
                    url: "{{ route('attendances.leaves.totalLeave') }}",
                    type: "GET",
                    data: {
                        start_date: start_date,
                        end_date: end_date,
                        balance: balance
                    },
                    success: function (response) {
                        if (isUnlimited === 'false') {
                            if (response['remaining'] <= 0) {
                                alert('Jumlah cuti yang di ambil melebihi jatah cuti');
                                $('#end_date').val('');
                            } else {
                                $('#amount').val(response['amount']);
                                $('#remaining').val(response['remaining']);
                            }
                        }
                    }
                });
            }
        }
    </script>
@endsection

