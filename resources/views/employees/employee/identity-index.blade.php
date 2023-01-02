<div class="row mt-2">
    <div class="col-md-6">
        <x-views.span-inline text="Nama" :value="$employee->name" />
        <x-views.span-inline text="Tempat Lahir" :value="$employee->place_of_birth" />
        <x-views.span-inline text="Nomor Induk Pegawai" :value="$employee->employee_number" />
        <x-views.span-inline text="Gender" :value="$employee->gender == 'm' ? 'Laki-Laki' : 'Perempuan'" />
        <x-views.span-inline text="Alamat KTP" :value="$employee->identity_address" />
        <x-views.span-inline text="Nomor Handphone" :value="$employee->mobile_phone_number" />
        <x-views.span-inline text="Status" :value="$employee->status_id ? $masters[$employee->status_id] : ''" />
        <x-views.span-inline text="Tanggal Masuk" :value="$employee->join_date ? setDate($employee->join_date, 't') : ''" />
        <x-views.span-inline text="PIN Mesin Absen" :value="$employee->attendance_pin" />
    </div>
    <div class="col-md-6">
        <x-views.span-inline text="Nama Panggilan" :value="$employee->nickname" />
        <x-views.span-inline text="Tanggal Lahir" :value="$employee->date_of_birth ? setDate($employee->date_of_birth) : ''" />
        <x-views.span-inline text="Nomor Identitas" :value="$employee->identity_number" />
        <x-views.span-inline text="Email" :value="$employee->email" />
        <x-views.span-inline text="Alamat Domisili" :value="$employee->address" />
        <x-views.span-inline text="Nomor Telepon" :value="$employee->phone_number" />
        <x-views.span-inline text="Status Perkawinan" :value="$employee->marital_status_id" />
        <x-views.span-inline text="Tanggal Keluar" :value="$employee->leave_date ? setDate($employee->leave_date) : ''" />
        <x-views.span-inline text="Agama" :value="$masters[$employee->religion_id] ?? ''" />
    </div>
</div>
