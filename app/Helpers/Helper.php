<?php

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Intervention\Image\Facades\Image;
use Maatwebsite\Excel\Facades\Excel;
use RealRashid\SweetAlert\Facades\Alert;
use Yajra\DataTables\DataTableAbstract;
use Yajra\DataTables\DataTables;

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

function deleteFile($path): bool
{
    if(Storage::exists($path)) Storage::delete($path);

    return true;
}

function defaultUploadFile($model, $request, $path, $filename,  $fieldName = 'filename', $resize = ''): void
{
    if($request->get('isDelete') == 't'){
        deleteFile($path.$model->$fieldName);
        $model->update([
            $fieldName => null,
        ]);
    }
    if ($request->hasFile($fieldName)) {
        $resize = $resize ?? false;
        $extension = $request->file($fieldName)->extension();
        if ($extension == 'png' || $extension == 'jpg' || $extension == 'jpeg') $resize = $resize ?? true;

        $resUpload = uploadFile(
            $request->file($fieldName),
            $filename,
            $path, $resize);

        $model->update([
            $fieldName => $resUpload,
        ]);
    }
}


function generatePagination($items, $form = 'form-filter'): void
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
        $acronym .= $w[0] ?? '';
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

function menu_path()
{
    $arrURL = explode('/', \Request::url());//GET URL
    $modul = !empty($arrURL[3]) ? $arrURL[3] : ''; //GET MODUL
    $menu = !empty($arrURL[4]) ? explode('?', $arrURL[4])[0] ?? '' : ''; //GET MENU WITHOUT PARAM

    return $modul.'/'.$menu;
}

/**
 * @throws Exception
 */
function generateDatatable($query, $filter , $customFields = [], $isModal = false, $isShowRoute = false, $customPrefix = null, $arrCustomAction = [], $iconShow = '', $editable = true, $deleteable = true)
{
    //generate datatables with filter
    $dataTables = DataTables::of($query);
    if($filter) $dataTables->filter($filter);

    //generate custom fields
    foreach ($customFields as $field) {
        if ($field['type'] == 'date') {
            $data = function ($model) use ($field) {
                return $model->{$field['name']} != '0000-00-00' && $model->{$field['name']} != null ? setDate($model->{$field['name']}) : '';
            };
        } else if ($field['type'] == 'time') {
            $data = function ($model) use ($field) {
                return substr($model->{$field['name']} ?? '', 0, 5);
            };
        } else if ($field['type'] == 'masters') {
            $data = function ($model) use ($field) {
                return $field['masters'][$model->{$field['name']}] ?? '';
            };
        } else if ($field['type'] == 'master_relationship') {
            $field['master_field'] = $field['master_field'] ?? 'name';
            $data = function ($model) use ($field) {
                return $model->{$field['masters']}->{$field['master_field']} ?? $field['null_value'] ?? '';
            };
        } else if ($field['type'] == 'multiple_relationship') {
            $field['master_field'] = $field['master_field'] ?? 'name';
            $data = function ($model) use ($field) {
                return $model->{$field['masters']}->{$field['second_masters']}->{$field['master_field']} ?? '';
            };
        } else if ($field['type'] == 'filename') {
            $data = function ($model) use ($field) {
                return $model->{$field['name']} ? view('components.datatables.download', [
                    'url' => $model->{$field['name']}
                ]) : '';
            };
        } else if ($field['type'] == 'status') {
            $data = function ($model) use ($field) {
                return view('components.views.status', [
                    'status' => $model->{$field['name']},
                ]);
            };
        } else if ($field['type'] == 'yesorno') {
            $data = function ($model) use ($field) {
                return view('components.views.yes-or-no', [
                    'value' => $model->{$field['name']},
                ]);
            };
        } else if ($field['type'] == 'photo') {
            $data = function ($model) use ($field) {
                return view('components.views.photo', [
                    'photo' => $model->{$field['name']},
                ]);
            };
        } else if ($field['type'] == 'gender') {
            $data = function ($model) use ($field) {
                return $model->{$field['name']} == 'M' ? 'Laki-laki' : 'Perempuan';
            };
        } else if ($field['type'] == 'currency'){
            $data = function ($model) use ($field) {
                return setCurrency($model->{$field['name']}) ?? '';
            };
        } else {
            $data = $field['value'];
        }

        if(!empty($field['isAdd'])){
            $dataTables->addColumn($field['name'], $data);
        }else{
            $dataTables->editColumn($field['name'], $data);
        }
    }

    //generate action
    $dataTables->addColumn('action', function ($model) use ($isModal, $isShowRoute, $customPrefix, $iconShow, $arrCustomAction, $deleteable, $editable) {
        $editPrefix = $customPrefix ? 'edit-'.$customPrefix : 'edit';
        $destroyPrefix = $customPrefix ? 'destroy-'.$customPrefix : 'destroy';
        $showPrefix = $customPrefix ? 'show-'.$customPrefix : 'show';
        $arrAction = [
            'menu_path' => menu_path(),
            'isModal' => $isModal,
        ];

        if($editable){
            $arrAction = array_merge($arrAction, [
                'url_edit' => route(Str::replace('/', '.', menu_path()) . '.'.$editPrefix, $model->id),
            ]);
        }

        if($deleteable){
            $arrAction = array_merge($arrAction, [
                'url_destroy' => route(Str::replace('/', '.', menu_path()) . '.'.$destroyPrefix, $model->id),
            ]);
        }

        if(!empty($arrCustomAction)){
            $actions = [];
            foreach ($arrCustomAction as $action){
                $ids = [];
                foreach ($action['ids'] as $id) {
                    $ids[] = $model->{$id};
                }

                $actions[] = [
                    'url' => route(Str::replace('/', '.', menu_path()) . '.'.$action['route'], $ids),
                    'icon' => $action['icon'],
                    'class-icon' => $action['class-icon'] ?? 'info',
                    'isModal' => $action['isModal'] ?? '',
                    'title' => $action['title'] ?? '',
                ];
            }

            $arrAction = array_merge($arrAction, ['customAction' => $actions]);
        }

        if(!empty($isShowRoute)){
            $arrAction = array_merge($arrAction, [
                'url_show' => route(Str::replace('/', '.', menu_path()) . '.'.$showPrefix, $model->id),
            ]);
            if(!empty($iconShow)){
                $arrAction = array_merge($arrAction, [
                    'icon_show' => $iconShow,
                ]);
            }
        }

        return view('components.views.action', $arrAction);
    });

    return $dataTables->addIndexColumn()->make();
}

function submitDataHelper($function, bool $isModal = false, string $route = 'index', array $param = []): JsonResponse|RedirectResponse
{
    try {
        DB::transaction(function () use ($function) {
            $function();
        });
        $status = 'success';
        $message = 'Data berhasil disimpan';
    } catch (Exception $e) {
        $status = 'error';
        $message = 'Error: ' . $e->getMessage();
    }

    if($isModal){
        return response()->json([
            'success' => $message,
            'url' => route(Str::replace('/', '.', menu_path()) . '.'.$route, $param)
        ]);
    }else{
        if($status == 'success'){
            Alert::success('Success', $message);
        }else{
            Alert::error('Error', $message);
        }

        return redirect()->route(Str::replace('/', '.', menu_path()) . '.'.$route, $param);
    }
}

function deleteDataHelper($function): RedirectResponse
{
    try {
        DB::transaction(function () use ($function) {
            $function();
        });

        $status = 'success';
        $message = 'Data berhasil dihapus';
    } catch (Exception $e) {

        $status = 'error';
        $message = 'Error: ' . $e->getMessage();
    }

    if($status == 'success'){
        Alert::success('Success', $message);
    }else{
        Alert::error('Error', $message);
    }

    return redirect()->back();
}

function importHelper($classImport, $request, $route = 'index')
{
    try {
        if ($request->hasFile('filename')) {
            Excel::import($classImport, $request->file('filename'));

            return response()->json([
                'success' => 'Data berhasil diimport',
                'url' => route(Str::replace('/', '.', menu_path()) . '.'.$route),
            ]);
        }
    } catch (Exception $e) {
        return response()->json([
            'success' => 'Gagal ' . $e->getMessage(),
            'url' => route(Str::replace('/', '.', menu_path()) . '.'.$route),
        ]);
    }
}

?>
