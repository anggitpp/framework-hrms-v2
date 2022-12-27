@props(['label' => '', 'placeholder' => '', 'name' => '', 'required' => '', 'value' => '', 'class' => '', 'event' => '', 'option' => '', 'all' => ''])
<select
    @if($event)
        onchange="{{ $event }}"
    @endif
        data-control="select2" name="{{ $name }}" id="{{ $name }}">
    @if($option)
        <option value="">{{ $option }}</option>
    @endif
    @if($all)
        <option value="All">All</option>
    @endif
    @for($i = 1; $i<=12; $i++)
        <option value="{{ $i }}" {{ $i == $value ? 'selected' : '' }} >{{ numToMonth($i) }}</option>
    @endfor
</select>
