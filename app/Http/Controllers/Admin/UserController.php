<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index()
    {
        $users = User::withCount('posts')->orderBy('name')->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.form', ['user' => new User(['role' => User::ROLE_EDITOR])]);
    }

    public function store(Request $request)
    {
        User::create($this->validated($request));

        return redirect()->route('admin.users.index')->with('success', 'Uživatel byl úspěšně vytvořen.');
    }

    public function edit(User $user)
    {
        return view('admin.users.form', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $this->validated($request, $user);

        // Přihlášený admin si nesmí sebrat práva – tím je zaručeno, že vždy zůstane aspoň jeden admin.
        if ($data['role'] !== $user->role && $user->is($request->user())) {
            return back()->withInput()->withErrors(['role' => 'Nemůžete změnit roli vlastnímu účtu.']);
        }

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'Uživatel byl upraven.');
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->is($request->user())) {
            return back()->withErrors(['Nemůžete smazat vlastní účet.']);
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Uživatel byl úspěšně smazán.');
    }

    private function validated(Request $request, ?User $user = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user?->id)],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users')->ignore($user?->id)],
            'role' => ['required', Rule::in(array_keys(User::ROLES))],
            'password' => [$user ? 'nullable' : 'required', 'confirmed', Password::defaults()],
        ], [], [
            'name' => 'jméno',
            'username' => 'uživatelské jméno',
            'email' => 'e-mail',
            'role' => 'role',
            'password' => 'heslo',
        ]);
    }
}
