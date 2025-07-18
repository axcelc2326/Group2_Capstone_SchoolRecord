<?php
namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $users = User::query()
            ->when($search, function($query) use ($search) {
                $query->where('name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%");
            })
            ->with('roles')
            ->orderBy('name', 'asc')
            ->paginate(15)
            ->through(fn($user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->roles->pluck('name')->first() ?? 'none',
            ])
            ->withQueryString();

        $roles = Role::all()->pluck('name');

        return Inertia::render('Admin/ManageUsers', [
            'users' => $users,
            'roles' => $roles,
            'filters' => [
                'search' => $search,
            ]
        ]);
    }

    public function show(User $user)
    {
        $user->load('roles');

        // ✅ Only teacher and parent roles are selectable
        $roles = ['teacher', 'parent'];

        return Inertia::render('Admin/UserShow', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->roles->pluck('name')->first() ?? 'none',
            ],
            'roles' => $roles,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6',
            'role' => 'required|in:teacher,parent', // ✅ Validate role
        ]);

        $user->email = $validated['email'];

        if ($validated['password']) {
            $user->password = bcrypt($validated['password']);
        }

        $user->save();

        // ✅ Update role
        $user->syncRoles($validated['role']);

        return redirect()->route('admin.users.index')->with('success', 'User updated.');
    }
}
