<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Exports\AlsusAlsintorExport;
use Maatwebsite\Excel\Facades\Excel;

try {
    echo "Attempting to generate Excel...\n";
    $export = new AlsusAlsintorExport(null); // All satker
    // We can't easily use Excel::store without a disk, so let's use the object directly if possible
    // Or just check if the collection() method works
    $collection = $export->collection();
    echo "Collection count: " . $collection->count() . "\n";
    foreach ($collection as $row) {
        print_r($row);
    }
    
    echo "\nTesting Excel::download (simulated)...\n";
    // This will try to send headers, which might fail in CLI, but let's see errors
    Excel::store($export, 'test_export.xlsx', 'local');
    echo "File stored in storage/app/test_export.xlsx\n";

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
