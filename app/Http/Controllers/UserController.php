<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'telegram_chat_id' => ['nullable', 'string', 'max:255', 'unique:users'],
            'whatsapp_number' => ['nullable', 'string', 'max:255', 'unique:users'],
        ]);

        $cleanedWhatsapp = $request->whatsapp_number ? preg_replace('/[^0-9]/', '', $request->whatsapp_number) : null;

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'telegram_chat_id' => $request->telegram_chat_id,
            'whatsapp_number' => $cleanedWhatsapp,
            'password' => Hash::make(Str::random(12)), // Contraseña aleatoria por defecto
        ]);

        return back()->with('success', 'User successfully created!');
    }
}
