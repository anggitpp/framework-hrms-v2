@props(['title', 'isCanSave' => true])
<div class="card-header border-0 pt-6 justify-content-between d-flex">
    <div class="card-title">
        <h3 class="card-label">
            {{ $title }}
        </h3>
    </div>
    <div>
        <a href="{{ url()->previous() }}" class="btn btn-light-primary me-3">
            <i class="fas fa-chart-pie"></i> Kembali
        </a>
        @if($isCanSave)
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-print"></i>Simpan
            </button>
        @endif
    </div>
</div>
