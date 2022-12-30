<?php

namespace App\Providers;

use App\Models\Setting\AppMenu;
use App\Models\Setting\AppModul;
use App\Models\Setting\AppSubModul;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //GET DATA MODULS
        $data['moduls'] = DB::table('app_moduls')->where('status', 't')->orderBy('order')->get();

        //GET DATA MENUS
        $data['menus'] = DB::table('app_menus')->where('status', 't')->get();

        //GET DATA SUB MODULS
        $subModuls = DB::table('app_sub_moduls')->where('status', 't')->orderBy('order')->get();

        $listFolder = [];
        foreach ($data['moduls'] as $key => $modul) {
            //GET LIST FOLDER FOR VIEW COMPOSER
            $listFolder[] = $modul->target . '.*';
        }

        $listFolder = array_merge($listFolder, ['layouts.app']);

        view()->composer($listFolder, function ($view) use ($data, $subModuls) {
            if(Auth::check()) {
                $arrURL = explode('/', $this->app->request->getRequestUri());//GET URL
                $data['module_path'] = $arrURL[1];
                $data['param'] = !empty($arrURL[2]) ? explode('?', $arrURL[2])[1] ?? '' : ''; //GET LIST PARAM
                $data['menu_path'] = !empty($arrURL[2]) ? explode('?', $arrURL[2])[0] ?? '' : ''; //GET LIST PARAM
                $data['selected_modul'] = $data['moduls']->where('target', $data['module_path'])->first();
                $data['selected_menu'] = $data['menus']->where('target', $data['menu_path'])->where('app_modul_id', $data['selected_modul']->id)->first();
                $data['sub_moduls'] = $subModuls->where('app_modul_id', $data['selected_modul']->id);
                $data['selected_sub_modul'] = $data['sub_moduls']->where('id', $data['selected_menu']->app_sub_modul_id)->first();
                foreach ($data['sub_moduls'] as $key => $sub_modul) {
                    //SET FIRST MENU FROM ALL SUB MODUL FOR SUB MODUL BUTTON IN HEADER
                    $data['sub_moduls'][$key]->menus = $data['menus']->where('app_sub_modul_id', $sub_modul->id);
                }

                $view->with('app_moduls', $data['moduls']);
                $view->with('selected_modul', $data['selected_modul']);
                $view->with('selected_sub_modul', $data['selected_sub_modul']);
                $view->with('selected_menu', $data['selected_menu']);
                $view->with('app_sub_moduls', $data['sub_moduls']);
                $view->with('param', $data['param']);
                $view->with('menu_path', $data['module_path']."/".$data['menu_path']);
                $view->with('menu_target', $data['menu_path']);
            }
        });
    }
}
