<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

$cafe = \App\Models\Cafe::find(2);
echo "CAFE 2 DATA:\n";
echo "Menu Images: " . json_encode($cafe->menu_images, JSON_PRETTY_PRINT) . "\n";
echo "Menus Count: " . $cafe->menus()->count() . "\n";
