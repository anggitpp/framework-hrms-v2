<?php //bbd55b95ba8452da520b79975cac4f5a
/** @noinspection all */

namespace App\Models\Setting {

    use App\Models\Payroll\PayrollSetting;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Relations\BelongsTo;
    use Illuminate\Database\Eloquent\Relations\HasMany;
    use Illuminate\Database\Eloquent\Relations\MorphToMany;
    use Illuminate\Notifications\DatabaseNotification;
    use Illuminate\Notifications\DatabaseNotificationCollection;
    use Illuminate\Support\Carbon;
    use Laravel\Sanctum\PersonalAccessToken;
    use LaravelIdea\Helper\App\Models\Payroll\_IH_PayrollSetting_QB;
    use LaravelIdea\Helper\App\Models\Setting\_IH_AppMasterCategory_C;
    use LaravelIdea\Helper\App\Models\Setting\_IH_AppMasterCategory_QB;
    use LaravelIdea\Helper\App\Models\Setting\_IH_AppMasterData_C;
    use LaravelIdea\Helper\App\Models\Setting\_IH_AppMasterData_QB;
    use LaravelIdea\Helper\App\Models\Setting\_IH_AppMenu_C;
    use LaravelIdea\Helper\App\Models\Setting\_IH_AppMenu_QB;
    use LaravelIdea\Helper\App\Models\Setting\_IH_AppModul_C;
    use LaravelIdea\Helper\App\Models\Setting\_IH_AppModul_QB;
    use LaravelIdea\Helper\App\Models\Setting\_IH_AppParameter_C;
    use LaravelIdea\Helper\App\Models\Setting\_IH_AppParameter_QB;
    use LaravelIdea\Helper\App\Models\Setting\_IH_AppSubModul_C;
    use LaravelIdea\Helper\App\Models\Setting\_IH_AppSubModul_QB;
    use LaravelIdea\Helper\App\Models\Setting\_IH_User_C;
    use LaravelIdea\Helper\App\Models\Setting\_IH_User_QB;
    use LaravelIdea\Helper\Illuminate\Notifications\_IH_DatabaseNotification_QB;
    use LaravelIdea\Helper\Laravel\Sanctum\_IH_PersonalAccessToken_C;
    use LaravelIdea\Helper\Laravel\Sanctum\_IH_PersonalAccessToken_QB;
    use LaravelIdea\Helper\Spatie\Permission\Models\_IH_Permission_C;
    use LaravelIdea\Helper\Spatie\Permission\Models\_IH_Permission_QB;
    use LaravelIdea\Helper\Spatie\Permission\Models\_IH_Role_C;
    use LaravelIdea\Helper\Spatie\Permission\Models\_IH_Role_QB;
    use Spatie\Permission\Models\Permission;
    use Spatie\Permission\Models\Role;
    
    /**
     * @property int $id
     * @property int|null $parent_id
     * @property string $name
     * @property string $code
     * @property string|null $description
     * @property int $order
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @method static _IH_AppMasterCategory_QB onWriteConnection()
     * @method _IH_AppMasterCategory_QB newQuery()
     * @method static _IH_AppMasterCategory_QB on(null|string $connection = null)
     * @method static _IH_AppMasterCategory_QB query()
     * @method static _IH_AppMasterCategory_QB with(array|string $relations)
     * @method _IH_AppMasterCategory_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_AppMasterCategory_C|AppMasterCategory[] all()
     * @mixin _IH_AppMasterCategory_QB
     */
    class AppMasterCategory extends Model {}
    
    /**
     * @property int $id
     * @property int|null $parent_id
     * @property string $code
     * @property string $name
     * @property string|null $description
     * @property int $order
     * @property string $status
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @property string $app_master_category_code
     * @property _IH_AppMasterData_C|AppMasterData[] $children
     * @property-read int $children_count
     * @method HasMany|_IH_AppMasterData_QB children()
     * @property AppMasterData|null $parent
     * @method BelongsTo|_IH_AppMasterData_QB parent()
     * @property PayrollSetting $payrollSetting
     * @method BelongsTo|_IH_PayrollSetting_QB payrollSetting()
     * @method static _IH_AppMasterData_QB onWriteConnection()
     * @method _IH_AppMasterData_QB newQuery()
     * @method static _IH_AppMasterData_QB on(null|string $connection = null)
     * @method static _IH_AppMasterData_QB query()
     * @method static _IH_AppMasterData_QB with(array|string $relations)
     * @method _IH_AppMasterData_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_AppMasterData_C|AppMasterData[] all()
     * @mixin _IH_AppMasterData_QB
     */
    class AppMasterData extends Model {}
    
    /**
     * @property int $id
     * @property int|null $parent_id
     * @property int $app_modul_id
     * @property int $app_sub_modul_id
     * @property string $name
     * @property string $target
     * @property string|null $description
     * @property string|null $icon
     * @property string|null $parameter
     * @property string $full_screen
     * @property string $status
     * @property int $order
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @property _IH_AppMenu_C|AppMenu[] $children
     * @property-read int $children_count
     * @method HasMany|_IH_AppMenu_QB children()
     * @property AppModul $modul
     * @method BelongsTo|_IH_AppModul_QB modul()
     * @property AppMenu|null $parent
     * @method BelongsTo|_IH_AppMenu_QB parent()
     * @property AppSubModul $subModul
     * @method BelongsTo|_IH_AppSubModul_QB subModul()
     * @method static _IH_AppMenu_QB onWriteConnection()
     * @method _IH_AppMenu_QB newQuery()
     * @method static _IH_AppMenu_QB on(null|string $connection = null)
     * @method static _IH_AppMenu_QB query()
     * @method static _IH_AppMenu_QB with(array|string $relations)
     * @method _IH_AppMenu_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_AppMenu_C|AppMenu[] all()
     * @ownLinks app_modul_id,\App\Models\Setting\AppModul,id|app_sub_modul_id,\App\Models\Setting\AppSubModul,id
     * @mixin _IH_AppMenu_QB
     */
    class AppMenu extends Model {}
    
    /**
     * @property int $id
     * @property string $name
     * @property string $target
     * @property string|null $description
     * @property string|null $icon
     * @property string $order
     * @property string $status
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @property _IH_AppMenu_C|AppMenu[] $menus
     * @property-read int $menus_count
     * @method HasMany|_IH_AppMenu_QB menus()
     * @property _IH_AppSubModul_C|AppSubModul[] $subModul
     * @property-read int $sub_modul_count
     * @method HasMany|_IH_AppSubModul_QB subModul()
     * @method static _IH_AppModul_QB onWriteConnection()
     * @method _IH_AppModul_QB newQuery()
     * @method static _IH_AppModul_QB on(null|string $connection = null)
     * @method static _IH_AppModul_QB query()
     * @method static _IH_AppModul_QB with(array|string $relations)
     * @method _IH_AppModul_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_AppModul_C|AppModul[] all()
     * @foreignLinks id,\App\Models\Setting\AppSubModul,app_modul_id|id,\App\Models\Setting\AppMenu,app_modul_id
     * @mixin _IH_AppModul_QB
     */
    class AppModul extends Model {}
    
    /**
     * @property int $id
     * @property string $code
     * @property string $name
     * @property string $value
     * @property string $description
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @method static _IH_AppParameter_QB onWriteConnection()
     * @method _IH_AppParameter_QB newQuery()
     * @method static _IH_AppParameter_QB on(null|string $connection = null)
     * @method static _IH_AppParameter_QB query()
     * @method static _IH_AppParameter_QB with(array|string $relations)
     * @method _IH_AppParameter_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_AppParameter_C|AppParameter[] all()
     * @mixin _IH_AppParameter_QB
     */
    class AppParameter extends Model {}
    
    /**
     * @property int $id
     * @property int $app_modul_id
     * @property string $name
     * @property string|null $description
     * @property int $order
     * @property string $status
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @property _IH_AppMenu_C|AppMenu[] $appMenu
     * @property-read int $app_menu_count
     * @method HasMany|_IH_AppMenu_QB appMenu()
     * @property AppModul $appModul
     * @method BelongsTo|_IH_AppModul_QB appModul()
     * @method static _IH_AppSubModul_QB onWriteConnection()
     * @method _IH_AppSubModul_QB newQuery()
     * @method static _IH_AppSubModul_QB on(null|string $connection = null)
     * @method static _IH_AppSubModul_QB query()
     * @method static _IH_AppSubModul_QB with(array|string $relations)
     * @method _IH_AppSubModul_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_AppSubModul_C|AppSubModul[] all()
     * @ownLinks app_modul_id,\App\Models\Setting\AppModul,id
     * @foreignLinks id,\App\Models\Setting\AppMenu,app_sub_modul_id
     * @mixin _IH_AppSubModul_QB
     */
    class AppSubModul extends Model {}
    
    /**
     * @property int $id
     * @property string $name
     * @property string $email
     * @property Carbon|null $email_verified_at
     * @property string $password
     * @property string|null $remember_token
     * @property Carbon|null $created_at
     * @property Carbon|null $updated_at
     * @property string|null $description
     * @property string|null $photo
     * @property string $status
     * @property int $role_id
     * @property Carbon|null $last_login
     * @property string|null $username
     * @property int|null $employee_id
     * @property string|null $access_locations
     * @property DatabaseNotificationCollection|DatabaseNotification[] $notifications
     * @property-read int $notifications_count
     * @method MorphToMany|_IH_DatabaseNotification_QB notifications()
     * @property _IH_Permission_C|Permission[] $permissions
     * @property-read int $permissions_count
     * @method MorphToMany|_IH_Permission_QB permissions()
     * @property DatabaseNotificationCollection|DatabaseNotification[] $readNotifications
     * @property-read int $read_notifications_count
     * @method MorphToMany|_IH_DatabaseNotification_QB readNotifications()
     * @property Role $role
     * @method BelongsTo|_IH_Role_QB role()
     * @property _IH_Role_C|Role[] $roles
     * @property-read int $roles_count
     * @method MorphToMany|_IH_Role_QB roles()
     * @property _IH_PersonalAccessToken_C|PersonalAccessToken[] $tokens
     * @property-read int $tokens_count
     * @method MorphToMany|_IH_PersonalAccessToken_QB tokens()
     * @property DatabaseNotificationCollection|DatabaseNotification[] $unreadNotifications
     * @property-read int $unread_notifications_count
     * @method MorphToMany|_IH_DatabaseNotification_QB unreadNotifications()
     * @method static _IH_User_QB onWriteConnection()
     * @method _IH_User_QB newQuery()
     * @method static _IH_User_QB on(null|string $connection = null)
     * @method static _IH_User_QB query()
     * @method static _IH_User_QB with(array|string $relations)
     * @method _IH_User_QB newModelQuery()
     * @method false|int increment(string $column, float|int $amount = 1, array $extra = [])
     * @method false|int decrement(string $column, float|int $amount = 1, array $extra = [])
     * @method static _IH_User_C|User[] all()
     * @ownLinks employee_id,\App\Models\Employee\Employee,id
     * @mixin _IH_User_QB
     */
    class User extends Model {}
}