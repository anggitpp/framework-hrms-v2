<?php //2fed6661e41bf10b6f7ea094bbabe23c
/** @noinspection all */

namespace Illuminate\Foundation\Console {

    use Illuminate\Foundation\Bus\PendingDispatch;
    
    /**
     * @method static PendingDispatch dispatch(array $data)
     * @method static void dispatchNow(array $data)
     * @method static void dispatchSync(array $data)
     */
    class QueuedCommand {}
}

namespace Illuminate\Queue {

    use Illuminate\Foundation\Bus\PendingDispatch;
    use Laravel\SerializableClosure\SerializableClosure;
    
    /**
     * @method static PendingDispatch dispatch(SerializableClosure $closure)
     * @method static void dispatchNow(SerializableClosure $closure)
     * @method static void dispatchSync(SerializableClosure $closure)
     */
    class CallQueuedClosure {}
}

namespace Maatwebsite\Excel\Jobs {

    use Illuminate\Contracts\Queue\ShouldQueue;
    use Illuminate\Foundation\Bus\PendingDispatch;
    use Maatwebsite\Excel\Concerns\FromQuery;
    use Maatwebsite\Excel\Concerns\FromView;
    use Maatwebsite\Excel\Files\TemporaryFile;
    
    /**
     * @method static PendingDispatch dispatch(object $sheetExport, TemporaryFile $temporaryFile, string $writerType, int $sheetIndex, array $data)
     * @method static void dispatchNow(object $sheetExport, TemporaryFile $temporaryFile, string $writerType, int $sheetIndex, array $data)
     * @method static void dispatchSync(object $sheetExport, TemporaryFile $temporaryFile, string $writerType, int $sheetIndex, array $data)
     */
    class AppendDataToSheet {}
    
    /**
     * @method static PendingDispatch dispatch(FromQuery $sheetExport, TemporaryFile $temporaryFile, string $writerType, int $sheetIndex, int $page, int $chunkSize)
     * @method static void dispatchNow(FromQuery $sheetExport, TemporaryFile $temporaryFile, string $writerType, int $sheetIndex, int $page, int $chunkSize)
     * @method static void dispatchSync(FromQuery $sheetExport, TemporaryFile $temporaryFile, string $writerType, int $sheetIndex, int $page, int $chunkSize)
     */
    class AppendQueryToSheet {}
    
    /**
     * @method static PendingDispatch dispatch(FromView $sheetExport, TemporaryFile $temporaryFile, string $writerType, int $sheetIndex)
     * @method static void dispatchNow(FromView $sheetExport, TemporaryFile $temporaryFile, string $writerType, int $sheetIndex)
     * @method static void dispatchSync(FromView $sheetExport, TemporaryFile $temporaryFile, string $writerType, int $sheetIndex)
     */
    class AppendViewToSheet {}
    
    /**
     * @method static PendingDispatch dispatch(object $export, TemporaryFile $temporaryFile, string $writerType)
     * @method static void dispatchNow(object $export, TemporaryFile $temporaryFile, string $writerType)
     * @method static void dispatchSync(object $export, TemporaryFile $temporaryFile, string $writerType)
     */
    class QueueExport {}
    
    /**
     * @method static PendingDispatch dispatch(ShouldQueue $import = null)
     * @method static void dispatchNow(ShouldQueue $import = null)
     * @method static void dispatchSync(ShouldQueue $import = null)
     */
    class QueueImport {}
}
