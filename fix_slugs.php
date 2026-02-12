<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Cafe;
use Illuminate\Support\Facades\DB;

echo "Fixing slugs...\n";
$cafes = Cafe::all();
echo "Found " . $cafes->count() . " cafes.\n";

foreach ($cafes as $c) {
    if (empty($c->slug)) {
        $slug = Cafe::generateUniqueSlug($c->name);
        echo "Updating {$c->name} (ID: {$c->id}) with slug: $slug\n";

        $affected = DB::table('cafes')->where('id', $c->id)->update(['slug' => $slug]);
        echo "Affected rows: $affected\n";
    } else {
        echo "Dah ada slug: {$c->name} ({$c->slug})\n";
    }
}
echo "Done!\n";
