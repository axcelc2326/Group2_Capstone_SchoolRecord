<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ParentController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        // Parents with their students
        $parents = User::role('parent')
            ->with(['students.class']) // include each student's class
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->paginate(50)
            ->withQueryString();

        // All classes (for dropdowns, selection, etc.)
        $classes = ClassModel::select('id', 'name', 'grade_level')->get();

        // All students with their class
        $students = Student::with('class')->get();

        return Inertia::render('Register/Index', [
            'parents' => $parents,
            'classes' => $classes,
            'students' => $students,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
        ]);

        $user->assignRole('parent'); // 👈 auto parent role

        return back()->with('success', 'Parent registered successfully!');
    }

    public function getStudents(User $parent)
    {
        $students = $parent->students()->with('class')->get();

        return response()->json([
            'students' => $students,
        ]);
    }
}
