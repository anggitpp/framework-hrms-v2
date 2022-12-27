<?php //bfc6ed111586070f1c4bccfb3ead11b5
/** @noinspection all */

namespace App\Models\ESS {

    use App\Models\Employee\Employee;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Relations\BelongsTo;
    use Illuminate\Support\Carbon;
    use LaravelIdea\Helper\App\Models\Employee\_IH_Employee_QB;
    use LaravelIdea\Helper\App\Models\ESS\_IH_EssTimesheet_C;
    use LaravelIdea\Helper\App\Models\ESS\_IH_EssTimesheet_QB;
    
    /**
     * @property int $id
     * @property int $employee_id
     * @property string $activity
     * @property string $output
     * @property Carbon $date
     * @property Carbon $start_time
     * @property Carbon $end_time
     * @property Carbon $duration
     * @property string $volume
     * @property string $type
     * @property string|null $description
     * @property string|null $created_by
     * @property string|null $updated_by
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @property Employee $employee
     * @method BelongsTo|_IH_Employee_QB employee()
     * @method static _IH_EssTimesheet_QB onWriteConnection()
     * @method _IH_EssTimesheet_QB newQuery()
     * @method static _IH_EssTimesheet_QB on(null|string $connection = null)
     * @method static _IH_EssTimesheet_QB query()
     * @method static _IH_EssTimesheet_QB with(array|string $relations)
     * @method _IH_EssTimesheet_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_EssTimesheet_C|EssTimesheet[] all()
     * @ownLinks employee_id,\App\Models\Employee\Employee,id
     * @mixin _IH_EssTimesheet_QB
     */
    class EssTimesheet extends Model {}
}