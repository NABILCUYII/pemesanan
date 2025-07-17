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
    /**
     * Helper to get the current authenticated user.
     *
     * @return \App\Models\User|null
     */
    protected function currentUser()
    {
        return Auth::user();
    }

    /**
     * Helper to check if the current user is admin.
     *
     * @return bool
     */
    protected function isAdmin()
    {
        $user = $this->currentUser();
        return $user && method_exists($user, 'isAdmin') && $user->isAdmin();
    }

    /**
     * Helper to return the forbidden Inertia response.
     *
     * @return \Inertia\Response
     */
    protected function forbiddenResponse()
    {
        $user = $this->currentUser();
        return Inertia::render('Forbidden', [
            'user' => $user ? [
                'name' => $user->name,
                'role' => $user->role->role ?? 'User'
            ] : null
        ]);
    }

    public function index(Request $request)
    {
        $block = $this->checkNewUserBlock();
        if ($block) return $block;
        if (!$this->isAdmin()) {
            return $this->forbiddenResponse();
        }

        $query = User::with('role');

        if ($request->q) {
            $query->where('name', 'like', '%' . $request->q . '%')
                  ->orWhere('email', 'like', '%' . $request->q . '%');
        }

        $perPage = $request->per_page ?? 10;
        $users = $query->paginate($perPage);

        return Inertia::render('users/index', [
            'users' => $users->items(), // array user untuk frontend
            'meta' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ],
            'query' => [
                'page' => $users->currentPage(),
                'per_page' => $users->perPage(),
                'q' => $request->q,
            ],
        ]);
    }
    public function create()
    {
        $block = $this->checkNewUserBlock();
        if ($block) return $block;
        if (!$this->isAdmin()) {
            return $this->forbiddenResponse();
        }

        return Inertia::render('users/create');
    }

    public function store(Request $request)
    {
        $block = $this->checkNewUserBlock();
        if ($block) return $block;
        if (!$this->isAdmin()) {
            return $this->forbiddenResponse();
        }

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
        $block = $this->checkNewUserBlock();
        if ($block) return $block;
        if (!$this->isAdmin()) {
            return $this->forbiddenResponse();
        }

        return Inertia::render('users/edit', [
            'user' => $user->load('role')
        ]);
    }

    public function update(Request $request, User $user)
    {
        $block = $this->checkNewUserBlock();
        if ($block) return $block;
        if (!$this->isAdmin()) {
            return $this->forbiddenResponse();
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
        $block = $this->checkNewUserBlock();
        if ($block) return $block;
        if (!$this->isAdmin()) {
            return $this->forbiddenResponse();
        }

        // Hapus semua permintaan yang dibuat user
        $user->permintaan()->delete();
        // Hapus semua peminjaman yang dibuat user
        $user->peminjaman()->delete();
        // Hapus semua inventaris yang dibuat user
        if (method_exists($user, 'inventaris')) {
            $user->inventaris()->delete();
        } else {
            \App\Models\Inventaris::where('user_id', $user->id)->delete();
        }
        // Hapus semua peminjaman di mana user menjadi approved_by, returned_by, started_by
        \App\Models\Peminjaman::where('approved_by', $user->id)->delete();
        \App\Models\Peminjaman::where('returned_by', $user->id)->delete();
        \App\Models\Peminjaman::where('started_by', $user->id)->delete();
        // Hapus semua inventaris di mana user menjadi approved_by, returned_by, started_by
        \App\Models\Inventaris::where('approved_by', $user->id)->delete();
        \App\Models\Inventaris::where('returned_by', $user->id)->delete();
        \App\Models\Inventaris::where('started_by', $user->id)->delete();
        // Setelah semua riwayat terkait dihapus, hapus user
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
