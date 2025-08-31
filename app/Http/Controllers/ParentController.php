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
        $classId = $request->input('class_id'); // selected class filter

        // Filtered parents query
        $parentQuery = User::role('parent')
            ->with(['students.class'])
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->when($classId, function ($query, $classId) {
                $query->whereHas('students', function ($q) use ($classId) {
                    $q->where('class_id', $classId);
                });
            });

        // Paginated parents (with filters)
        $parents = $parentQuery->paginate(50)->withQueryString();

        // ✅ Total parents ever created (all with parent role, ignoring filters)
        $totalMadeParents = User::role('parent')->count();

        // All classes for dropdown
        $classes = ClassModel::select('id', 'name', 'grade_level')->get();

        return Inertia::render('Register/Index', [
            'parents' => $parents,
            'classes' => $classes,
            'totalMadeParents' => $totalMadeParents, // 👈 pass to frontend
            'filters' => [
                'search' => $search,
                'class_id' => $classId,
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

    public function update(Request $request, User $parent)
    {
        // Validate input
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $parent->id,
            'password' => 'nullable|string|confirmed|min:6',
        ]);

        // Update parent info
        $parent->name = $data['name'];
        $parent->email = $data['email'];
        if (!empty($data['password'])) {
            $parent->password = bcrypt($data['password']);
        }
        $parent->save();

        return back()->with('success', 'Parent Updated successfully!');
    }

    public function destroy($id)
    {
        $parent = User::findOrFail($id);

        // Delete all students linked to this parent
        $parent->students()->delete();

        // Delete the parent user account
        $parent->delete();

        return back()->with('success', 'Parent and their students deleted successfully.');
    }

    public function getStudents(User $parent)
    {
        $students = $parent->students()->with('class')->get();

        return response()->json([
            'students' => $students,
        ]);
    }
}
