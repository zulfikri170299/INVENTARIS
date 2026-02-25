<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Satker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (auth()->user()->role !== 'Super Admin') {
                abort(403, 'Akses ditolak.');
            }
            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with('satker')->latest()->paginate(10);
        $satkers = Satker::all();
        return view('user.index', compact('users', 'satkers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:Super Admin,Super Admin 2,Admin Satker,Pimpinan'],
            'satker_id' => ['nullable', 'exists:satkers,id'],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'satker_id' => $request->satker_id,
        ]);

        return back()->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'role' => ['required', 'in:Super Admin,Super Admin 2,Admin Satker,Pimpinan'],
            'satker_id' => ['nullable', 'exists:satkers,id'],
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'satker_id' => $request->satker_id,
        ];

        if ($request->filled('password')) {
            $request->validate([
                'password' => ['confirmed', Rules\Password::defaults()],
            ]);
            $data['password'] = Hash::make($request->password);
        }

        \Log::debug("Updating User ID: " . $id, $data);
        $user->update($data);

        return redirect()->route('user.index')->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak bisa menghapus akun sendiri.');
        }

        $user->delete();

        return back()->with('success', 'User berhasil dihapus.');
    }

    /**
     * Reset the specified user's password.
     */
    public function resetPassword(User $user)
    {
        // Reset to a default password (e.g., 'password')
        $newPassword = 'password'; 
        
        $user->update([
            'password' => Hash::make($newPassword),
        ]);

        return back()->with('success', "Password untuk {$user->name} berhasil direset menjadi: {$newPassword}");
    }
}
