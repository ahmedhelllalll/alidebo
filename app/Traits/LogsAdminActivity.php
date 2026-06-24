<?php

namespace App\Traits;

use App\Models\AdminActivityLog;
use Illuminate\Database\Eloquent\Model;

trait LogsAdminActivity
{
    /**
     * Log an action performed by an admin.
     *
     * @param string $action Provide a descriptive action key (e.g., 'category_created', 'business_approved')
     * @param Model $model The Eloquent model affected.
     */
    protected function logAdminAction(string $action, Model $model): void
    {
        if (!auth()->check()) {
            return;
        }

        AdminActivityLog::create([
            'admin_id' => auth()->id(),
            'action' => $action,
            'model_type' => get_class($model),
            'model_id' => $model->id,
        ]);
    }
}
