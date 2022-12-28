@props(['url_edit' => '', 'url_destroy' => '', 'url_show' => '', 'isModal' => true, 'url_slot' => '', 'icon_slot' => 'fa-solid fa-list', 'isModalSlot' => true, 'menu_path' => ''])
<div class="text-center justify-content-between">
    @if($url_slot)
        <a
            @if($isModalSlot)
                data-url="{{ $url_slot }}"
            @else
                href="{{ $url_slot }}"
            @endif
            class="btn btn-icon btn-light-info w-30px h-30px me-1 {{ $isModalSlot ? 'btn-modal' : '' }}"
            @if($isModalSlot)
                data-bs-toggle="modal"
            @endif
        >
            <i class="{{ $icon_slot }}"></i>
        </a>
    @endif
    @if($url_show)
        <a href="{{ $url_show }}" class="btn btn-icon btn-light-success w-30px h-30px me-1">
            <i class="fa-solid fa-list"></i>
        </a>
    @endif
    @if($url_edit)
        @can('edit '.$menu_path)
            @if($isModal)
                <a data-bs-toggle="modal" class="btn btn-icon btn-light-primary w-30px h-30px me-1 btn-modal" data-url="{{ $url_edit }}">
                    <i class="fa-solid fa-pen"></i>
                </a>
            @else
                <a href="{{ $url_edit }}" class="btn btn-icon btn-light-primary w-30px h-30px me-1">
                    <i class="fa-solid fa-pen"></i>
                </a>
            @endif
        @endcan
    @endif
    @if($url_destroy)
        @can('delete '.$menu_path)
            <button href="{{ $url_destroy }}" id="delete" class="btn btn-icon btn-light-danger w-30px h-30px">
                <i class="fa-solid fa-trash"></i>
            </button>
        @endcan
    @endif
</div>
