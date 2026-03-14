<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\AlsusController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

// Mock user login
$user = User::first();
Auth::login($user);

$request = Request::create('/alsus-alsintor/export-summary', 'GET');
$controller = new AlsusController();

ob_start();
$response = $controller->exportSummary($request);
$output = ob_get_clean();

echo "Response Class: " . get_class($response) . "\n";
if (method_exists($response, 'headers')) {
    echo "Headers:\n";
    print_r($response->headers->all());
}

if ($output !== '') {
    echo "WARNING: Unexpected output detected before download (Length: " . strlen($output) . ")\n";
    echo "First 100 chars of unexpected output: [" . substr($output, 0, 100) . "]\n";
}

// Check if it's a binary response
if (method_exists($response, 'getFile')) {
    $file = $response->getFile();
    echo "File Path: " . $file->getPathname() . "\n";
    echo "File Size: " . $file->getSize() . " bytes\n";
}
