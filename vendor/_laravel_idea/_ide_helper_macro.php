<?php //e0327117f40363de2da01040254887ba
/** @noinspection all */

namespace Illuminate\Database\Eloquent {
    
    /**
     * @method $this onlyTrashed()
     * @method int restore()
     * @method $this withTrashed($withTrashed = true)
     * @method $this withoutTrashed()
     */
    class Builder {}
}

namespace Illuminate\Http {
    
    /**
     * @method bool hasValidRelativeSignature()
     * @method bool hasValidSignature($absolute = true)
     * @method bool hasValidSignatureWhileIgnoring($ignoreQuery = [], $absolute = true)
     * @method array validate(array $rules, ...$params)
     * @method void validateWithBag(string $errorBag, array $rules, ...$params)
     */
    class Request {}
}

namespace Illuminate\Routing {
    
    /**
     * @method $this permission($permissions = [])
     * @method $this role($roles = [])
     */
    class Route {}
}

namespace Illuminate\Support {
    
    /**
     * @method $this debug()
     * @method void downloadExcel(string $fileName, string $writerType = null, $withHeadings = false, array $responseHeaders = [])
     * @method void storeExcel(string $filePath, string $disk = null, string $writerType = null, $withHeadings = false)
     */
    class Collection {}
}

namespace Illuminate\Support\Facades {
    
    /**
     * @method void auth($options = [])
     * @method void confirmPassword()
     * @method void emailVerification()
     * @method void resetPassword()
     */
    class Route {}
}

namespace Yajra\DataTables {
    
    /**
     * @method $this addTransformer($transformer)
     * @method $this setSerializer($serializer)
     * @method $this setTransformer($transformer)
     */
    class DataTableAbstract {}
}
