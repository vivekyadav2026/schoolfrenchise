<?php

namespace App\Policies;

use App\Models\FeePayment;
use App\Models\User;

class FeePaymentPolicy
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

    public function view(User $user, FeePayment $payment): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isCentre() || $user->isStaff()) {
            $centreId = $user->centre_id ?? $user->centre?->id;
            return $centreId && (int)$payment->centre_id === (int)$centreId;
        }

        if ($user->isStudent()) {
            return (int)$payment->student?->user_id === (int)$user->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isCentre() || $user->isStaff();
    }
}
