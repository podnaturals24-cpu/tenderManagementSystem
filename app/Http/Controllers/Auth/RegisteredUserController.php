<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Show the admin registration view (only for admins).
     */
    public function create(Request $request): View
    {
        // Only allow if logged-in user is admin.
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403);
        }

        // Render a view that includes role selector (admin or user)
        return view('auth.admin-register'); // create this blade
    }

    /**
     * Handle an incoming registration request from admin.
     */
    public function store(Request $request): RedirectResponse
    {
        // Only admins can create users through this endpoint.
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:user,admin'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            // store hashed password
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        // Optional: trigger Registered event if you rely on it
        event(new Registered($user));

        // After admin creates user, do not log them in — redirect back with success.
        return redirect()->route('admin.dashboard')->with('success', 'User created successfully.');
    }
}
