<?php

namespace App\Policies;

use App\Models\Reception;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ReceptionPolicy
{
    public function modify(User $user, Reception $reception): Response
    {
        return $user -> id === $reception -> user_id 
        ? Response ::allow()
        : Response ::deny("Tu n'est pas le popriétaire de cet publication");
    }
}
