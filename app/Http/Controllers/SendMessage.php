<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class SendMessage extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $data = $request->validate([
            'platform' => 'required|in:' . implode(',', array_keys(config('platforms'))),
            'message' => 'required',
            'users' => 'required|array',
            'users.*' => 'int|exists:users,id'
        ]);

        $platformService = app(config('platforms')[$data['platform']]);
        $users = User::findMany($data['users'])->all();
        $platformService->sendMassMessage($users, $data['message']);

        return back()->with('success', 'Message sent');
    }
}
