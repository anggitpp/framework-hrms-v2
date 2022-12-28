<?php //a93330b2cc5d4c9fa636076f367dec26
/** @noinspection all */

namespace App\Models\Employee {

    use App\Models\Setting\AppMasterData;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Relations\BelongsTo;
    use Illuminate\Database\Eloquent\Relations\HasMany;
    use Illuminate\Database\Eloquent\Relations\HasOne;
    use Illuminate\Support\Carbon;
    use LaravelIdea\Helper\App\Models\Employee\_IH_EmployeeEducation_C;
    use LaravelIdea\Helper\App\Models\Employee\_IH_EmployeeEducation_QB;
    use LaravelIdea\Helper\App\Models\Employee\_IH_EmployeeFamily_C;
    use LaravelIdea\Helper\App\Models\Employee\_IH_EmployeeFamily_QB;
    use LaravelIdea\Helper\App\Models\Employee\_IH_EmployeePosition_C;
    use LaravelIdea\Helper\App\Models\Employee\_IH_EmployeePosition_QB;
    use LaravelIdea\Helper\App\Models\Employee\_IH_EmployeeSignatureSetting_C;
    use LaravelIdea\Helper\App\Models\Employee\_IH_EmployeeSignatureSetting_QB;
    use LaravelIdea\Helper\App\Models\Employee\_IH_EmployeeUnitStructure_C;
    use LaravelIdea\Helper\App\Models\Employee\_IH_EmployeeUnitStructure_QB;
    use LaravelIdea\Helper\App\Models\Employee\_IH_Employee_C;
    use LaravelIdea\Helper\App\Models\Employee\_IH_Employee_QB;
    use LaravelIdea\Helper\App\Models\Setting\_IH_AppMasterData_QB;
    
    /**
     * @property int $id
     * @property string $name
     * @property string $employee_number
     * @property string|null $nickname
     * @property string|null $place_of_birth
     * @property Carbon|null $date_of_birth
     * @property string|null $identity_number
     * @property string|null $address
     * @property string|null $identity_address
     * @property int|null $marital_status_id
     * @property string|null $phone_number
     * @property Carbon $join_date
     * @property Carbon|null $leave_date
     * @property string|null $photo
     * @property string|null $identity_file
     * @property string|null $email
     * @property string|null $mobile_phone_number
     * @property int $status_id
     * @property string|null $created_by
     * @property string|null $updated_by
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @property string|null $gender
     * @property string|null $attendance_pin
     * @property EmployeePosition $position
     * @method HasOne|_IH_EmployeePosition_QB position()
     * @property _IH_EmployeePosition_C|EmployeePosition[] $positions
     * @property-read int $positions_count
     * @method HasMany|_IH_EmployeePosition_QB positions()
     * @method static _IH_Employee_QB onWriteConnection()
     * @method _IH_Employee_QB newQuery()
     * @method static _IH_Employee_QB on(null|string $connection = null)
     * @method static _IH_Employee_QB query()
     * @method static _IH_Employee_QB with(array|string $relations)
     * @method _IH_Employee_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_Employee_C|Employee[] all()
     * @foreignLinks id,\App\Models\Setting\User,employee_id|id,\App\Models\Employee\EmployeePosition,employee_id|id,\App\Models\Attendance\Attendance,employee_id|id,\App\Models\Attendance\AttendanceWorkSchedule,employee_id|id,\App\Models\ESS\EssTimesheet,employee_id|id,\App\Models\Attendance\AttendanceLeave,employee_id|id,\App\Models\Attendance\AttendancePermission,employee_id|id,\App\Models\Attendance\AttendanceOvertime,employee_id|id,\App\Models\Attendance\AttendanceCorrection,employee_id|id,\App\Models\Employee\EmployeeSignatureSetting,employee_id|id,\App\Models\Employee\EmployeeFamily,employee_id|id,\App\Models\Employee\EmployeeEducation,employee_id
     * @mixin _IH_Employee_QB
     */
    class Employee extends Model {}
    
    /**
     * @property int $id
     * @property int $employee_id
     * @property int $level_id
     * @property string $name
     * @property string|null $major
     * @property string|null $essay
     * @property string|null $city
     * @property string|null $score
     * @property string|null $start_year
     * @property string|null $end_year
     * @property string|null $description
     * @property string|null $filename
     * @property string|null $created_by
     * @property string|null $updated_by
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @property Employee $employee
     * @method BelongsTo|_IH_Employee_QB employee()
     * @property AppMasterData $level
     * @method BelongsTo|_IH_AppMasterData_QB level()
     * @method static _IH_EmployeeEducation_QB onWriteConnection()
     * @method _IH_EmployeeEducation_QB newQuery()
     * @method static _IH_EmployeeEducation_QB on(null|string $connection = null)
     * @method static _IH_EmployeeEducation_QB query()
     * @method static _IH_EmployeeEducation_QB with(array|string $relations)
     * @method _IH_EmployeeEducation_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_EmployeeEducation_C|EmployeeEducation[] all()
     * @ownLinks employee_id,\App\Models\Employee\Employee,id
     * @mixin _IH_EmployeeEducation_QB
     */
    class EmployeeEducation extends Model {}
    
    /**
     * @property int $id
     * @property int $employee_id
     * @property int $relationship_id
     * @property string $name
     * @property string|null $identity_number
     * @property string $gender
     * @property Carbon|null $birth_date
     * @property string|null $birth_place
     * @property string|null $filename
     * @property string|null $description
     * @property string|null $created_by
     * @property string|null $updated_by
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @property Employee $employee
     * @method BelongsTo|_IH_Employee_QB employee()
     * @property AppMasterData $relationship
     * @method BelongsTo|_IH_AppMasterData_QB relationship()
     * @method static _IH_EmployeeFamily_QB onWriteConnection()
     * @method _IH_EmployeeFamily_QB newQuery()
     * @method static _IH_EmployeeFamily_QB on(null|string $connection = null)
     * @method static _IH_EmployeeFamily_QB query()
     * @method static _IH_EmployeeFamily_QB with(array|string $relations)
     * @method _IH_EmployeeFamily_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_EmployeeFamily_C|EmployeeFamily[] all()
     * @ownLinks employee_id,\App\Models\Employee\Employee,id
     * @mixin _IH_EmployeeFamily_QB
     */
    class EmployeeFamily extends Model {}
    
    /**
     * @property int $id
     * @property int $employee_id
     * @property int $employee_type_id
     * @property int $position_id
     * @property int $rank_id
     * @property int|null $grade_id
     * @property Carbon|null $sk_date
     * @property string|null $sk_number
     * @property Carbon $start_date
     * @property Carbon|null $end_date
     * @property int|null $unit_id
     * @property int $location_id
     * @property int|null $shift_id
     * @property string $status
     * @property string|null $created_by
     * @property string|null $updated_by
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @property int|null $leader_id
     * @property Employee $employee
     * @method BelongsTo|_IH_Employee_QB employee()
     * @method static _IH_EmployeePosition_QB onWriteConnection()
     * @method _IH_EmployeePosition_QB newQuery()
     * @method static _IH_EmployeePosition_QB on(null|string $connection = null)
     * @method static _IH_EmployeePosition_QB query()
     * @method static _IH_EmployeePosition_QB with(array|string $relations)
     * @method _IH_EmployeePosition_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_EmployeePosition_C|EmployeePosition[] all()
     * @ownLinks employee_id,\App\Models\Employee\Employee,id
     * @mixin _IH_EmployeePosition_QB
     */
    class EmployeePosition extends Model {}
    
    /**
     * @property int $id
     * @property int|null $location_id
     * @property int $employee_id
     * @property string|null $description
     * @property string $status
     * @property string|null $created_by
     * @property string|null $updated_by
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @property Employee $employee
     * @method BelongsTo|_IH_Employee_QB employee()
     * @property AppMasterData|null $location
     * @method BelongsTo|_IH_AppMasterData_QB location()
     * @method static _IH_EmployeeSignatureSetting_QB onWriteConnection()
     * @method _IH_EmployeeSignatureSetting_QB newQuery()
     * @method static _IH_EmployeeSignatureSetting_QB on(null|string $connection = null)
     * @method static _IH_EmployeeSignatureSetting_QB query()
     * @method static _IH_EmployeeSignatureSetting_QB with(array|string $relations)
     * @method _IH_EmployeeSignatureSetting_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_EmployeeSignatureSetting_C|EmployeeSignatureSetting[] all()
     * @ownLinks employee_id,\App\Models\Employee\Employee,id
     * @mixin _IH_EmployeeSignatureSetting_QB
     */
    class EmployeeSignatureSetting extends Model {}
    
    /**
     * @property int $id
     * @property int $unit_id
     * @property int $leader_id
     * @property int|null $administration_id
     * @property string|null $created_by
     * @property string|null $updated_by
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @property Employee|null $administration
     * @method BelongsTo|_IH_Employee_QB administration()
     * @property Employee $leader
     * @method BelongsTo|_IH_Employee_QB leader()
     * @property AppMasterData $unit
     * @method BelongsTo|_IH_AppMasterData_QB unit()
     * @method static _IH_EmployeeUnitStructure_QB onWriteConnection()
     * @method _IH_EmployeeUnitStructure_QB newQuery()
     * @method static _IH_EmployeeUnitStructure_QB on(null|string $connection = null)
     * @method static _IH_EmployeeUnitStructure_QB query()
     * @method static _IH_EmployeeUnitStructure_QB with(array|string $relations)
     * @method _IH_EmployeeUnitStructure_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_EmployeeUnitStructure_C|EmployeeUnitStructure[] all()
     * @mixin _IH_EmployeeUnitStructure_QB
     */
    class EmployeeUnitStructure extends Model {}
}