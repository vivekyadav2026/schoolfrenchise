<?php

namespace App\Policies;

use App\Models\Admission;
use App\Models\User;

class AdmissionPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }
        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isCentre() || $user->isStaff();
    }

    public function view(User $user, Admission $admission): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isCentre() || $user->isStaff()) {
            $centreId = $user->centre_id ?? $user->centre?->id;
            return $centreId && (int)$admission->centre_id === (int)$centreId;
        }

        if ($user->isStudent()) {
            return (int)$admission->student?->user_id === (int)$user->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isCentre();
    }

    public function update(User $user, Admission $admission): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isCentre()) {
            $centreId = $user->centre_id ?? $user->centre?->id;
            return $centreId && (int)$admission->centre_id === (int)$centreId;
        }

        return false;
    }
}
