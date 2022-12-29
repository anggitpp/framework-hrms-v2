<?php //efc39e0d068cccc1454d768c2182371a
/** @noinspection all */

namespace LaravelIdea\Helper\App\Models\Employee {

    use App\Models\Employee\Employee;
    use App\Models\Employee\EmployeeContact;
    use App\Models\Employee\EmployeeEducation;
    use App\Models\Employee\EmployeeFamily;
    use App\Models\Employee\EmployeePosition;
    use App\Models\Employee\EmployeeSignatureSetting;
    use App\Models\Employee\EmployeeTraining;
    use App\Models\Employee\EmployeeUnitStructure;
    use App\Models\Employee\EmployeeWork;
    use Illuminate\Contracts\Support\Arrayable;
    use Illuminate\Database\Query\Expression;
    use Illuminate\Pagination\LengthAwarePaginator;
    use Illuminate\Pagination\Paginator;
    use LaravelIdea\Helper\_BaseBuilder;
    use LaravelIdea\Helper\_BaseCollection;
    
    /**
     * @method EmployeeContact|null getOrPut($key, $value)
     * @method EmployeeContact|$this shift(int $count = 1)
     * @method EmployeeContact|null firstOrFail(callable|string $key = null, $operator = null, $value = null)
     * @method EmployeeContact|$this pop(int $count = 1)
     * @method EmployeeContact|null pull($key, \Closure $default = null)
     * @method EmployeeContact|null last(callable $callback = null, \Closure $default = null)
     * @method EmployeeContact|$this random(callable|int|null $number = null)
     * @method EmployeeContact|null sole(callable|string $key = null, $operator = null, $value = null)
     * @method EmployeeContact|null get($key, \Closure $default = null)
     * @method EmployeeContact|null first(callable $callback = null, \Closure $default = null)
     * @method EmployeeContact|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method EmployeeContact|null find($key, $default = null)
     * @method EmployeeContact[] all()
     */
    class _IH_EmployeeContact_C extends _BaseCollection {
        /**
         * @param int $size
         * @return EmployeeContact[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }
    
    /**
     * @method _IH_EmployeeContact_QB whereId($value)
     * @method _IH_EmployeeContact_QB whereEmployeeId($value)
     * @method _IH_EmployeeContact_QB whereRelationshipId($value)
     * @method _IH_EmployeeContact_QB whereName($value)
     * @method _IH_EmployeeContact_QB wherePhoneNumber($value)
     * @method _IH_EmployeeContact_QB whereDescription($value)
     * @method _IH_EmployeeContact_QB whereCreatedBy($value)
     * @method _IH_EmployeeContact_QB whereUpdatedBy($value)
     * @method _IH_EmployeeContact_QB whereCreatedAt($value)
     * @method _IH_EmployeeContact_QB whereUpdatedAt($value)
     * @method EmployeeContact baseSole(array|string $columns = ['*'])
     * @method EmployeeContact create(array $attributes = [])
     * @method _IH_EmployeeContact_C|EmployeeContact[] cursor()
     * @method EmployeeContact|null|_IH_EmployeeContact_C|EmployeeContact[] find($id, array|string $columns = ['*'])
     * @method _IH_EmployeeContact_C|EmployeeContact[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method EmployeeContact|_IH_EmployeeContact_C|EmployeeContact[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method EmployeeContact|_IH_EmployeeContact_C|EmployeeContact[] findOrFail($id, array|string $columns = ['*'])
     * @method EmployeeContact|_IH_EmployeeContact_C|EmployeeContact[] findOrNew($id, array|string $columns = ['*'])
     * @method EmployeeContact first(array|string $columns = ['*'])
     * @method EmployeeContact firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method EmployeeContact firstOrCreate(array $attributes = [], array $values = [])
     * @method EmployeeContact firstOrFail(array|string $columns = ['*'])
     * @method EmployeeContact firstOrNew(array $attributes = [], array $values = [])
     * @method EmployeeContact firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method EmployeeContact forceCreate(array $attributes)
     * @method _IH_EmployeeContact_C|EmployeeContact[] fromQuery(string $query, array $bindings = [])
     * @method _IH_EmployeeContact_C|EmployeeContact[] get(array|string $columns = ['*'])
     * @method EmployeeContact getModel()
     * @method EmployeeContact[] getModels(array|string $columns = ['*'])
     * @method _IH_EmployeeContact_C|EmployeeContact[] hydrate(array $items)
     * @method EmployeeContact make(array $attributes = [])
     * @method EmployeeContact newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|EmployeeContact[]|_IH_EmployeeContact_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Paginator|EmployeeContact[]|_IH_EmployeeContact_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method EmployeeContact sole(array|string $columns = ['*'])
     * @method EmployeeContact updateOrCreate(array $attributes, array $values = [])
     */
    class _IH_EmployeeContact_QB extends _BaseBuilder {}
    
    /**
     * @method EmployeeEducation|null getOrPut($key, $value)
     * @method EmployeeEducation|$this shift(int $count = 1)
     * @method EmployeeEducation|null firstOrFail(callable|string $key = null, $operator = null, $value = null)
     * @method EmployeeEducation|$this pop(int $count = 1)
     * @method EmployeeEducation|null pull($key, \Closure $default = null)
     * @method EmployeeEducation|null last(callable $callback = null, \Closure $default = null)
     * @method EmployeeEducation|$this random(callable|int|null $number = null)
     * @method EmployeeEducation|null sole(callable|string $key = null, $operator = null, $value = null)
     * @method EmployeeEducation|null get($key, \Closure $default = null)
     * @method EmployeeEducation|null first(callable $callback = null, \Closure $default = null)
     * @method EmployeeEducation|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method EmployeeEducation|null find($key, $default = null)
     * @method EmployeeEducation[] all()
     */
    class _IH_EmployeeEducation_C extends _BaseCollection {
        /**
         * @param int $size
         * @return EmployeeEducation[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }
    
    /**
     * @method _IH_EmployeeEducation_QB whereId($value)
     * @method _IH_EmployeeEducation_QB whereEmployeeId($value)
     * @method _IH_EmployeeEducation_QB whereLevelId($value)
     * @method _IH_EmployeeEducation_QB whereName($value)
     * @method _IH_EmployeeEducation_QB whereMajor($value)
     * @method _IH_EmployeeEducation_QB whereEssay($value)
     * @method _IH_EmployeeEducation_QB whereCity($value)
     * @method _IH_EmployeeEducation_QB whereScore($value)
     * @method _IH_EmployeeEducation_QB whereStartYear($value)
     * @method _IH_EmployeeEducation_QB whereEndYear($value)
     * @method _IH_EmployeeEducation_QB whereDescription($value)
     * @method _IH_EmployeeEducation_QB whereFilename($value)
     * @method _IH_EmployeeEducation_QB whereCreatedBy($value)
     * @method _IH_EmployeeEducation_QB whereUpdatedBy($value)
     * @method _IH_EmployeeEducation_QB whereCreatedAt($value)
     * @method _IH_EmployeeEducation_QB whereUpdatedAt($value)
     * @method EmployeeEducation baseSole(array|string $columns = ['*'])
     * @method EmployeeEducation create(array $attributes = [])
     * @method _IH_EmployeeEducation_C|EmployeeEducation[] cursor()
     * @method EmployeeEducation|null|_IH_EmployeeEducation_C|EmployeeEducation[] find($id, array|string $columns = ['*'])
     * @method _IH_EmployeeEducation_C|EmployeeEducation[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method EmployeeEducation|_IH_EmployeeEducation_C|EmployeeEducation[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method EmployeeEducation|_IH_EmployeeEducation_C|EmployeeEducation[] findOrFail($id, array|string $columns = ['*'])
     * @method EmployeeEducation|_IH_EmployeeEducation_C|EmployeeEducation[] findOrNew($id, array|string $columns = ['*'])
     * @method EmployeeEducation first(array|string $columns = ['*'])
     * @method EmployeeEducation firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method EmployeeEducation firstOrCreate(array $attributes = [], array $values = [])
     * @method EmployeeEducation firstOrFail(array|string $columns = ['*'])
     * @method EmployeeEducation firstOrNew(array $attributes = [], array $values = [])
     * @method EmployeeEducation firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method EmployeeEducation forceCreate(array $attributes)
     * @method _IH_EmployeeEducation_C|EmployeeEducation[] fromQuery(string $query, array $bindings = [])
     * @method _IH_EmployeeEducation_C|EmployeeEducation[] get(array|string $columns = ['*'])
     * @method EmployeeEducation getModel()
     * @method EmployeeEducation[] getModels(array|string $columns = ['*'])
     * @method _IH_EmployeeEducation_C|EmployeeEducation[] hydrate(array $items)
     * @method EmployeeEducation make(array $attributes = [])
     * @method EmployeeEducation newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|EmployeeEducation[]|_IH_EmployeeEducation_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Paginator|EmployeeEducation[]|_IH_EmployeeEducation_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method EmployeeEducation sole(array|string $columns = ['*'])
     * @method EmployeeEducation updateOrCreate(array $attributes, array $values = [])
     */
    class _IH_EmployeeEducation_QB extends _BaseBuilder {}
    
    /**
     * @method EmployeeFamily|null getOrPut($key, $value)
     * @method EmployeeFamily|$this shift(int $count = 1)
     * @method EmployeeFamily|null firstOrFail(callable|string $key = null, $operator = null, $value = null)
     * @method EmployeeFamily|$this pop(int $count = 1)
     * @method EmployeeFamily|null pull($key, \Closure $default = null)
     * @method EmployeeFamily|null last(callable $callback = null, \Closure $default = null)
     * @method EmployeeFamily|$this random(callable|int|null $number = null)
     * @method EmployeeFamily|null sole(callable|string $key = null, $operator = null, $value = null)
     * @method EmployeeFamily|null get($key, \Closure $default = null)
     * @method EmployeeFamily|null first(callable $callback = null, \Closure $default = null)
     * @method EmployeeFamily|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method EmployeeFamily|null find($key, $default = null)
     * @method EmployeeFamily[] all()
     */
    class _IH_EmployeeFamily_C extends _BaseCollection {
        /**
         * @param int $size
         * @return EmployeeFamily[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }
    
    /**
     * @method _IH_EmployeeFamily_QB whereId($value)
     * @method _IH_EmployeeFamily_QB whereEmployeeId($value)
     * @method _IH_EmployeeFamily_QB whereRelationshipId($value)
     * @method _IH_EmployeeFamily_QB whereName($value)
     * @method _IH_EmployeeFamily_QB whereIdentityNumber($value)
     * @method _IH_EmployeeFamily_QB whereGender($value)
     * @method _IH_EmployeeFamily_QB whereBirthDate($value)
     * @method _IH_EmployeeFamily_QB whereBirthPlace($value)
     * @method _IH_EmployeeFamily_QB whereFilename($value)
     * @method _IH_EmployeeFamily_QB whereDescription($value)
     * @method _IH_EmployeeFamily_QB whereCreatedBy($value)
     * @method _IH_EmployeeFamily_QB whereUpdatedBy($value)
     * @method _IH_EmployeeFamily_QB whereCreatedAt($value)
     * @method _IH_EmployeeFamily_QB whereUpdatedAt($value)
     * @method EmployeeFamily baseSole(array|string $columns = ['*'])
     * @method EmployeeFamily create(array $attributes = [])
     * @method _IH_EmployeeFamily_C|EmployeeFamily[] cursor()
     * @method EmployeeFamily|null|_IH_EmployeeFamily_C|EmployeeFamily[] find($id, array|string $columns = ['*'])
     * @method _IH_EmployeeFamily_C|EmployeeFamily[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method EmployeeFamily|_IH_EmployeeFamily_C|EmployeeFamily[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method EmployeeFamily|_IH_EmployeeFamily_C|EmployeeFamily[] findOrFail($id, array|string $columns = ['*'])
     * @method EmployeeFamily|_IH_EmployeeFamily_C|EmployeeFamily[] findOrNew($id, array|string $columns = ['*'])
     * @method EmployeeFamily first(array|string $columns = ['*'])
     * @method EmployeeFamily firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method EmployeeFamily firstOrCreate(array $attributes = [], array $values = [])
     * @method EmployeeFamily firstOrFail(array|string $columns = ['*'])
     * @method EmployeeFamily firstOrNew(array $attributes = [], array $values = [])
     * @method EmployeeFamily firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method EmployeeFamily forceCreate(array $attributes)
     * @method _IH_EmployeeFamily_C|EmployeeFamily[] fromQuery(string $query, array $bindings = [])
     * @method _IH_EmployeeFamily_C|EmployeeFamily[] get(array|string $columns = ['*'])
     * @method EmployeeFamily getModel()
     * @method EmployeeFamily[] getModels(array|string $columns = ['*'])
     * @method _IH_EmployeeFamily_C|EmployeeFamily[] hydrate(array $items)
     * @method EmployeeFamily make(array $attributes = [])
     * @method EmployeeFamily newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|EmployeeFamily[]|_IH_EmployeeFamily_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Paginator|EmployeeFamily[]|_IH_EmployeeFamily_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method EmployeeFamily sole(array|string $columns = ['*'])
     * @method EmployeeFamily updateOrCreate(array $attributes, array $values = [])
     */
    class _IH_EmployeeFamily_QB extends _BaseBuilder {}
    
    /**
     * @method EmployeePosition|null getOrPut($key, $value)
     * @method EmployeePosition|$this shift(int $count = 1)
     * @method EmployeePosition|null firstOrFail(callable|string $key = null, $operator = null, $value = null)
     * @method EmployeePosition|$this pop(int $count = 1)
     * @method EmployeePosition|null pull($key, \Closure $default = null)
     * @method EmployeePosition|null last(callable $callback = null, \Closure $default = null)
     * @method EmployeePosition|$this random(callable|int|null $number = null)
     * @method EmployeePosition|null sole(callable|string $key = null, $operator = null, $value = null)
     * @method EmployeePosition|null get($key, \Closure $default = null)
     * @method EmployeePosition|null first(callable $callback = null, \Closure $default = null)
     * @method EmployeePosition|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method EmployeePosition|null find($key, $default = null)
     * @method EmployeePosition[] all()
     */
    class _IH_EmployeePosition_C extends _BaseCollection {
        /**
         * @param int $size
         * @return EmployeePosition[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }
    
    /**
     * @method _IH_EmployeePosition_QB whereId($value)
     * @method _IH_EmployeePosition_QB whereEmployeeId($value)
     * @method _IH_EmployeePosition_QB whereEmployeeTypeId($value)
     * @method _IH_EmployeePosition_QB wherePositionId($value)
     * @method _IH_EmployeePosition_QB whereRankId($value)
     * @method _IH_EmployeePosition_QB whereGradeId($value)
     * @method _IH_EmployeePosition_QB whereSkDate($value)
     * @method _IH_EmployeePosition_QB whereSkNumber($value)
     * @method _IH_EmployeePosition_QB whereStartDate($value)
     * @method _IH_EmployeePosition_QB whereEndDate($value)
     * @method _IH_EmployeePosition_QB whereUnitId($value)
     * @method _IH_EmployeePosition_QB whereLocationId($value)
     * @method _IH_EmployeePosition_QB whereShiftId($value)
     * @method _IH_EmployeePosition_QB whereStatus($value)
     * @method _IH_EmployeePosition_QB whereCreatedBy($value)
     * @method _IH_EmployeePosition_QB whereUpdatedBy($value)
     * @method _IH_EmployeePosition_QB whereCreatedAt($value)
     * @method _IH_EmployeePosition_QB whereUpdatedAt($value)
     * @method _IH_EmployeePosition_QB whereLeaderId($value)
     * @method EmployeePosition baseSole(array|string $columns = ['*'])
     * @method EmployeePosition create(array $attributes = [])
     * @method _IH_EmployeePosition_C|EmployeePosition[] cursor()
     * @method EmployeePosition|null|_IH_EmployeePosition_C|EmployeePosition[] find($id, array|string $columns = ['*'])
     * @method _IH_EmployeePosition_C|EmployeePosition[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method EmployeePosition|_IH_EmployeePosition_C|EmployeePosition[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method EmployeePosition|_IH_EmployeePosition_C|EmployeePosition[] findOrFail($id, array|string $columns = ['*'])
     * @method EmployeePosition|_IH_EmployeePosition_C|EmployeePosition[] findOrNew($id, array|string $columns = ['*'])
     * @method EmployeePosition first(array|string $columns = ['*'])
     * @method EmployeePosition firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method EmployeePosition firstOrCreate(array $attributes = [], array $values = [])
     * @method EmployeePosition firstOrFail(array|string $columns = ['*'])
     * @method EmployeePosition firstOrNew(array $attributes = [], array $values = [])
     * @method EmployeePosition firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method EmployeePosition forceCreate(array $attributes)
     * @method _IH_EmployeePosition_C|EmployeePosition[] fromQuery(string $query, array $bindings = [])
     * @method _IH_EmployeePosition_C|EmployeePosition[] get(array|string $columns = ['*'])
     * @method EmployeePosition getModel()
     * @method EmployeePosition[] getModels(array|string $columns = ['*'])
     * @method _IH_EmployeePosition_C|EmployeePosition[] hydrate(array $items)
     * @method EmployeePosition make(array $attributes = [])
     * @method EmployeePosition newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|EmployeePosition[]|_IH_EmployeePosition_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Paginator|EmployeePosition[]|_IH_EmployeePosition_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method EmployeePosition sole(array|string $columns = ['*'])
     * @method EmployeePosition updateOrCreate(array $attributes, array $values = [])
     * @method _IH_EmployeePosition_QB active()
     */
    class _IH_EmployeePosition_QB extends _BaseBuilder {}
    
    /**
     * @method EmployeeSignatureSetting|null getOrPut($key, $value)
     * @method EmployeeSignatureSetting|$this shift(int $count = 1)
     * @method EmployeeSignatureSetting|null firstOrFail(callable|string $key = null, $operator = null, $value = null)
     * @method EmployeeSignatureSetting|$this pop(int $count = 1)
     * @method EmployeeSignatureSetting|null pull($key, \Closure $default = null)
     * @method EmployeeSignatureSetting|null last(callable $callback = null, \Closure $default = null)
     * @method EmployeeSignatureSetting|$this random(callable|int|null $number = null)
     * @method EmployeeSignatureSetting|null sole(callable|string $key = null, $operator = null, $value = null)
     * @method EmployeeSignatureSetting|null get($key, \Closure $default = null)
     * @method EmployeeSignatureSetting|null first(callable $callback = null, \Closure $default = null)
     * @method EmployeeSignatureSetting|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method EmployeeSignatureSetting|null find($key, $default = null)
     * @method EmployeeSignatureSetting[] all()
     */
    class _IH_EmployeeSignatureSetting_C extends _BaseCollection {
        /**
         * @param int $size
         * @return EmployeeSignatureSetting[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }
    
    /**
     * @method _IH_EmployeeSignatureSetting_QB whereId($value)
     * @method _IH_EmployeeSignatureSetting_QB whereLocationId($value)
     * @method _IH_EmployeeSignatureSetting_QB whereEmployeeId($value)
     * @method _IH_EmployeeSignatureSetting_QB whereDescription($value)
     * @method _IH_EmployeeSignatureSetting_QB whereStatus($value)
     * @method _IH_EmployeeSignatureSetting_QB whereCreatedBy($value)
     * @method _IH_EmployeeSignatureSetting_QB whereUpdatedBy($value)
     * @method _IH_EmployeeSignatureSetting_QB whereCreatedAt($value)
     * @method _IH_EmployeeSignatureSetting_QB whereUpdatedAt($value)
     * @method EmployeeSignatureSetting baseSole(array|string $columns = ['*'])
     * @method EmployeeSignatureSetting create(array $attributes = [])
     * @method _IH_EmployeeSignatureSetting_C|EmployeeSignatureSetting[] cursor()
     * @method EmployeeSignatureSetting|null|_IH_EmployeeSignatureSetting_C|EmployeeSignatureSetting[] find($id, array|string $columns = ['*'])
     * @method _IH_EmployeeSignatureSetting_C|EmployeeSignatureSetting[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method EmployeeSignatureSetting|_IH_EmployeeSignatureSetting_C|EmployeeSignatureSetting[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method EmployeeSignatureSetting|_IH_EmployeeSignatureSetting_C|EmployeeSignatureSetting[] findOrFail($id, array|string $columns = ['*'])
     * @method EmployeeSignatureSetting|_IH_EmployeeSignatureSetting_C|EmployeeSignatureSetting[] findOrNew($id, array|string $columns = ['*'])
     * @method EmployeeSignatureSetting first(array|string $columns = ['*'])
     * @method EmployeeSignatureSetting firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method EmployeeSignatureSetting firstOrCreate(array $attributes = [], array $values = [])
     * @method EmployeeSignatureSetting firstOrFail(array|string $columns = ['*'])
     * @method EmployeeSignatureSetting firstOrNew(array $attributes = [], array $values = [])
     * @method EmployeeSignatureSetting firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method EmployeeSignatureSetting forceCreate(array $attributes)
     * @method _IH_EmployeeSignatureSetting_C|EmployeeSignatureSetting[] fromQuery(string $query, array $bindings = [])
     * @method _IH_EmployeeSignatureSetting_C|EmployeeSignatureSetting[] get(array|string $columns = ['*'])
     * @method EmployeeSignatureSetting getModel()
     * @method EmployeeSignatureSetting[] getModels(array|string $columns = ['*'])
     * @method _IH_EmployeeSignatureSetting_C|EmployeeSignatureSetting[] hydrate(array $items)
     * @method EmployeeSignatureSetting make(array $attributes = [])
     * @method EmployeeSignatureSetting newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|EmployeeSignatureSetting[]|_IH_EmployeeSignatureSetting_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Paginator|EmployeeSignatureSetting[]|_IH_EmployeeSignatureSetting_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method EmployeeSignatureSetting sole(array|string $columns = ['*'])
     * @method EmployeeSignatureSetting updateOrCreate(array $attributes, array $values = [])
     * @method _IH_EmployeeSignatureSetting_QB active()
     */
    class _IH_EmployeeSignatureSetting_QB extends _BaseBuilder {}
    
    /**
     * @method EmployeeTraining|null getOrPut($key, $value)
     * @method EmployeeTraining|$this shift(int $count = 1)
     * @method EmployeeTraining|null firstOrFail(callable|string $key = null, $operator = null, $value = null)
     * @method EmployeeTraining|$this pop(int $count = 1)
     * @method EmployeeTraining|null pull($key, \Closure $default = null)
     * @method EmployeeTraining|null last(callable $callback = null, \Closure $default = null)
     * @method EmployeeTraining|$this random(callable|int|null $number = null)
     * @method EmployeeTraining|null sole(callable|string $key = null, $operator = null, $value = null)
     * @method EmployeeTraining|null get($key, \Closure $default = null)
     * @method EmployeeTraining|null first(callable $callback = null, \Closure $default = null)
     * @method EmployeeTraining|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method EmployeeTraining|null find($key, $default = null)
     * @method EmployeeTraining[] all()
     */
    class _IH_EmployeeTraining_C extends _BaseCollection {
        /**
         * @param int $size
         * @return EmployeeTraining[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }
    
    /**
     * @method _IH_EmployeeTraining_QB whereId($value)
     * @method _IH_EmployeeTraining_QB whereEmployeeId($value)
     * @method _IH_EmployeeTraining_QB whereCertificateNumber($value)
     * @method _IH_EmployeeTraining_QB whereSubject($value)
     * @method _IH_EmployeeTraining_QB whereInstitution($value)
     * @method _IH_EmployeeTraining_QB whereCategoryId($value)
     * @method _IH_EmployeeTraining_QB whereTypeId($value)
     * @method _IH_EmployeeTraining_QB whereStartDate($value)
     * @method _IH_EmployeeTraining_QB whereEndDate($value)
     * @method _IH_EmployeeTraining_QB whereLocation($value)
     * @method _IH_EmployeeTraining_QB whereDescription($value)
     * @method _IH_EmployeeTraining_QB whereFilename($value)
     * @method _IH_EmployeeTraining_QB whereCreatedBy($value)
     * @method _IH_EmployeeTraining_QB whereUpdatedBy($value)
     * @method _IH_EmployeeTraining_QB whereCreatedAt($value)
     * @method _IH_EmployeeTraining_QB whereUpdatedAt($value)
     * @method EmployeeTraining baseSole(array|string $columns = ['*'])
     * @method EmployeeTraining create(array $attributes = [])
     * @method _IH_EmployeeTraining_C|EmployeeTraining[] cursor()
     * @method EmployeeTraining|null|_IH_EmployeeTraining_C|EmployeeTraining[] find($id, array|string $columns = ['*'])
     * @method _IH_EmployeeTraining_C|EmployeeTraining[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method EmployeeTraining|_IH_EmployeeTraining_C|EmployeeTraining[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method EmployeeTraining|_IH_EmployeeTraining_C|EmployeeTraining[] findOrFail($id, array|string $columns = ['*'])
     * @method EmployeeTraining|_IH_EmployeeTraining_C|EmployeeTraining[] findOrNew($id, array|string $columns = ['*'])
     * @method EmployeeTraining first(array|string $columns = ['*'])
     * @method EmployeeTraining firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method EmployeeTraining firstOrCreate(array $attributes = [], array $values = [])
     * @method EmployeeTraining firstOrFail(array|string $columns = ['*'])
     * @method EmployeeTraining firstOrNew(array $attributes = [], array $values = [])
     * @method EmployeeTraining firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method EmployeeTraining forceCreate(array $attributes)
     * @method _IH_EmployeeTraining_C|EmployeeTraining[] fromQuery(string $query, array $bindings = [])
     * @method _IH_EmployeeTraining_C|EmployeeTraining[] get(array|string $columns = ['*'])
     * @method EmployeeTraining getModel()
     * @method EmployeeTraining[] getModels(array|string $columns = ['*'])
     * @method _IH_EmployeeTraining_C|EmployeeTraining[] hydrate(array $items)
     * @method EmployeeTraining make(array $attributes = [])
     * @method EmployeeTraining newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|EmployeeTraining[]|_IH_EmployeeTraining_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Paginator|EmployeeTraining[]|_IH_EmployeeTraining_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method EmployeeTraining sole(array|string $columns = ['*'])
     * @method EmployeeTraining updateOrCreate(array $attributes, array $values = [])
     */
    class _IH_EmployeeTraining_QB extends _BaseBuilder {}
    
    /**
     * @method EmployeeUnitStructure|null getOrPut($key, $value)
     * @method EmployeeUnitStructure|$this shift(int $count = 1)
     * @method EmployeeUnitStructure|null firstOrFail(callable|string $key = null, $operator = null, $value = null)
     * @method EmployeeUnitStructure|$this pop(int $count = 1)
     * @method EmployeeUnitStructure|null pull($key, \Closure $default = null)
     * @method EmployeeUnitStructure|null last(callable $callback = null, \Closure $default = null)
     * @method EmployeeUnitStructure|$this random(callable|int|null $number = null)
     * @method EmployeeUnitStructure|null sole(callable|string $key = null, $operator = null, $value = null)
     * @method EmployeeUnitStructure|null get($key, \Closure $default = null)
     * @method EmployeeUnitStructure|null first(callable $callback = null, \Closure $default = null)
     * @method EmployeeUnitStructure|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method EmployeeUnitStructure|null find($key, $default = null)
     * @method EmployeeUnitStructure[] all()
     */
    class _IH_EmployeeUnitStructure_C extends _BaseCollection {
        /**
         * @param int $size
         * @return EmployeeUnitStructure[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }
    
    /**
     * @method _IH_EmployeeUnitStructure_QB whereId($value)
     * @method _IH_EmployeeUnitStructure_QB whereUnitId($value)
     * @method _IH_EmployeeUnitStructure_QB whereLeaderId($value)
     * @method _IH_EmployeeUnitStructure_QB whereAdministrationId($value)
     * @method _IH_EmployeeUnitStructure_QB whereCreatedBy($value)
     * @method _IH_EmployeeUnitStructure_QB whereUpdatedBy($value)
     * @method _IH_EmployeeUnitStructure_QB whereCreatedAt($value)
     * @method _IH_EmployeeUnitStructure_QB whereUpdatedAt($value)
     * @method EmployeeUnitStructure baseSole(array|string $columns = ['*'])
     * @method EmployeeUnitStructure create(array $attributes = [])
     * @method _IH_EmployeeUnitStructure_C|EmployeeUnitStructure[] cursor()
     * @method EmployeeUnitStructure|null|_IH_EmployeeUnitStructure_C|EmployeeUnitStructure[] find($id, array|string $columns = ['*'])
     * @method _IH_EmployeeUnitStructure_C|EmployeeUnitStructure[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method EmployeeUnitStructure|_IH_EmployeeUnitStructure_C|EmployeeUnitStructure[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method EmployeeUnitStructure|_IH_EmployeeUnitStructure_C|EmployeeUnitStructure[] findOrFail($id, array|string $columns = ['*'])
     * @method EmployeeUnitStructure|_IH_EmployeeUnitStructure_C|EmployeeUnitStructure[] findOrNew($id, array|string $columns = ['*'])
     * @method EmployeeUnitStructure first(array|string $columns = ['*'])
     * @method EmployeeUnitStructure firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method EmployeeUnitStructure firstOrCreate(array $attributes = [], array $values = [])
     * @method EmployeeUnitStructure firstOrFail(array|string $columns = ['*'])
     * @method EmployeeUnitStructure firstOrNew(array $attributes = [], array $values = [])
     * @method EmployeeUnitStructure firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method EmployeeUnitStructure forceCreate(array $attributes)
     * @method _IH_EmployeeUnitStructure_C|EmployeeUnitStructure[] fromQuery(string $query, array $bindings = [])
     * @method _IH_EmployeeUnitStructure_C|EmployeeUnitStructure[] get(array|string $columns = ['*'])
     * @method EmployeeUnitStructure getModel()
     * @method EmployeeUnitStructure[] getModels(array|string $columns = ['*'])
     * @method _IH_EmployeeUnitStructure_C|EmployeeUnitStructure[] hydrate(array $items)
     * @method EmployeeUnitStructure make(array $attributes = [])
     * @method EmployeeUnitStructure newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|EmployeeUnitStructure[]|_IH_EmployeeUnitStructure_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Paginator|EmployeeUnitStructure[]|_IH_EmployeeUnitStructure_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method EmployeeUnitStructure sole(array|string $columns = ['*'])
     * @method EmployeeUnitStructure updateOrCreate(array $attributes, array $values = [])
     */
    class _IH_EmployeeUnitStructure_QB extends _BaseBuilder {}
    
    /**
     * @method EmployeeWork|null getOrPut($key, $value)
     * @method EmployeeWork|$this shift(int $count = 1)
     * @method EmployeeWork|null firstOrFail(callable|string $key = null, $operator = null, $value = null)
     * @method EmployeeWork|$this pop(int $count = 1)
     * @method EmployeeWork|null pull($key, \Closure $default = null)
     * @method EmployeeWork|null last(callable $callback = null, \Closure $default = null)
     * @method EmployeeWork|$this random(callable|int|null $number = null)
     * @method EmployeeWork|null sole(callable|string $key = null, $operator = null, $value = null)
     * @method EmployeeWork|null get($key, \Closure $default = null)
     * @method EmployeeWork|null first(callable $callback = null, \Closure $default = null)
     * @method EmployeeWork|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method EmployeeWork|null find($key, $default = null)
     * @method EmployeeWork[] all()
     */
    class _IH_EmployeeWork_C extends _BaseCollection {
        /**
         * @param int $size
         * @return EmployeeWork[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }
    
    /**
     * @method _IH_EmployeeWork_QB whereId($value)
     * @method _IH_EmployeeWork_QB whereEmployeeId($value)
     * @method _IH_EmployeeWork_QB whereCompany($value)
     * @method _IH_EmployeeWork_QB wherePosition($value)
     * @method _IH_EmployeeWork_QB whereStartDate($value)
     * @method _IH_EmployeeWork_QB whereEndDate($value)
     * @method _IH_EmployeeWork_QB whereCity($value)
     * @method _IH_EmployeeWork_QB whereJobDesc($value)
     * @method _IH_EmployeeWork_QB whereDescription($value)
     * @method _IH_EmployeeWork_QB whereFilename($value)
     * @method _IH_EmployeeWork_QB whereCreatedBy($value)
     * @method _IH_EmployeeWork_QB whereUpdatedBy($value)
     * @method _IH_EmployeeWork_QB whereCreatedAt($value)
     * @method _IH_EmployeeWork_QB whereUpdatedAt($value)
     * @method EmployeeWork baseSole(array|string $columns = ['*'])
     * @method EmployeeWork create(array $attributes = [])
     * @method _IH_EmployeeWork_C|EmployeeWork[] cursor()
     * @method EmployeeWork|null|_IH_EmployeeWork_C|EmployeeWork[] find($id, array|string $columns = ['*'])
     * @method _IH_EmployeeWork_C|EmployeeWork[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method EmployeeWork|_IH_EmployeeWork_C|EmployeeWork[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method EmployeeWork|_IH_EmployeeWork_C|EmployeeWork[] findOrFail($id, array|string $columns = ['*'])
     * @method EmployeeWork|_IH_EmployeeWork_C|EmployeeWork[] findOrNew($id, array|string $columns = ['*'])
     * @method EmployeeWork first(array|string $columns = ['*'])
     * @method EmployeeWork firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method EmployeeWork firstOrCreate(array $attributes = [], array $values = [])
     * @method EmployeeWork firstOrFail(array|string $columns = ['*'])
     * @method EmployeeWork firstOrNew(array $attributes = [], array $values = [])
     * @method EmployeeWork firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method EmployeeWork forceCreate(array $attributes)
     * @method _IH_EmployeeWork_C|EmployeeWork[] fromQuery(string $query, array $bindings = [])
     * @method _IH_EmployeeWork_C|EmployeeWork[] get(array|string $columns = ['*'])
     * @method EmployeeWork getModel()
     * @method EmployeeWork[] getModels(array|string $columns = ['*'])
     * @method _IH_EmployeeWork_C|EmployeeWork[] hydrate(array $items)
     * @method EmployeeWork make(array $attributes = [])
     * @method EmployeeWork newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|EmployeeWork[]|_IH_EmployeeWork_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Paginator|EmployeeWork[]|_IH_EmployeeWork_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method EmployeeWork sole(array|string $columns = ['*'])
     * @method EmployeeWork updateOrCreate(array $attributes, array $values = [])
     */
    class _IH_EmployeeWork_QB extends _BaseBuilder {}
    
    /**
     * @method Employee|null getOrPut($key, $value)
     * @method Employee|$this shift(int $count = 1)
     * @method Employee|null firstOrFail(callable|string $key = null, $operator = null, $value = null)
     * @method Employee|$this pop(int $count = 1)
     * @method Employee|null pull($key, \Closure $default = null)
     * @method Employee|null last(callable $callback = null, \Closure $default = null)
     * @method Employee|$this random(callable|int|null $number = null)
     * @method Employee|null sole(callable|string $key = null, $operator = null, $value = null)
     * @method Employee|null get($key, \Closure $default = null)
     * @method Employee|null first(callable $callback = null, \Closure $default = null)
     * @method Employee|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method Employee|null find($key, $default = null)
     * @method Employee[] all()
     */
    class _IH_Employee_C extends _BaseCollection {
        /**
         * @param int $size
         * @return Employee[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }
    
    /**
     * @method _IH_Employee_QB whereId($value)
     * @method _IH_Employee_QB whereName($value)
     * @method _IH_Employee_QB whereEmployeeNumber($value)
     * @method _IH_Employee_QB whereNickname($value)
     * @method _IH_Employee_QB wherePlaceOfBirth($value)
     * @method _IH_Employee_QB whereDateOfBirth($value)
     * @method _IH_Employee_QB whereIdentityNumber($value)
     * @method _IH_Employee_QB whereAddress($value)
     * @method _IH_Employee_QB whereIdentityAddress($value)
     * @method _IH_Employee_QB whereMaritalStatusId($value)
     * @method _IH_Employee_QB wherePhoneNumber($value)
     * @method _IH_Employee_QB whereJoinDate($value)
     * @method _IH_Employee_QB whereLeaveDate($value)
     * @method _IH_Employee_QB wherePhoto($value)
     * @method _IH_Employee_QB whereIdentityFile($value)
     * @method _IH_Employee_QB whereEmail($value)
     * @method _IH_Employee_QB whereMobilePhoneNumber($value)
     * @method _IH_Employee_QB whereStatusId($value)
     * @method _IH_Employee_QB whereCreatedBy($value)
     * @method _IH_Employee_QB whereUpdatedBy($value)
     * @method _IH_Employee_QB whereCreatedAt($value)
     * @method _IH_Employee_QB whereUpdatedAt($value)
     * @method _IH_Employee_QB whereGender($value)
     * @method _IH_Employee_QB whereAttendancePin($value)
     * @method Employee baseSole(array|string $columns = ['*'])
     * @method Employee create(array $attributes = [])
     * @method _IH_Employee_C|Employee[] cursor()
     * @method Employee|null|_IH_Employee_C|Employee[] find($id, array|string $columns = ['*'])
     * @method _IH_Employee_C|Employee[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method Employee|_IH_Employee_C|Employee[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method Employee|_IH_Employee_C|Employee[] findOrFail($id, array|string $columns = ['*'])
     * @method Employee|_IH_Employee_C|Employee[] findOrNew($id, array|string $columns = ['*'])
     * @method Employee first(array|string $columns = ['*'])
     * @method Employee firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method Employee firstOrCreate(array $attributes = [], array $values = [])
     * @method Employee firstOrFail(array|string $columns = ['*'])
     * @method Employee firstOrNew(array $attributes = [], array $values = [])
     * @method Employee firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method Employee forceCreate(array $attributes)
     * @method _IH_Employee_C|Employee[] fromQuery(string $query, array $bindings = [])
     * @method _IH_Employee_C|Employee[] get(array|string $columns = ['*'])
     * @method Employee getModel()
     * @method Employee[] getModels(array|string $columns = ['*'])
     * @method _IH_Employee_C|Employee[] hydrate(array $items)
     * @method Employee make(array $attributes = [])
     * @method Employee newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|Employee[]|_IH_Employee_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Paginator|Employee[]|_IH_Employee_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Employee sole(array|string $columns = ['*'])
     * @method Employee updateOrCreate(array $attributes, array $values = [])
     * @method _IH_Employee_QB active()
     */
    class _IH_Employee_QB extends _BaseBuilder {}
}