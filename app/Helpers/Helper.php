<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Intervention\Image\Facades\Image;

function setCurrency($value)
{
    return number_format($value);
}

function resetCurrency($value)
{
    list($value) = explode("]", str_replace("[>", "", $value));
    $value = $value == "" ? "0" : $value;

    $ret = substr($value, 0, 1) == "." ? "0" . $value : $value;
    $ret = str_replace(",", "", $ret);

    return $ret;
}

function convertTimeToSeconds(String $time)
{
    $time = explode(":", $time);
    $seconds = $time[0] * 3600;
    $seconds += $time[1] * 60;
    $seconds += $time[2];

    return $seconds;
}

function convertMinutesToTime($minute, $format = '%02d:%02d') {
    if ($minute < 1) {
        return;
    }
    $hours = floor($minute / 60);
    $minutes = ($minute % 60);
    return sprintf($format, $hours, $minutes);
}

function setDate($value, $format = null)
{
    return Carbon::createFromFormat('Y-m-d', $value)->translatedFormat($format ? 'd F Y' : 'd/m/Y');
}

function setDateTime($value, $format = null)
{
    return Carbon::createFromFormat('Y-m-d H:i:s', $value)->format($format ?: 'd/m/Y H:i:s');
}

function resetDate($value)
{
    return Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
}

function defaultStatus()
{
    return array('t' => 'Aktif', 'f' => 'Tidak Aktif');
}

function yesOrNoOption()
{
    return array('t' => 'Ya', 'f' => 'Tidak');
}

function defaultStatusApproval()
{
    return array('t' => 'Disetujui', 'f' => 'Ditolak');
}

function uploadOne(UploadedFile $uploadedFile, $folder = null, $disk = 'public', $filename = null)
{
    $name = !is_null($filename) ? $filename : Str::random(25);

    $file = $uploadedFile->storeAs($folder, $name . '.' . $uploadedFile->getClientOriginalExtension(), $disk);

    return $file;
}

function uploadFile($image, $name, $folder, $resize = false, $width = 300, $height = 300)
{
    if($image){
        // Make a file path where image will be stored [ folder path + file name + file extension]
        $filePath = $folder . $name. '.' . $image->getClientOriginalExtension();
        // Upload image
        uploadOne($image, $folder, 'public', $name);
        // Set user profile image path in database to filePath
        $picture = $filePath;

        //RESIZE
        if($resize) {
            $thumbnailpath = public_path('storage/' . $picture);
            $img = Image::make($thumbnailpath)->resize($width, $height, function ($constraint) {
                $constraint->aspectRatio();
            });
            $img->save($thumbnailpath);
        }

        return $picture;

    }
}

function generatePagination($items, $form = 'form-filter')
{
    $pagination = $items->withQueryString()->onEachSide(0)->links();
    $arrNumber = array("10", "50", "100", "500", "1000");
    ?>
    <div class="d-flex justify-content-between align-items-center">
        <div class="ml-1">
            <select name="paginate" form="<?= $form ?>" onchange="document.getElementById('<?= $form ?>').submit();" class="form-control">
                <?php
                foreach ($arrNumber as $key => $value)
                {
                    $selected = 10;
                    if(!empty($_GET['paginate']))
                        $selected = $_GET['paginate'] == $value ? 'selected' : '';
                echo "<option $selected value=\"$value\">$value</option>";
                }
                ?>
            </select>
        </div>
        <div class="ml-1 <?= $items->total() <= $items->perPage() ? 'mb-1' : '' ?>">
            <i style="font-size: 12px">Showing <?= $items->firstItem() ?> to <?= $items->lastItem() ?> of <?= $items->total() ?> entries</i>
        </div>
        <div>
            <?php
            if($items->total() >= $items->perPage()) {
                echo $pagination;
            }else{
                ?>
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-end mt-1 mr-2 mb-2">
                        <li class="page-item prev"><a class="page-link"></a></li>
                        <li class="page-item active" aria-current="page">
                            <a class="page-link">1</a>
                        </li>
                        <li class="page-item next"><a class="page-link"></a></li>
                    </ul>
                </nav>
            <?php
            }
            ?>
        </div>

    </div>
    <br>
<?php
}

function numToRoman($number)
{
    $map = array('M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1);
    $returnValue = '';
    while ($number > 0) {
        foreach ($map as $roman => $int) {
            if($number >= $int) {
                $number -= $int;
                $returnValue .= $roman;
                break;
            }
        }
    }
    return $returnValue;
}

function numToMonth($number)
{
    \Carbon\Carbon::setLocale('id');
    $date = \Carbon\Carbon::parse('2021-'.$number.'-01');
    $monthName = $date->translatedFormat('F');

    return $monthName;
}

function numToAlpha($number) {
    $numeric = $number % 26;
    $letter = chr(65 + $numeric);
    $num2 = intval($number / 26);
    if ($num2 > 0) {
        return numToAlpha($num2 - 1) . $letter;
    } else {
        return $letter;
    }
}

function getStatusApproval($value)
{
    if($value == 't') {
        $badge = 'success';
        $text = 'Approved';
    } else if($value == 'p') {
        $badge = 'info';
        $text = 'Pending';
    } else if($value == 'f'){
        $badge = 'danger';
        $text = 'Rejected';
    }else{
        $badge = 'secondary';
        $text = 'Need Action';
    }

    return "<div class='badge badge-$badge'>$text</div>";
}

function getIcon($value, $size = 'fa-2x')
{
    $extension = pathinfo(strtolower($value), PATHINFO_EXTENSION);

    $arrImage = array(
        'doc' => 'word',
        'docx' => 'word',
        'pdf' => 'pdf',
        'xls' => 'excel',
        'xlsx' => 'excel',
        'csv' => 'csv',
        'jpg' => 'image',
        'jpeg' => 'image',
        'png' => 'image',
        'jfif' => 'image',
    );

    $image = $arrImage[$extension];

    $icon = "<i class=\"fas $size fa-file-$image\"></i>";

    return $icon;
}

function getInitial($sentences, $length = 2)
{
    $words = explode(' ', $sentences);
    $acronym = '';

    foreach ($words as $w) {
        $acronym .= $w[0];
    }

    return strtoupper(substr($acronym, 0, $length));
}

function getAllBadges()
{
    $arr = array(
        'primary',
        'success',
        'danger',
        'warning',
        'info',
        'dark',
    );

    return $arr;
}

function getParameterMenu(String $pathMenu)
{
    list($modul, $menu) = explode('/', $pathMenu);
    return DB::table('app_menus as t1')->join('app_moduls as t2', 't1.app_modul_id', '=', 't2.id')
        ->where('t2.target', $modul)
        ->where('t1.target', $menu)
        ->first()->parameter;
}

?>
