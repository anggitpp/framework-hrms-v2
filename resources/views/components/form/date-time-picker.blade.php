<div class="form-group">
    <label for="fp-default">{{ $label }}</label>
    <div class="row">
        <div class="col-md-6">
            <div class="input-group input-group-merge">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i data-feather='calendar'></i></span>
                </div>
                <input type="text"
                       id="{{ $name }}"
                       name="{{ $name }}"
                       value="{{ empty($value) ? empty(old($name)) ? '' : old($name) : setDate($value) }}"
                       class="form-control flatpickr-basic"/>
            </div>
        </div>
        <div class="col-md-6">
            <div class="input-group input-group-merge">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i data-feather='clock'></i></span>
                </div>
                <input type="text"
                       class="form-control flatpickr-time text-left flatpickr-input active col-md-6"
                       value="{{ empty($value2) ? empty(old($name2)) ? '' : old($name2) : setDate($value2) }}"
                       readonly="readonly"/>
            </div>
        </div>
    </div>
</div>
