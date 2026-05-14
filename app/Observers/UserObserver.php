<?php

namespace App\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class UserObserver
{
    public function saving(Model $user): void
    {
        if ($user->isDirty('password') && ! str_starts_with((string) $user->password, '$2y$')) {
            $user->password = Hash::make($user->password);
        }
    }
}
