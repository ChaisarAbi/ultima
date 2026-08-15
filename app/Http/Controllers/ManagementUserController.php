<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\ActivityLog;

class ManagementUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Search by name or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(10);
        
        // Role counts for stats
        $userStats = [
            'total' => User::count(),
            'manajer' => User::where('role', 'manajer')->count(),
            'office' => User::where('role', 'office')->count(),
            'teknisi' => User::where('role', 'teknisi')->count(),
        ];

        return view('management.users.index', compact('users', 'userStats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('management.users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:manajer,office,teknisi',
        ]);

        try {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'],
            ]);

            ActivityLog::log('create_user', 'Menambahkan user baru: ' . $user->name . ' (' . $validated['role'] . ')', null, null, [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ]);

            return redirect()->route('management.users.index')
                ->with('success', 'User berhasil ditambahkan.');
        } catch (\Exception $e) {
            Log::error('Failed to create user: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Gagal menambahkan user. Silakan coba lagi.']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return view('management.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('management.users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:manajer,office,teknisi',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        try {
            $oldData = $user->toArray();
            
            $updateData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'role' => $validated['role'],
            ];

            if (!empty($validated['password'])) {
                $updateData['password'] = Hash::make($validated['password']);
            }

            $user->update($updateData);

            ActivityLog::log('update_user', 'Mengupdate user: ' . $user->name, null, $oldData, $user->toArray());

            return redirect()->route('management.users.index')
                ->with('success', 'User berhasil diupdate.');
        } catch (\Exception $e) {
            Log::error('Failed to update user: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Gagal mengupdate user. Silakan coba lagi.']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Prevent deleting yourself
        if (auth()->id() === $user->id) {
            return back()->withErrors(['error' => 'Anda tidak dapat menghapus akun sendiri.']);
        }

        $userName = $user->name;
        $user->delete();

        ActivityLog::log('delete_user', 'Menghapus user: ' . $userName);

        return redirect()->route('management.users.index')
            ->with('success', 'User berhasil dihapus.');
    }

    /**
     * Reset password page
     */
    public function resetPassword(User $user)
    {
        return view('management.users.reset-password', compact('user'));
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request, User $user)
    {
        $validated = $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        ActivityLog::log('reset_password', 'Reset password user: ' . $user->name);

        return redirect()->route('management.users.index')
            ->with('success', 'Password berhasil direset.');
    }
}