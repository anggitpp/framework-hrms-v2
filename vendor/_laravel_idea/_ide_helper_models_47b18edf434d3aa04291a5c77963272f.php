<?php //c1fde2c5f6760fc5f0a1fce000125e85
/** @noinspection all */

namespace App\Models\Attendance {

    use App\Models\Employee\Employee;
    use App\Models\Setting\AppMasterData;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Relations\BelongsTo;
    use Illuminate\Support\Carbon;
    use LaravelIdea\Helper\App\Models\Attendance\_IH_AttendanceCorrection_C;
    use LaravelIdea\Helper\App\Models\Attendance\_IH_AttendanceCorrection_QB;
    use LaravelIdea\Helper\App\Models\Attendance\_IH_AttendanceHoliday_C;
    use LaravelIdea\Helper\App\Models\Attendance\_IH_AttendanceHoliday_QB;
    use LaravelIdea\Helper\App\Models\Attendance\_IH_AttendanceLeaveMaster_C;
    use LaravelIdea\Helper\App\Models\Attendance\_IH_AttendanceLeaveMaster_QB;
    use LaravelIdea\Helper\App\Models\Attendance\_IH_AttendanceLeave_C;
    use LaravelIdea\Helper\App\Models\Attendance\_IH_AttendanceLeave_QB;
    use LaravelIdea\Helper\App\Models\Attendance\_IH_AttendanceLocationSetting_C;
    use LaravelIdea\Helper\App\Models\Attendance\_IH_AttendanceLocationSetting_QB;
    use LaravelIdea\Helper\App\Models\Attendance\_IH_AttendanceMachineSetting_C;
    use LaravelIdea\Helper\App\Models\Attendance\_IH_AttendanceMachineSetting_QB;
    use LaravelIdea\Helper\App\Models\Attendance\_IH_AttendanceOvertime_C;
    use LaravelIdea\Helper\App\Models\Attendance\_IH_AttendanceOvertime_QB;
    use LaravelIdea\Helper\App\Models\Attendance\_IH_AttendancePermission_C;
    use LaravelIdea\Helper\App\Models\Attendance\_IH_AttendancePermission_QB;
    use LaravelIdea\Helper\App\Models\Attendance\_IH_AttendanceShift_C;
    use LaravelIdea\Helper\App\Models\Attendance\_IH_AttendanceShift_QB;
    use LaravelIdea\Helper\App\Models\Attendance\_IH_AttendanceWorkSchedule_C;
    use LaravelIdea\Helper\App\Models\Attendance\_IH_AttendanceWorkSchedule_QB;
    use LaravelIdea\Helper\App\Models\Attendance\_IH_Attendance_C;
    use LaravelIdea\Helper\App\Models\Attendance\_IH_Attendance_QB;
    use LaravelIdea\Helper\App\Models\Employee\_IH_Employee_QB;
    use LaravelIdea\Helper\App\Models\Setting\_IH_AppMasterData_QB;
    
    /**
     * @property int $id
     * @property int $employee_id
     * @property int|null $location_id
     * @property string $type
     * @property Carbon|null $start_date
     * @property Carbon|null $start_time
     * @property float|null $start_latitude
     * @property float|null $start_longitude
     * @property string|null $start_image
     * @property string|null $start_address
     * @property Carbon|null $end_date
     * @property Carbon|null $end_time
     * @property float|null $end_latitude
     * @property float|null $end_longitude
     * @property string|null $end_image
     * @property string|null $end_address
     * @property Carbon|null $duration
     * @property string|null $description
     * @property string|null $created_by
     * @property string|null $updated_by
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @property int|null $type_id
     * @method static _IH_Attendance_QB onWriteConnection()
     * @method _IH_Attendance_QB newQuery()
     * @method static _IH_Attendance_QB on(null|string $connection = null)
     * @method static _IH_Attendance_QB query()
     * @method static _IH_Attendance_QB with(array|string $relations)
     * @method _IH_Attendance_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_Attendance_C|Attendance[] all()
     * @ownLinks employee_id,\App\Models\Employee\Employee,id
     * @mixin _IH_Attendance_QB
     */
    class Attendance extends Model {}
    
    /**
     * @property int $id
     * @property string $number
     * @property int $employee_id
     * @property Carbon $date
     * @property Carbon $attendance_date
     * @property Carbon $start_time
     * @property Carbon $end_time
     * @property Carbon $actual_start_time
     * @property Carbon $actual_end_time
     * @property string|null $description
     * @property string|null $approved_by
     * @property string|null $approved_status
     * @property Carbon|null $approved_date
     * @property string|null $approved_note
     * @property string $created_by
     * @property string|null $updated_by
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @property Carbon|null $duration
     * @property Carbon|null $actual_duration
     * @property Employee $employee
     * @method BelongsTo|_IH_Employee_QB employee()
     * @method static _IH_AttendanceCorrection_QB onWriteConnection()
     * @method _IH_AttendanceCorrection_QB newQuery()
     * @method static _IH_AttendanceCorrection_QB on(null|string $connection = null)
     * @method static _IH_AttendanceCorrection_QB query()
     * @method static _IH_AttendanceCorrection_QB with(array|string $relations)
     * @method _IH_AttendanceCorrection_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_AttendanceCorrection_C|AttendanceCorrection[] all()
     * @ownLinks employee_id,\App\Models\Employee\Employee,id
     * @mixin _IH_AttendanceCorrection_QB
     */
    class AttendanceCorrection extends Model {}
    
    /**
     * @property int $id
     * @property string $name
     * @property Carbon $start_date
     * @property Carbon $end_date
     * @property string|null $description
     * @property string $status
     * @property string|null $created_by
     * @property string|null $updated_by
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @method static _IH_AttendanceHoliday_QB onWriteConnection()
     * @method _IH_AttendanceHoliday_QB newQuery()
     * @method static _IH_AttendanceHoliday_QB on(null|string $connection = null)
     * @method static _IH_AttendanceHoliday_QB query()
     * @method static _IH_AttendanceHoliday_QB with(array|string $relations)
     * @method _IH_AttendanceHoliday_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_AttendanceHoliday_C|AttendanceHoliday[] all()
     * @mixin _IH_AttendanceHoliday_QB
     */
    class AttendanceHoliday extends Model {}
    
    /**
     * @property int $id
     * @property int $employee_id
     * @property int $leave_master_id
     * @property string $number
     * @property Carbon $date
     * @property Carbon $start_date
     * @property Carbon $end_date
     * @property int $balance
     * @property int $amount
     * @property int $remaining
     * @property string|null $description
     * @property string|null $filename
     * @property string|null $approved_by
     * @property string|null $approved_status
     * @property Carbon|null $approved_date
     * @property string|null $approved_note
     * @property string $created_by
     * @property string|null $updated_by
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @property Employee $employee
     * @method BelongsTo|_IH_Employee_QB employee()
     * @property AttendanceLeaveMaster $leaveMaster
     * @method BelongsTo|_IH_AttendanceLeaveMaster_QB leaveMaster()
     * @method static _IH_AttendanceLeave_QB onWriteConnection()
     * @method _IH_AttendanceLeave_QB newQuery()
     * @method static _IH_AttendanceLeave_QB on(null|string $connection = null)
     * @method static _IH_AttendanceLeave_QB query()
     * @method static _IH_AttendanceLeave_QB with(array|string $relations)
     * @method _IH_AttendanceLeave_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_AttendanceLeave_C|AttendanceLeave[] all()
     * @ownLinks employee_id,\App\Models\Employee\Employee,id
     * @mixin _IH_AttendanceLeave_QB
     */
    class AttendanceLeave extends Model {}
    
    /**
     * @property int $id
     * @property int|null $location_id
     * @property string $name
     * @property int $balance
     * @property Carbon $start_date
     * @property Carbon $end_date
     * @property int|null $work_period
     * @property string $gender
     * @property string|null $description
     * @property string $status
     * @property string|null $created_by
     * @property string|null $updated_by
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @property AppMasterData|null $location
     * @method BelongsTo|_IH_AppMasterData_QB location()
     * @method static _IH_AttendanceLeaveMaster_QB onWriteConnection()
     * @method _IH_AttendanceLeaveMaster_QB newQuery()
     * @method static _IH_AttendanceLeaveMaster_QB on(null|string $connection = null)
     * @method static _IH_AttendanceLeaveMaster_QB query()
     * @method static _IH_AttendanceLeaveMaster_QB with(array|string $relations)
     * @method _IH_AttendanceLeaveMaster_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_AttendanceLeaveMaster_C|AttendanceLeaveMaster[] all()
     * @mixin _IH_AttendanceLeaveMaster_QB
     */
    class AttendanceLeaveMaster extends Model {}
    
    /**
     * @property int $id
     * @property int $location_id
     * @property string|null $address
     * @property float|null $latitude
     * @property float|null $longitude
     * @property string|null $radius
     * @property string|null $created_by
     * @property string|null $updated_by
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @property string $wfh
     * @method static _IH_AttendanceLocationSetting_QB onWriteConnection()
     * @method _IH_AttendanceLocationSetting_QB newQuery()
     * @method static _IH_AttendanceLocationSetting_QB on(null|string $connection = null)
     * @method static _IH_AttendanceLocationSetting_QB query()
     * @method static _IH_AttendanceLocationSetting_QB with(array|string $relations)
     * @method _IH_AttendanceLocationSetting_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_AttendanceLocationSetting_C|AttendanceLocationSetting[] all()
     * @mixin _IH_AttendanceLocationSetting_QB
     */
    class AttendanceLocationSetting extends Model {}
    
    /**
     * @property int $id
     * @property int $location_id
     * @property string|null $serial_number
     * @property string $name
     * @property string $ip_address
     * @property string|null $address
     * @property string|null $description
     * @property string $status
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @property AppMasterData $location
     * @method BelongsTo|_IH_AppMasterData_QB location()
     * @method static _IH_AttendanceMachineSetting_QB onWriteConnection()
     * @method _IH_AttendanceMachineSetting_QB newQuery()
     * @method static _IH_AttendanceMachineSetting_QB on(null|string $connection = null)
     * @method static _IH_AttendanceMachineSetting_QB query()
     * @method static _IH_AttendanceMachineSetting_QB with(array|string $relations)
     * @method _IH_AttendanceMachineSetting_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_AttendanceMachineSetting_C|AttendanceMachineSetting[] all()
     * @mixin _IH_AttendanceMachineSetting_QB
     */
    class AttendanceMachineSetting extends Model {}
    
    /**
     * @property int $id
     * @property int $employee_id
     * @property string $number
     * @property Carbon $date
     * @property Carbon $start_date
     * @property Carbon $end_date
     * @property Carbon $start_time
     * @property Carbon $end_time
     * @property Carbon $duration
     * @property string|null $description
     * @property string|null $filename
     * @property string|null $approved_by
     * @property string|null $approved_status
     * @property Carbon|null $approved_date
     * @property string|null $approved_note
     * @property string $created_by
     * @property string|null $updated_by
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @property Employee $employee
     * @method BelongsTo|_IH_Employee_QB employee()
     * @method static _IH_AttendanceOvertime_QB onWriteConnection()
     * @method _IH_AttendanceOvertime_QB newQuery()
     * @method static _IH_AttendanceOvertime_QB on(null|string $connection = null)
     * @method static _IH_AttendanceOvertime_QB query()
     * @method static _IH_AttendanceOvertime_QB with(array|string $relations)
     * @method _IH_AttendanceOvertime_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_AttendanceOvertime_C|AttendanceOvertime[] all()
     * @ownLinks employee_id,\App\Models\Employee\Employee,id
     * @mixin _IH_AttendanceOvertime_QB
     */
    class AttendanceOvertime extends Model {}
    
    /**
     * @property int $id
     * @property string $number
     * @property int $employee_id
     * @property int $category_id
     * @property Carbon $date
     * @property Carbon $start_date
     * @property Carbon $end_date
     * @property string|null $description
     * @property string|null $filename
     * @property string|null $approved_by
     * @property string|null $approved_status
     * @property Carbon|null $approved_date
     * @property string|null $approved_note
     * @property string $created_by
     * @property string|null $updated_by
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @property AppMasterData $category
     * @method BelongsTo|_IH_AppMasterData_QB category()
     * @property Employee $employee
     * @method BelongsTo|_IH_Employee_QB employee()
     * @method static _IH_AttendancePermission_QB onWriteConnection()
     * @method _IH_AttendancePermission_QB newQuery()
     * @method static _IH_AttendancePermission_QB on(null|string $connection = null)
     * @method static _IH_AttendancePermission_QB query()
     * @method static _IH_AttendancePermission_QB with(array|string $relations)
     * @method _IH_AttendancePermission_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_AttendancePermission_C|AttendancePermission[] all()
     * @ownLinks employee_id,\App\Models\Employee\Employee,id
     * @mixin _IH_AttendancePermission_QB
     */
    class AttendancePermission extends Model {}
    
    /**
     * @property int $id
     * @property int|null $location_id
     * @property string $name
     * @property string $code
     * @property Carbon $start
     * @property Carbon $end
     * @property string $night_shift
     * @property string|null $description
     * @property string $status
     * @property string|null $created_by
     * @property string|null $updated_by
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @property AppMasterData|null $location
     * @method BelongsTo|_IH_AppMasterData_QB location()
     * @method static _IH_AttendanceShift_QB onWriteConnection()
     * @method _IH_AttendanceShift_QB newQuery()
     * @method static _IH_AttendanceShift_QB on(null|string $connection = null)
     * @method static _IH_AttendanceShift_QB query()
     * @method static _IH_AttendanceShift_QB with(array|string $relations)
     * @method _IH_AttendanceShift_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_AttendanceShift_C|AttendanceShift[] all()
     * @mixin _IH_AttendanceShift_QB
     */
    class AttendanceShift extends Model {}
    
    /**
     * @property int $id
     * @property int $employee_id
     * @property int $shift_id
     * @property int|null $location_id
     * @property Carbon $date
     * @property Carbon $start_time
     * @property Carbon $end_time
     * @property string|null $description
     * @property string|null $created_by
     * @property string|null $updated_by
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @method static _IH_AttendanceWorkSchedule_QB onWriteConnection()
     * @method _IH_AttendanceWorkSchedule_QB newQuery()
     * @method static _IH_AttendanceWorkSchedule_QB on(null|string $connection = null)
     * @method static _IH_AttendanceWorkSchedule_QB query()
     * @method static _IH_AttendanceWorkSchedule_QB with(array|string $relations)
     * @method _IH_AttendanceWorkSchedule_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_AttendanceWorkSchedule_C|AttendanceWorkSchedule[] all()
     * @ownLinks employee_id,\App\Models\Employee\Employee,id
     * @mixin _IH_AttendanceWorkSchedule_QB
     */
    class AttendanceWorkSchedule extends Model {}
}