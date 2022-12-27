@props(['label' => '', 'required' => '', 'name' => ''])
<div class="form-group mb-5">
    <label class="form-label {{ $required ? 'required' : '' }}">{{ $label }}</label>
    <div class="mb-3">
        <input class="form-control" type="file" name="{{ $name }}" id="formFile">
    </div>
</div>
