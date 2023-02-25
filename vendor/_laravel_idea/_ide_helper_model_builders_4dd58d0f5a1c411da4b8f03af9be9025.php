<?php //2658329c06b79404a7a33894e6099f62
/** @noinspection all */

namespace LaravelIdea\Helper\App\Models\Setting {

    use App\Models\Setting\AppInfo;
    use App\Models\Setting\AppMasterCategory;
    use App\Models\Setting\AppMasterData;
    use App\Models\Setting\AppMenu;
    use App\Models\Setting\AppModul;
    use App\Models\Setting\AppParameter;
    use App\Models\Setting\AppSubModul;
    use App\Models\Setting\User;
    use Illuminate\Contracts\Support\Arrayable;
    use Illuminate\Database\Query\Expression;
    use Illuminate\Pagination\LengthAwarePaginator;
    use Illuminate\Pagination\Paginator;
    use Illuminate\Support\Collection;
    use LaravelIdea\Helper\_BaseBuilder;
    use LaravelIdea\Helper\_BaseCollection;
    use Spatie\Permission\Contracts\Permission;
    use Spatie\Permission\Contracts\Role;
    
    /**
     * @method AppInfo|null getOrPut($key, $value)
     * @method AppInfo|$this shift(int $count = 1)
     * @method AppInfo|null firstOrFail(callable|string $key = null, $operator = null, $value = null)
     * @method AppInfo|$this pop(int $count = 1)
     * @method AppInfo|null pull($key, \Closure $default = null)
     * @method AppInfo|null last(callable $callback = null, \Closure $default = null)
     * @method AppInfo|$this random(callable|int|null $number = null)
     * @method AppInfo|null sole(callable|string $key = null, $operator = null, $value = null)
     * @method AppInfo|null get($key, \Closure $default = null)
     * @method AppInfo|null first(callable $callback = null, \Closure $default = null)
     * @method AppInfo|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method AppInfo|null find($key, $default = null)
     * @method AppInfo[] all()
     */
    class _IH_AppInfo_C extends _BaseCollection {
        /**
         * @param int $size
         * @return AppInfo[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }
    
    /**
     * @method _IH_AppInfo_QB whereId($value)
     * @method _IH_AppInfo_QB whereTitle($value)
     * @method _IH_AppInfo_QB wherePrimaryColor($value)
     * @method _IH_AppInfo_QB whereLightPrimaryColor($value)
     * @method _IH_AppInfo_QB whereBackgroundLightPrimaryColor($value)
     * @method _IH_AppInfo_QB whereLoginPageTitle($value)
     * @method _IH_AppInfo_QB whereLoginPageSubtitle($value)
     * @method _IH_AppInfo_QB whereLoginPageDescription($value)
     * @method _IH_AppInfo_QB whereLoginPageLogo($value)
     * @method _IH_AppInfo_QB whereLoginPageBackgroundImage($value)
     * @method _IH_AppInfo_QB whereLoginPageImage($value)
     * @method _IH_AppInfo_QB whereFooterText($value)
     * @method _IH_AppInfo_QB whereAppVersion($value)
     * @method _IH_AppInfo_QB whereAppLogo($value)
     * @method _IH_AppInfo_QB whereAppLogoSmall($value)
     * @method _IH_AppInfo_QB whereAppIcon($value)
     * @method _IH_AppInfo_QB whereCreatedAt($value)
     * @method _IH_AppInfo_QB whereUpdatedAt($value)
     * @method AppInfo baseSole(array|string $columns = ['*'])
     * @method AppInfo create(array $attributes = [])
     * @method _IH_AppInfo_C|AppInfo[] cursor()
     * @method AppInfo|null|_IH_AppInfo_C|AppInfo[] find($id, array|string $columns = ['*'])
     * @method _IH_AppInfo_C|AppInfo[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method AppInfo|_IH_AppInfo_C|AppInfo[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method AppInfo|_IH_AppInfo_C|AppInfo[] findOrFail($id, array|string $columns = ['*'])
     * @method AppInfo|_IH_AppInfo_C|AppInfo[] findOrNew($id, array|string $columns = ['*'])
     * @method AppInfo first(array|string $columns = ['*'])
     * @method AppInfo firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method AppInfo firstOrCreate(array $attributes = [], array $values = [])
     * @method AppInfo firstOrFail(array|string $columns = ['*'])
     * @method AppInfo firstOrNew(array $attributes = [], array $values = [])
     * @method AppInfo firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method AppInfo forceCreate(array $attributes)
     * @method _IH_AppInfo_C|AppInfo[] fromQuery(string $query, array $bindings = [])
     * @method _IH_AppInfo_C|AppInfo[] get(array|string $columns = ['*'])
     * @method AppInfo getModel()
     * @method AppInfo[] getModels(array|string $columns = ['*'])
     * @method _IH_AppInfo_C|AppInfo[] hydrate(array $items)
     * @method AppInfo make(array $attributes = [])
     * @method AppInfo newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|AppInfo[]|_IH_AppInfo_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Paginator|AppInfo[]|_IH_AppInfo_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method AppInfo sole(array|string $columns = ['*'])
     * @method AppInfo updateOrCreate(array $attributes, array $values = [])
     */
    class _IH_AppInfo_QB extends _BaseBuilder {}
    
    /**
     * @method AppMasterCategory|null getOrPut($key, $value)
     * @method AppMasterCategory|$this shift(int $count = 1)
     * @method AppMasterCategory|null firstOrFail(callable|string $key = null, $operator = null, $value = null)
     * @method AppMasterCategory|$this pop(int $count = 1)
     * @method AppMasterCategory|null pull($key, \Closure $default = null)
     * @method AppMasterCategory|null last(callable $callback = null, \Closure $default = null)
     * @method AppMasterCategory|$this random(callable|int|null $number = null)
     * @method AppMasterCategory|null sole(callable|string $key = null, $operator = null, $value = null)
     * @method AppMasterCategory|null get($key, \Closure $default = null)
     * @method AppMasterCategory|null first(callable $callback = null, \Closure $default = null)
     * @method AppMasterCategory|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method AppMasterCategory|null find($key, $default = null)
     * @method AppMasterCategory[] all()
     */
    class _IH_AppMasterCategory_C extends _BaseCollection {
        /**
         * @param int $size
         * @return AppMasterCategory[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }
    
    /**
     * @method _IH_AppMasterCategory_QB whereId($value)
     * @method _IH_AppMasterCategory_QB whereParentId($value)
     * @method _IH_AppMasterCategory_QB whereName($value)
     * @method _IH_AppMasterCategory_QB whereCode($value)
     * @method _IH_AppMasterCategory_QB whereDescription($value)
     * @method _IH_AppMasterCategory_QB whereOrder($value)
     * @method _IH_AppMasterCategory_QB whereCreatedAt($value)
     * @method _IH_AppMasterCategory_QB whereUpdatedAt($value)
     * @method AppMasterCategory baseSole(array|string $columns = ['*'])
     * @method AppMasterCategory create(array $attributes = [])
     * @method _IH_AppMasterCategory_C|AppMasterCategory[] cursor()
     * @method AppMasterCategory|null|_IH_AppMasterCategory_C|AppMasterCategory[] find($id, array|string $columns = ['*'])
     * @method _IH_AppMasterCategory_C|AppMasterCategory[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method AppMasterCategory|_IH_AppMasterCategory_C|AppMasterCategory[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method AppMasterCategory|_IH_AppMasterCategory_C|AppMasterCategory[] findOrFail($id, array|string $columns = ['*'])
     * @method AppMasterCategory|_IH_AppMasterCategory_C|AppMasterCategory[] findOrNew($id, array|string $columns = ['*'])
     * @method AppMasterCategory first(array|string $columns = ['*'])
     * @method AppMasterCategory firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method AppMasterCategory firstOrCreate(array $attributes = [], array $values = [])
     * @method AppMasterCategory firstOrFail(array|string $columns = ['*'])
     * @method AppMasterCategory firstOrNew(array $attributes = [], array $values = [])
     * @method AppMasterCategory firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method AppMasterCategory forceCreate(array $attributes)
     * @method _IH_AppMasterCategory_C|AppMasterCategory[] fromQuery(string $query, array $bindings = [])
     * @method _IH_AppMasterCategory_C|AppMasterCategory[] get(array|string $columns = ['*'])
     * @method AppMasterCategory getModel()
     * @method AppMasterCategory[] getModels(array|string $columns = ['*'])
     * @method _IH_AppMasterCategory_C|AppMasterCategory[] hydrate(array $items)
     * @method AppMasterCategory make(array $attributes = [])
     * @method AppMasterCategory newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|AppMasterCategory[]|_IH_AppMasterCategory_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Paginator|AppMasterCategory[]|_IH_AppMasterCategory_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method AppMasterCategory sole(array|string $columns = ['*'])
     * @method AppMasterCategory updateOrCreate(array $attributes, array $values = [])
     */
    class _IH_AppMasterCategory_QB extends _BaseBuilder {}
    
    /**
     * @method AppMasterData|null getOrPut($key, $value)
     * @method AppMasterData|$this shift(int $count = 1)
     * @method AppMasterData|null firstOrFail(callable|string $key = null, $operator = null, $value = null)
     * @method AppMasterData|$this pop(int $count = 1)
     * @method AppMasterData|null pull($key, \Closure $default = null)
     * @method AppMasterData|null last(callable $callback = null, \Closure $default = null)
     * @method AppMasterData|$this random(callable|int|null $number = null)
     * @method AppMasterData|null sole(callable|string $key = null, $operator = null, $value = null)
     * @method AppMasterData|null get($key, \Closure $default = null)
     * @method AppMasterData|null first(callable $callback = null, \Closure $default = null)
     * @method AppMasterData|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method AppMasterData|null find($key, $default = null)
     * @method AppMasterData[] all()
     */
    class _IH_AppMasterData_C extends _BaseCollection {
        /**
         * @param int $size
         * @return AppMasterData[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }
    
    /**
     * @method _IH_AppMasterData_QB whereId($value)
     * @method _IH_AppMasterData_QB whereParentId($value)
     * @method _IH_AppMasterData_QB whereCode($value)
     * @method _IH_AppMasterData_QB whereName($value)
     * @method _IH_AppMasterData_QB whereDescription($value)
     * @method _IH_AppMasterData_QB whereOrder($value)
     * @method _IH_AppMasterData_QB whereStatus($value)
     * @method _IH_AppMasterData_QB whereCreatedAt($value)
     * @method _IH_AppMasterData_QB whereUpdatedAt($value)
     * @method _IH_AppMasterData_QB whereAppMasterCategoryCode($value)
     * @method AppMasterData baseSole(array|string $columns = ['*'])
     * @method AppMasterData create(array $attributes = [])
     * @method _IH_AppMasterData_C|AppMasterData[] cursor()
     * @method AppMasterData|null|_IH_AppMasterData_C|AppMasterData[] find($id, array|string $columns = ['*'])
     * @method _IH_AppMasterData_C|AppMasterData[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method AppMasterData|_IH_AppMasterData_C|AppMasterData[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method AppMasterData|_IH_AppMasterData_C|AppMasterData[] findOrFail($id, array|string $columns = ['*'])
     * @method AppMasterData|_IH_AppMasterData_C|AppMasterData[] findOrNew($id, array|string $columns = ['*'])
     * @method AppMasterData first(array|string $columns = ['*'])
     * @method AppMasterData firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method AppMasterData firstOrCreate(array $attributes = [], array $values = [])
     * @method AppMasterData firstOrFail(array|string $columns = ['*'])
     * @method AppMasterData firstOrNew(array $attributes = [], array $values = [])
     * @method AppMasterData firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method AppMasterData forceCreate(array $attributes)
     * @method _IH_AppMasterData_C|AppMasterData[] fromQuery(string $query, array $bindings = [])
     * @method _IH_AppMasterData_C|AppMasterData[] get(array|string $columns = ['*'])
     * @method AppMasterData getModel()
     * @method AppMasterData[] getModels(array|string $columns = ['*'])
     * @method _IH_AppMasterData_C|AppMasterData[] hydrate(array $items)
     * @method AppMasterData make(array $attributes = [])
     * @method AppMasterData newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|AppMasterData[]|_IH_AppMasterData_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Paginator|AppMasterData[]|_IH_AppMasterData_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method AppMasterData sole(array|string $columns = ['*'])
     * @method AppMasterData updateOrCreate(array $attributes, array $values = [])
     */
    class _IH_AppMasterData_QB extends _BaseBuilder {}
    
    /**
     * @method AppMenu|null getOrPut($key, $value)
     * @method AppMenu|$this shift(int $count = 1)
     * @method AppMenu|null firstOrFail(callable|string $key = null, $operator = null, $value = null)
     * @method AppMenu|$this pop(int $count = 1)
     * @method AppMenu|null pull($key, \Closure $default = null)
     * @method AppMenu|null last(callable $callback = null, \Closure $default = null)
     * @method AppMenu|$this random(callable|int|null $number = null)
     * @method AppMenu|null sole(callable|string $key = null, $operator = null, $value = null)
     * @method AppMenu|null get($key, \Closure $default = null)
     * @method AppMenu|null first(callable $callback = null, \Closure $default = null)
     * @method AppMenu|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method AppMenu|null find($key, $default = null)
     * @method AppMenu[] all()
     */
    class _IH_AppMenu_C extends _BaseCollection {
        /**
         * @param int $size
         * @return AppMenu[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }
    
    /**
     * @method _IH_AppMenu_QB whereId($value)
     * @method _IH_AppMenu_QB whereParentId($value)
     * @method _IH_AppMenu_QB whereAppModulId($value)
     * @method _IH_AppMenu_QB whereAppSubModulId($value)
     * @method _IH_AppMenu_QB whereName($value)
     * @method _IH_AppMenu_QB whereTarget($value)
     * @method _IH_AppMenu_QB whereDescription($value)
     * @method _IH_AppMenu_QB whereIcon($value)
     * @method _IH_AppMenu_QB whereParameter($value)
     * @method _IH_AppMenu_QB whereFullScreen($value)
     * @method _IH_AppMenu_QB whereStatus($value)
     * @method _IH_AppMenu_QB whereOrder($value)
     * @method _IH_AppMenu_QB whereCreatedAt($value)
     * @method _IH_AppMenu_QB whereUpdatedAt($value)
     * @method AppMenu baseSole(array|string $columns = ['*'])
     * @method AppMenu create(array $attributes = [])
     * @method _IH_AppMenu_C|AppMenu[] cursor()
     * @method AppMenu|null|_IH_AppMenu_C|AppMenu[] find($id, array|string $columns = ['*'])
     * @method _IH_AppMenu_C|AppMenu[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method AppMenu|_IH_AppMenu_C|AppMenu[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method AppMenu|_IH_AppMenu_C|AppMenu[] findOrFail($id, array|string $columns = ['*'])
     * @method AppMenu|_IH_AppMenu_C|AppMenu[] findOrNew($id, array|string $columns = ['*'])
     * @method AppMenu first(array|string $columns = ['*'])
     * @method AppMenu firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method AppMenu firstOrCreate(array $attributes = [], array $values = [])
     * @method AppMenu firstOrFail(array|string $columns = ['*'])
     * @method AppMenu firstOrNew(array $attributes = [], array $values = [])
     * @method AppMenu firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method AppMenu forceCreate(array $attributes)
     * @method _IH_AppMenu_C|AppMenu[] fromQuery(string $query, array $bindings = [])
     * @method _IH_AppMenu_C|AppMenu[] get(array|string $columns = ['*'])
     * @method AppMenu getModel()
     * @method AppMenu[] getModels(array|string $columns = ['*'])
     * @method _IH_AppMenu_C|AppMenu[] hydrate(array $items)
     * @method AppMenu make(array $attributes = [])
     * @method AppMenu newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|AppMenu[]|_IH_AppMenu_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Paginator|AppMenu[]|_IH_AppMenu_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method AppMenu sole(array|string $columns = ['*'])
     * @method AppMenu updateOrCreate(array $attributes, array $values = [])
     * @method _IH_AppMenu_QB active($id = "")
     */
    class _IH_AppMenu_QB extends _BaseBuilder {}
    
    /**
     * @method AppModul|null getOrPut($key, $value)
     * @method AppModul|$this shift(int $count = 1)
     * @method AppModul|null firstOrFail(callable|string $key = null, $operator = null, $value = null)
     * @method AppModul|$this pop(int $count = 1)
     * @method AppModul|null pull($key, \Closure $default = null)
     * @method AppModul|null last(callable $callback = null, \Closure $default = null)
     * @method AppModul|$this random(callable|int|null $number = null)
     * @method AppModul|null sole(callable|string $key = null, $operator = null, $value = null)
     * @method AppModul|null get($key, \Closure $default = null)
     * @method AppModul|null first(callable $callback = null, \Closure $default = null)
     * @method AppModul|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method AppModul|null find($key, $default = null)
     * @method AppModul[] all()
     */
    class _IH_AppModul_C extends _BaseCollection {
        /**
         * @param int $size
         * @return AppModul[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }
    
    /**
     * @method _IH_AppModul_QB whereId($value)
     * @method _IH_AppModul_QB whereName($value)
     * @method _IH_AppModul_QB whereTarget($value)
     * @method _IH_AppModul_QB whereDescription($value)
     * @method _IH_AppModul_QB whereIcon($value)
     * @method _IH_AppModul_QB whereOrder($value)
     * @method _IH_AppModul_QB whereStatus($value)
     * @method _IH_AppModul_QB whereCreatedAt($value)
     * @method _IH_AppModul_QB whereUpdatedAt($value)
     * @method AppModul baseSole(array|string $columns = ['*'])
     * @method AppModul create(array $attributes = [])
     * @method _IH_AppModul_C|AppModul[] cursor()
     * @method AppModul|null|_IH_AppModul_C|AppModul[] find($id, array|string $columns = ['*'])
     * @method _IH_AppModul_C|AppModul[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method AppModul|_IH_AppModul_C|AppModul[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method AppModul|_IH_AppModul_C|AppModul[] findOrFail($id, array|string $columns = ['*'])
     * @method AppModul|_IH_AppModul_C|AppModul[] findOrNew($id, array|string $columns = ['*'])
     * @method AppModul first(array|string $columns = ['*'])
     * @method AppModul firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method AppModul firstOrCreate(array $attributes = [], array $values = [])
     * @method AppModul firstOrFail(array|string $columns = ['*'])
     * @method AppModul firstOrNew(array $attributes = [], array $values = [])
     * @method AppModul firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method AppModul forceCreate(array $attributes)
     * @method _IH_AppModul_C|AppModul[] fromQuery(string $query, array $bindings = [])
     * @method _IH_AppModul_C|AppModul[] get(array|string $columns = ['*'])
     * @method AppModul getModel()
     * @method AppModul[] getModels(array|string $columns = ['*'])
     * @method _IH_AppModul_C|AppModul[] hydrate(array $items)
     * @method AppModul make(array $attributes = [])
     * @method AppModul newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|AppModul[]|_IH_AppModul_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Paginator|AppModul[]|_IH_AppModul_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method AppModul sole(array|string $columns = ['*'])
     * @method AppModul updateOrCreate(array $attributes, array $values = [])
     * @method _IH_AppModul_QB active($id = "")
     */
    class _IH_AppModul_QB extends _BaseBuilder {}
    
    /**
     * @method AppParameter|null getOrPut($key, $value)
     * @method AppParameter|$this shift(int $count = 1)
     * @method AppParameter|null firstOrFail(callable|string $key = null, $operator = null, $value = null)
     * @method AppParameter|$this pop(int $count = 1)
     * @method AppParameter|null pull($key, \Closure $default = null)
     * @method AppParameter|null last(callable $callback = null, \Closure $default = null)
     * @method AppParameter|$this random(callable|int|null $number = null)
     * @method AppParameter|null sole(callable|string $key = null, $operator = null, $value = null)
     * @method AppParameter|null get($key, \Closure $default = null)
     * @method AppParameter|null first(callable $callback = null, \Closure $default = null)
     * @method AppParameter|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method AppParameter|null find($key, $default = null)
     * @method AppParameter[] all()
     */
    class _IH_AppParameter_C extends _BaseCollection {
        /**
         * @param int $size
         * @return AppParameter[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }
    
    /**
     * @method _IH_AppParameter_QB whereId($value)
     * @method _IH_AppParameter_QB whereCode($value)
     * @method _IH_AppParameter_QB whereName($value)
     * @method _IH_AppParameter_QB whereValue($value)
     * @method _IH_AppParameter_QB whereDescription($value)
     * @method _IH_AppParameter_QB whereCreatedAt($value)
     * @method _IH_AppParameter_QB whereUpdatedAt($value)
     * @method AppParameter baseSole(array|string $columns = ['*'])
     * @method AppParameter create(array $attributes = [])
     * @method _IH_AppParameter_C|AppParameter[] cursor()
     * @method AppParameter|null|_IH_AppParameter_C|AppParameter[] find($id, array|string $columns = ['*'])
     * @method _IH_AppParameter_C|AppParameter[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method AppParameter|_IH_AppParameter_C|AppParameter[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method AppParameter|_IH_AppParameter_C|AppParameter[] findOrFail($id, array|string $columns = ['*'])
     * @method AppParameter|_IH_AppParameter_C|AppParameter[] findOrNew($id, array|string $columns = ['*'])
     * @method AppParameter first(array|string $columns = ['*'])
     * @method AppParameter firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method AppParameter firstOrCreate(array $attributes = [], array $values = [])
     * @method AppParameter firstOrFail(array|string $columns = ['*'])
     * @method AppParameter firstOrNew(array $attributes = [], array $values = [])
     * @method AppParameter firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method AppParameter forceCreate(array $attributes)
     * @method _IH_AppParameter_C|AppParameter[] fromQuery(string $query, array $bindings = [])
     * @method _IH_AppParameter_C|AppParameter[] get(array|string $columns = ['*'])
     * @method AppParameter getModel()
     * @method AppParameter[] getModels(array|string $columns = ['*'])
     * @method _IH_AppParameter_C|AppParameter[] hydrate(array $items)
     * @method AppParameter make(array $attributes = [])
     * @method AppParameter newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|AppParameter[]|_IH_AppParameter_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Paginator|AppParameter[]|_IH_AppParameter_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method AppParameter sole(array|string $columns = ['*'])
     * @method AppParameter updateOrCreate(array $attributes, array $values = [])
     */
    class _IH_AppParameter_QB extends _BaseBuilder {}
    
    /**
     * @method AppSubModul|null getOrPut($key, $value)
     * @method AppSubModul|$this shift(int $count = 1)
     * @method AppSubModul|null firstOrFail(callable|string $key = null, $operator = null, $value = null)
     * @method AppSubModul|$this pop(int $count = 1)
     * @method AppSubModul|null pull($key, \Closure $default = null)
     * @method AppSubModul|null last(callable $callback = null, \Closure $default = null)
     * @method AppSubModul|$this random(callable|int|null $number = null)
     * @method AppSubModul|null sole(callable|string $key = null, $operator = null, $value = null)
     * @method AppSubModul|null get($key, \Closure $default = null)
     * @method AppSubModul|null first(callable $callback = null, \Closure $default = null)
     * @method AppSubModul|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method AppSubModul|null find($key, $default = null)
     * @method AppSubModul[] all()
     */
    class _IH_AppSubModul_C extends _BaseCollection {
        /**
         * @param int $size
         * @return AppSubModul[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }
    
    /**
     * @method _IH_AppSubModul_QB whereId($value)
     * @method _IH_AppSubModul_QB whereAppModulId($value)
     * @method _IH_AppSubModul_QB whereName($value)
     * @method _IH_AppSubModul_QB whereDescription($value)
     * @method _IH_AppSubModul_QB whereOrder($value)
     * @method _IH_AppSubModul_QB whereStatus($value)
     * @method _IH_AppSubModul_QB whereCreatedAt($value)
     * @method _IH_AppSubModul_QB whereUpdatedAt($value)
     * @method AppSubModul baseSole(array|string $columns = ['*'])
     * @method AppSubModul create(array $attributes = [])
     * @method _IH_AppSubModul_C|AppSubModul[] cursor()
     * @method AppSubModul|null|_IH_AppSubModul_C|AppSubModul[] find($id, array|string $columns = ['*'])
     * @method _IH_AppSubModul_C|AppSubModul[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method AppSubModul|_IH_AppSubModul_C|AppSubModul[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method AppSubModul|_IH_AppSubModul_C|AppSubModul[] findOrFail($id, array|string $columns = ['*'])
     * @method AppSubModul|_IH_AppSubModul_C|AppSubModul[] findOrNew($id, array|string $columns = ['*'])
     * @method AppSubModul first(array|string $columns = ['*'])
     * @method AppSubModul firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method AppSubModul firstOrCreate(array $attributes = [], array $values = [])
     * @method AppSubModul firstOrFail(array|string $columns = ['*'])
     * @method AppSubModul firstOrNew(array $attributes = [], array $values = [])
     * @method AppSubModul firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method AppSubModul forceCreate(array $attributes)
     * @method _IH_AppSubModul_C|AppSubModul[] fromQuery(string $query, array $bindings = [])
     * @method _IH_AppSubModul_C|AppSubModul[] get(array|string $columns = ['*'])
     * @method AppSubModul getModel()
     * @method AppSubModul[] getModels(array|string $columns = ['*'])
     * @method _IH_AppSubModul_C|AppSubModul[] hydrate(array $items)
     * @method AppSubModul make(array $attributes = [])
     * @method AppSubModul newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|AppSubModul[]|_IH_AppSubModul_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Paginator|AppSubModul[]|_IH_AppSubModul_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method AppSubModul sole(array|string $columns = ['*'])
     * @method AppSubModul updateOrCreate(array $attributes, array $values = [])
     * @method _IH_AppSubModul_QB active()
     */
    class _IH_AppSubModul_QB extends _BaseBuilder {}
    
    /**
     * @method User|null getOrPut($key, $value)
     * @method User|$this shift(int $count = 1)
     * @method User|null firstOrFail(callable|string $key = null, $operator = null, $value = null)
     * @method User|$this pop(int $count = 1)
     * @method User|null pull($key, \Closure $default = null)
     * @method User|null last(callable $callback = null, \Closure $default = null)
     * @method User|$this random(callable|int|null $number = null)
     * @method User|null sole(callable|string $key = null, $operator = null, $value = null)
     * @method User|null get($key, \Closure $default = null)
     * @method User|null first(callable $callback = null, \Closure $default = null)
     * @method User|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method User|null find($key, $default = null)
     * @method User[] all()
     */
    class _IH_User_C extends _BaseCollection {
        /**
         * @param int $size
         * @return User[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }
    
    /**
     * @method _IH_User_QB whereId($value)
     * @method _IH_User_QB whereName($value)
     * @method _IH_User_QB whereEmail($value)
     * @method _IH_User_QB whereEmailVerifiedAt($value)
     * @method _IH_User_QB wherePassword($value)
     * @method _IH_User_QB whereRememberToken($value)
     * @method _IH_User_QB whereCreatedAt($value)
     * @method _IH_User_QB whereUpdatedAt($value)
     * @method _IH_User_QB whereDescription($value)
     * @method _IH_User_QB wherePhoto($value)
     * @method _IH_User_QB whereStatus($value)
     * @method _IH_User_QB whereRoleId($value)
     * @method _IH_User_QB whereLastLogin($value)
     * @method _IH_User_QB whereUsername($value)
     * @method _IH_User_QB whereEmployeeId($value)
     * @method _IH_User_QB whereAccessLocations($value)
     * @method User baseSole(array|string $columns = ['*'])
     * @method User create(array $attributes = [])
     * @method _IH_User_C|User[] cursor()
     * @method User|null|_IH_User_C|User[] find($id, array|string $columns = ['*'])
     * @method _IH_User_C|User[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method User|_IH_User_C|User[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method User|_IH_User_C|User[] findOrFail($id, array|string $columns = ['*'])
     * @method User|_IH_User_C|User[] findOrNew($id, array|string $columns = ['*'])
     * @method User first(array|string $columns = ['*'])
     * @method User firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method User firstOrCreate(array $attributes = [], array $values = [])
     * @method User firstOrFail(array|string $columns = ['*'])
     * @method User firstOrNew(array $attributes = [], array $values = [])
     * @method User firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method User forceCreate(array $attributes)
     * @method _IH_User_C|User[] fromQuery(string $query, array $bindings = [])
     * @method _IH_User_C|User[] get(array|string $columns = ['*'])
     * @method User getModel()
     * @method User[] getModels(array|string $columns = ['*'])
     * @method _IH_User_C|User[] hydrate(array $items)
     * @method User make(array $attributes = [])
     * @method User newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|User[]|_IH_User_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Paginator|User[]|_IH_User_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method User sole(array|string $columns = ['*'])
     * @method User updateOrCreate(array $attributes, array $values = [])
     * @method _IH_User_QB permission(array|Collection|int|Permission|string $permissions)
     * @method _IH_User_QB role(array|Collection|int|Role|string $roles, string $guard = null)
     */
    class _IH_User_QB extends _BaseBuilder {}
}