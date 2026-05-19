<?php

namespace Modules\AuditLog;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Modules\AuditLog\Filament\Resources\AuditLogResource;
use Modules\AuditLog\Models\AuditLog;
use Modules\AuditLog\Observers\AuditableObserver;

class AuditLogServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/Config/auditlog.php', 'auditlog');
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');

        $this->publishes([
            __DIR__ . '/Config/auditlog.php' => config_path('auditlog.php'),
        ], 'auditlog-config');

        if (class_exists(Filament::class)) {
            Filament::serving(function () {
                Filament::registerResources([
                    AuditLogResource::class,
                ]);
            });
        }

        if (config('auditlog.enabled', true)) {
            $this->registerObservers();
        }
    }

    protected function registerObservers(): void
    {
        $observer = AuditableObserver::class;

        foreach ($this->resolveModelClasses() as $modelClass) {
            if (!is_subclass_of($modelClass, Model::class)) {
                continue;
            }

            if ($modelClass === AuditLog::class) {
                continue;
            }

            $modelClass::observe($observer);
        }
    }

    /**
     * @return array<int, class-string<Model>>
     */
    protected function resolveModelClasses(): array
    {
        $models = [];

        $includedModels = config('auditlog.include_models', []);
        foreach ($includedModels as $includedModel) {
            if (is_string($includedModel) && class_exists($includedModel)) {
                $models[] = $includedModel;
            }
        }

        if (config('auditlog.auto_discover_models', true)) {
            foreach ($this->discoverModelClasses() as $modelClass) {
                $models[] = $modelClass;
            }
        }

        $excluded = array_filter(config('auditlog.exclude_models', []), 'is_string');

        return array_values(array_unique(array_filter($models, function ($class) use ($excluded) {
            return is_string($class) && !in_array($class, $excluded, true);
        })));
    }

    /**
     * @return array<int, class-string<Model>>
     */
    protected function discoverModelClasses(): array
    {
        $directories = [];
        foreach (config('auditlog.model_paths', []) as $pathPattern) {
            $resolved = glob(base_path($pathPattern), GLOB_ONLYDIR) ?: [];
            foreach ($resolved as $dir) {
                $directories[] = $dir;
            }
        }

        $classes = [];
        foreach (array_unique($directories) as $directory) {
            if (!is_dir($directory)) {
                continue;
            }

            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS)
            );

            foreach ($iterator as $file) {
                if (!$file->isFile() || $file->getExtension() !== 'php') {
                    continue;
                }

                $class = $this->classNameFromPath($file->getPathname());
                if (!$class || !class_exists($class)) {
                    continue;
                }

                if (!is_subclass_of($class, Model::class)) {
                    continue;
                }

                $reflection = new \ReflectionClass($class);
                if ($reflection->isAbstract()) {
                    continue;
                }

                $classes[] = $class;
            }
        }

        return array_values(array_unique($classes));
    }

    protected function classNameFromPath(string $absolutePath): ?string
    {
        $relative = str_replace(base_path() . DIRECTORY_SEPARATOR, '', $absolutePath);
        $relative = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relative);

        if (!str_ends_with($relative, '.php')) {
            return null;
        }

        $classPath = substr($relative, 0, -4);
        $classPath = str_replace(DIRECTORY_SEPARATOR, '\\', $classPath);

        if (str_starts_with($classPath, 'app\\')) {
            return 'App\\' . substr($classPath, 4);
        }

        if (str_starts_with($classPath, 'Modules\\')) {
            return $classPath;
        }

        return null;
    }
}
