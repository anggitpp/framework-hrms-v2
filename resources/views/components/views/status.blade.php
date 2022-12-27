@props(['status'])
<span class="badge badge-{{ $status == 't' ? 'success' : 'danger' }}">
    {{ $status == 't' ? 'Aktif' : 'Tidak Aktif' }}
</span>
