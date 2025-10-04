<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Project;
use App\Models\Property;
use App\Models\Agent;
use App\Models\Blog;
use App\Services\ImageOptimizationService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class MigrateImagesToOptimized extends Command
{
    protected $signature = 'images:migrate-to-optimized {--dry-run : Show what would be migrated without making changes} {--model= : Migrate specific model (project,property,agent,blog)}';
    
    protected $description = 'Migrate existing images to optimized responsive format';

    protected ImageOptimizationService $imageService;

    public function __construct(ImageOptimizationService $imageService)
    {
        parent::__construct();
        $this->imageService = $imageService;
    }

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $specificModel = $this->option('model');
        
        $this->info('🚀 Iniciando migración de imágenes a formato optimizado...');
        
        if ($dryRun) {
            $this->warn('⚠️  MODO DRY-RUN: No se realizarán cambios reales');
        }

        $models = $specificModel ? [$specificModel] : ['project', 'property', 'agent', 'blog'];
        
        foreach ($models as $modelType) {
            $this->migrateModel($modelType, $dryRun);
        }
        
        $this->info('✅ Migración completada');
    }

    protected function migrateModel(string $modelType, bool $dryRun)
    {
        $this->info("📁 Migrando modelo: {$modelType}");
        
        $modelClass = match($modelType) {
            'project' => Project::class,
            'property' => Property::class,
            'agent' => Agent::class,
            'blog' => Blog::class,
            default => null
        };
        
        if (!$modelClass) {
            $this->error("Modelo desconocido: {$modelType}");
            return;
        }
        
        $records = $modelClass::all();
        $this->info("Encontrados {$records->count()} registros");
        
        $migrated = 0;
        $skipped = 0;
        $errors = 0;
        
        foreach ($records as $record) {
            try {
                $result = $this->migrateRecord($record, $modelType, $dryRun);
                
                if ($result['migrated']) {
                    $migrated++;
                    $name = $record->name ?? $record->title ?? "ID {$record->id}";
                    $this->line("✅ Migrado: {$name}");
                } else {
                    $skipped++;
                    $name = $record->name ?? $record->title ?? "ID {$record->id}";
                    $this->line("⏭️  Omitido: {$name} - {$result['reason']}");
                }
                
            } catch (\Exception $e) {
                $errors++;
                $name = $record->name ?? $record->title ?? "ID {$record->id}";
                $this->error("❌ Error en {$name}: {$e->getMessage()}");
                Log::error("Migration error for {$modelType} {$record->id}", [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }
        
        $this->table(['Resultado', 'Cantidad'], [
            ['Migrados', $migrated],
            ['Omitidos', $skipped],
            ['Errores', $errors]
        ]);
    }

    protected function migrateRecord($record, string $modelType, bool $dryRun): array
    {
        $coverField = match($modelType) {
            'agent' => 'profile_picture',
            default => 'cover_image'
        };
        
        $needsMigration = false;
        $changes = [];
        
        // Check cover image
        if ($record->{$coverField} && is_string($record->{$coverField})) {
            if (Storage::disk('public')->exists($record->{$coverField})) {
                $needsMigration = true;
                $changes['cover'] = $record->{$coverField};
            }
        }
        
        // Check gallery
        if ($record->gallery && is_array($record->gallery)) {
            $stringImages = array_filter($record->gallery, 'is_string');
            if (!empty($stringImages)) {
                foreach ($stringImages as $image) {
                    if (Storage::disk('public')->exists($image)) {
                        $needsMigration = true;
                        $changes['gallery'][] = $image;
                    }
                }
            }
        }
        
        if (!$needsMigration) {
            return ['migrated' => false, 'reason' => 'Ya está en formato optimizado o no tiene imágenes válidas'];
        }
        
        if ($dryRun) {
            return ['migrated' => true, 'reason' => 'Sería migrado (dry-run)'];
        }
        
        // Esta función requiere más desarrollo para la conversión real
        // Por ahora solo simula la migración
        return ['migrated' => false, 'reason' => 'Migración real pendiente de implementación'];
    }
}
