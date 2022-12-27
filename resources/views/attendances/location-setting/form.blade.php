<div class="card">
    <form method="POST" id="form-edit" action="{{ route('attendances.location-settings.update', $master->id) }}">
        @csrf
        @if(!empty($master))
            @method('PATCH')
        @endif
        <x-form.modal-header title="Edit Setting Lokasi" />
        <div class="separator mt-2 mb-5 d-flex"></div>
        <div class="card-body pt-0">
            <x-form.input label="Lokasi Kerja" readonly class="form-control-solid" value="{{ $master->name ?? '' }}"/>
            <x-form.textarea label="Alamat" name="address" value="{{ $location->address ?? '' }}"/>
            <div class="row">
                <div class="col-md-6">
                    <x-form.input label="Latitude" name="latitude" value="{{ $location->latitude ?? '' }}"/>
                </div>
                <div class="col-md-6">
                    <x-form.input label="Longitude" name="longitude" value="{{ $location->longitude ?? '' }}"/>
                </div>
            </div>
            <x-form.input label="Radius (M)" name="radius" numeric value="{{ $location->radius ?? '' }}"/>
            <x-form.toggle-button label="Absen WFH (Work From Home)" name="wfh" value="{{ $location->wfh ?? '' }}" data="t"/>
        </div>
        <x-form.modal-footer />
    </form>
</div>
