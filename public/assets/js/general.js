$(function (){
    $(document).on('click', '#delete', function (e){
        e.preventDefault();
        var link = $(this).attr("href");

        Swal.fire({
            title: 'Are you sure?',
            text: "Delete this data?",
            icon: 'success',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('formDelete').action = link;
                document.getElementById('formDelete').submit();
            }
        })
    });
});

function showGlobalModal(uri, width){
    let currentURL = window.location.href;
    let splitURL = currentURL.split("?");
    let url = uri;
    $.ajax({
        url:
            splitURL[1] === undefined ? url
                : url + '?' + splitURL[1],
        method: "GET",
        success: function (data){
            $('#modal-form').find('.modal-body').html(data);
            $('#modal-form').modal('show');
            $('#modalDialogDefault').addClass(width);
        },
        error: function (error){
            console.log(error);
        }
    });
}

$(document).on('click', '.btn-modal', function(e){
    e.preventDefault();
    var url = $(this).data('url');
    var width = $(this).data('size') === undefined ? 'mw-650px' : $(this).data('size');
    showGlobalModal(url, width);
});

$(document).on('click', '#btnExport', function(e){
    e.preventDefault();
    let url = $(this).data('url');
    let type = $(this).data('type');
    if(type === 'undefined' || type === '' || type === 'excel') type = 'export';

    let filter = $('#filter').val();

    let filterMonth = $('#filterMonth').val();
    let filterYear = $('#filterYear').val();

    //GET ALL FILTER
    let combo_1 = $('#combo_1').val();
    let combo_2 = $('#combo_2').val();
    let combo_3 = $('#combo_3').val();
    let combo_4 = $('#combo_4').val();
    let combo_5 = $('#combo_5').val();
    let combo_6 = $('#combo_6').val();
    let combo_7 = $('#combo_7').val();
    let combo_8 = $('#combo_8').val();
    let combo_9 = $('#combo_9').val();
    let combo_10 = $('#combo_10').val();

    let filter_1 = $('#filter_1').val();
    let filter_2 = $('#filter_2').val();
    let filter_3 = $('#filter_3').val();
    let filter_4 = $('#filter_4').val();
    let filter_5 = $('#filter_5').val();
    let filter_6 = $('#filter_6').val();
    let filter_7 = $('#filter_7').val();
    let filter_8 = $('#filter_8').val();
    let filter_9 = $('#filter_9').val();
    let filter_10 = $('#filter_10').val();

    let completeURL = url + '/'+type+'?filter=' + filter + '&filterMonth=' + filterMonth + '&filterYear=' + filterYear + '&combo_1=' + combo_1 + '&combo_2=' + combo_2 + '&combo_3=' + combo_3 + '&combo_4=' + combo_4 + '&combo_5=' + combo_5 + '&combo_6=' + combo_6 + '&combo_7=' + combo_7 + '&combo_8=' + combo_8 + '&combo_9=' + combo_9 + '&combo_10=' + combo_10 + '&filter_1=' + filter_1 + '&filter_2=' + filter_2 + '&filter_3=' + filter_3 + '&filter_4=' + filter_4 + '&filter_5=' + filter_5 + '&filter_6=' + filter_6 + '&filter_7=' + filter_7 + '&filter_8=' + filter_8 + '&filter_9=' + filter_9 + '&filter_10=' + filter_10;
    window.open(completeURL, '_blank');
});

$('#modal-form').on('click', '#btnSubmit', function(){
    let btnSubmit = this;
    btnSubmit.setAttribute('data-kt-indicator', 'on');
    btnSubmit.disabled = true;
    var form = $(this).closest("form");
    var formId = new FormData(document.getElementById("form-edit"));
    var action = form.attr('action');
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
        }
    });
    $.ajax({
        url: action,
        method: 'post',
        processData: false,
        contentType: false,
        cache: false,
        data: formId,
        success: function(result){
            if(result.errors)
            {
                $.each(result.errors, function(key, value){
                    $('<p>'+value+'</p>').appendTo('#'+key+'-error');
                    $('#'+key+'-error').css('display', 'block');
                    $('#'+key).addClass("is-invalid");
                });
                btnSubmit.removeAttribute('data-kt-indicator');
                btnSubmit.disabled = false;
            }
            else
            {
                $('#modal-edit').modal('hide');
                let icon = 'success';
                if (result.success.indexOf("Gagal") >= 0) icon = 'error';
                Swal.fire({
                text: result.success,
                icon: icon,
                buttonsStyling: false,
                confirmButtonText: "Ok",
                customClass: {
                    confirmButton: "btn btn-primary"
                }
                }).then(function() {
                    window.location=result.url;
                });
            }
        },
    });
});

$('#modal-form').on('hidden.bs.modal', function () {
    $('select').select2({
        dropdownParent: $('#form-filter')
    });
})

$('#modal-form').on('shown.bs.modal', function (e) {
    $(".input-date").flatpickr(
        {
            dateFormat: "d/m/Y",
        }
    );

    $(".input-time").flatpickr(
        {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            time_24hr: true,
            static : true,
        }
    );
    $('.form-select').select2({
        dropdownParent: $('#modal-form')
    });
})

$(".input-date").flatpickr(
    {
        dateFormat: "d/m/Y",
    }
);

$(".input-daterange").flatpickr(
    {
        dateFormat: "d/m/Y",
        altInput: true,
        altFormat: "d/m/Y",
        mode: "range",
    }
);

$(".input-time").flatpickr(
    {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true,
    }
);

// getSub = function getSub(value, child, route) {
//     alert('test');
//     route = route == undefined ? 'subMasters' : route;
//     var pathArray = window.location.pathname.split( '/' );
//     var parentId = value;
//     if (parentId) {
//         $.ajax({
//             url: '/' + [pathArray[1], pathArray[2], route, parentId].join('/'),
//             type: "GET",
//             dataType: "json",
//             success: function (data) {
//                 var firstOption = $('select[name='+child+']').find("option:first-child").text();
//                 $('select[name='+child+']').empty();
//                 $('select[name='+child+']').append('<option value=""> '+ firstOption + '</option>');
//                 $.each(data, function (key, value) {
//                     $('select[name='+child+']').append('<option value="' + key + '">' + value + '</option>');
//                 });
//             }
//         });
//     } else {
//         $('select[name='+child+']').empty();
//         // var firstOption = $('select[name='+child+']').find("option:first-child").text();
//         // $('select[name='+child+']').append('<option value=""> '+ firstOption + '</option>');
//     }
// }

getSub = function getSub(value, child, route) {
    route = route == undefined ? 'subMasters' : route;
    var pathArray = window.location.pathname.split( '/' );
    var parentId = value;
    if (parentId) {
        $.ajax({
            url: '/' + [pathArray[1], pathArray[2], route, parentId].join('/'),
            type: "GET",
            success: function (data) {
                // alert(data);
                $('select[name='+child+']').empty();
                if(data.length == 0) $('select[name='+child+']').append('<option value="">&nbsp;</option>');
                $.each(data, function (key, datas) {
                    $('select[name='+child+']').append('<option value="' + datas.id + '">' + datas.name + '</option>');
                });
            }
        });
    } else {
        $('select[name='+child+']').empty();
        var firstOption = $('select[name='+child+']').find("option:first-child").text();
        $('select[name='+child+']').append('<option value=""> '+ firstOption + '</option>');
    }
}

setNumber = function setNumber (elem) {
    replace = elem.value.replace(/[\\A-Za-z!"?$%^&*_={};.\:'/@#~,?\<>?|`?\]\[]/g, '');
    elem.value = replace;
}

setCurrency = function setCurrency (elem) {
    replace = formatCurrency(elem.value.replace(/[\\A-Za-z!"?$%^&*+_={}; ()\-\:'/@#~,?\<>?|`?\]\[]/g, ''));
    if (replace.length == 0) replace = 0;
    elem.value = replace;
}

setNoSpacing = function setNoSpacing (elem) {
    replace = elem.value.replace(/ /g, "");
    elem.value = replace;
}

formatCurrency = function formatCurrency (val) {
    x = val.split(".");
    num = x[0];

    if (num < 0) return "";
    num = num.toString().replace(/\$|\,/g, '');
    if (isNaN(num))
        num = "0";
    sign = (num == (num = Math.abs(num)));
    num = Math.floor(num * 100 + 0.50000000001);
    cents = num % 100;
    num = Math.floor(num / 100).toString();
    if (cents < 10)
        cents = "0" + cents;
    for (var i = 0; i < Math.floor((num.length - (1 + i)) / 3); i++)
        num = num.substring(0, num.length - (4 * i + 3)) + ',' +
            num.substring(num.length - (4 * i + 3));

    if (x.length == 1)
        return (((sign) ? '' : '-') + num);
    else
        return (((sign) ? '' : '-') + num + "." + x[1].substr(0, 4));
}
