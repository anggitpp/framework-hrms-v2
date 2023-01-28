@props(['value'])
<span class="badge badge-{{ $value == 't' || $value == 1 ? 'success' : 'danger' }}">
    {{ $value == 't' || $value == 1 ? 'Ya' : 'Tidak' }}
</span>
