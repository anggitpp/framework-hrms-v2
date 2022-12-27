@props(['label' => '', 'placeholder' => '', 'name' => '', 'required' => '', 'value' => '', 'class' => '', 'event' => '', 'datas' => [], 'option' => '', 'all' => ''])
@if($label)
    <div class="form-group mb-5">
        <label class="form-label {!! $required ? "required" : "" !!}">{{ $label }}</label>
        <select
            @if($event)
                onchange="{{ $event }}"
            @endif
            @error($name)
                {{ $attributes->merge(['class' => 'form-control form-select is-invalid '.$class]) }}
            @else
                {{ $attributes->merge(['class' => 'form-control form-select '.$class]) }}
            @enderror
            data-control="select2" name="{{ $name }}" id="{{ $name }}">
            @if($option)
                <option value="">{{ $option }}</option>
            @endif
            @if($all)
                <option value="All">All</option>
            @endif
            @foreach($datas as $key => $values)
                    <option value="{{ $key }}" {{ $key == $value || $key == old($name) ? 'selected' : '' }} >{{ $values }}</option>
            @endforeach
        </select>
        @if($required)
            @error($name)
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="invalid-feedback" id="{{ $name }}-error"></div>
        @endif
    </div>
@else
    <div class="form-group me-2">
        <select
            @if($event)
                onchange="{{ $event }}"
            @endif
            {{ $attributes->merge(['class' => 'form-control '.$class]) }}
            data-control="select2" name="{{ $name }}" id="{{ $name }}">
            @if($option)
                <option value="">{{ $option }}</option>
            @endif
            @if($all)
                <option value="All">All</option>
            @endif
            @foreach($datas as $key => $values)
                <option value="{{ $key }}" {{ $key == $value ? 'selected' : '' }} >{{ $values }}</option>
            @endforeach
        </select>
    </div>
@endif
