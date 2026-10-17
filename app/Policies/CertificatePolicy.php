<?php

namespace App\Policies;

use App\Models\Certificate;
use App\Models\User;

class CertificatePolicy
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
        return $user->isAdmin() || $user->isCentre();
    }

    public function view(User $user, Certificate $certificate): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isCentre()) {
            $centreId = $user->centre_id ?? $user->centre?->id;
            return $centreId && (int)$certificate->centre_id === (int)$centreId;
        }

        if ($user->isStudent()) {
            return (int)$certificate->student?->user_id === (int)$user->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        // Only Central Board Admin or Super Admin can issue certificates
        return $user->isAdmin();
    }
}
