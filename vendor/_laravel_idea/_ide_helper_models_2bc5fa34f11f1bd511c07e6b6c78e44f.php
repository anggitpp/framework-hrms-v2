<?php //4b59f1e2fecedb55d381efd5fd03f0fe
/** @noinspection all */

namespace App\Models\Payroll {

    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Support\Carbon;
    use LaravelIdea\Helper\App\Models\Payroll\_IH_PayrollComponent_C;
    use LaravelIdea\Helper\App\Models\Payroll\_IH_PayrollComponent_QB;
    use LaravelIdea\Helper\App\Models\Payroll\_IH_PayrollFixed_C;
    use LaravelIdea\Helper\App\Models\Payroll\_IH_PayrollFixed_QB;
    use LaravelIdea\Helper\App\Models\Payroll\_IH_PayrollMaster_C;
    use LaravelIdea\Helper\App\Models\Payroll\_IH_PayrollMaster_QB;
    use LaravelIdea\Helper\App\Models\Payroll\_IH_PayrollSetting_C;
    use LaravelIdea\Helper\App\Models\Payroll\_IH_PayrollSetting_QB;
    
    /**
     * @property int $id
     * @property int $master_id
     * @property string $type
     * @property string $code
     * @property string $name
     * @property string|null $description
     * @property string $status
     * @property string $calculation_type
     * @property string $calculation_cut_off
     * @property string|null $calculation_cut_off_date_start
     * @property string|null $calculation_cut_off_date_end
     * @property string|null $calculation_description
     * @property string|null $calculation_amount
     * @property string|null $calculation_amount_min
     * @property string|null $calculation_amount_max
     * @property string|null $created_by
     * @property string|null $updated_by
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @method static _IH_PayrollComponent_QB onWriteConnection()
     * @method _IH_PayrollComponent_QB newQuery()
     * @method static _IH_PayrollComponent_QB on(null|string $connection = null)
     * @method static _IH_PayrollComponent_QB query()
     * @method static _IH_PayrollComponent_QB with(array|string $relations)
     * @method _IH_PayrollComponent_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_PayrollComponent_C|PayrollComponent[] all()
     * @mixin _IH_PayrollComponent_QB
     */
    class PayrollComponent extends Model {}
    
    /**
     * @property int $id
     * @property string $code
     * @property string $name
     * @property string $amount
     * @property string|null $description
     * @property string $status
     * @property string|null $created_by
     * @property string|null $updated_by
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @method static _IH_PayrollFixed_QB onWriteConnection()
     * @method _IH_PayrollFixed_QB newQuery()
     * @method static _IH_PayrollFixed_QB on(null|string $connection = null)
     * @method static _IH_PayrollFixed_QB query()
     * @method static _IH_PayrollFixed_QB with(array|string $relations)
     * @method _IH_PayrollFixed_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_PayrollFixed_C|PayrollFixed[] all()
     * @mixin _IH_PayrollFixed_QB
     */
    class PayrollFixed extends Model {}
    
    /**
     * @property int $id
     * @property string $code
     * @property string $name
     * @property string|null $description
     * @property string $status
     * @property string|null $created_by
     * @property string|null $updated_by
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @method static _IH_PayrollMaster_QB onWriteConnection()
     * @method _IH_PayrollMaster_QB newQuery()
     * @method static _IH_PayrollMaster_QB on(null|string $connection = null)
     * @method static _IH_PayrollMaster_QB query()
     * @method static _IH_PayrollMaster_QB with(array|string $relations)
     * @method _IH_PayrollMaster_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_PayrollMaster_C|PayrollMaster[] all()
     * @mixin _IH_PayrollMaster_QB
     */
    class PayrollMaster extends Model {}
    
    /**
     * @property int $id
     * @property string $code
     * @property string $reference_field
     * @property int $reference_id
     * @property int $amount
     * @property string|null $created_by
     * @property string|null $updated_by
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @method static _IH_PayrollSetting_QB onWriteConnection()
     * @method _IH_PayrollSetting_QB newQuery()
     * @method static _IH_PayrollSetting_QB on(null|string $connection = null)
     * @method static _IH_PayrollSetting_QB query()
     * @method static _IH_PayrollSetting_QB with(array|string $relations)
     * @method _IH_PayrollSetting_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_PayrollSetting_C|PayrollSetting[] all()
     * @mixin _IH_PayrollSetting_QB
     */
    class PayrollSetting extends Model {}
}