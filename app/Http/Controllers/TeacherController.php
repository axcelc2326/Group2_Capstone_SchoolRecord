<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $teachers = User::role('teacher')
            ->with('class') // 👈 or 'classes'
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->paginate(10)
            ->withQueryString();

        $totalTeachers = User::role('teacher')->count();

        return Inertia::render('Admin/MyTeacher', [
            'teachers' => $teachers,
            'totalTeachers' => $totalTeachers,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    public function toggleStatus($id)
    {
        $teacher = User::role('teacher')->findOrFail($id);

        $teacher->status = $teacher->status === 'active' ? 'inactive' : 'active';
        $teacher->save();

        return back()->with('success', "Teacher account {$teacher->status} successfully.");
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Extract first word from full name (preserve original case)
        $firstName = explode(' ', trim($validated['name']))[0];
        
        // Generate email: firstname@gmail.com (lowercase for email)
        $email = strtolower($firstName) . '@gmail.com';
        
        // Generate password: first 3 letters + _2025 (keeps original case)
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

        $user->assignRole('teacher');

        return back()->with('success', 'Teacher registered successfully! Email: ' . $email . ' | Password: ' . $password);
    }

    public function update(Request $request, $id)
    {
        $teacher = User::role('teacher')->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $teacher->id,
            'password' => 'nullable|min:6|confirmed',
        ]);

        $teacher->name = $validated['name'];
        $teacher->email = $validated['email'];

        if (!empty($validated['password'])) {
            $teacher->password = bcrypt($validated['password']);
        }

        $teacher->save();

        return back()->with('success', 'Teacher updated successfully.');
    }

    public function destroy($id)
    {
        $teacher = User::role('teacher')->findOrFail($id);

        // detach related classes
        if (method_exists($teacher, 'classes')) {
            $teacher->classes()->detach();
        }

        // delete related announcements
        if (method_exists($teacher, 'announcements')) {
            $teacher->announcements()->delete();
        }

        $teacher->delete();

        return back()->with('success', 'Teacher deleted successfully.');
    }

    public function myStudents(Request $request)
    {
        $user = Auth::user();
        $search = $request->input('search');

        $class = $user->classTeaching;

        if (!$class) {
            return Inertia::render('Teacher/MyStudents', [
                'students' => [],
                'message' => 'You are not assigned to any class.',
            ]);
        }

        $query = $class->students()->where('approved_by_teacher', true);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%$search%")
                ->orWhere('last_name', 'like', "%$search%");
            });
        }

        $students = $query->orderBy('last_name')
            ->with('grades', 'parent') // load parent relationship
            ->paginate(25)
            ->withQueryString();

        $students->getCollection()->transform(function ($student) {
            $student->parent_name = $student->parent
                ? $student->parent->name // use 'name' instead of first_name + last_name
                : 'N/A';
            return $student;
        });

        return Inertia::render('Teacher/MyStudents', [
            'students' => $students,
            'filters' => ['search' => $search],
        ]);
    }

    public function viewStudentGrades($studentId)
    {
        $student = Student::with([
            'grades.subject',
            'class',
        ])->findOrFail($studentId);

        // ✅ Only fetch subjects for the student's grade level
        $subjects = Subject::where('grade_level', $student->class->grade_level)->get();

        // ✅ Group grades by quarter → subject_id → grade
        $grades = $student->grades
            ->groupBy('quarter')
            ->map(function ($quarterGrades) {
                return $quarterGrades->keyBy('subject_id')->map->grade;
            })
            ->toArray(); // ✅ convert to plain array for Inertia

        return Inertia::render('Teacher/ViewStudentGrades', [
            'student' => $student,
            'subjects' => $subjects,
            'grades'   => $grades, // ✅ added grouped grades
        ]);
    }
}
