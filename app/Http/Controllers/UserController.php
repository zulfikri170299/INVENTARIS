<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Satker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Traits\LogActivity;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\UserExport;

class UserController extends Controller implements HasMiddleware
{
    use LogActivity;
    public static function middleware(): array
    {
        return [
            new Middleware(function ($request, $next) {
                if (auth()->user()->role !== 'Super Admin') {
                    abort(403, 'Akses ditolak.');
                }
                return $next($request);
            }),
        ];
    }

    private function getFilteredQuery(Request $request)
    {
        $query = User::with('satker');

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('role', 'like', '%' . $request->search . '%');
            });
        }

        return $query->latest();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = $this->getFilteredQuery($request);
        $perPage = $request->input('per_page', 10);
        $users = $query->paginate($perPage)->withQueryString();
        $satkers = Satker::all();

        return view('user.index', compact('users', 'satkers'));
    }

    public function exportExcel(Request $request)
    {
        $query = $this->getFilteredQuery($request);
        return Excel::download(new UserExport($query), 'data-user.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $users = $this->getFilteredQuery($request)->get();
        $pdf = Pdf::loadView('user.pdf', compact('users'));
        return $pdf->download('data-user.pdf');
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

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'satker_id' => $request->satker_id,
        ]);

        $this->logActivity('Tambah User', 'Menambahkan user baru: ' . $user->name . ' (' . $user->role . ')', 'Manajemen User');

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

        $this->logActivity('Update User', 'Memperbarui data user: ' . $user->name . ' (' . $user->role . ')', 'Manajemen User');

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

        $this->logActivity('Hapus User', 'Menghapus user: ' . $user->name . ' (' . $user->role . ')', 'Manajemen User');
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

        $this->logActivity('Reset Password', 'Mereset password user: ' . $user->name, 'Manajemen User');

        return back()->with('success', "Password untuk {$user->name} berhasil direset menjadi: {$newPassword}");
    }
}
