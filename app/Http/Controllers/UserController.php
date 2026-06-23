<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Check if current user has admin access (including mapped roles)
     */
    private function isAdmin(): bool
    {
        $userRole = Auth::user()->role;
        
        // Role mappings (same as middleware)
        $adminRoles = ['admin', 'admin_full_access', 'admin_laman', 'admin_member', 'admin_bnh'];
        
        return in_array($userRole, $adminRoles);
    }

    /**
     * Get all valid roles
     */
    private function getValidRoles(): array
    {
        return [
            'admin',
            'admin_full_access',
            'admin_laman',
            'admin_member',
            'admin_bnh',
            'organizer',
            'bendahara',
            'sponsor',
            'merchandise',
            'participant'
        ];
    }

    /**
     * Show login page
     */
    public function login()
    {
        return view('login');
    }

    /**
     * Handle login process
     */
    public function auth(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();

            $user = Auth::user();

            if ($this->isAdmin() || $user->role === 'organizer') {
                return redirect()->route('dashboard.index');
            }

            return redirect('/');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah'
        ])->withInput();
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Berhasil logout');
    }

    /**
     * Display a listing of users (Admin only)
     */
    public function index()
    {
        if (!$this->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $users = User::latest()->paginate(15);
        $totalUsers = User::count();
        $totalAdmins = User::whereIn('role', ['admin', 'admin_full_access', 'admin_laman', 'admin_member', 'admin_bnh'])->count();
        $totalOrganizers = User::where('role', 'organizer')->count();
        $totalBendahara = User::where('role', 'bendahara')->count();
        $totalSponsor = User::where('role', 'sponsor')->count();
        $totalMerchandise = User::where('role', 'merchandise')->count();
        $totalParticipants = User::where('role', 'participant')->count();

        return view('users.index', compact(
            'users', 
            'totalUsers', 
            'totalAdmins', 
            'totalOrganizers',
            'totalBendahara',
            'totalSponsor',
            'totalMerchandise',
            'totalParticipants'
        ));
    }

    /**
     * Show form to create new user
     */
    public function create()
    {
        if (!$this->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $roles = $this->getValidRoles();
        
        return view('users.create', compact('roles'));
    }

    /**
     * Store a new user
     */
    public function store(Request $request)
    {
        if (!$this->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $validRoles = $this->getValidRoles();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'role' => 'required|in:' . implode(',', $validRoles)
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil ditambahkan');
    }

    /**
     * Display user details
     */
    public function show(User $user)
    {
        if (!$this->isAdmin()) {
            abort(403, 'Unauthorized access');
        }
        
        return view('users.show', compact('user'));
    }

    /**
     * Show form to edit user
     */
    public function edit(User $user)
    {
        if (!$this->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $roles = $this->getValidRoles();
        
        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Update user
     */
    public function update(Request $request, User $user)
    {
        if (!$this->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        $validRoles = $this->getValidRoles();

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:' . implode(',', $validRoles)
        ];

        if ($request->filled('password')) {
            $rules['password'] = 'min:6|confirmed';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil diupdate');
    }

    /**
     * Delete user
     */
    public function destroy(User $user)
    {
        if (!$this->isAdmin()) {
            abort(403, 'Unauthorized access');
        }

        // Prevent admin from deleting themselves
        if ($user->id === Auth::id()) {
            return redirect()->route('users.index')
                ->with('error', 'Tidak dapat menghapus akun sendiri');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User berhasil dihapus');
    }

    /**
     * Fix all users with wrong roles
     * Run this once: php artisan tinker -> app(UserController::class)->fixUserRoles()
     */
    public function fixUserRoles()
    {
        $roleMapping = [
            'admin.full@sh3.com' => 'admin_full_access',
            'admin.laman@sh3.com' => 'admin_laman',
            'admin.member@sh3.com' => 'admin_member',
            'admin.bnh@sh3.com' => 'admin_bnh',
            'organizer@sh3.com' => 'organizer',
            'bendahara@sh3.com' => 'bendahara',
            'sponsor@sh3.com' => 'sponsor',
            'merchandise@sh3.com' => 'merchandise',
            'participant@sh3.com' => 'participant',
        ];

        $updated = [];
        foreach ($roleMapping as $email => $role) {
            $user = User::where('email', $email)->first();
            if ($user && $user->role !== $role) {
                $user->role = $role;
                $user->save();
                $updated[] = $email . ' -> ' . $role;
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Roles updated',
            'updated' => $updated
        ]);
    }
}