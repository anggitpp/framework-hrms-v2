@props(["label" => "", "required" => "", "name" => "", "placeholder" => "", "value" => "", "name2" => "", "value2" => "", "class" => "", "nospacing" => "", "numeric" => "", "currency" => "", "maxlength" => ""])
<div class="form-group mb-5">
    <label class="form-label {!! $required ? "required" : "" !!}">{{ $label }}</label>
    <div class="row d-flex align-items-center">
        <div class="col-md-2">
            <input type="text"
                   id="{{ $name }}"
                   {{ $attributes->merge(['class' => 'form-control w-100px '.$class]) }}
                   name="{{ $name }}"
                   value="{{ old($name, $currency ? setCurrency($value) : $value) }}"
                   placeholder="{{ $placeholder }}"
                   maxlength="{{ $maxlength }}"
                {{ $nospacing ? 'onkeyup=setNoSpacing(this);' : '' }}
                {{ $numeric ? 'onkeyup=setNumber(this);' : '' }}
                {{ $currency ? 'onkeyup=setCurrency(this);' : '' }}
                {{ $currency ? 'style=text-align:right;' : '' }}
            />
        </div>
        <div class="col-sm-1">
            <span class="ms-3 mt-5">s/d</span>
        </div>
        <div class="col-md-2">
            <input type="text"
                   id="{{ $name2 }}"
                   {{ $attributes->merge(['class' => 'form-control w-100px '.$class]) }}
                   name="{{ $name2 }}"
                   value="{{ old($name2, $currency ? setCurrency($value2) : $value2) }}"
                   placeholder="{{ $placeholder }}"
                   maxlength="{{ $maxlength }}"
                {{ $nospacing ? 'onkeyup=setNoSpacing(this);' : '' }}
                {{ $numeric ? 'onkeyup=setNumber(this);' : '' }}
                {{ $currency ? 'onkeyup=setCurrency(this);' : '' }}
                {{ $currency ? 'style=text-align:right;' : '' }}
            />
        </div>

    </div>
</div>
