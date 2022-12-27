<?php //bf6b253b25a0592a4accebab8e9285de
/** @noinspection all */

namespace LaravelIdea\Helper\App\Models\ESS {

    use App\Models\ESS\EssTimesheet;
    use Illuminate\Contracts\Support\Arrayable;
    use Illuminate\Database\Query\Expression;
    use Illuminate\Pagination\LengthAwarePaginator;
    use Illuminate\Pagination\Paginator;
    use LaravelIdea\Helper\_BaseBuilder;
    use LaravelIdea\Helper\_BaseCollection;
    
    /**
     * @method EssTimesheet|null getOrPut($key, $value)
     * @method EssTimesheet|$this shift(int $count = 1)
     * @method EssTimesheet|null firstOrFail(callable|string $key = null, $operator = null, $value = null)
     * @method EssTimesheet|$this pop(int $count = 1)
     * @method EssTimesheet|null pull($key, \Closure $default = null)
     * @method EssTimesheet|null last(callable $callback = null, \Closure $default = null)
     * @method EssTimesheet|$this random(callable|int|null $number = null)
     * @method EssTimesheet|null sole(callable|string $key = null, $operator = null, $value = null)
     * @method EssTimesheet|null get($key, \Closure $default = null)
     * @method EssTimesheet|null first(callable $callback = null, \Closure $default = null)
     * @method EssTimesheet|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method EssTimesheet|null find($key, $default = null)
     * @method EssTimesheet[] all()
     */
    class _IH_EssTimesheet_C extends _BaseCollection {
        /**
         * @param int $size
         * @return EssTimesheet[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }
    
    /**
     * @method _IH_EssTimesheet_QB whereId($value)
     * @method _IH_EssTimesheet_QB whereEmployeeId($value)
     * @method _IH_EssTimesheet_QB whereActivity($value)
     * @method _IH_EssTimesheet_QB whereOutput($value)
     * @method _IH_EssTimesheet_QB whereStartTime($value)
     * @method _IH_EssTimesheet_QB whereEndTime($value)
     * @method _IH_EssTimesheet_QB whereDuration($value)
     * @method _IH_EssTimesheet_QB whereVolume($value)
     * @method _IH_EssTimesheet_QB whereType($value)
     * @method _IH_EssTimesheet_QB whereDescription($value)
     * @method _IH_EssTimesheet_QB whereCreatedBy($value)
     * @method _IH_EssTimesheet_QB whereUpdatedBy($value)
     * @method _IH_EssTimesheet_QB whereCreatedAt($value)
     * @method _IH_EssTimesheet_QB whereUpdatedAt($value)
     * @method EssTimesheet baseSole(array|string $columns = ['*'])
     * @method EssTimesheet create(array $attributes = [])
     * @method _IH_EssTimesheet_C|EssTimesheet[] cursor()
     * @method EssTimesheet|null|_IH_EssTimesheet_C|EssTimesheet[] find($id, array|string $columns = ['*'])
     * @method _IH_EssTimesheet_C|EssTimesheet[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method EssTimesheet|_IH_EssTimesheet_C|EssTimesheet[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method EssTimesheet|_IH_EssTimesheet_C|EssTimesheet[] findOrFail($id, array|string $columns = ['*'])
     * @method EssTimesheet|_IH_EssTimesheet_C|EssTimesheet[] findOrNew($id, array|string $columns = ['*'])
     * @method EssTimesheet first(array|string $columns = ['*'])
     * @method EssTimesheet firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method EssTimesheet firstOrCreate(array $attributes = [], array $values = [])
     * @method EssTimesheet firstOrFail(array|string $columns = ['*'])
     * @method EssTimesheet firstOrNew(array $attributes = [], array $values = [])
     * @method EssTimesheet firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method EssTimesheet forceCreate(array $attributes)
     * @method _IH_EssTimesheet_C|EssTimesheet[] fromQuery(string $query, array $bindings = [])
     * @method _IH_EssTimesheet_C|EssTimesheet[] get(array|string $columns = ['*'])
     * @method EssTimesheet getModel()
     * @method EssTimesheet[] getModels(array|string $columns = ['*'])
     * @method _IH_EssTimesheet_C|EssTimesheet[] hydrate(array $items)
     * @method EssTimesheet make(array $attributes = [])
     * @method EssTimesheet newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|EssTimesheet[]|_IH_EssTimesheet_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Paginator|EssTimesheet[]|_IH_EssTimesheet_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method EssTimesheet sole(array|string $columns = ['*'])
     * @method EssTimesheet updateOrCreate(array $attributes, array $values = [])
     */
    class _IH_EssTimesheet_QB extends _BaseBuilder {}
}