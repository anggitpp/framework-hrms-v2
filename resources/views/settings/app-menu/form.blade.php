<div class="card">
    <form method="POST" id="form-edit" action="{{ empty($menu) ? route('settings.app-menus.store') : route('settings.app-menus.update', $menu->id) }}">
        @csrf
        @if(!empty($menu))
            @method('PATCH')
        @endif
        <x-form.modal-header title="{{ empty($menu) ? __('Tambah Menu') : __('Edit Menu') }}" />
        <div class="separator mt-2 mb-5 d-flex"></div>
        <div class="card-body pt-0">
            <div class="row">
                <div class="col-md-6">
                    <x-form.select label="Modul" name="app_modul_id" required :datas="$moduls" event="getSub(this.value, 'app_sub_modul_id', 'sub-moduls');" value="{{ $menu->app_modul_id ?? $filterModul }}" />
                </div>
                <div class="col-md-6">
                    <x-form.select label="Sub Modul" name="app_sub_modul_id" required :datas="$submoduls" value="{{ $menu->app_sub_modul_id ?? $filterSubModul }}" />
                </div>
            </div>
            <x-form.input label="Nama" placeholder="Masukkan Nama" name="name" value="{{ $menu->name ?? '' }}" required/>
            <div class="row">
                <div class="col-md-6">
                    <x-form.input label="Target" placeholder="Masukkan Target" name="target" value="{{ $menu->target ?? '' }}" required/>
                </div>
                <div class="col-md-6">
                    <x-form.input label="Parameter" placeholder="Masukkan Parameter" name="parameter" value="{{ $menu->parameter ?? '' }}"/>
                </div>
            </div>
            <x-form.checkbox label="Permission" name="permission" :arr="$arrPermission" :datas="$permissions ?? []" />
            <x-form.textarea label="Keterangan" placeholder="Masukkan Keterangan" name="description" value="{{ $menu->description ?? '' }}" />
            <div class="row">
                <div class="col-md-6">
                    <x-form.input label="Icon" placeholder="Masukkan Icon" name="icon" value="{{ $menu->icon ?? '' }}"/>
                </div>
                <div class="col-md-6">
                    <x-form.input label="Order" placeholder="Masukkan Order" name="order" value="{{ $menu->order ?? 1 }}"/>
                </div>
            </div>
            <x-form.radio label="Status" name="status" :datas="$statusOption" value="{{ $menu->status ?? '' }}" />
        </div>
        <x-form.modal-footer />
    </form>
</div>
