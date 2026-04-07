<?php

namespace App\Traits;

use App\Models\Auditoria;
use Illuminate\Database\Eloquent\Model;

/**
 * Trait para registrar automáticamente create/update/delete en auditorías.
 * Usar en el modelo: use \App\Traits\Auditable;
 */
trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(function (Model $model) {
            Auditoria::create([
                'user_id'        => auth()->id(),
                'tabla'          => $model->getTable(),
                'registro_id'    => $model->getKey(),
                'accion'         => 'create',
                'campo'          => null,
                'valor_anterior' => null,
                'valor_nuevo'    => null,
            ]);
        });

        static::updated(function (Model $model) {
            $userId = auth()->id();
            $tabla = $model->getTable();
            $registroId = $model->getKey();
            $changes = $model->getChanges();

            foreach ($changes as $campo => $nuevo) {
                if (in_array($campo, ['updated_at'])) {
                    continue;
                }
                $anterior = $model->getOriginal($campo);

                Auditoria::create([
                    'user_id'        => $userId,
                    'tabla'          => $tabla,
                    'registro_id'    => $registroId,
                    'accion'         => 'update',
                    'campo'          => $campo,
                    'valor_anterior' => is_null($anterior) ? null : (string) $anterior,
                    'valor_nuevo'    => is_null($nuevo) ? null : (string) $nuevo,
                ]);
            }
        });

        static::deleted(function (Model $model) {
            Auditoria::create([
                'user_id'        => auth()->id(),
                'tabla'          => $model->getTable(),
                'registro_id'    => $model->getKey(),
                'accion'         => 'delete',
                'campo'          => null,
                'valor_anterior' => null,
                'valor_nuevo'    => null,
            ]);
        });
    }
}
