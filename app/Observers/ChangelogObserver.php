<?php

namespace App\Observers;

use App\Services\ChangelogService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

/**
 * Observer générique qui alimente automatiquement le journal des changements
 * à chaque création / mise à jour / suppression d'une entité surveillée.
 *
 * Enregistré (AppServiceProvider) sur : Mission, Farm, Client, Report,
 * Photo et DataEntry.
 */
class ChangelogObserver
{
    public function created(Model $model): void
    {
        $this->record($model, ChangelogService::OPERATION_CREATED, $model->getAttributes());
    }

    public function updated(Model $model): void
    {
        $changes = $model->getChanges();

        $this->record($model, ChangelogService::OPERATION_UPDATED, $changes);
    }

    public function deleted(Model $model): void
    {
        $this->record($model, ChangelogService::OPERATION_DELETED, $model->getAttributes());
    }

    public function restored(Model $model): void
    {
        $this->record($model, ChangelogService::OPERATION_RESTORED, $model->getAttributes());
    }

    protected function record(Model $model, string $operation, array $payload): void
    {
        try {
            app(ChangelogService::class)->record(
                class_basename($model),
                (int) $model->getKey(),
                $operation,
                $payload
            );
        } catch (\Throwable $e) {
            Log::channel('api')->error('ChangelogObserver - Échec de l\'enregistrement', [
                'model' => get_class($model),
                'operation' => $operation,
                'error' => $e->getMessage(),
            ]);
        }
    }
}