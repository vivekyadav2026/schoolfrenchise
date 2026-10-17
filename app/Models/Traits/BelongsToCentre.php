<?php

namespace App\Models\Traits;

use App\Models\Centre;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait BelongsToCentre
{
    public static function bootBelongsToCentre(): void
    {
        // Global scope to isolate centre data
        static::addGlobalScope('centre_isolation', function (Builder $builder) {
            if (Auth::check()) {
                $user = Auth::user();

                // If user is a centre manager or staff, restrict queries to their centre
                if ($user->isCentre() || $user->isStaff()) {
                    $centreId = $user->centre_id ?? $user->centre?->id;
                    if ($centreId) {
                        $builder->where($builder->getModel()->getTable() . '.centre_id', $centreId);
                    }
                }
            }
        });

        // Automatically assign centre_id when creating new records if not explicitly passed
        static::creating(function (Model $model) {
            if (empty($model->centre_id) && Auth::check()) {
                $user = Auth::user();
                if ($user->isCentre() || $user->isStaff()) {
                    $model->centre_id = $user->centre_id ?? $user->centre?->id;
                }
            }
        });
    }

    public function centre()
    {
        return $this->belongsTo(Centre::class);
    }
}
