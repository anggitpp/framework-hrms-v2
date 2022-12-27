<?php //4c50c85a90cb6d938829d361d9b042d1
/** @noinspection all */

namespace LaravelIdea\Helper\App\Models\Attendance {

    use App\Models\Attendance\Attendance;
    use App\Models\Attendance\AttendanceCorrection;
    use App\Models\Attendance\AttendanceHoliday;
    use App\Models\Attendance\AttendanceLeave;
    use App\Models\Attendance\AttendanceLeaveMaster;
    use App\Models\Attendance\AttendanceLocationSetting;
    use App\Models\Attendance\AttendanceMachineSetting;
    use App\Models\Attendance\AttendanceOvertime;
    use App\Models\Attendance\AttendancePermission;
    use App\Models\Attendance\AttendanceShift;
    use App\Models\Attendance\AttendanceWorkSchedule;
    use Illuminate\Contracts\Support\Arrayable;
    use Illuminate\Database\Query\Expression;
    use Illuminate\Pagination\LengthAwarePaginator;
    use Illuminate\Pagination\Paginator;
    use LaravelIdea\Helper\_BaseBuilder;
    use LaravelIdea\Helper\_BaseCollection;
    
    /**
     * @method AttendanceCorrection|null getOrPut($key, $value)
     * @method AttendanceCorrection|$this shift(int $count = 1)
     * @method AttendanceCorrection|null firstOrFail(callable|string $key = null, $operator = null, $value = null)
     * @method AttendanceCorrection|$this pop(int $count = 1)
     * @method AttendanceCorrection|null pull($key, \Closure $default = null)
     * @method AttendanceCorrection|null last(callable $callback = null, \Closure $default = null)
     * @method AttendanceCorrection|$this random(callable|int|null $number = null)
     * @method AttendanceCorrection|null sole(callable|string $key = null, $operator = null, $value = null)
     * @method AttendanceCorrection|null get($key, \Closure $default = null)
     * @method AttendanceCorrection|null first(callable $callback = null, \Closure $default = null)
     * @method AttendanceCorrection|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method AttendanceCorrection|null find($key, $default = null)
     * @method AttendanceCorrection[] all()
     */
    class _IH_AttendanceCorrection_C extends _BaseCollection {
        /**
         * @param int $size
         * @return AttendanceCorrection[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }
    
    /**
     * @method _IH_AttendanceCorrection_QB whereId($value)
     * @method _IH_AttendanceCorrection_QB whereNumber($value)
     * @method _IH_AttendanceCorrection_QB whereEmployeeId($value)
     * @method _IH_AttendanceCorrection_QB whereAttendanceDate($value)
     * @method _IH_AttendanceCorrection_QB whereStartTime($value)
     * @method _IH_AttendanceCorrection_QB whereEndTime($value)
     * @method _IH_AttendanceCorrection_QB whereActualStartTime($value)
     * @method _IH_AttendanceCorrection_QB whereActualEndTime($value)
     * @method _IH_AttendanceCorrection_QB whereDescription($value)
     * @method _IH_AttendanceCorrection_QB whereApprovedBy($value)
     * @method _IH_AttendanceCorrection_QB whereApprovedStatus($value)
     * @method _IH_AttendanceCorrection_QB whereApprovedDate($value)
     * @method _IH_AttendanceCorrection_QB whereApprovedNote($value)
     * @method _IH_AttendanceCorrection_QB whereCreatedBy($value)
     * @method _IH_AttendanceCorrection_QB whereUpdatedBy($value)
     * @method _IH_AttendanceCorrection_QB whereCreatedAt($value)
     * @method _IH_AttendanceCorrection_QB whereUpdatedAt($value)
     * @method _IH_AttendanceCorrection_QB whereDuration($value)
     * @method _IH_AttendanceCorrection_QB whereActualDuration($value)
     * @method AttendanceCorrection baseSole(array|string $columns = ['*'])
     * @method AttendanceCorrection create(array $attributes = [])
     * @method _IH_AttendanceCorrection_C|AttendanceCorrection[] cursor()
     * @method AttendanceCorrection|null|_IH_AttendanceCorrection_C|AttendanceCorrection[] find($id, array|string $columns = ['*'])
     * @method _IH_AttendanceCorrection_C|AttendanceCorrection[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method AttendanceCorrection|_IH_AttendanceCorrection_C|AttendanceCorrection[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method AttendanceCorrection|_IH_AttendanceCorrection_C|AttendanceCorrection[] findOrFail($id, array|string $columns = ['*'])
     * @method AttendanceCorrection|_IH_AttendanceCorrection_C|AttendanceCorrection[] findOrNew($id, array|string $columns = ['*'])
     * @method AttendanceCorrection first(array|string $columns = ['*'])
     * @method AttendanceCorrection firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method AttendanceCorrection firstOrCreate(array $attributes = [], array $values = [])
     * @method AttendanceCorrection firstOrFail(array|string $columns = ['*'])
     * @method AttendanceCorrection firstOrNew(array $attributes = [], array $values = [])
     * @method AttendanceCorrection firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method AttendanceCorrection forceCreate(array $attributes)
     * @method _IH_AttendanceCorrection_C|AttendanceCorrection[] fromQuery(string $query, array $bindings = [])
     * @method _IH_AttendanceCorrection_C|AttendanceCorrection[] get(array|string $columns = ['*'])
     * @method AttendanceCorrection getModel()
     * @method AttendanceCorrection[] getModels(array|string $columns = ['*'])
     * @method _IH_AttendanceCorrection_C|AttendanceCorrection[] hydrate(array $items)
     * @method AttendanceCorrection make(array $attributes = [])
     * @method AttendanceCorrection newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|AttendanceCorrection[]|_IH_AttendanceCorrection_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Paginator|AttendanceCorrection[]|_IH_AttendanceCorrection_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method AttendanceCorrection sole(array|string $columns = ['*'])
     * @method AttendanceCorrection updateOrCreate(array $attributes, array $values = [])
     */
    class _IH_AttendanceCorrection_QB extends _BaseBuilder {}
    
    /**
     * @method AttendanceHoliday|null getOrPut($key, $value)
     * @method AttendanceHoliday|$this shift(int $count = 1)
     * @method AttendanceHoliday|null firstOrFail(callable|string $key = null, $operator = null, $value = null)
     * @method AttendanceHoliday|$this pop(int $count = 1)
     * @method AttendanceHoliday|null pull($key, \Closure $default = null)
     * @method AttendanceHoliday|null last(callable $callback = null, \Closure $default = null)
     * @method AttendanceHoliday|$this random(callable|int|null $number = null)
     * @method AttendanceHoliday|null sole(callable|string $key = null, $operator = null, $value = null)
     * @method AttendanceHoliday|null get($key, \Closure $default = null)
     * @method AttendanceHoliday|null first(callable $callback = null, \Closure $default = null)
     * @method AttendanceHoliday|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method AttendanceHoliday|null find($key, $default = null)
     * @method AttendanceHoliday[] all()
     */
    class _IH_AttendanceHoliday_C extends _BaseCollection {
        /**
         * @param int $size
         * @return AttendanceHoliday[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }
    
    /**
     * @method _IH_AttendanceHoliday_QB whereId($value)
     * @method _IH_AttendanceHoliday_QB whereName($value)
     * @method _IH_AttendanceHoliday_QB whereStartDate($value)
     * @method _IH_AttendanceHoliday_QB whereEndDate($value)
     * @method _IH_AttendanceHoliday_QB whereDescription($value)
     * @method _IH_AttendanceHoliday_QB whereStatus($value)
     * @method _IH_AttendanceHoliday_QB whereCreatedBy($value)
     * @method _IH_AttendanceHoliday_QB whereUpdatedBy($value)
     * @method _IH_AttendanceHoliday_QB whereCreatedAt($value)
     * @method _IH_AttendanceHoliday_QB whereUpdatedAt($value)
     * @method AttendanceHoliday baseSole(array|string $columns = ['*'])
     * @method AttendanceHoliday create(array $attributes = [])
     * @method _IH_AttendanceHoliday_C|AttendanceHoliday[] cursor()
     * @method AttendanceHoliday|null|_IH_AttendanceHoliday_C|AttendanceHoliday[] find($id, array|string $columns = ['*'])
     * @method _IH_AttendanceHoliday_C|AttendanceHoliday[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method AttendanceHoliday|_IH_AttendanceHoliday_C|AttendanceHoliday[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method AttendanceHoliday|_IH_AttendanceHoliday_C|AttendanceHoliday[] findOrFail($id, array|string $columns = ['*'])
     * @method AttendanceHoliday|_IH_AttendanceHoliday_C|AttendanceHoliday[] findOrNew($id, array|string $columns = ['*'])
     * @method AttendanceHoliday first(array|string $columns = ['*'])
     * @method AttendanceHoliday firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method AttendanceHoliday firstOrCreate(array $attributes = [], array $values = [])
     * @method AttendanceHoliday firstOrFail(array|string $columns = ['*'])
     * @method AttendanceHoliday firstOrNew(array $attributes = [], array $values = [])
     * @method AttendanceHoliday firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method AttendanceHoliday forceCreate(array $attributes)
     * @method _IH_AttendanceHoliday_C|AttendanceHoliday[] fromQuery(string $query, array $bindings = [])
     * @method _IH_AttendanceHoliday_C|AttendanceHoliday[] get(array|string $columns = ['*'])
     * @method AttendanceHoliday getModel()
     * @method AttendanceHoliday[] getModels(array|string $columns = ['*'])
     * @method _IH_AttendanceHoliday_C|AttendanceHoliday[] hydrate(array $items)
     * @method AttendanceHoliday make(array $attributes = [])
     * @method AttendanceHoliday newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|AttendanceHoliday[]|_IH_AttendanceHoliday_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Paginator|AttendanceHoliday[]|_IH_AttendanceHoliday_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method AttendanceHoliday sole(array|string $columns = ['*'])
     * @method AttendanceHoliday updateOrCreate(array $attributes, array $values = [])
     */
    class _IH_AttendanceHoliday_QB extends _BaseBuilder {}
    
    /**
     * @method AttendanceLeaveMaster|null getOrPut($key, $value)
     * @method AttendanceLeaveMaster|$this shift(int $count = 1)
     * @method AttendanceLeaveMaster|null firstOrFail(callable|string $key = null, $operator = null, $value = null)
     * @method AttendanceLeaveMaster|$this pop(int $count = 1)
     * @method AttendanceLeaveMaster|null pull($key, \Closure $default = null)
     * @method AttendanceLeaveMaster|null last(callable $callback = null, \Closure $default = null)
     * @method AttendanceLeaveMaster|$this random(callable|int|null $number = null)
     * @method AttendanceLeaveMaster|null sole(callable|string $key = null, $operator = null, $value = null)
     * @method AttendanceLeaveMaster|null get($key, \Closure $default = null)
     * @method AttendanceLeaveMaster|null first(callable $callback = null, \Closure $default = null)
     * @method AttendanceLeaveMaster|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method AttendanceLeaveMaster|null find($key, $default = null)
     * @method AttendanceLeaveMaster[] all()
     */
    class _IH_AttendanceLeaveMaster_C extends _BaseCollection {
        /**
         * @param int $size
         * @return AttendanceLeaveMaster[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }
    
    /**
     * @method _IH_AttendanceLeaveMaster_QB whereId($value)
     * @method _IH_AttendanceLeaveMaster_QB whereLocationId($value)
     * @method _IH_AttendanceLeaveMaster_QB whereName($value)
     * @method _IH_AttendanceLeaveMaster_QB whereBalance($value)
     * @method _IH_AttendanceLeaveMaster_QB whereStartDate($value)
     * @method _IH_AttendanceLeaveMaster_QB whereEndDate($value)
     * @method _IH_AttendanceLeaveMaster_QB whereWorkPeriod($value)
     * @method _IH_AttendanceLeaveMaster_QB whereGender($value)
     * @method _IH_AttendanceLeaveMaster_QB whereDescription($value)
     * @method _IH_AttendanceLeaveMaster_QB whereStatus($value)
     * @method _IH_AttendanceLeaveMaster_QB whereCreatedBy($value)
     * @method _IH_AttendanceLeaveMaster_QB whereUpdatedBy($value)
     * @method _IH_AttendanceLeaveMaster_QB whereCreatedAt($value)
     * @method _IH_AttendanceLeaveMaster_QB whereUpdatedAt($value)
     * @method AttendanceLeaveMaster baseSole(array|string $columns = ['*'])
     * @method AttendanceLeaveMaster create(array $attributes = [])
     * @method _IH_AttendanceLeaveMaster_C|AttendanceLeaveMaster[] cursor()
     * @method AttendanceLeaveMaster|null|_IH_AttendanceLeaveMaster_C|AttendanceLeaveMaster[] find($id, array|string $columns = ['*'])
     * @method _IH_AttendanceLeaveMaster_C|AttendanceLeaveMaster[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method AttendanceLeaveMaster|_IH_AttendanceLeaveMaster_C|AttendanceLeaveMaster[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method AttendanceLeaveMaster|_IH_AttendanceLeaveMaster_C|AttendanceLeaveMaster[] findOrFail($id, array|string $columns = ['*'])
     * @method AttendanceLeaveMaster|_IH_AttendanceLeaveMaster_C|AttendanceLeaveMaster[] findOrNew($id, array|string $columns = ['*'])
     * @method AttendanceLeaveMaster first(array|string $columns = ['*'])
     * @method AttendanceLeaveMaster firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method AttendanceLeaveMaster firstOrCreate(array $attributes = [], array $values = [])
     * @method AttendanceLeaveMaster firstOrFail(array|string $columns = ['*'])
     * @method AttendanceLeaveMaster firstOrNew(array $attributes = [], array $values = [])
     * @method AttendanceLeaveMaster firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method AttendanceLeaveMaster forceCreate(array $attributes)
     * @method _IH_AttendanceLeaveMaster_C|AttendanceLeaveMaster[] fromQuery(string $query, array $bindings = [])
     * @method _IH_AttendanceLeaveMaster_C|AttendanceLeaveMaster[] get(array|string $columns = ['*'])
     * @method AttendanceLeaveMaster getModel()
     * @method AttendanceLeaveMaster[] getModels(array|string $columns = ['*'])
     * @method _IH_AttendanceLeaveMaster_C|AttendanceLeaveMaster[] hydrate(array $items)
     * @method AttendanceLeaveMaster make(array $attributes = [])
     * @method AttendanceLeaveMaster newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|AttendanceLeaveMaster[]|_IH_AttendanceLeaveMaster_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Paginator|AttendanceLeaveMaster[]|_IH_AttendanceLeaveMaster_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method AttendanceLeaveMaster sole(array|string $columns = ['*'])
     * @method AttendanceLeaveMaster updateOrCreate(array $attributes, array $values = [])
     */
    class _IH_AttendanceLeaveMaster_QB extends _BaseBuilder {}
    
    /**
     * @method AttendanceLeave|null getOrPut($key, $value)
     * @method AttendanceLeave|$this shift(int $count = 1)
     * @method AttendanceLeave|null firstOrFail(callable|string $key = null, $operator = null, $value = null)
     * @method AttendanceLeave|$this pop(int $count = 1)
     * @method AttendanceLeave|null pull($key, \Closure $default = null)
     * @method AttendanceLeave|null last(callable $callback = null, \Closure $default = null)
     * @method AttendanceLeave|$this random(callable|int|null $number = null)
     * @method AttendanceLeave|null sole(callable|string $key = null, $operator = null, $value = null)
     * @method AttendanceLeave|null get($key, \Closure $default = null)
     * @method AttendanceLeave|null first(callable $callback = null, \Closure $default = null)
     * @method AttendanceLeave|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method AttendanceLeave|null find($key, $default = null)
     * @method AttendanceLeave[] all()
     */
    class _IH_AttendanceLeave_C extends _BaseCollection {
        /**
         * @param int $size
         * @return AttendanceLeave[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }
    
    /**
     * @method _IH_AttendanceLeave_QB whereId($value)
     * @method _IH_AttendanceLeave_QB whereEmployeeId($value)
     * @method _IH_AttendanceLeave_QB whereLeaveMasterId($value)
     * @method _IH_AttendanceLeave_QB whereNumber($value)
     * @method _IH_AttendanceLeave_QB whereStartDate($value)
     * @method _IH_AttendanceLeave_QB whereEndDate($value)
     * @method _IH_AttendanceLeave_QB whereBalance($value)
     * @method _IH_AttendanceLeave_QB whereAmount($value)
     * @method _IH_AttendanceLeave_QB whereRemaining($value)
     * @method _IH_AttendanceLeave_QB whereDescription($value)
     * @method _IH_AttendanceLeave_QB whereFilename($value)
     * @method _IH_AttendanceLeave_QB whereApprovedBy($value)
     * @method _IH_AttendanceLeave_QB whereApprovedStatus($value)
     * @method _IH_AttendanceLeave_QB whereApprovedDate($value)
     * @method _IH_AttendanceLeave_QB whereApprovedNote($value)
     * @method _IH_AttendanceLeave_QB whereCreatedBy($value)
     * @method _IH_AttendanceLeave_QB whereUpdatedBy($value)
     * @method _IH_AttendanceLeave_QB whereCreatedAt($value)
     * @method _IH_AttendanceLeave_QB whereUpdatedAt($value)
     * @method AttendanceLeave baseSole(array|string $columns = ['*'])
     * @method AttendanceLeave create(array $attributes = [])
     * @method _IH_AttendanceLeave_C|AttendanceLeave[] cursor()
     * @method AttendanceLeave|null|_IH_AttendanceLeave_C|AttendanceLeave[] find($id, array|string $columns = ['*'])
     * @method _IH_AttendanceLeave_C|AttendanceLeave[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method AttendanceLeave|_IH_AttendanceLeave_C|AttendanceLeave[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method AttendanceLeave|_IH_AttendanceLeave_C|AttendanceLeave[] findOrFail($id, array|string $columns = ['*'])
     * @method AttendanceLeave|_IH_AttendanceLeave_C|AttendanceLeave[] findOrNew($id, array|string $columns = ['*'])
     * @method AttendanceLeave first(array|string $columns = ['*'])
     * @method AttendanceLeave firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method AttendanceLeave firstOrCreate(array $attributes = [], array $values = [])
     * @method AttendanceLeave firstOrFail(array|string $columns = ['*'])
     * @method AttendanceLeave firstOrNew(array $attributes = [], array $values = [])
     * @method AttendanceLeave firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method AttendanceLeave forceCreate(array $attributes)
     * @method _IH_AttendanceLeave_C|AttendanceLeave[] fromQuery(string $query, array $bindings = [])
     * @method _IH_AttendanceLeave_C|AttendanceLeave[] get(array|string $columns = ['*'])
     * @method AttendanceLeave getModel()
     * @method AttendanceLeave[] getModels(array|string $columns = ['*'])
     * @method _IH_AttendanceLeave_C|AttendanceLeave[] hydrate(array $items)
     * @method AttendanceLeave make(array $attributes = [])
     * @method AttendanceLeave newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|AttendanceLeave[]|_IH_AttendanceLeave_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Paginator|AttendanceLeave[]|_IH_AttendanceLeave_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method AttendanceLeave sole(array|string $columns = ['*'])
     * @method AttendanceLeave updateOrCreate(array $attributes, array $values = [])
     */
    class _IH_AttendanceLeave_QB extends _BaseBuilder {}
    
    /**
     * @method AttendanceLocationSetting|null getOrPut($key, $value)
     * @method AttendanceLocationSetting|$this shift(int $count = 1)
     * @method AttendanceLocationSetting|null firstOrFail(callable|string $key = null, $operator = null, $value = null)
     * @method AttendanceLocationSetting|$this pop(int $count = 1)
     * @method AttendanceLocationSetting|null pull($key, \Closure $default = null)
     * @method AttendanceLocationSetting|null last(callable $callback = null, \Closure $default = null)
     * @method AttendanceLocationSetting|$this random(callable|int|null $number = null)
     * @method AttendanceLocationSetting|null sole(callable|string $key = null, $operator = null, $value = null)
     * @method AttendanceLocationSetting|null get($key, \Closure $default = null)
     * @method AttendanceLocationSetting|null first(callable $callback = null, \Closure $default = null)
     * @method AttendanceLocationSetting|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method AttendanceLocationSetting|null find($key, $default = null)
     * @method AttendanceLocationSetting[] all()
     */
    class _IH_AttendanceLocationSetting_C extends _BaseCollection {
        /**
         * @param int $size
         * @return AttendanceLocationSetting[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }
    
    /**
     * @method _IH_AttendanceLocationSetting_QB whereId($value)
     * @method _IH_AttendanceLocationSetting_QB whereLocationId($value)
     * @method _IH_AttendanceLocationSetting_QB whereAddress($value)
     * @method _IH_AttendanceLocationSetting_QB whereLatitude($value)
     * @method _IH_AttendanceLocationSetting_QB whereLongitude($value)
     * @method _IH_AttendanceLocationSetting_QB whereRadius($value)
     * @method _IH_AttendanceLocationSetting_QB whereCreatedBy($value)
     * @method _IH_AttendanceLocationSetting_QB whereUpdatedBy($value)
     * @method _IH_AttendanceLocationSetting_QB whereCreatedAt($value)
     * @method _IH_AttendanceLocationSetting_QB whereUpdatedAt($value)
     * @method _IH_AttendanceLocationSetting_QB whereWfh($value)
     * @method AttendanceLocationSetting baseSole(array|string $columns = ['*'])
     * @method AttendanceLocationSetting create(array $attributes = [])
     * @method _IH_AttendanceLocationSetting_C|AttendanceLocationSetting[] cursor()
     * @method AttendanceLocationSetting|null|_IH_AttendanceLocationSetting_C|AttendanceLocationSetting[] find($id, array|string $columns = ['*'])
     * @method _IH_AttendanceLocationSetting_C|AttendanceLocationSetting[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method AttendanceLocationSetting|_IH_AttendanceLocationSetting_C|AttendanceLocationSetting[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method AttendanceLocationSetting|_IH_AttendanceLocationSetting_C|AttendanceLocationSetting[] findOrFail($id, array|string $columns = ['*'])
     * @method AttendanceLocationSetting|_IH_AttendanceLocationSetting_C|AttendanceLocationSetting[] findOrNew($id, array|string $columns = ['*'])
     * @method AttendanceLocationSetting first(array|string $columns = ['*'])
     * @method AttendanceLocationSetting firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method AttendanceLocationSetting firstOrCreate(array $attributes = [], array $values = [])
     * @method AttendanceLocationSetting firstOrFail(array|string $columns = ['*'])
     * @method AttendanceLocationSetting firstOrNew(array $attributes = [], array $values = [])
     * @method AttendanceLocationSetting firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method AttendanceLocationSetting forceCreate(array $attributes)
     * @method _IH_AttendanceLocationSetting_C|AttendanceLocationSetting[] fromQuery(string $query, array $bindings = [])
     * @method _IH_AttendanceLocationSetting_C|AttendanceLocationSetting[] get(array|string $columns = ['*'])
     * @method AttendanceLocationSetting getModel()
     * @method AttendanceLocationSetting[] getModels(array|string $columns = ['*'])
     * @method _IH_AttendanceLocationSetting_C|AttendanceLocationSetting[] hydrate(array $items)
     * @method AttendanceLocationSetting make(array $attributes = [])
     * @method AttendanceLocationSetting newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|AttendanceLocationSetting[]|_IH_AttendanceLocationSetting_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Paginator|AttendanceLocationSetting[]|_IH_AttendanceLocationSetting_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method AttendanceLocationSetting sole(array|string $columns = ['*'])
     * @method AttendanceLocationSetting updateOrCreate(array $attributes, array $values = [])
     */
    class _IH_AttendanceLocationSetting_QB extends _BaseBuilder {}
    
    /**
     * @method AttendanceMachineSetting|null getOrPut($key, $value)
     * @method AttendanceMachineSetting|$this shift(int $count = 1)
     * @method AttendanceMachineSetting|null firstOrFail(callable|string $key = null, $operator = null, $value = null)
     * @method AttendanceMachineSetting|$this pop(int $count = 1)
     * @method AttendanceMachineSetting|null pull($key, \Closure $default = null)
     * @method AttendanceMachineSetting|null last(callable $callback = null, \Closure $default = null)
     * @method AttendanceMachineSetting|$this random(callable|int|null $number = null)
     * @method AttendanceMachineSetting|null sole(callable|string $key = null, $operator = null, $value = null)
     * @method AttendanceMachineSetting|null get($key, \Closure $default = null)
     * @method AttendanceMachineSetting|null first(callable $callback = null, \Closure $default = null)
     * @method AttendanceMachineSetting|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method AttendanceMachineSetting|null find($key, $default = null)
     * @method AttendanceMachineSetting[] all()
     */
    class _IH_AttendanceMachineSetting_C extends _BaseCollection {
        /**
         * @param int $size
         * @return AttendanceMachineSetting[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }
    
    /**
     * @method _IH_AttendanceMachineSetting_QB whereId($value)
     * @method _IH_AttendanceMachineSetting_QB whereLocationId($value)
     * @method _IH_AttendanceMachineSetting_QB whereSerialNumber($value)
     * @method _IH_AttendanceMachineSetting_QB whereName($value)
     * @method _IH_AttendanceMachineSetting_QB whereIpAddress($value)
     * @method _IH_AttendanceMachineSetting_QB whereAddress($value)
     * @method _IH_AttendanceMachineSetting_QB whereDescription($value)
     * @method _IH_AttendanceMachineSetting_QB whereStatus($value)
     * @method _IH_AttendanceMachineSetting_QB whereCreatedAt($value)
     * @method _IH_AttendanceMachineSetting_QB whereUpdatedAt($value)
     * @method AttendanceMachineSetting baseSole(array|string $columns = ['*'])
     * @method AttendanceMachineSetting create(array $attributes = [])
     * @method _IH_AttendanceMachineSetting_C|AttendanceMachineSetting[] cursor()
     * @method AttendanceMachineSetting|null|_IH_AttendanceMachineSetting_C|AttendanceMachineSetting[] find($id, array|string $columns = ['*'])
     * @method _IH_AttendanceMachineSetting_C|AttendanceMachineSetting[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method AttendanceMachineSetting|_IH_AttendanceMachineSetting_C|AttendanceMachineSetting[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method AttendanceMachineSetting|_IH_AttendanceMachineSetting_C|AttendanceMachineSetting[] findOrFail($id, array|string $columns = ['*'])
     * @method AttendanceMachineSetting|_IH_AttendanceMachineSetting_C|AttendanceMachineSetting[] findOrNew($id, array|string $columns = ['*'])
     * @method AttendanceMachineSetting first(array|string $columns = ['*'])
     * @method AttendanceMachineSetting firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method AttendanceMachineSetting firstOrCreate(array $attributes = [], array $values = [])
     * @method AttendanceMachineSetting firstOrFail(array|string $columns = ['*'])
     * @method AttendanceMachineSetting firstOrNew(array $attributes = [], array $values = [])
     * @method AttendanceMachineSetting firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method AttendanceMachineSetting forceCreate(array $attributes)
     * @method _IH_AttendanceMachineSetting_C|AttendanceMachineSetting[] fromQuery(string $query, array $bindings = [])
     * @method _IH_AttendanceMachineSetting_C|AttendanceMachineSetting[] get(array|string $columns = ['*'])
     * @method AttendanceMachineSetting getModel()
     * @method AttendanceMachineSetting[] getModels(array|string $columns = ['*'])
     * @method _IH_AttendanceMachineSetting_C|AttendanceMachineSetting[] hydrate(array $items)
     * @method AttendanceMachineSetting make(array $attributes = [])
     * @method AttendanceMachineSetting newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|AttendanceMachineSetting[]|_IH_AttendanceMachineSetting_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Paginator|AttendanceMachineSetting[]|_IH_AttendanceMachineSetting_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method AttendanceMachineSetting sole(array|string $columns = ['*'])
     * @method AttendanceMachineSetting updateOrCreate(array $attributes, array $values = [])
     */
    class _IH_AttendanceMachineSetting_QB extends _BaseBuilder {}
    
    /**
     * @method AttendanceOvertime|null getOrPut($key, $value)
     * @method AttendanceOvertime|$this shift(int $count = 1)
     * @method AttendanceOvertime|null firstOrFail(callable|string $key = null, $operator = null, $value = null)
     * @method AttendanceOvertime|$this pop(int $count = 1)
     * @method AttendanceOvertime|null pull($key, \Closure $default = null)
     * @method AttendanceOvertime|null last(callable $callback = null, \Closure $default = null)
     * @method AttendanceOvertime|$this random(callable|int|null $number = null)
     * @method AttendanceOvertime|null sole(callable|string $key = null, $operator = null, $value = null)
     * @method AttendanceOvertime|null get($key, \Closure $default = null)
     * @method AttendanceOvertime|null first(callable $callback = null, \Closure $default = null)
     * @method AttendanceOvertime|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method AttendanceOvertime|null find($key, $default = null)
     * @method AttendanceOvertime[] all()
     */
    class _IH_AttendanceOvertime_C extends _BaseCollection {
        /**
         * @param int $size
         * @return AttendanceOvertime[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }
    
    /**
     * @method _IH_AttendanceOvertime_QB whereId($value)
     * @method _IH_AttendanceOvertime_QB whereEmployeeId($value)
     * @method _IH_AttendanceOvertime_QB whereNumber($value)
     * @method _IH_AttendanceOvertime_QB whereStartDate($value)
     * @method _IH_AttendanceOvertime_QB whereEndDate($value)
     * @method _IH_AttendanceOvertime_QB whereStartTime($value)
     * @method _IH_AttendanceOvertime_QB whereEndTime($value)
     * @method _IH_AttendanceOvertime_QB whereDuration($value)
     * @method _IH_AttendanceOvertime_QB whereDescription($value)
     * @method _IH_AttendanceOvertime_QB whereFilename($value)
     * @method _IH_AttendanceOvertime_QB whereApprovedBy($value)
     * @method _IH_AttendanceOvertime_QB whereApprovedStatus($value)
     * @method _IH_AttendanceOvertime_QB whereApprovedDate($value)
     * @method _IH_AttendanceOvertime_QB whereApprovedNote($value)
     * @method _IH_AttendanceOvertime_QB whereCreatedBy($value)
     * @method _IH_AttendanceOvertime_QB whereUpdatedBy($value)
     * @method _IH_AttendanceOvertime_QB whereCreatedAt($value)
     * @method _IH_AttendanceOvertime_QB whereUpdatedAt($value)
     * @method AttendanceOvertime baseSole(array|string $columns = ['*'])
     * @method AttendanceOvertime create(array $attributes = [])
     * @method _IH_AttendanceOvertime_C|AttendanceOvertime[] cursor()
     * @method AttendanceOvertime|null|_IH_AttendanceOvertime_C|AttendanceOvertime[] find($id, array|string $columns = ['*'])
     * @method _IH_AttendanceOvertime_C|AttendanceOvertime[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method AttendanceOvertime|_IH_AttendanceOvertime_C|AttendanceOvertime[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method AttendanceOvertime|_IH_AttendanceOvertime_C|AttendanceOvertime[] findOrFail($id, array|string $columns = ['*'])
     * @method AttendanceOvertime|_IH_AttendanceOvertime_C|AttendanceOvertime[] findOrNew($id, array|string $columns = ['*'])
     * @method AttendanceOvertime first(array|string $columns = ['*'])
     * @method AttendanceOvertime firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method AttendanceOvertime firstOrCreate(array $attributes = [], array $values = [])
     * @method AttendanceOvertime firstOrFail(array|string $columns = ['*'])
     * @method AttendanceOvertime firstOrNew(array $attributes = [], array $values = [])
     * @method AttendanceOvertime firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method AttendanceOvertime forceCreate(array $attributes)
     * @method _IH_AttendanceOvertime_C|AttendanceOvertime[] fromQuery(string $query, array $bindings = [])
     * @method _IH_AttendanceOvertime_C|AttendanceOvertime[] get(array|string $columns = ['*'])
     * @method AttendanceOvertime getModel()
     * @method AttendanceOvertime[] getModels(array|string $columns = ['*'])
     * @method _IH_AttendanceOvertime_C|AttendanceOvertime[] hydrate(array $items)
     * @method AttendanceOvertime make(array $attributes = [])
     * @method AttendanceOvertime newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|AttendanceOvertime[]|_IH_AttendanceOvertime_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Paginator|AttendanceOvertime[]|_IH_AttendanceOvertime_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method AttendanceOvertime sole(array|string $columns = ['*'])
     * @method AttendanceOvertime updateOrCreate(array $attributes, array $values = [])
     */
    class _IH_AttendanceOvertime_QB extends _BaseBuilder {}
    
    /**
     * @method AttendancePermission|null getOrPut($key, $value)
     * @method AttendancePermission|$this shift(int $count = 1)
     * @method AttendancePermission|null firstOrFail(callable|string $key = null, $operator = null, $value = null)
     * @method AttendancePermission|$this pop(int $count = 1)
     * @method AttendancePermission|null pull($key, \Closure $default = null)
     * @method AttendancePermission|null last(callable $callback = null, \Closure $default = null)
     * @method AttendancePermission|$this random(callable|int|null $number = null)
     * @method AttendancePermission|null sole(callable|string $key = null, $operator = null, $value = null)
     * @method AttendancePermission|null get($key, \Closure $default = null)
     * @method AttendancePermission|null first(callable $callback = null, \Closure $default = null)
     * @method AttendancePermission|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method AttendancePermission|null find($key, $default = null)
     * @method AttendancePermission[] all()
     */
    class _IH_AttendancePermission_C extends _BaseCollection {
        /**
         * @param int $size
         * @return AttendancePermission[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }
    
    /**
     * @method _IH_AttendancePermission_QB whereId($value)
     * @method _IH_AttendancePermission_QB whereNumber($value)
     * @method _IH_AttendancePermission_QB whereEmployeeId($value)
     * @method _IH_AttendancePermission_QB whereCategoryId($value)
     * @method _IH_AttendancePermission_QB whereStartDate($value)
     * @method _IH_AttendancePermission_QB whereEndDate($value)
     * @method _IH_AttendancePermission_QB whereDescription($value)
     * @method _IH_AttendancePermission_QB whereFilename($value)
     * @method _IH_AttendancePermission_QB whereApprovedBy($value)
     * @method _IH_AttendancePermission_QB whereApprovedStatus($value)
     * @method _IH_AttendancePermission_QB whereApprovedDate($value)
     * @method _IH_AttendancePermission_QB whereApprovedNote($value)
     * @method _IH_AttendancePermission_QB whereCreatedBy($value)
     * @method _IH_AttendancePermission_QB whereUpdatedBy($value)
     * @method _IH_AttendancePermission_QB whereCreatedAt($value)
     * @method _IH_AttendancePermission_QB whereUpdatedAt($value)
     * @method AttendancePermission baseSole(array|string $columns = ['*'])
     * @method AttendancePermission create(array $attributes = [])
     * @method _IH_AttendancePermission_C|AttendancePermission[] cursor()
     * @method AttendancePermission|null|_IH_AttendancePermission_C|AttendancePermission[] find($id, array|string $columns = ['*'])
     * @method _IH_AttendancePermission_C|AttendancePermission[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method AttendancePermission|_IH_AttendancePermission_C|AttendancePermission[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method AttendancePermission|_IH_AttendancePermission_C|AttendancePermission[] findOrFail($id, array|string $columns = ['*'])
     * @method AttendancePermission|_IH_AttendancePermission_C|AttendancePermission[] findOrNew($id, array|string $columns = ['*'])
     * @method AttendancePermission first(array|string $columns = ['*'])
     * @method AttendancePermission firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method AttendancePermission firstOrCreate(array $attributes = [], array $values = [])
     * @method AttendancePermission firstOrFail(array|string $columns = ['*'])
     * @method AttendancePermission firstOrNew(array $attributes = [], array $values = [])
     * @method AttendancePermission firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method AttendancePermission forceCreate(array $attributes)
     * @method _IH_AttendancePermission_C|AttendancePermission[] fromQuery(string $query, array $bindings = [])
     * @method _IH_AttendancePermission_C|AttendancePermission[] get(array|string $columns = ['*'])
     * @method AttendancePermission getModel()
     * @method AttendancePermission[] getModels(array|string $columns = ['*'])
     * @method _IH_AttendancePermission_C|AttendancePermission[] hydrate(array $items)
     * @method AttendancePermission make(array $attributes = [])
     * @method AttendancePermission newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|AttendancePermission[]|_IH_AttendancePermission_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Paginator|AttendancePermission[]|_IH_AttendancePermission_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method AttendancePermission sole(array|string $columns = ['*'])
     * @method AttendancePermission updateOrCreate(array $attributes, array $values = [])
     */
    class _IH_AttendancePermission_QB extends _BaseBuilder {}
    
    /**
     * @method AttendanceShift|null getOrPut($key, $value)
     * @method AttendanceShift|$this shift(int $count = 1)
     * @method AttendanceShift|null firstOrFail(callable|string $key = null, $operator = null, $value = null)
     * @method AttendanceShift|$this pop(int $count = 1)
     * @method AttendanceShift|null pull($key, \Closure $default = null)
     * @method AttendanceShift|null last(callable $callback = null, \Closure $default = null)
     * @method AttendanceShift|$this random(callable|int|null $number = null)
     * @method AttendanceShift|null sole(callable|string $key = null, $operator = null, $value = null)
     * @method AttendanceShift|null get($key, \Closure $default = null)
     * @method AttendanceShift|null first(callable $callback = null, \Closure $default = null)
     * @method AttendanceShift|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method AttendanceShift|null find($key, $default = null)
     * @method AttendanceShift[] all()
     */
    class _IH_AttendanceShift_C extends _BaseCollection {
        /**
         * @param int $size
         * @return AttendanceShift[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }
    
    /**
     * @method _IH_AttendanceShift_QB whereId($value)
     * @method _IH_AttendanceShift_QB whereLocationId($value)
     * @method _IH_AttendanceShift_QB whereName($value)
     * @method _IH_AttendanceShift_QB whereCode($value)
     * @method _IH_AttendanceShift_QB whereStart($value)
     * @method _IH_AttendanceShift_QB whereEnd($value)
     * @method _IH_AttendanceShift_QB whereNightShift($value)
     * @method _IH_AttendanceShift_QB whereDescription($value)
     * @method _IH_AttendanceShift_QB whereStatus($value)
     * @method _IH_AttendanceShift_QB whereCreatedBy($value)
     * @method _IH_AttendanceShift_QB whereUpdatedBy($value)
     * @method _IH_AttendanceShift_QB whereCreatedAt($value)
     * @method _IH_AttendanceShift_QB whereUpdatedAt($value)
     * @method AttendanceShift baseSole(array|string $columns = ['*'])
     * @method AttendanceShift create(array $attributes = [])
     * @method _IH_AttendanceShift_C|AttendanceShift[] cursor()
     * @method AttendanceShift|null|_IH_AttendanceShift_C|AttendanceShift[] find($id, array|string $columns = ['*'])
     * @method _IH_AttendanceShift_C|AttendanceShift[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method AttendanceShift|_IH_AttendanceShift_C|AttendanceShift[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method AttendanceShift|_IH_AttendanceShift_C|AttendanceShift[] findOrFail($id, array|string $columns = ['*'])
     * @method AttendanceShift|_IH_AttendanceShift_C|AttendanceShift[] findOrNew($id, array|string $columns = ['*'])
     * @method AttendanceShift first(array|string $columns = ['*'])
     * @method AttendanceShift firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method AttendanceShift firstOrCreate(array $attributes = [], array $values = [])
     * @method AttendanceShift firstOrFail(array|string $columns = ['*'])
     * @method AttendanceShift firstOrNew(array $attributes = [], array $values = [])
     * @method AttendanceShift firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method AttendanceShift forceCreate(array $attributes)
     * @method _IH_AttendanceShift_C|AttendanceShift[] fromQuery(string $query, array $bindings = [])
     * @method _IH_AttendanceShift_C|AttendanceShift[] get(array|string $columns = ['*'])
     * @method AttendanceShift getModel()
     * @method AttendanceShift[] getModels(array|string $columns = ['*'])
     * @method _IH_AttendanceShift_C|AttendanceShift[] hydrate(array $items)
     * @method AttendanceShift make(array $attributes = [])
     * @method AttendanceShift newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|AttendanceShift[]|_IH_AttendanceShift_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Paginator|AttendanceShift[]|_IH_AttendanceShift_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method AttendanceShift sole(array|string $columns = ['*'])
     * @method AttendanceShift updateOrCreate(array $attributes, array $values = [])
     */
    class _IH_AttendanceShift_QB extends _BaseBuilder {}
    
    /**
     * @method AttendanceWorkSchedule|null getOrPut($key, $value)
     * @method AttendanceWorkSchedule|$this shift(int $count = 1)
     * @method AttendanceWorkSchedule|null firstOrFail(callable|string $key = null, $operator = null, $value = null)
     * @method AttendanceWorkSchedule|$this pop(int $count = 1)
     * @method AttendanceWorkSchedule|null pull($key, \Closure $default = null)
     * @method AttendanceWorkSchedule|null last(callable $callback = null, \Closure $default = null)
     * @method AttendanceWorkSchedule|$this random(callable|int|null $number = null)
     * @method AttendanceWorkSchedule|null sole(callable|string $key = null, $operator = null, $value = null)
     * @method AttendanceWorkSchedule|null get($key, \Closure $default = null)
     * @method AttendanceWorkSchedule|null first(callable $callback = null, \Closure $default = null)
     * @method AttendanceWorkSchedule|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method AttendanceWorkSchedule|null find($key, $default = null)
     * @method AttendanceWorkSchedule[] all()
     */
    class _IH_AttendanceWorkSchedule_C extends _BaseCollection {
        /**
         * @param int $size
         * @return AttendanceWorkSchedule[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }
    
    /**
     * @method _IH_AttendanceWorkSchedule_QB whereId($value)
     * @method _IH_AttendanceWorkSchedule_QB whereEmployeeId($value)
     * @method _IH_AttendanceWorkSchedule_QB whereShiftId($value)
     * @method _IH_AttendanceWorkSchedule_QB whereLocationId($value)
     * @method _IH_AttendanceWorkSchedule_QB whereStartTime($value)
     * @method _IH_AttendanceWorkSchedule_QB whereEndTime($value)
     * @method _IH_AttendanceWorkSchedule_QB whereDescription($value)
     * @method _IH_AttendanceWorkSchedule_QB whereCreatedBy($value)
     * @method _IH_AttendanceWorkSchedule_QB whereUpdatedBy($value)
     * @method _IH_AttendanceWorkSchedule_QB whereCreatedAt($value)
     * @method _IH_AttendanceWorkSchedule_QB whereUpdatedAt($value)
     * @method AttendanceWorkSchedule baseSole(array|string $columns = ['*'])
     * @method AttendanceWorkSchedule create(array $attributes = [])
     * @method _IH_AttendanceWorkSchedule_C|AttendanceWorkSchedule[] cursor()
     * @method AttendanceWorkSchedule|null|_IH_AttendanceWorkSchedule_C|AttendanceWorkSchedule[] find($id, array|string $columns = ['*'])
     * @method _IH_AttendanceWorkSchedule_C|AttendanceWorkSchedule[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method AttendanceWorkSchedule|_IH_AttendanceWorkSchedule_C|AttendanceWorkSchedule[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method AttendanceWorkSchedule|_IH_AttendanceWorkSchedule_C|AttendanceWorkSchedule[] findOrFail($id, array|string $columns = ['*'])
     * @method AttendanceWorkSchedule|_IH_AttendanceWorkSchedule_C|AttendanceWorkSchedule[] findOrNew($id, array|string $columns = ['*'])
     * @method AttendanceWorkSchedule first(array|string $columns = ['*'])
     * @method AttendanceWorkSchedule firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method AttendanceWorkSchedule firstOrCreate(array $attributes = [], array $values = [])
     * @method AttendanceWorkSchedule firstOrFail(array|string $columns = ['*'])
     * @method AttendanceWorkSchedule firstOrNew(array $attributes = [], array $values = [])
     * @method AttendanceWorkSchedule firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method AttendanceWorkSchedule forceCreate(array $attributes)
     * @method _IH_AttendanceWorkSchedule_C|AttendanceWorkSchedule[] fromQuery(string $query, array $bindings = [])
     * @method _IH_AttendanceWorkSchedule_C|AttendanceWorkSchedule[] get(array|string $columns = ['*'])
     * @method AttendanceWorkSchedule getModel()
     * @method AttendanceWorkSchedule[] getModels(array|string $columns = ['*'])
     * @method _IH_AttendanceWorkSchedule_C|AttendanceWorkSchedule[] hydrate(array $items)
     * @method AttendanceWorkSchedule make(array $attributes = [])
     * @method AttendanceWorkSchedule newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|AttendanceWorkSchedule[]|_IH_AttendanceWorkSchedule_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Paginator|AttendanceWorkSchedule[]|_IH_AttendanceWorkSchedule_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method AttendanceWorkSchedule sole(array|string $columns = ['*'])
     * @method AttendanceWorkSchedule updateOrCreate(array $attributes, array $values = [])
     */
    class _IH_AttendanceWorkSchedule_QB extends _BaseBuilder {}
    
    /**
     * @method Attendance|null getOrPut($key, $value)
     * @method Attendance|$this shift(int $count = 1)
     * @method Attendance|null firstOrFail(callable|string $key = null, $operator = null, $value = null)
     * @method Attendance|$this pop(int $count = 1)
     * @method Attendance|null pull($key, \Closure $default = null)
     * @method Attendance|null last(callable $callback = null, \Closure $default = null)
     * @method Attendance|$this random(callable|int|null $number = null)
     * @method Attendance|null sole(callable|string $key = null, $operator = null, $value = null)
     * @method Attendance|null get($key, \Closure $default = null)
     * @method Attendance|null first(callable $callback = null, \Closure $default = null)
     * @method Attendance|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method Attendance|null find($key, $default = null)
     * @method Attendance[] all()
     */
    class _IH_Attendance_C extends _BaseCollection {
        /**
         * @param int $size
         * @return Attendance[][]
         */
        public function chunk($size)
        {
            return [];
        }
    }
    
    /**
     * @method _IH_Attendance_QB whereId($value)
     * @method _IH_Attendance_QB whereEmployeeId($value)
     * @method _IH_Attendance_QB whereLocationId($value)
     * @method _IH_Attendance_QB whereType($value)
     * @method _IH_Attendance_QB whereStartDate($value)
     * @method _IH_Attendance_QB whereStartTime($value)
     * @method _IH_Attendance_QB whereStartLatitude($value)
     * @method _IH_Attendance_QB whereStartLongitude($value)
     * @method _IH_Attendance_QB whereStartImage($value)
     * @method _IH_Attendance_QB whereStartAddress($value)
     * @method _IH_Attendance_QB whereEndDate($value)
     * @method _IH_Attendance_QB whereEndTime($value)
     * @method _IH_Attendance_QB whereEndLatitude($value)
     * @method _IH_Attendance_QB whereEndLongitude($value)
     * @method _IH_Attendance_QB whereEndImage($value)
     * @method _IH_Attendance_QB whereEndAddress($value)
     * @method _IH_Attendance_QB whereDuration($value)
     * @method _IH_Attendance_QB whereDescription($value)
     * @method _IH_Attendance_QB whereCreatedBy($value)
     * @method _IH_Attendance_QB whereUpdatedBy($value)
     * @method _IH_Attendance_QB whereCreatedAt($value)
     * @method _IH_Attendance_QB whereUpdatedAt($value)
     * @method _IH_Attendance_QB whereTypeId($value)
     * @method Attendance baseSole(array|string $columns = ['*'])
     * @method Attendance create(array $attributes = [])
     * @method _IH_Attendance_C|Attendance[] cursor()
     * @method Attendance|null|_IH_Attendance_C|Attendance[] find($id, array|string $columns = ['*'])
     * @method _IH_Attendance_C|Attendance[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method Attendance|_IH_Attendance_C|Attendance[] findOr($id, array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method Attendance|_IH_Attendance_C|Attendance[] findOrFail($id, array|string $columns = ['*'])
     * @method Attendance|_IH_Attendance_C|Attendance[] findOrNew($id, array|string $columns = ['*'])
     * @method Attendance first(array|string $columns = ['*'])
     * @method Attendance firstOr(array|\Closure|string $columns = ['*'], \Closure $callback = null)
     * @method Attendance firstOrCreate(array $attributes = [], array $values = [])
     * @method Attendance firstOrFail(array|string $columns = ['*'])
     * @method Attendance firstOrNew(array $attributes = [], array $values = [])
     * @method Attendance firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method Attendance forceCreate(array $attributes)
     * @method _IH_Attendance_C|Attendance[] fromQuery(string $query, array $bindings = [])
     * @method _IH_Attendance_C|Attendance[] get(array|string $columns = ['*'])
     * @method Attendance getModel()
     * @method Attendance[] getModels(array|string $columns = ['*'])
     * @method _IH_Attendance_C|Attendance[] hydrate(array $items)
     * @method Attendance make(array $attributes = [])
     * @method Attendance newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|Attendance[]|_IH_Attendance_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Paginator|Attendance[]|_IH_Attendance_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method Attendance sole(array|string $columns = ['*'])
     * @method Attendance updateOrCreate(array $attributes, array $values = [])
     */
    class _IH_Attendance_QB extends _BaseBuilder {}
}