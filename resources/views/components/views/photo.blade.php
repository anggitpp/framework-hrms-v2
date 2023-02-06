@props(['photo'])
<div class="d-flex align-items-center">
    @if($photo)
        <div class="symbol symbol-50px symbol-md-50px me-6">
            <img src="{{ asset('storage'.$photo) }}" alt="photo" />
        </div>
    @endif
</div>