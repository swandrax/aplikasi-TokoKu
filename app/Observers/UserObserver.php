<?php

namespace App\Observers;

use App\Models\User;
use App\Services\ActivityLogger;

class UserObserver
{
    public function created(User $user): void
    {
        ActivityLogger::log(
            'user.created',
            User::class,
            $user->id,
            "Pengguna baru '{$user->name}' dengan role '{$user->role}' telah didaftarkan."
        );
    }

    public function updated(User $user): void
    {
        // Check if role changed
        if ($user->isDirty('role')) {
            $oldRole = $user->getOriginal('role');
            $newRole = $user->role;
            ActivityLogger::log(
                'user.role_changed',
                User::class,
                $user->id,
                "Role pengguna '{$user->name}' diubah dari '{$oldRole}' menjadi '{$newRole}' oleh Administrator."
            );
        }

        // Check if active status changed
        if ($user->isDirty('is_active')) {
            $statusStr = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
            ActivityLogger::log(
                'user.status_changed',
                User::class,
                $user->id,
                "Akun pengguna '{$user->name}' telah {$statusStr}."
            );
        }
    }
}
