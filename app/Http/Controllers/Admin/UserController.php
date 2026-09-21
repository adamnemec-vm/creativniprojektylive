<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::latest()->paginate(15);
        
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:'.User::class],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Uživatel byl úspěšně vytvořen.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Ochrana hlavního administrátora
        if ($user->username === 'admin' || $user->id === 1) {
             return redirect()->route('admin.users.index')->withErrors(['Hlavního administrátora nelze smazat.']);
        }

        if (User::count() <= 1) {
            return redirect()->route('admin.users.index')->withErrors(['Posledního uživatele nelze smazat.']);
        }
        
        if (auth()->id() === $user->id) {
             return redirect()->route('admin.users.index')->withErrors(['Nemůžete smazat vlastní účet.']);
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Uživatel byl úspěšně smazán.');
    }
}
