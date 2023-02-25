<?php //9daad373bc1810cd4a201bc1de247ed7
/** @noinspection all */

namespace App\Models\Payroll {

    use App\Models\Employee\Employee;
    use App\Models\Employee\EmployeePosition;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Relations\BelongsTo;
    use Illuminate\Support\Carbon;
    use LaravelIdea\Helper\App\Models\Employee\_IH_EmployeePosition_QB;
    use LaravelIdea\Helper\App\Models\Employee\_IH_Employee_QB;
    use LaravelIdea\Helper\App\Models\Payroll\_IH_PayrollComponentProcessDetail_C;
    use LaravelIdea\Helper\App\Models\Payroll\_IH_PayrollComponentProcessDetail_QB;
    use LaravelIdea\Helper\App\Models\Payroll\_IH_PayrollComponentProcess_C;
    use LaravelIdea\Helper\App\Models\Payroll\_IH_PayrollComponentProcess_QB;
    use LaravelIdea\Helper\App\Models\Payroll\_IH_PayrollComponent_C;
    use LaravelIdea\Helper\App\Models\Payroll\_IH_PayrollComponent_QB;
    use LaravelIdea\Helper\App\Models\Payroll\_IH_PayrollFixed_C;
    use LaravelIdea\Helper\App\Models\Payroll\_IH_PayrollFixed_QB;
    use LaravelIdea\Helper\App\Models\Payroll\_IH_PayrollMaster_C;
    use LaravelIdea\Helper\App\Models\Payroll\_IH_PayrollMaster_QB;
    use LaravelIdea\Helper\App\Models\Payroll\_IH_PayrollSetting_C;
    use LaravelIdea\Helper\App\Models\Payroll\_IH_PayrollSetting_QB;
    use LaravelIdea\Helper\App\Models\Payroll\_IH_PayrollUpload_C;
    use LaravelIdea\Helper\App\Models\Payroll\_IH_PayrollUpload_QB;
    
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
     * @property int $payroll_master_id
     * @property int $location_id
     * @property string $code
     * @property string $month
     * @property string $year
     * @property int $component_id
     * @property string $total_amount
     * @property int $total_employee
     * @property string|null $status
     * @property string|null $approved_status
     * @property string|null $approved_by
     * @property Carbon|null $approved_at
     * @property string|null $approved_note
     * @property string|null $created_by
     * @property string|null $updated_by
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @method static _IH_PayrollComponentProcess_QB onWriteConnection()
     * @method _IH_PayrollComponentProcess_QB newQuery()
     * @method static _IH_PayrollComponentProcess_QB on(null|string $connection = null)
     * @method static _IH_PayrollComponentProcess_QB query()
     * @method static _IH_PayrollComponentProcess_QB with(array|string $relations)
     * @method _IH_PayrollComponentProcess_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_PayrollComponentProcess_C|PayrollComponentProcess[] all()
     * @ownLinks payroll_master_id,\App\Models\Payroll\PayrollMaster,id
     * @foreignLinks id,\App\Models\Payroll\PayrollComponentProcessDetail,payroll_component_process_id
     * @mixin _IH_PayrollComponentProcess_QB
     */
    class PayrollComponentProcess extends Model {}
    
    /**
     * @property int $id
     * @property int $payroll_component_process_id
     * @property int $employee_id
     * @property string $amount
     * @property string|null $note
     * @property string|null $created_by
     * @property string|null $updated_by
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @method static _IH_PayrollComponentProcessDetail_QB onWriteConnection()
     * @method _IH_PayrollComponentProcessDetail_QB newQuery()
     * @method static _IH_PayrollComponentProcessDetail_QB on(null|string $connection = null)
     * @method static _IH_PayrollComponentProcessDetail_QB query()
     * @method static _IH_PayrollComponentProcessDetail_QB with(array|string $relations)
     * @method _IH_PayrollComponentProcessDetail_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_PayrollComponentProcessDetail_C|PayrollComponentProcessDetail[] all()
     * @ownLinks payroll_component_process_id,\App\Models\Payroll\PayrollComponentProcess,id|employee_id,\App\Models\Employee\Employee,id
     * @mixin _IH_PayrollComponentProcessDetail_QB
     */
    class PayrollComponentProcessDetail extends Model {}
    
    /**
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
     * @foreignLinks id,\App\Models\Payroll\PayrollComponentProcess,payroll_master_id
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
    
    /**
     * @property int $id
     * @property string $code
     * @property string $month
     * @property string $year
     * @property int $component_id
     * @property int $employee_id
     * @property string $amount
     * @property string|null $description
     * @property string|null $created_by
     * @property string|null $updated_by
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @property PayrollComponent $component
     * @method BelongsTo|_IH_PayrollComponent_QB component()
     * @property Employee $employee
     * @method BelongsTo|_IH_Employee_QB employee()
     * @property EmployeePosition $position
     * @method BelongsTo|_IH_EmployeePosition_QB position()
     * @method static _IH_PayrollUpload_QB onWriteConnection()
     * @method _IH_PayrollUpload_QB newQuery()
     * @method static _IH_PayrollUpload_QB on(null|string $connection = null)
     * @method static _IH_PayrollUpload_QB query()
     * @method static _IH_PayrollUpload_QB with(array|string $relations)
     * @method _IH_PayrollUpload_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_PayrollUpload_C|PayrollUpload[] all()
     * @ownLinks employee_id,\App\Models\Employee\Employee,id
     * @mixin _IH_PayrollUpload_QB
     */
    class PayrollUpload extends Model {}
}