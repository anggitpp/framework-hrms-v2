<?php //3f547efab0d785d1e3ef4a4acfe3d20c
/** @noinspection all */

namespace LaravelIdea\Helper\App\Models\Payroll {

    use App\Models\Payroll\PayrollComponent;
    use App\Models\Payroll\PayrollComponentProcess;
    use App\Models\Payroll\PayrollComponentProcessDetail;
    use App\Models\Payroll\PayrollFixed;
    use App\Models\Payroll\PayrollMaster;
    use App\Models\Payroll\PayrollSetting;
    use App\Models\Payroll\PayrollUpload;
    use Illuminate\Contracts\Support\Arrayable;
    use Illuminate\Database\Query\Expression;
    use Illuminate\Pagination\LengthAwarePaginator;
    use Illuminate\Pagination\Paginator;
    use LaravelIdea\Helper\_BaseBuilder;
    use LaravelIdea\Helper\_BaseCollection;
    
    /**
     * @method PayrollComponentProcessDetail|null getOrPut($key, $value)
     * @method PayrollComponentProcessDetail|$this shift(int $count = 1)
     * @method PayrollComponentProcessDetail|null firstOrFail(callable|string $key = null, $operator = null, $value = null)
     * @method PayrollComponentProcessDetail|$this pop(int $count = 1)
     * @method PayrollComponentProcessDetail|null pull($key, \Closure $default = null)
     * @method PayrollComponentProcessDetail|null last(callable $callback = null, \Closure $default = null)
     * @method PayrollComponentProcessDetail|$this random(callable|int|null $number = null)
     * @method PayrollComponentProcessDetail|null sole(callable|string $key = null, $operator = null, $value = null)
     * @method PayrollComponentProcessDetail|null get($key, \Closure $default = null)
     * @method PayrollComponentProcessDetail|null first(callable $callback = null, \Closure $default = null)
     * @method PayrollComponentProcessDetail|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method PayrollComponentProcessDetail|null find($key, $default = null)
     * @method PayrollComponentProcessDetail[] all()
     */
    class _IH_PayrollComponentProcessDetail_C extends _BaseCollection {
        /**
         * @param int $size
         * @return PayrollComponentProcessDetail[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }
    
    /**
     * @method _IH_PayrollComponentProcessDetail_QB whereId($value)
     * @method _IH_PayrollComponentProcessDetail_QB wherePayrollComponentProcessId($value)
     * @method _IH_PayrollComponentProcessDetail_QB whereEmployeeId($value)
     * @method _IH_PayrollComponentProcessDetail_QB whereAmount($value)
     * @method _IH_PayrollComponentProcessDetail_QB whereNote($value)
     * @method _IH_PayrollComponentProcessDetail_QB whereCreatedBy($value)
     * @method _IH_PayrollComponentProcessDetail_QB whereUpdatedBy($value)
     * @method _IH_PayrollComponentProcessDetail_QB whereCreatedAt($value)
     * @method _IH_PayrollComponentProcessDetail_QB whereUpdatedAt($value)
     * @method PayrollComponentProcessDetail baseSole(array|string $columns = ['*'])
     * @method PayrollComponentProcessDetail create(array $attributes = [])
     * @method _IH_PayrollComponentProcessDetail_C|PayrollComponentProcessDetail[] cursor()
     * @method PayrollComponentProcessDetail|null|_IH_PayrollComponentProcessDetail_C|PayrollComponentProcessDetail[] find($id, array|string $columns = ['*'])
     * @method _IH_PayrollComponentProcessDetail_C|PayrollComponentProcessDetail[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method PayrollComponentProcessDetail|_IH_PayrollComponentProcessDetail_C|PayrollComponentProcessDetail[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method PayrollComponentProcessDetail|_IH_PayrollComponentProcessDetail_C|PayrollComponentProcessDetail[] findOrFail($id, array|string $columns = ['*'])
     * @method PayrollComponentProcessDetail|_IH_PayrollComponentProcessDetail_C|PayrollComponentProcessDetail[] findOrNew($id, array|string $columns = ['*'])
     * @method PayrollComponentProcessDetail first(array|string $columns = ['*'])
     * @method PayrollComponentProcessDetail firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method PayrollComponentProcessDetail firstOrCreate(array $attributes = [], array $values = [])
     * @method PayrollComponentProcessDetail firstOrFail(array|string $columns = ['*'])
     * @method PayrollComponentProcessDetail firstOrNew(array $attributes = [], array $values = [])
     * @method PayrollComponentProcessDetail firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method PayrollComponentProcessDetail forceCreate(array $attributes)
     * @method _IH_PayrollComponentProcessDetail_C|PayrollComponentProcessDetail[] fromQuery(string $query, array $bindings = [])
     * @method _IH_PayrollComponentProcessDetail_C|PayrollComponentProcessDetail[] get(array|string $columns = ['*'])
     * @method PayrollComponentProcessDetail getModel()
     * @method PayrollComponentProcessDetail[] getModels(array|string $columns = ['*'])
     * @method _IH_PayrollComponentProcessDetail_C|PayrollComponentProcessDetail[] hydrate(array $items)
     * @method PayrollComponentProcessDetail make(array $attributes = [])
     * @method PayrollComponentProcessDetail newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|PayrollComponentProcessDetail[]|_IH_PayrollComponentProcessDetail_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Paginator|PayrollComponentProcessDetail[]|_IH_PayrollComponentProcessDetail_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method PayrollComponentProcessDetail sole(array|string $columns = ['*'])
     * @method PayrollComponentProcessDetail updateOrCreate(array $attributes, array $values = [])
     */
    class _IH_PayrollComponentProcessDetail_QB extends _BaseBuilder {}
    
    /**
     * @method PayrollComponentProcess|null getOrPut($key, $value)
     * @method PayrollComponentProcess|$this shift(int $count = 1)
     * @method PayrollComponentProcess|null firstOrFail(callable|string $key = null, $operator = null, $value = null)
     * @method PayrollComponentProcess|$this pop(int $count = 1)
     * @method PayrollComponentProcess|null pull($key, \Closure $default = null)
     * @method PayrollComponentProcess|null last(callable $callback = null, \Closure $default = null)
     * @method PayrollComponentProcess|$this random(callable|int|null $number = null)
     * @method PayrollComponentProcess|null sole(callable|string $key = null, $operator = null, $value = null)
     * @method PayrollComponentProcess|null get($key, \Closure $default = null)
     * @method PayrollComponentProcess|null first(callable $callback = null, \Closure $default = null)
     * @method PayrollComponentProcess|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method PayrollComponentProcess|null find($key, $default = null)
     * @method PayrollComponentProcess[] all()
     */
    class _IH_PayrollComponentProcess_C extends _BaseCollection {
        /**
         * @param int $size
         * @return PayrollComponentProcess[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }
    
    /**
     * @method _IH_PayrollComponentProcess_QB whereId($value)
     * @method _IH_PayrollComponentProcess_QB wherePayrollMasterId($value)
     * @method _IH_PayrollComponentProcess_QB whereLocationId($value)
     * @method _IH_PayrollComponentProcess_QB whereCode($value)
     * @method _IH_PayrollComponentProcess_QB whereComponentId($value)
     * @method _IH_PayrollComponentProcess_QB whereTotalAmount($value)
     * @method _IH_PayrollComponentProcess_QB whereTotalEmployee($value)
     * @method _IH_PayrollComponentProcess_QB whereStatus($value)
     * @method _IH_PayrollComponentProcess_QB whereApprovedStatus($value)
     * @method _IH_PayrollComponentProcess_QB whereApprovedBy($value)
     * @method _IH_PayrollComponentProcess_QB whereApprovedAt($value)
     * @method _IH_PayrollComponentProcess_QB whereApprovedNote($value)
     * @method _IH_PayrollComponentProcess_QB whereCreatedBy($value)
     * @method _IH_PayrollComponentProcess_QB whereUpdatedBy($value)
     * @method _IH_PayrollComponentProcess_QB whereCreatedAt($value)
     * @method _IH_PayrollComponentProcess_QB whereUpdatedAt($value)
     * @method PayrollComponentProcess baseSole(array|string $columns = ['*'])
     * @method PayrollComponentProcess create(array $attributes = [])
     * @method _IH_PayrollComponentProcess_C|PayrollComponentProcess[] cursor()
     * @method PayrollComponentProcess|null|_IH_PayrollComponentProcess_C|PayrollComponentProcess[] find($id, array|string $columns = ['*'])
     * @method _IH_PayrollComponentProcess_C|PayrollComponentProcess[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method PayrollComponentProcess|_IH_PayrollComponentProcess_C|PayrollComponentProcess[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method PayrollComponentProcess|_IH_PayrollComponentProcess_C|PayrollComponentProcess[] findOrFail($id, array|string $columns = ['*'])
     * @method PayrollComponentProcess|_IH_PayrollComponentProcess_C|PayrollComponentProcess[] findOrNew($id, array|string $columns = ['*'])
     * @method PayrollComponentProcess first(array|string $columns = ['*'])
     * @method PayrollComponentProcess firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method PayrollComponentProcess firstOrCreate(array $attributes = [], array $values = [])
     * @method PayrollComponentProcess firstOrFail(array|string $columns = ['*'])
     * @method PayrollComponentProcess firstOrNew(array $attributes = [], array $values = [])
     * @method PayrollComponentProcess firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method PayrollComponentProcess forceCreate(array $attributes)
     * @method _IH_PayrollComponentProcess_C|PayrollComponentProcess[] fromQuery(string $query, array $bindings = [])
     * @method _IH_PayrollComponentProcess_C|PayrollComponentProcess[] get(array|string $columns = ['*'])
     * @method PayrollComponentProcess getModel()
     * @method PayrollComponentProcess[] getModels(array|string $columns = ['*'])
     * @method _IH_PayrollComponentProcess_C|PayrollComponentProcess[] hydrate(array $items)
     * @method PayrollComponentProcess make(array $attributes = [])
     * @method PayrollComponentProcess newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|PayrollComponentProcess[]|_IH_PayrollComponentProcess_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Paginator|PayrollComponentProcess[]|_IH_PayrollComponentProcess_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method PayrollComponentProcess sole(array|string $columns = ['*'])
     * @method PayrollComponentProcess updateOrCreate(array $attributes, array $values = [])
     */
    class _IH_PayrollComponentProcess_QB extends _BaseBuilder {}
    
    /**
     * @method PayrollComponent|null getOrPut($key, $value)
     * @method PayrollComponent|$this shift(int $count = 1)
     * @method PayrollComponent|null firstOrFail(callable|string $key = null, $operator = null, $value = null)
     * @method PayrollComponent|$this pop(int $count = 1)
     * @method PayrollComponent|null pull($key, \Closure $default = null)
     * @method PayrollComponent|null last(callable $callback = null, \Closure $default = null)
     * @method PayrollComponent|$this random(callable|int|null $number = null)
     * @method PayrollComponent|null sole(callable|string $key = null, $operator = null, $value = null)
     * @method PayrollComponent|null get($key, \Closure $default = null)
     * @method PayrollComponent|null first(callable $callback = null, \Closure $default = null)
     * @method PayrollComponent|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method PayrollComponent|null find($key, $default = null)
     * @method PayrollComponent[] all()
     */
    class _IH_PayrollComponent_C extends _BaseCollection {
        /**
         * @param int $size
         * @return PayrollComponent[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }
    
    /**
     * @method _IH_PayrollComponent_QB whereId($value)
     * @method _IH_PayrollComponent_QB whereMasterId($value)
     * @method _IH_PayrollComponent_QB whereType($value)
     * @method _IH_PayrollComponent_QB whereCode($value)
     * @method _IH_PayrollComponent_QB whereName($value)
     * @method _IH_PayrollComponent_QB whereDescription($value)
     * @method _IH_PayrollComponent_QB whereStatus($value)
     * @method _IH_PayrollComponent_QB whereCalculationType($value)
     * @method _IH_PayrollComponent_QB whereCalculationCutOff($value)
     * @method _IH_PayrollComponent_QB whereCalculationCutOffDateStart($value)
     * @method _IH_PayrollComponent_QB whereCalculationCutOffDateEnd($value)
     * @method _IH_PayrollComponent_QB whereCalculationDescription($value)
     * @method _IH_PayrollComponent_QB whereCalculationAmount($value)
     * @method _IH_PayrollComponent_QB whereCalculationAmountMin($value)
     * @method _IH_PayrollComponent_QB whereCalculationAmountMax($value)
     * @method _IH_PayrollComponent_QB whereCreatedBy($value)
     * @method _IH_PayrollComponent_QB whereUpdatedBy($value)
     * @method _IH_PayrollComponent_QB whereCreatedAt($value)
     * @method _IH_PayrollComponent_QB whereUpdatedAt($value)
     * @method PayrollComponent baseSole(array|string $columns = ['*'])
     * @method PayrollComponent create(array $attributes = [])
     * @method _IH_PayrollComponent_C|PayrollComponent[] cursor()
     * @method PayrollComponent|null|_IH_PayrollComponent_C|PayrollComponent[] find($id, array|string $columns = ['*'])
     * @method _IH_PayrollComponent_C|PayrollComponent[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method PayrollComponent|_IH_PayrollComponent_C|PayrollComponent[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method PayrollComponent|_IH_PayrollComponent_C|PayrollComponent[] findOrFail($id, array|string $columns = ['*'])
     * @method PayrollComponent|_IH_PayrollComponent_C|PayrollComponent[] findOrNew($id, array|string $columns = ['*'])
     * @method PayrollComponent first(array|string $columns = ['*'])
     * @method PayrollComponent firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method PayrollComponent firstOrCreate(array $attributes = [], array $values = [])
     * @method PayrollComponent firstOrFail(array|string $columns = ['*'])
     * @method PayrollComponent firstOrNew(array $attributes = [], array $values = [])
     * @method PayrollComponent firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method PayrollComponent forceCreate(array $attributes)
     * @method _IH_PayrollComponent_C|PayrollComponent[] fromQuery(string $query, array $bindings = [])
     * @method _IH_PayrollComponent_C|PayrollComponent[] get(array|string $columns = ['*'])
     * @method PayrollComponent getModel()
     * @method PayrollComponent[] getModels(array|string $columns = ['*'])
     * @method _IH_PayrollComponent_C|PayrollComponent[] hydrate(array $items)
     * @method PayrollComponent make(array $attributes = [])
     * @method PayrollComponent newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|PayrollComponent[]|_IH_PayrollComponent_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Paginator|PayrollComponent[]|_IH_PayrollComponent_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method PayrollComponent sole(array|string $columns = ['*'])
     * @method PayrollComponent updateOrCreate(array $attributes, array $values = [])
     */
    class _IH_PayrollComponent_QB extends _BaseBuilder {}
    
    /**
     * @method PayrollFixed|null getOrPut($key, $value)
     * @method PayrollFixed|$this shift(int $count = 1)
     * @method PayrollFixed|null firstOrFail(callable|string $key = null, $operator = null, $value = null)
     * @method PayrollFixed|$this pop(int $count = 1)
     * @method PayrollFixed|null pull($key, \Closure $default = null)
     * @method PayrollFixed|null last(callable $callback = null, \Closure $default = null)
     * @method PayrollFixed|$this random(callable|int|null $number = null)
     * @method PayrollFixed|null sole(callable|string $key = null, $operator = null, $value = null)
     * @method PayrollFixed|null get($key, \Closure $default = null)
     * @method PayrollFixed|null first(callable $callback = null, \Closure $default = null)
     * @method PayrollFixed|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method PayrollFixed|null find($key, $default = null)
     * @method PayrollFixed[] all()
     */
    class _IH_PayrollFixed_C extends _BaseCollection {
        /**
         * @param int $size
         * @return PayrollFixed[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }
    
    /**
     * @method PayrollFixed baseSole(array|string $columns = ['*'])
     * @method PayrollFixed create(array $attributes = [])
     * @method _IH_PayrollFixed_C|PayrollFixed[] cursor()
     * @method PayrollFixed|null|_IH_PayrollFixed_C|PayrollFixed[] find($id, array|string $columns = ['*'])
     * @method _IH_PayrollFixed_C|PayrollFixed[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method PayrollFixed|_IH_PayrollFixed_C|PayrollFixed[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method PayrollFixed|_IH_PayrollFixed_C|PayrollFixed[] findOrFail($id, array|string $columns = ['*'])
     * @method PayrollFixed|_IH_PayrollFixed_C|PayrollFixed[] findOrNew($id, array|string $columns = ['*'])
     * @method PayrollFixed first(array|string $columns = ['*'])
     * @method PayrollFixed firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method PayrollFixed firstOrCreate(array $attributes = [], array $values = [])
     * @method PayrollFixed firstOrFail(array|string $columns = ['*'])
     * @method PayrollFixed firstOrNew(array $attributes = [], array $values = [])
     * @method PayrollFixed firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method PayrollFixed forceCreate(array $attributes)
     * @method _IH_PayrollFixed_C|PayrollFixed[] fromQuery(string $query, array $bindings = [])
     * @method _IH_PayrollFixed_C|PayrollFixed[] get(array|string $columns = ['*'])
     * @method PayrollFixed getModel()
     * @method PayrollFixed[] getModels(array|string $columns = ['*'])
     * @method _IH_PayrollFixed_C|PayrollFixed[] hydrate(array $items)
     * @method PayrollFixed make(array $attributes = [])
     * @method PayrollFixed newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|PayrollFixed[]|_IH_PayrollFixed_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Paginator|PayrollFixed[]|_IH_PayrollFixed_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method PayrollFixed sole(array|string $columns = ['*'])
     * @method PayrollFixed updateOrCreate(array $attributes, array $values = [])
     */
    class _IH_PayrollFixed_QB extends _BaseBuilder {}
    
    /**
     * @method PayrollMaster|null getOrPut($key, $value)
     * @method PayrollMaster|$this shift(int $count = 1)
     * @method PayrollMaster|null firstOrFail(callable|string $key = null, $operator = null, $value = null)
     * @method PayrollMaster|$this pop(int $count = 1)
     * @method PayrollMaster|null pull($key, \Closure $default = null)
     * @method PayrollMaster|null last(callable $callback = null, \Closure $default = null)
     * @method PayrollMaster|$this random(callable|int|null $number = null)
     * @method PayrollMaster|null sole(callable|string $key = null, $operator = null, $value = null)
     * @method PayrollMaster|null get($key, \Closure $default = null)
     * @method PayrollMaster|null first(callable $callback = null, \Closure $default = null)
     * @method PayrollMaster|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method PayrollMaster|null find($key, $default = null)
     * @method PayrollMaster[] all()
     */
    class _IH_PayrollMaster_C extends _BaseCollection {
        /**
         * @param int $size
         * @return PayrollMaster[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }
    
    /**
     * @method _IH_PayrollMaster_QB whereId($value)
     * @method _IH_PayrollMaster_QB whereCode($value)
     * @method _IH_PayrollMaster_QB whereName($value)
     * @method _IH_PayrollMaster_QB whereDescription($value)
     * @method _IH_PayrollMaster_QB whereStatus($value)
     * @method _IH_PayrollMaster_QB whereCreatedBy($value)
     * @method _IH_PayrollMaster_QB whereUpdatedBy($value)
     * @method _IH_PayrollMaster_QB whereCreatedAt($value)
     * @method _IH_PayrollMaster_QB whereUpdatedAt($value)
     * @method PayrollMaster baseSole(array|string $columns = ['*'])
     * @method PayrollMaster create(array $attributes = [])
     * @method _IH_PayrollMaster_C|PayrollMaster[] cursor()
     * @method PayrollMaster|null|_IH_PayrollMaster_C|PayrollMaster[] find($id, array|string $columns = ['*'])
     * @method _IH_PayrollMaster_C|PayrollMaster[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method PayrollMaster|_IH_PayrollMaster_C|PayrollMaster[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method PayrollMaster|_IH_PayrollMaster_C|PayrollMaster[] findOrFail($id, array|string $columns = ['*'])
     * @method PayrollMaster|_IH_PayrollMaster_C|PayrollMaster[] findOrNew($id, array|string $columns = ['*'])
     * @method PayrollMaster first(array|string $columns = ['*'])
     * @method PayrollMaster firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method PayrollMaster firstOrCreate(array $attributes = [], array $values = [])
     * @method PayrollMaster firstOrFail(array|string $columns = ['*'])
     * @method PayrollMaster firstOrNew(array $attributes = [], array $values = [])
     * @method PayrollMaster firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method PayrollMaster forceCreate(array $attributes)
     * @method _IH_PayrollMaster_C|PayrollMaster[] fromQuery(string $query, array $bindings = [])
     * @method _IH_PayrollMaster_C|PayrollMaster[] get(array|string $columns = ['*'])
     * @method PayrollMaster getModel()
     * @method PayrollMaster[] getModels(array|string $columns = ['*'])
     * @method _IH_PayrollMaster_C|PayrollMaster[] hydrate(array $items)
     * @method PayrollMaster make(array $attributes = [])
     * @method PayrollMaster newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|PayrollMaster[]|_IH_PayrollMaster_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Paginator|PayrollMaster[]|_IH_PayrollMaster_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method PayrollMaster sole(array|string $columns = ['*'])
     * @method PayrollMaster updateOrCreate(array $attributes, array $values = [])
     */
    class _IH_PayrollMaster_QB extends _BaseBuilder {}
    
    /**
     * @method PayrollSetting|null getOrPut($key, $value)
     * @method PayrollSetting|$this shift(int $count = 1)
     * @method PayrollSetting|null firstOrFail(callable|string $key = null, $operator = null, $value = null)
     * @method PayrollSetting|$this pop(int $count = 1)
     * @method PayrollSetting|null pull($key, \Closure $default = null)
     * @method PayrollSetting|null last(callable $callback = null, \Closure $default = null)
     * @method PayrollSetting|$this random(callable|int|null $number = null)
     * @method PayrollSetting|null sole(callable|string $key = null, $operator = null, $value = null)
     * @method PayrollSetting|null get($key, \Closure $default = null)
     * @method PayrollSetting|null first(callable $callback = null, \Closure $default = null)
     * @method PayrollSetting|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method PayrollSetting|null find($key, $default = null)
     * @method PayrollSetting[] all()
     */
    class _IH_PayrollSetting_C extends _BaseCollection {
        /**
         * @param int $size
         * @return PayrollSetting[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }
    
    /**
     * @method _IH_PayrollSetting_QB whereId($value)
     * @method _IH_PayrollSetting_QB whereCode($value)
     * @method _IH_PayrollSetting_QB whereReferenceField($value)
     * @method _IH_PayrollSetting_QB whereReferenceId($value)
     * @method _IH_PayrollSetting_QB whereAmount($value)
     * @method _IH_PayrollSetting_QB whereCreatedBy($value)
     * @method _IH_PayrollSetting_QB whereUpdatedBy($value)
     * @method _IH_PayrollSetting_QB whereCreatedAt($value)
     * @method _IH_PayrollSetting_QB whereUpdatedAt($value)
     * @method PayrollSetting baseSole(array|string $columns = ['*'])
     * @method PayrollSetting create(array $attributes = [])
     * @method _IH_PayrollSetting_C|PayrollSetting[] cursor()
     * @method PayrollSetting|null|_IH_PayrollSetting_C|PayrollSetting[] find($id, array|string $columns = ['*'])
     * @method _IH_PayrollSetting_C|PayrollSetting[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method PayrollSetting|_IH_PayrollSetting_C|PayrollSetting[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method PayrollSetting|_IH_PayrollSetting_C|PayrollSetting[] findOrFail($id, array|string $columns = ['*'])
     * @method PayrollSetting|_IH_PayrollSetting_C|PayrollSetting[] findOrNew($id, array|string $columns = ['*'])
     * @method PayrollSetting first(array|string $columns = ['*'])
     * @method PayrollSetting firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method PayrollSetting firstOrCreate(array $attributes = [], array $values = [])
     * @method PayrollSetting firstOrFail(array|string $columns = ['*'])
     * @method PayrollSetting firstOrNew(array $attributes = [], array $values = [])
     * @method PayrollSetting firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method PayrollSetting forceCreate(array $attributes)
     * @method _IH_PayrollSetting_C|PayrollSetting[] fromQuery(string $query, array $bindings = [])
     * @method _IH_PayrollSetting_C|PayrollSetting[] get(array|string $columns = ['*'])
     * @method PayrollSetting getModel()
     * @method PayrollSetting[] getModels(array|string $columns = ['*'])
     * @method _IH_PayrollSetting_C|PayrollSetting[] hydrate(array $items)
     * @method PayrollSetting make(array $attributes = [])
     * @method PayrollSetting newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|PayrollSetting[]|_IH_PayrollSetting_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Paginator|PayrollSetting[]|_IH_PayrollSetting_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method PayrollSetting sole(array|string $columns = ['*'])
     * @method PayrollSetting updateOrCreate(array $attributes, array $values = [])
     */
    class _IH_PayrollSetting_QB extends _BaseBuilder {}
    
    /**
     * @method PayrollUpload|null getOrPut($key, $value)
     * @method PayrollUpload|$this shift(int $count = 1)
     * @method PayrollUpload|null firstOrFail(callable|string $key = null, $operator = null, $value = null)
     * @method PayrollUpload|$this pop(int $count = 1)
     * @method PayrollUpload|null pull($key, \Closure $default = null)
     * @method PayrollUpload|null last(callable $callback = null, \Closure $default = null)
     * @method PayrollUpload|$this random(callable|int|null $number = null)
     * @method PayrollUpload|null sole(callable|string $key = null, $operator = null, $value = null)
     * @method PayrollUpload|null get($key, \Closure $default = null)
     * @method PayrollUpload|null first(callable $callback = null, \Closure $default = null)
     * @method PayrollUpload|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method PayrollUpload|null find($key, $default = null)
     * @method PayrollUpload[] all()
     */
    class _IH_PayrollUpload_C extends _BaseCollection {
        /**
         * @param int $size
         * @return PayrollUpload[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }
    
    /**
     * @method _IH_PayrollUpload_QB whereId($value)
     * @method _IH_PayrollUpload_QB whereCode($value)
     * @method _IH_PayrollUpload_QB whereComponentId($value)
     * @method _IH_PayrollUpload_QB whereEmployeeId($value)
     * @method _IH_PayrollUpload_QB whereAmount($value)
     * @method _IH_PayrollUpload_QB whereDescription($value)
     * @method _IH_PayrollUpload_QB whereCreatedBy($value)
     * @method _IH_PayrollUpload_QB whereUpdatedBy($value)
     * @method _IH_PayrollUpload_QB whereCreatedAt($value)
     * @method _IH_PayrollUpload_QB whereUpdatedAt($value)
     * @method PayrollUpload baseSole(array|string $columns = ['*'])
     * @method PayrollUpload create(array $attributes = [])
     * @method _IH_PayrollUpload_C|PayrollUpload[] cursor()
     * @method PayrollUpload|null|_IH_PayrollUpload_C|PayrollUpload[] find($id, array|string $columns = ['*'])
     * @method _IH_PayrollUpload_C|PayrollUpload[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method PayrollUpload|_IH_PayrollUpload_C|PayrollUpload[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method PayrollUpload|_IH_PayrollUpload_C|PayrollUpload[] findOrFail($id, array|string $columns = ['*'])
     * @method PayrollUpload|_IH_PayrollUpload_C|PayrollUpload[] findOrNew($id, array|string $columns = ['*'])
     * @method PayrollUpload first(array|string $columns = ['*'])
     * @method PayrollUpload firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method PayrollUpload firstOrCreate(array $attributes = [], array $values = [])
     * @method PayrollUpload firstOrFail(array|string $columns = ['*'])
     * @method PayrollUpload firstOrNew(array $attributes = [], array $values = [])
     * @method PayrollUpload firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method PayrollUpload forceCreate(array $attributes)
     * @method _IH_PayrollUpload_C|PayrollUpload[] fromQuery(string $query, array $bindings = [])
     * @method _IH_PayrollUpload_C|PayrollUpload[] get(array|string $columns = ['*'])
     * @method PayrollUpload getModel()
     * @method PayrollUpload[] getModels(array|string $columns = ['*'])
     * @method _IH_PayrollUpload_C|PayrollUpload[] hydrate(array $items)
     * @method PayrollUpload make(array $attributes = [])
     * @method PayrollUpload newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|PayrollUpload[]|_IH_PayrollUpload_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Paginator|PayrollUpload[]|_IH_PayrollUpload_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method PayrollUpload sole(array|string $columns = ['*'])
     * @method PayrollUpload updateOrCreate(array $attributes, array $values = [])
     */
    class _IH_PayrollUpload_QB extends _BaseBuilder {}
}