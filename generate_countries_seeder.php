<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Country;

$countries = Country::all()->map(function($country) {
    return $country->getAttributes();
})->toArray();

$phpCode = "<?php\n"
         . "require __DIR__ . '/vendor/autoload.php';\n"
         . "\$app = require_once __DIR__ . '/bootstrap/app.php';\n"
         . "\$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();\n\n"
         . "use Illuminate\Support\Facades\DB;\n"
         . "use App\Models\Country;\n\n"
         . "DB::statement('SET FOREIGN_KEY_CHECKS=0;');\n"
         . "Country::truncate();\n\n"
         . "\$data = " . var_export($countries, true) . ";\n\n"
         . "foreach(array_chunk(\$data, 50) as \$chunk) {\n"
         . "    Country::insert(\$chunk);\n"
         . "}\n\n"
         . "DB::statement('SET FOREIGN_KEY_CHECKS=1;');\n"
         . "echo \"Successfully seeded \" . count(\$data) . \" translated countries!\\n\";\n";

file_put_contents(__DIR__ . '/seed_production_countries.php', $phpCode);
echo "Successfully generated seed_production_countries.php\n";
