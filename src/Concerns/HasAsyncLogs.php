<?php

declare(strict_types=1);

namespace FredikaPutra\AsyncLogger\Concerns;

use FredikaPutra\AsyncLogger\Enums\OperationType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait HasAsyncLogs
{
    /**
     * Logs the current observer event to the observer log.
     */
    private static function logObserverEvent(string $event, Model $model): void
    {
        Log::info(
            class_basename($model).' model '.$event,
            ['data' => self::buildEventData($event, $model)]
        );
    }

    /**
     * Extract relevant data based on the event type.
     *
     * @return array<string, mixed>
     */
    private static function buildEventData(string $event, Model $model): array
    {
        $baseData = match ($event) {
            'created' => ['attributes' => array_keys($model->getAttributes())],
            'updating' => ['changed_fields' => array_keys($model->getDirty())],
            'updated' => ['changed_fields' => array_keys($model->getChanges())],
            default => [],
        };

        $baseData['id'] = $model->getKey() ?? 'N/A';

        return $baseData;
    }

    /**
     * Logs a resource action.
     *
     * @param  array<string>  $resource_properties
     */
    private static function logResourceAction(
        string $action,
        OperationType $operation_type,
        Model $resource,
    ): void {
        Log::info('Resource Action', [
            'action' => str($action)->snake()->toString(),
            'actor' => data_get(Auth::user(), 'email', '-'),
            'actor_id' => data_get(Auth::user(), 'id', '-'),
            'operation_type' => $operation_type->value,
            'resource' => str(class_basename($resource))->snake()->toString(),
            'id' => $resource->getKey() ?? 'N/A',
            'resource_properties' => array_keys($resource->getChanges()),
        ]);
    }
}