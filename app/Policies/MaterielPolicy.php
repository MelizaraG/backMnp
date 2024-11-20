<?php

namespace App\Policies;

use App\Models\Materiel;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MaterielPolicy
{
    public function modify(User $user, Materiel $materiel): Response
    {
        return $user -> id === $materiel -> user_id 
        ? Response ::allow()
        : Response ::deny("Tu n'est pas le popriétaire de cet publication");
    }
}
