<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class ParentController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $classId = $request->input('class_id');
        $hasStudents = $request->input('has_students'); // New filter: 'with_students', 'without_students', or null for all

        // Filtered parents query - explicitly exclude teacher and admin roles
        $parentQuery = User::whereHas('roles', function ($query) {
                $query->where('name', 'parent');
            })
            ->with(['students.class'])
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->when($classId !== null, function ($query) use ($classId) {
                if ($classId === 'null') {
                    $query->whereHas('students', function ($q) {
                        $q->whereNull('class_id');
                    });
                } else {
                    $query->whereHas('students', function ($q) use ($classId) {
                        $q->where('class_id', $classId);
                    });
                }
            })
            ->when($hasStudents !== null, function ($query) use ($hasStudents) {
                if ($hasStudents === 'without_students') {
                    $query->whereDoesntHave('students');
                } elseif ($hasStudents === 'with_students') {
                    $query->whereHas('students');
                }
                // If 'all' or any other value, don't apply any student existence filter
            });

        // Paginated parents (with filters)
        $parents = $parentQuery->paginate(50)->withQueryString();

        // ✅ Total parents ever created - also use the same explicit filter
        $totalMadeParents = User::whereHas('roles', function ($query) {
            $query->where('name', 'parent');
        })->count();

        // All classes for dropdown
        $classes = ClassModel::select('id', 'name', 'grade_level')->get();

        return Inertia::render('Register/Index', [
            'parents' => $parents,
            'classes' => $classes,
            'totalMadeParents' => $totalMadeParents,
            'filters' => [
                'search' => $search,
                'class_id' => $classId,
                'has_students' => $hasStudents, // Add the new filter to filters array
            ],
        ]);
    }

    public function getStudents(User $parent)
    {
        $students = $parent->students()->with('class')->get();

        return response()->json([
            'students' => $students,
        ]);
    }

    public function toggleStatus($id)
    {
        $parent = User::findOrFail($id);

        $parent->status = $parent->status === 'active' ? 'inactive' : 'active';
        $parent->save();

        return back()->with('success', 'Parent status updated successfully.');
    }

    public function store(Request $request)
    {
        // Validate both parent and student data
        $validated = $request->validate([
            // Parent field
            'name' => 'required|string|max:255',
            
            // Student fields
            'student_first_name' => 'required|string|max:255',
            'student_middle_name' => 'nullable|string|max:255',
            'student_last_name' => 'required|string|max:255',
            'student_lrn' => 'required|digits:12|unique:students,lrn',
            'student_gender' => 'required|in:male,female',
            'class_id' => 'required|exists:classes,id',
        ]);

        // Start database transaction to ensure both operations succeed or fail together
        DB::beginTransaction();

        try {
            // Create Parent Account (your original logic)
            $firstName = explode(' ', trim($validated['name']))[0];
            $email = strtolower($firstName) . '@gmail.com';
            $password = substr($firstName, 0, 3) . '_2025';

            // Check if email already exists and make it unique if needed
            $baseEmail = $email;
            $counter = 1;
            while (User::where('email', $email)->exists()) {
                $email = str_replace('@gmail.com', $counter . '@gmail.com', $baseEmail);
                $counter++;
            }

            $user = User::create([
                'name' => $validated['name'],
                'email' => $email,
                'password' => bcrypt($password),
            ]);

            $user->assignRole('parent');

            // Create Student Record linked to this parent
            Student::create([
                'first_name' => $validated['student_first_name'],
                'middle_name' => $validated['student_middle_name'],
                'last_name' => $validated['student_last_name'],
                'lrn' => $validated['student_lrn'],
                'gender' => $validated['student_gender'],
                'class_id' => $validated['class_id'],
                'parent_id' => $user->id, // Use the created parent's ID
                'approved_by_teacher' => true,
            ]);

            DB::commit();

            return back()->with('success', 'Parent and Student registered successfully! Parent Email: ' . $email . ' | Password: ' . $password);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Registration failed: ' . $e->getMessage());
        }
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

        // Loop through each student of the parent
        foreach ($parent->students as $student) {
            // Delete related grade remarks
            $student->gradeRemarks()->delete();

            // Delete related grades
            $student->grades()->delete();

            // Delete the student itself
            $student->delete();
        }

        // Finally delete the parent user
        $parent->delete();

        return back()->with('success', 'Parent and their students (with grades/remarks) deleted successfully.');
    }
}
