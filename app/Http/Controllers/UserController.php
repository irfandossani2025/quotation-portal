<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private function adminOnly(): void
    {
        abort_unless(auth()->user()->role === 'admin', 403);
    }

    public function index()
    {
        $this->adminOnly();

        return view('users.index', ['users' => User::orderBy('office')->orderBy('name')->get()]);
    }

    public function store(Request $request)
    {
        $this->adminOnly();
        $data = $request->validate([
            'name' => ['required', 'max:255'], 'email' => ['required', 'email', 'unique:users'], 'phone' => ['required', 'max:50'],
            'office' => ['required', 'in:Muscat,Dubai'], 'role' => ['required', 'in:admin,preparer,pricing'], 'password' => ['required', 'min:12'],
        ]);
        User::create($data);

        return back()->with('success', 'User account created.');
    }
}
