<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Check if user is admin
        if (!$user || !$user->role || ($user->role->role ?? '') !== 'Admin') {
            return inertia('Forbidden', [
                'user' => $user ? [
                    'name' => $user->name,
                    'role' => $user->role->role ?? 'User'
                ] : null
            ]);
        }
        
        $users = User::with('role')->get();
        return Inertia::render('users/index', [
            'users' => $users
        ]);
    }

    public function create()
    {
        $user = Auth::user();

        // Check if user is admin
        if (!$user || !$user->role || ($user->role->role ?? '') !== 'Admin') {
            return inertia('Forbidden', [
                'user' => $user ? [
                    'name' => $user->name,
                    'role' => $user->role->role ?? 'User'
                ] : null
            ]);
        }
        
        return Inertia::render('users/create');
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        // Check if user is admin
        if (!$user || !$user->role || ($user->role->role ?? '') !== 'Admin') {
            return inertia('Forbidden', [
                'user' => $user ? [
                    'name' => $user->name,
                    'role' => $user->role->role ?? 'User'
                ] : null
            ]);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|string'
        ]);

        $newUser = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
        ]);

        $newUser->role()->create([
            'role' => $validated['role']
        ]);

        return redirect()->route('users.index');
    }

    public function edit(User $user)
    {
        $currentUser = Auth::user();

        // Check if user is admin
        if (!$currentUser || !$currentUser->role || ($currentUser->role->role ?? '') !== 'Admin') {
            return inertia('Forbidden', [
                'user' => $currentUser ? [
                    'name' => $currentUser->name,
                    'role' => $currentUser->role->role ?? 'User'
                ] : null
            ]);
        }
        
        return Inertia::render('users/edit', [
            'user' => $user->load('role')
        ]);
    }

    public function update(Request $request, User $user)
    {
        $currentUser = Auth::user();

        // Check if user is admin
        if (!$currentUser || !$currentUser->role || ($currentUser->role->role ?? '') !== 'Admin') {
            return inertia('Forbidden', [
                'user' => $currentUser ? [
                    'name' => $currentUser->name,
                    'role' => $currentUser->role->role ?? 'User'
                ] : null
            ]);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'nullable|string'
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if (array_key_exists('role', $validated) && $validated['role'] !== null && $validated['role'] !== '') {
            if ($user->role) {
                $user->role()->update([
                    'role' => $validated['role']
                ]);
            } else {
                $user->role()->create([
                    'role' => $validated['role']
                ]);
            }
        }

        return redirect()->route('users.index');
    }

    public function destroy(User $user)
    {
        $currentUser = Auth::user();

        // Check if user is admin
        if (!$currentUser || !$currentUser->role || ($currentUser->role->role ?? '') !== 'Admin') {
            return inertia('Forbidden', [
                'user' => $currentUser ? [
                    'name' => $currentUser->name,
                    'role' => $currentUser->role->role ?? 'User'
                ] : null
            ]);
        }
        // Delete profile photo from storage if exists
        if ($user->photo && Storage::disk('public')->exists($user->photo)) {
            Storage::disk('public')->delete($user->photo);
        }
        if ($user->role) {
            $user->role()->delete();
        }
        $user->delete();
        return redirect()->route('users.index');
    }
}
