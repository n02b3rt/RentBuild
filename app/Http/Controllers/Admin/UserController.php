<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    // Lista użytkowników (bez soft deleted)
    public function index()
    {
        $users = User::whereNull('deleted_at')->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    // Formularz tworzenia nowego użytkownika
    public function create()
    {
        return view('admin.users.create');
    }

    // Zapis nowego użytkownika z walidacją
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'address' => 'nullable|string|max:500',
            'shipping_address' => 'nullable|string|max:500',
            'role' => 'nullable|string|max:50',
        ]);

        $user = new User();
        $user->first_name = $validated['first_name'];
        $user->last_name = $validated['last_name'];
        $user->phone = $validated['phone'] ?? null;
        $user->email = $validated['email'];
        $user->password = bcrypt($validated['password']);
        $user->address = $validated['address'] ?? null;
        $user->shipping_address = $validated['shipping_address'] ?? null;
        $user->role = $validated['role'] ?? null;
        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'Użytkownik został dodany.');
    }

    // Formularz edycji użytkownika
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    // Aktualizacja użytkownika z walidacją
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => [
                'required',
                'email',
                'unique:users,email,' . $user->id,
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.(pl|com|org|net)$/', // Sprawdzanie poprawności domeny
            ],
            'password' => 'nullable|string|min:8|confirmed',
            'address' => 'nullable|string|max:500',
            'shipping_address' => 'nullable|string|max:500',
            'role' => 'nullable|string|max:50',
        ]);

        $user->first_name = $validated['first_name'];
        $user->last_name = $validated['last_name'];
        $user->phone = $validated['phone'] ?? null;
        $user->email = $validated['email'];
        if (!empty($validated['password'])) {
            $user->password = bcrypt($validated['password']);
        }
        $user->address = $validated['address'] ?? null;
        $user->shipping_address = $validated['shipping_address'] ?? null;
        $user->role = $validated['role'] ?? null;
        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'Użytkownik został zaktualizowany.');
    }

    // Soft delete użytkownika
    public function destroy(User $user)
    {
        $user->delete(); // soft delete
        return redirect()->route('admin.users.index')->with('success', 'Użytkownik został usunięty.');
    }
}
