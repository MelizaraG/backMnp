<?php

namespace App\Policies;

use App\Models\Sortie;
use App\Models\User;

class SortiePolicy
{
    public function deleteSortie(User $user, Sortie $sortie)
    {
        return $user->id === $sortie->user_id;
    }

    public function updateSortie(User $user, Sortie $sortie)
    {
        return $user->id === $sortie->user_id;
    }
}
