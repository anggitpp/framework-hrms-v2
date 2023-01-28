<?php //178f2a8ca2057fc48ef0b95fd3371e32
/** @noinspection all */

namespace Illuminate\Database\Eloquent {
    
    /**
     * @method static $this onlyTrashed()
     * @method static int restore()
     * @method static $this withTrashed($withTrashed = true)
     * @method static $this withoutTrashed()
     */
    class Builder {}
}

namespace Illuminate\Http {
    
    /**
     * @method static bool hasValidRelativeSignature()
     * @method static bool hasValidSignature($absolute = true)
     * @method static bool hasValidSignatureWhileIgnoring($ignoreQuery = [], $absolute = true)
     * @method static array validate(array $rules, ...$params)
     * @method static void validateWithBag(string $errorBag, array $rules, ...$params)
     */
    class Request {}
}

namespace Illuminate\Routing {
    
    /**
     * @method static $this permission($permissions = [])
     * @method static $this role($roles = [])
     */
    class Route {}
}

namespace Illuminate\Support {
    
    /**
     * @method static $this debug()
     * @method static void downloadExcel(string $fileName, string $writerType = null, $withHeadings = false, array $responseHeaders = [])
     * @method static void storeExcel(string $filePath, string $disk = null, string $writerType = null, $withHeadings = false)
     */
    class Collection {}
}

namespace Illuminate\Support\Facades {
    
    /**
     * @method static void auth($options = [])
     * @method static void confirmPassword()
     * @method static void emailVerification()
     * @method static void resetPassword()
     */
    class Route {}
}

namespace Yajra\DataTables {
    
    /**
     * @method static $this addTransformer($transformer)
     * @method static $this setSerializer($serializer)
     * @method static $this setTransformer($transformer)
     */
    class DataTableAbstract {}
}
