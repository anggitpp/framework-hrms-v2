@extends('layouts.app')
@section('content')
    <div class="card">
        <form method="POST" id="form-edit" action="{{ empty($component) ? route(Str::replace('/', '.', $menu_path).'.store') : route(Str::replace('/', '.', $menu_path).'.update', $component->id) }}" enctype="multipart/form-data">
            @csrf
            @php
                $divCalculation = 'd-none';
                $divPeriod = 'd-none';
            @endphp
            @if(!empty($component))
                @method('PATCH')
                @php
                    if($component->calculation_type == '3') $divCalculation = '';
                    if($component->calculation_cut_off == '2') $divPeriod = '';
                @endphp
            @endif
            @php
                $type = $selected_menu->parameter == 'a' ? 'Penambah' : 'Pengurang';
            @endphp
            <x-form.header title="{{ empty($component) ? 'Tambah '.$type : 'Edit '.$type }}" />
            <input type="hidden" name="type" value="{{ $selected_menu->parameter }}" />
            <input type="hidden" name="master_id" value="{{ $_GET['master_id'] }}" />
            <div class="separator mt-2 mb-5 d-flex"></div>
            <div class="card-body pt-0">
                <div class="row">
                    <div class="col-md-6">
                        <x-form.input label="Kode" name="code" value="{{ $component->code ?? '' }}" class="w-50" required />
                        <x-form.input label="Nama" name="name" value="{{ $component->name ?? '' }}" required />
                        <x-form.textarea label="Keterangan" placeholder="Masukkan Keterangan" name="description" value="{{ $component->description ?? '' }}" />
                    </div>
                    <div class="col-md-6">
                        <x-form.input label="Tipe" value="{{ $type }}" readonly />
                        <x-form.radio label="Status" name="status" :datas="$statusOption" value="{{ $component->status ?? '' }}" />
                    </div>
                </div>
                <div class="separator mt-2 mb-5 d-flex"></div>
                <div class="row">
                    <div class="col-md-6">
                        <x-form.radio label="Tipe Perhitungan" name="calculation_type" :datas="$calculationTypes" value="{{ $component->calculation_type ?? '' }}" event="changeCalculationType();" />
                    </div>
                    <div class="col-md-6 {{ $divCalculation }}" id="calculation_div">
                        <x-form.input label="Nilai" name="calculation_amount" value="{{ $component->calculation_amount ?? '' }}" class="w-50" required />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <x-form.radio label="Periode Cut Off" name="calculation_cut_off" :datas="$cutOffTypes" value="{{ $component->calculation_cut_off ?? '' }}" event="changePeriodType();" />
                        <x-form.textarea label="Keterangan" placeholder="Masukkan Keterangan" name="description" value="{{ $component->description ?? '' }}" />
                    </div>
                    <div class="col-md-6 {{ $divPeriod }}" id="period_div">
                        <x-form.double-input label="Tanggal Mulai & Selesai"
                                             name="calculation_cut_off_date_start" value="{{ $component->calculation_cut_off_date_start ?? '' }}"
                                             name2="calculation_cut_off_date_end" value2="{{ $component->calculation_cut_off_date_end ?? '' }}"
                                             class="text-end" maxlength="2" required />
                    </div>
                </div>
            </div>
        </form>
    </div>
    <script>
        function changeCalculationType()
        {
            let calculationType = $('input[name="calculation_type"]:checked').val();
            if(calculationType === '3') {
                $('#calculation_div').removeClass('d-none');
            } else {
                $('#calculation_div').addClass('d-none');
            }
        }

        function changePeriodType()
        {
            let calculationPeriodType = $('input[name="calculation_cut_off"]:checked').val();
            if(calculationPeriodType === '2') {
                $('#period_div').removeClass('d-none');
            } else {
                $('#period_div').addClass('d-none');
            }
        }
    </script>
@endsection
