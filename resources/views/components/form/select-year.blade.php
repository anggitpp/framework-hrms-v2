@php
$range = $range ?? 5;
$startYear = date('Y') - $range;
$endYear = date('Y') + $range;
@endphp
@if($label)
    <div class="form-group">
        <label>{{ $label }} {!! $required ? "<span style='color: red'> *</span>" : "" !!}</label>
        <select name="{{ $name }}"
                class="select form-control "
                id="{{ $name }}"
                @if($event)
                onchange="{{ $event }}"
            @endif
        >
            @if($options)
                <option value="">{{ $options }}</option>
            @endif
            @if($all)
                <option value="All">All</option>
            @endif
            @for($i = 1; $i<=12; $i++)
                <option value="{{ $i }}" {{ $i == $value ? 'selected' : '' }} >{{ numToMonth($i) }}</option>
            @endfor
        </select>
        @if($required)
            <div class="invalid-feedback" id="{{ $name }}-error"></div>
        @endif
    </div>
@else
    <div class="pr-1">
        <select name="{{ $name }}"
                class="select form-control "
                id="{{ $name }}"
                @if($event)
                onchange="{{ $event }}"
            @endif
        >
            @if($options)
                <option value="">{{ $options }}</option>
            @endif
            @if($all)
                <option value="All">All</option>
            @endif
            @for($i = $startYear; $i<=$endYear; $i++)
                <option value="{{ $i }}" {{ $i == $value ? 'selected' : '' }} >{{ $i }}</option>
            @endfor
        </select>
    </div>
@endif
