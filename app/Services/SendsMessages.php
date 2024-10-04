<?php

namespace App\Services;

use App\Models\User;

interface SendsMessages
{
    public function sendMessage(User $user, $message);

    public function sendMassMessage(array $users, $message);
}
