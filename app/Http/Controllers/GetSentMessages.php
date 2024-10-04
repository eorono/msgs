<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GetSentMessages extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $messages = auth()->user()->messages;

        return view('sent', compact('messages'));
    }
}
