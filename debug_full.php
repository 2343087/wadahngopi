<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

$cafe = \App\Models\Cafe::with('menus')->find(2);

echo "--- CAFE INFO ---\n";
echo "Name: " . $cafe->name . "\n";
echo "Status: " . $cafe->status . "\n";

echo "\n--- MENU IMAGES ---\n";
var_dump($cafe->menu_images);

echo "\n--- INDIVIDUAL MENUS ---\n";
foreach ($cafe->menus as $m) {
    echo "ID: {$m->id} | Name: {$m->name} | Type: '{$m->type}' | Price: {$m->price}\n";
}

echo "\n--- COMPUTED CATEGORIES ---\n";
$menuCats = $cafe->menus->pluck('type')->unique();
$galleryCats = collect($cafe->menu_images)->pluck('tag')->unique();
$allCats = $menuCats->merge($galleryCats)->filter()->unique()->values()->all();
var_dump($allCats);
echo "Default Tab: " . (!empty($allCats) ? $allCats[0] : 'EMPTY') . "\n";
