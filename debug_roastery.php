<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$roastery = App\Models\Roastery::where('slug', 'wadah-kopi-roastery')->first();
if ($roastery) {
    echo "Image Path: " . $roastery->image_path . "\n";
    echo "Images: \n";
    print_r($roastery->images);
    echo "Social Links: \n";
    print_r($roastery->social_links);

    echo "\nFull URL logic test:\n";
    $rawImages = collect([$roastery->image_path])
        ->merge($roastery->images ?? [])
        ->filter()
        ->map(function ($img) {
            return str_starts_with($img, 'http') ? $img : \Illuminate\Support\Facades\Storage::url($img);
        })
        ->values()
        ->all();
    print_r($rawImages);

} else {
    echo "Roastery not found.\n";
}
