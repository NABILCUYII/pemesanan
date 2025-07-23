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

    // API endpoint: hanya user dengan role 'penggunaBARU'
    public function getNewUsers()
    {
        $users = User::whereHas('role', function($q) {
            $q->where('role', 'penggunaBARU');
        })->with('role')->get();
        // Pastikan data role selalu ada (meskipun null)
        $usersArr = $users->map(function($user) {
            $arr = $user->toArray();
            $arr['role'] = $user->role ? $user->role->toArray() : null;
            return $arr;
        });
        return response()->json(['users' => $usersArr]);
    }

    // Halaman verifikasi user baru (opsional, jika ingin render inertia)
    public function newuser()
    {
        $users = User::whereHas('role', function($q) {
            $q->where('role', 'penggunaBARU');
        })->with('role')->get();
        // Pastikan data role selalu ada (meskipun null)
        $usersArr = $users->map(function($user) {
            $arr = $user->toArray();
            $arr['role'] = $user->role ? $user->role->toArray() : null;
            return $arr;
        });
        return Inertia::render('users/NEWuser', [
            'users' => $usersArr
        ]);
    }
    
    public function index()
    {
        $user = Auth::user();

        // Check if user is admin
        if (!$user || !$user->role || ($user->role->role ?? '') !== 'admin') {
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
        if (!$user || !$user->role || ($user->role->role ?? '') !== 'admin') {
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
        if (!$user || !$user->role || ($user->role->role ?? '') !== 'admin') {
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
        if (!$currentUser || !$currentUser->role || ($currentUser->role->role ?? '') !== 'admin') {
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
        if (!$currentUser || !$currentUser->role || ($currentUser->role->role ?? '') !== 'admin') {
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
        if (!$currentUser || !$currentUser->role || ($currentUser->role->role ?? '') !== 'admin') {
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

    public function showApi($id) {
        $user = User::with('role')->findOrFail($id);
        // Pastikan data role selalu ada (meskipun null)
        $userArr = $user->toArray();
        $userArr['role'] = $user->role ? $user->role->toArray() : null;
        return response()->json(['user' => $userArr]);
    }

    public function approveNewUser($id)
    {
        $user = User::with('role')->findOrFail($id);
        if ($user->role && $user->role->role === 'penggunaBARU') {
            $user->role->role = 'user';
            $user->role->save();
        }
        $user->status = 'active';
        $user->save();
        return redirect()->back();
    }

    public function rejectNewUser($id)
    {
        $user = User::with('role')->findOrFail($id);
        // Hapus relasi role jika ada
        if ($user->role) {
            $user->role()->delete();
        }
        $user->delete();
        return redirect()->back();
    }


}