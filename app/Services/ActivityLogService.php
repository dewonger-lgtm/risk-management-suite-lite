<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Request;

/**
 * Class ActivityLogService
 * 
 * Service untuk logging semua aktivitas user untuk audit trail.
 */
class ActivityLogService
{
    /**
     * Log user activity
     */
    public function log(
        string $userId,
        string $companyId,
        string $action,
        string $model,
        string $modelId
    ): ActivityLog {
        return ActivityLog::create([
            'user_id' => $userId,
            'company_id' => $companyId,
            'action' => $action,
            'model' => $model,
            'model_id' => $modelId,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Log activity with changes tracking
     */
    public function logWithChanges(
        string $userId,
        string $companyId,
        string $action,
        string $model,
        string $modelId,
        array $oldData,
        array $newData
    ): ActivityLog {
        $changes = $this->getChanges($oldData, $newData);

        return ActivityLog::create([
            'user_id' => $userId,
            'company_id' => $companyId,
            'action' => $action,
            'model' => $model,
            'model_id' => $modelId,
            'changes' => $changes,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Get changes between old and new data
     */
    private function getChanges(array $oldData, array $newData): array
    {
        $changes = [];

        foreach ($newData as $key => $newValue) {
            $oldValue = $oldData[$key] ?? null;

            if ($oldValue !== $newValue) {
                $changes[$key] = [
                    'old' => $oldValue,
                    'new' => $newValue,
                ];
            }
        }

        return $changes;
    }
}
