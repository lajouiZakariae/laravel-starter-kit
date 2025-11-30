<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

Artisan::command('make:service {name?}', function (): void {
    $name = $this->argument('name');

    while (blank($name)) {
        $name = $this->ask('Service name is required');
    }

    $className = str($name)->studly();

    $preparedName = $className->endsWith('Service') ? $className : $className->append('Service');

    try {
        $serviceFilePath = makeService($preparedName);

        $this->info("Service {$preparedName} created successfully at {$serviceFilePath}.");
    } catch (Throwable $th) {
        $this->error($th->getMessage());
    }
});

if (! function_exists('makeService')) {
    function makeService(string $name): string {
        $serviceFileContent = <<<EOT
    <?php

    namespace App\Services;

    class {$name} {
        public function __construct() {}
    }
    EOT;

        $serviceFilePath = app_path("Services/{$name}.php");

        if (File::exists($serviceFilePath)) {
            throw new Exception("Service {$name} already exists");
        }

        if (! File::isDirectory(app_path('Services'))) {
            File::makeDirectory(app_path('Services'), recursive: true);
        }

        $written = File::put($serviceFilePath, $serviceFileContent);

        if (! $written) {
            throw new Exception("Failed to create service {$name}");
        }

        return $serviceFilePath;
    }
}
