<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Http;

function saveHtml($name, $url)
{
    echo "Saving $name...\n";
    $response = Http::withHeaders([
        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
    ])->get($url);
    file_put_contents($name.'.html', $response->body());
    echo "$name saved (".strlen($response->body())." bytes)\n";
}

saveHtml('detik_food', 'https://www.detik.com/tag/kopi');
saveHtml('otten_majalah', 'https://majalah.ottencoffee.co.id/');
saveHtml('kompas_kopi', 'https://www.kompas.com/tag/kopi');
