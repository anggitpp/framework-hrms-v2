@props(['label' => '', 'name' => '', 'placeholder' => '', 'value' => '', 'class' => '', 'required' => '', 'error' => ''])
<div class="form-group mb-5">
    <label class="form-label {!! $required ? "required" : "" !!}">{{ $label }}</label>
    <textarea
        name="{{ $name }}"
        id="{{ $name }}"
        @error($name) @php $error = "is-invalid"; @endphp @enderror
        {{ $attributes->merge(['class' => 'form-control '.$class.' '.$error]) }}>{{ empty($value) ? empty(old($name)) ? '' : old($name) : $value }}</textarea>
        @error($name)
            <div class="invalid-feedback" id="{{ $name }}-error">
                {{ $message }}
            </div>
        @enderror
</div>
