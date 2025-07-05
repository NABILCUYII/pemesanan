<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index()
    {

        // Check if user is admin
        if (!auth()->user()->isAdmin()) {
            return inertia('Forbidden', [
                'user' => auth()->user() ? [
                    'name' => auth()->user()->name,
                    'role' => auth()->user()->role ?? 'User'
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

        // Check if user is admin
        if (!auth()->user()->isAdmin()) {
            return inertia('Forbidden', [
                'user' => auth()->user() ? [
                    'name' => auth()->user()->name,
                    'role' => auth()->user()->role ?? 'User'
                ] : null
            ]);
        }
        
        return Inertia::render('users/create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|string'
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
        ]);

        $user->role()->create([
            'role' => $validated['role']
        ]);

        return redirect()->route('users.index');
    }

    public function edit(User $user)
    {

        // Check if user is admin
        if (!auth()->user()->isAdmin()) {
            return inertia('Forbidden', [
                'user' => auth()->user() ? [
                    'name' => auth()->user()->name,
                    'role' => auth()->user()->role ?? 'User'
                ] : null
            ]);
        }
        
        return Inertia::render('users/edit', [
            'user' => $user->load('role')
        ]);
    }

    public function update(Request $request, User $user)
    {
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
            $user->role()->update([
                'role' => $validated['role']
            ]);
        }

        return redirect()->route('users.index');
    }

    public function destroy(User $user)
    {

        // Check if user is admin
        if (!auth()->user()->isAdmin()) {
            return inertia('Forbidden', [
                'user' => auth()->user() ? [
                    'name' => auth()->user()->name,
                    'role' => auth()->user()->role ?? 'User'
                ] : null
            ]);
        }
        $user->role()->delete();
        $user->delete();
        return redirect()->route('users.index');
    }
}
