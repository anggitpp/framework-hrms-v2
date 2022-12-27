<div class="card">
    <form method="POST" id="form-edit" action="{{ route('employees.setting-unit-structures.update', $master->id) }}">
        @csrf
        @if(!empty($master))
            @method('PATCH')
        @endif
        <x-form.modal-header title="{{ __('Edit Unit Struktur') }}" />
        <div class="separator mt-2 mb-5 d-flex"></div>
        <div class="card-body pt-0">
            <x-form.input label="Unit" readonly class="form-control-solid" value="{{ $master->name ?? '' }}"/>
            <x-form.select label="Atasan" name="leader_id" option="- Pilih Atasan -" :datas="$employees" value="{{ $unit->leader_id ?? '' }}" />
            <x-form.select label="Admin/Tata Usaha" name="administration_id" option="- Pilih Admin -" :datas="$employees" value="{{ $unit->administration_id ?? '' }}" />
        </div>
        <x-form.modal-footer />
    </form>
</div>
