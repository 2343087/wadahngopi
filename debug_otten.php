<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Http;

$url = 'https://majalah.ottencoffee.co.id/category/edukasi/';
$response = Http::withHeaders([
    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
])->get($url);

echo 'Status: '.$response->status()."\n";
$body = $response->body();
file_put_contents('otten_body.html', $body);
echo 'Body length: '.strlen($body)."\n";
echo 'Snippet: '.substr(strip_tags($body), 0, 500)."\n";
