<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function showProfile()
    {
        $user = Auth::user();
        return view('profile.show', compact('user'));
    }

    public function editProfile()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'telephone' => 'nullable|string|max:30',
            'photo' => 'nullable|image|max:51200', // 50MB
        ], [
            'photo.max' => 'La photo ne doit pas dépasser 50MB (limite serveur PHP).',
        ]);

        if ($request->hasFile('photo')) {
            if ($user->photo_path) {
                Storage::disk('public')->delete($user->photo_path);
            }
            $path = $request->file('photo')->store('profiles', 'public');
            $user->photo_path = $path;
        }

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->telephone = $validated['telephone'] ?? null;
        $user->save();

        return redirect()->route('profile.show')->with('success', 'Profil mis à jour avec succès.');
    }
} 