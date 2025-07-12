<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user()->load('roles');
        $role = $user->getRoleNames()->first();

        switch ($role) {
            case 'admin':
                return Inertia::render('Admin/Dashboard', ['user' => $user]);
            case 'teacher':
                return Inertia::render('Teacher/Dashboard', ['user' => $user]);
            case 'parent':
                return Inertia::render('Parent/Dashboard', ['user' => $user]);
            default:
                abort(403, 'Unauthorized');
        }
    }
}
