<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

function inspectSource($name, $url)
{
    echo "\n--- Inspecting $name ($url) ---\n";
    $response = Http::withHeaders([
        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
    ])->get($url);

    if (! $response->successful()) {
        echo 'Failed to fetch: '.$response->status()."\n";

        return;
    }

    $crawler = new Crawler($response->body());

    // Test various selectors
    $selectors = ['article', '.article__list', '.post-item', '.item', '.list-item', 'h3 a', 'h2 a'];
    foreach ($selectors as $sel) {
        $count = $crawler->filter($sel)->count();
        if ($count > 0) {
            echo "Selector '$sel' found $count items.\n";
            $node = $crawler->filter($sel)->first();
            echo '  Title: '.($node->filter('h1, h2, h3, .title')->count() ? trim($node->filter('h1, h2, h3, .title')->text()) : 'N/A')."\n";
            break;
        }
    }
}

inspectSource('Kompas', 'https://www.kompas.com/tag/kopi');
inspectSource('Antara', 'https://www.antaranews.com/tag/kopi');
inspectSource('IDN Times', 'https://www.idntimes.com/tag/kopi');
inspectSource('Okezone', 'https://www.okezone.com/tag/kopi');
inspectSource('Tempo', 'https://www.tempo.co/tag/kopi');
