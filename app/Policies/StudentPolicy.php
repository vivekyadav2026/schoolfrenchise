<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;

class StudentPolicy
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

    public function view(User $user, Student $student): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isCentre() || $user->isStaff()) {
            $centreId = $user->centre_id ?? $user->centre?->id;
            return $centreId && (int)$student->centre_id === (int)$centreId;
        }

        if ($user->isStudent()) {
            return (int)$student->user_id === (int)$user->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isCentre() || $user->isStaff();
    }

    public function update(User $user, Student $student): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isCentre() || $user->isStaff()) {
            $centreId = $user->centre_id ?? $user->centre?->id;
            return $centreId && (int)$student->centre_id === (int)$centreId;
        }

        if ($user->isStudent()) {
            // Students can only update their own non-academic contact info
            return (int)$student->user_id === (int)$user->id;
        }

        return false;
    }

    public function delete(User $user, Student $student): bool
    {
        return $user->isSuperAdmin();
    }
}
