<?php //e1e576490d0d3cecb10a115f04e44281
/** @noinspection all */

namespace App\Models\Employee {

    use App\Models\Setting\AppMasterData;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Relations\BelongsTo;
    use Illuminate\Database\Eloquent\Relations\HasMany;
    use Illuminate\Database\Eloquent\Relations\HasOne;
    use Illuminate\Support\Carbon;
    use LaravelIdea\Helper\App\Models\Employee\_IH_EmployeeAsset_C;
    use LaravelIdea\Helper\App\Models\Employee\_IH_EmployeeAsset_QB;
    use LaravelIdea\Helper\App\Models\Employee\_IH_EmployeeContact_C;
    use LaravelIdea\Helper\App\Models\Employee\_IH_EmployeeContact_QB;
    use LaravelIdea\Helper\App\Models\Employee\_IH_EmployeeEducation_C;
    use LaravelIdea\Helper\App\Models\Employee\_IH_EmployeeEducation_QB;
    use LaravelIdea\Helper\App\Models\Employee\_IH_EmployeeFamily_C;
    use LaravelIdea\Helper\App\Models\Employee\_IH_EmployeeFamily_QB;
    use LaravelIdea\Helper\App\Models\Employee\_IH_EmployeeFile_C;
    use LaravelIdea\Helper\App\Models\Employee\_IH_EmployeeFile_QB;
    use LaravelIdea\Helper\App\Models\Employee\_IH_EmployeePosition_C;
    use LaravelIdea\Helper\App\Models\Employee\_IH_EmployeePosition_QB;
    use LaravelIdea\Helper\App\Models\Employee\_IH_EmployeeRehired_C;
    use LaravelIdea\Helper\App\Models\Employee\_IH_EmployeeRehired_QB;
    use LaravelIdea\Helper\App\Models\Employee\_IH_EmployeeSignatureSetting_C;
    use LaravelIdea\Helper\App\Models\Employee\_IH_EmployeeSignatureSetting_QB;
    use LaravelIdea\Helper\App\Models\Employee\_IH_EmployeeTermination_C;
    use LaravelIdea\Helper\App\Models\Employee\_IH_EmployeeTermination_QB;
    use LaravelIdea\Helper\App\Models\Employee\_IH_EmployeeTraining_C;
    use LaravelIdea\Helper\App\Models\Employee\_IH_EmployeeTraining_QB;
    use LaravelIdea\Helper\App\Models\Employee\_IH_EmployeeUnitStructure_C;
    use LaravelIdea\Helper\App\Models\Employee\_IH_EmployeeUnitStructure_QB;
    use LaravelIdea\Helper\App\Models\Employee\_IH_EmployeeWork_C;
    use LaravelIdea\Helper\App\Models\Employee\_IH_EmployeeWork_QB;
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
     * @property int|null $religion_id
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
     * @foreignLinks id,\App\Models\Setting\User,employee_id|id,\App\Models\Employee\EmployeePosition,employee_id|id,\App\Models\Attendance\Attendance,employee_id|id,\App\Models\Attendance\AttendanceWorkSchedule,employee_id|id,\App\Models\ESS\EssTimesheet,employee_id|id,\App\Models\Attendance\AttendanceLeave,employee_id|id,\App\Models\Attendance\AttendancePermission,employee_id|id,\App\Models\Attendance\AttendanceOvertime,employee_id|id,\App\Models\Attendance\AttendanceCorrection,employee_id|id,\App\Models\Employee\EmployeeSignatureSetting,employee_id|id,\App\Models\Employee\EmployeeFamily,employee_id|id,\App\Models\Employee\EmployeeEducation,employee_id|id,\App\Models\Employee\EmployeeContact,employee_id|id,\App\Models\Employee\EmployeeTraining,employee_id|id,\App\Models\Employee\EmployeeWork,employee_id|id,\App\Models\Employee\EmployeeAsset,employee_id|id,\App\Models\Employee\EmployeeFile,employee_id|id,\App\Models\Employee\EmployeeTermination,employee_id|id,\App\Models\Employee\EmployeeRehired,employee_id|id,\App\Models\Payroll\PayrollUpload,employee_id|id,\App\Models\Payroll\PayrollComponentProcessDetail,employee_id
     * @mixin _IH_Employee_QB
     */
    class Employee extends Model {}
    
    /**
     * @property int $id
     * @property int $employee_id
     * @property string $name
     * @property string|null $number
     * @property Carbon $date
     * @property int|null $category_id
     * @property int|null $type_id
     * @property Carbon|null $start_date
     * @property Carbon|null $end_date
     * @property string|null $description
     * @property string|null $filename
     * @property string $status
     * @property string|null $created_by
     * @property string|null $updated_by
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @property AppMasterData|null $category
     * @method BelongsTo|_IH_AppMasterData_QB category()
     * @property Employee $employee
     * @method BelongsTo|_IH_Employee_QB employee()
     * @property AppMasterData|null $type
     * @method BelongsTo|_IH_AppMasterData_QB type()
     * @method static _IH_EmployeeAsset_QB onWriteConnection()
     * @method _IH_EmployeeAsset_QB newQuery()
     * @method static _IH_EmployeeAsset_QB on(null|string $connection = null)
     * @method static _IH_EmployeeAsset_QB query()
     * @method static _IH_EmployeeAsset_QB with(array|string $relations)
     * @method _IH_EmployeeAsset_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_EmployeeAsset_C|EmployeeAsset[] all()
     * @ownLinks employee_id,\App\Models\Employee\Employee,id
     * @mixin _IH_EmployeeAsset_QB
     */
    class EmployeeAsset extends Model {}
    
    /**
     * @property int $id
     * @property int $employee_id
     * @property int $relationship_id
     * @property string $name
     * @property string $phone_number
     * @property string|null $description
     * @property string|null $created_by
     * @property string|null $updated_by
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @property Employee $employee
     * @method BelongsTo|_IH_Employee_QB employee()
     * @property AppMasterData $relationship
     * @method BelongsTo|_IH_AppMasterData_QB relationship()
     * @method static _IH_EmployeeContact_QB onWriteConnection()
     * @method _IH_EmployeeContact_QB newQuery()
     * @method static _IH_EmployeeContact_QB on(null|string $connection = null)
     * @method static _IH_EmployeeContact_QB query()
     * @method static _IH_EmployeeContact_QB with(array|string $relations)
     * @method _IH_EmployeeContact_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_EmployeeContact_C|EmployeeContact[] all()
     * @ownLinks employee_id,\App\Models\Employee\Employee,id
     * @mixin _IH_EmployeeContact_QB
     */
    class EmployeeContact extends Model {}
    
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
     * @property string $name
     * @property string|null $filename
     * @property string|null $description
     * @property string|null $created_by
     * @property string|null $updated_by
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @property Employee $employee
     * @method BelongsTo|_IH_Employee_QB employee()
     * @method static _IH_EmployeeFile_QB onWriteConnection()
     * @method _IH_EmployeeFile_QB newQuery()
     * @method static _IH_EmployeeFile_QB on(null|string $connection = null)
     * @method static _IH_EmployeeFile_QB query()
     * @method static _IH_EmployeeFile_QB with(array|string $relations)
     * @method _IH_EmployeeFile_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_EmployeeFile_C|EmployeeFile[] all()
     * @ownLinks employee_id,\App\Models\Employee\Employee,id
     * @mixin _IH_EmployeeFile_QB
     */
    class EmployeeFile extends Model {}
    
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
     * @property AppMasterData $position
     * @method BelongsTo|_IH_AppMasterData_QB position()
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
     * @property string $number
     * @property int $employee_id
     * @property Carbon $date
     * @property string|null $description
     * @property string|null $filename
     * @property Carbon $effective_date
     * @property string|null $approved_by
     * @property string|null $approved_status
     * @property Carbon|null $approved_date
     * @property string|null $approved_note
     * @property string|null $created_by
     * @property string|null $updated_by
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @property Employee $employee
     * @method BelongsTo|_IH_Employee_QB employee()
     * @method static _IH_EmployeeRehired_QB onWriteConnection()
     * @method _IH_EmployeeRehired_QB newQuery()
     * @method static _IH_EmployeeRehired_QB on(null|string $connection = null)
     * @method static _IH_EmployeeRehired_QB query()
     * @method static _IH_EmployeeRehired_QB with(array|string $relations)
     * @method _IH_EmployeeRehired_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_EmployeeRehired_C|EmployeeRehired[] all()
     * @ownLinks employee_id,\App\Models\Employee\Employee,id
     * @mixin _IH_EmployeeRehired_QB
     */
    class EmployeeRehired extends Model {}
    
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
     * @property string $number
     * @property int $employee_id
     * @property int $reason_id
     * @property int $type_id
     * @property Carbon $date
     * @property string|null $description
     * @property string|null $filename
     * @property Carbon $effective_date
     * @property string|null $note
     * @property string|null $approved_by
     * @property string|null $approved_status
     * @property Carbon|null $approved_date
     * @property string|null $approved_note
     * @property string|null $created_by
     * @property string|null $updated_by
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @property Employee $employee
     * @method BelongsTo|_IH_Employee_QB employee()
     * @property AppMasterData $reason
     * @method BelongsTo|_IH_AppMasterData_QB reason()
     * @property AppMasterData $type
     * @method BelongsTo|_IH_AppMasterData_QB type()
     * @method static _IH_EmployeeTermination_QB onWriteConnection()
     * @method _IH_EmployeeTermination_QB newQuery()
     * @method static _IH_EmployeeTermination_QB on(null|string $connection = null)
     * @method static _IH_EmployeeTermination_QB query()
     * @method static _IH_EmployeeTermination_QB with(array|string $relations)
     * @method _IH_EmployeeTermination_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_EmployeeTermination_C|EmployeeTermination[] all()
     * @ownLinks employee_id,\App\Models\Employee\Employee,id
     * @mixin _IH_EmployeeTermination_QB
     */
    class EmployeeTermination extends Model {}
    
    /**
     * @property int $id
     * @property int $employee_id
     * @property string|null $certificate_number
     * @property string $subject
     * @property string $institution
     * @property int|null $category_id
     * @property int|null $type_id
     * @property Carbon $start_date
     * @property Carbon $end_date
     * @property string|null $location
     * @property string|null $description
     * @property string|null $filename
     * @property string|null $created_by
     * @property string|null $updated_by
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @property AppMasterData|null $category
     * @method BelongsTo|_IH_AppMasterData_QB category()
     * @property Employee $employee
     * @method BelongsTo|_IH_Employee_QB employee()
     * @property AppMasterData|null $type
     * @method BelongsTo|_IH_AppMasterData_QB type()
     * @method static _IH_EmployeeTraining_QB onWriteConnection()
     * @method _IH_EmployeeTraining_QB newQuery()
     * @method static _IH_EmployeeTraining_QB on(null|string $connection = null)
     * @method static _IH_EmployeeTraining_QB query()
     * @method static _IH_EmployeeTraining_QB with(array|string $relations)
     * @method _IH_EmployeeTraining_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_EmployeeTraining_C|EmployeeTraining[] all()
     * @ownLinks employee_id,\App\Models\Employee\Employee,id
     * @mixin _IH_EmployeeTraining_QB
     */
    class EmployeeTraining extends Model {}
    
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
    
    /**
     * @property int $id
     * @property int $employee_id
     * @property string $company
     * @property string $position
     * @property Carbon $start_date
     * @property Carbon|null $end_date
     * @property string|null $city
     * @property string|null $job_desc
     * @property string|null $description
     * @property string|null $filename
     * @property string|null $created_by
     * @property string|null $updated_by
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @property Employee $employee
     * @method BelongsTo|_IH_Employee_QB employee()
     * @method static _IH_EmployeeWork_QB onWriteConnection()
     * @method _IH_EmployeeWork_QB newQuery()
     * @method static _IH_EmployeeWork_QB on(null|string $connection = null)
     * @method static _IH_EmployeeWork_QB query()
     * @method static _IH_EmployeeWork_QB with(array|string $relations)
     * @method _IH_EmployeeWork_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_EmployeeWork_C|EmployeeWork[] all()
     * @ownLinks employee_id,\App\Models\Employee\Employee,id
     * @mixin _IH_EmployeeWork_QB
     */
    class EmployeeWork extends Model {}
}