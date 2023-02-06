@props(['route' => '', 'text' => '', 'class' => '', 'filter' => ''])
<button {{ $attributes->merge(['class' => 'btn btn-primary btn-modal '.$class]) }} data-bs-toggle="modal" data-url="{{ $route }}" data-filter="{{ $filter }}">
    <i class="fa-solid fa-plus"></i> {{ $text }}
</button>
