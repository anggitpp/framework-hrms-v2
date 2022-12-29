@props(['label' => '', 'required' => '', 'name' => '', 'value' => ''])
@php
    if($value){
        $classExist = 'd-block';
        $classEmpty = 'd-none';
    }else{
        $classExist = 'd-none';
        $classEmpty = 'd-block';
    }
@endphp
<div class="form-group mb-5">
    <label class="form-label {{ $required ? 'required' : '' }}">{{ $label }}</label>
    <div class="mb-3">
        <div id="file-exist" class="{{ $classExist }}">
            <a href="storage/{{ $value }}" class="btn btn-icon btn-light-dark w-40px h-40px me-1" download="">
                <i class="fa-solid fa-download"></i>
            </a>
            <a class="btn btn-icon btn-light-danger w-40px h-40px" onclick="deleteFile();">
                <i class="fa-solid fa-trash"></i>
            </a>
        </div>
        <div id="file-empty" class="{{ $classEmpty }}">
            <input class="form-control" type="file" name="{{ $name }}" id="formFile">
        </div>
        <input type="hidden" name="isDelete" id="isDelete">
    </div>
</div>
<script>
    function deleteFile(){
        $('#file-exist').addClass('d-none');
        $('#file-empty').removeClass('d-none');
        $('#isDelete').val('t');
    }
</script>
