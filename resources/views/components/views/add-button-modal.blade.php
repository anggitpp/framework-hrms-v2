@props(['route' => '', 'text' => '', 'class' => ''])
<button {{ $attributes->merge(['class' => 'btn btn-primary btn-modal '.$class]) }} data-bs-toggle="modal" data-url="{{ $route }}">
    <i class="fa-solid fa-plus"></i> {{ $text }}
</button>
