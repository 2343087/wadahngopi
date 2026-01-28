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
        'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8',
    ])->get($url);

    if (! $response->successful()) {
        echo 'Failed to fetch: '.$response->status()."\n";

        return;
    }

    $crawler = new Crawler($response->body());

    // Broad search for articles
    $articles = $crawler->filter('article, .jeg_post, .article__list, .post-item, .article__list__item, .list-content__item');
    if ($articles->count() === 0) {
        $articles = $crawler->filter('.jeg_main_content .jeg_post, .jeg_posts article');
    }

    echo 'Found '.$articles->count()." potential articles.\n";

    if ($articles->count() === 0) {
        echo 'HTML Snippet: '.substr(strip_tags($response->body()), 0, 1000)."\n";
    }

    $articles->slice(0, 3)->each(function ($node, $i) {
        echo 'Article '.($i + 1).":\n";
        $titleNode = $node->filter('h1, h2, h3, .title, .jeg_post_title, .article__title');
        $title = $titleNode->count() ? trim($titleNode->text()) : 'NO TITLE FOUND';
        echo "  Title: $title\n";

        $node->filter('img')->each(function ($img, $j) {
            echo '  Image '.($j + 1).":\n";
            echo '    src: '.$img->attr('src')."\n";
            echo '    data-src: '.$img->attr('data-src')."\n";
            echo '    data-original: '.$img->attr('data-original')."\n";
            echo '    class: '.$img->attr('class')."\n";
        });
    });
}

inspectSource('Detik', 'https://www.detik.com/tag/kopi');
inspectSource('Otten', 'https://majalah.ottencoffee.co.id/');
inspectSource('Kompas', 'https://www.kompas.com/tag/kopi');
