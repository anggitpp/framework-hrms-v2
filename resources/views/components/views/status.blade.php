@props(['status'])
<span class="badge badge-{{ $status == 't' || $status == 1 ? 'success' : 'danger' }}">
    {{ $status == 't' || $status == 1 ? 'Aktif' : 'Tidak Aktif' }}
</span>
