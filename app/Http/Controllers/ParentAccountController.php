<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class ParentAccountController extends Controller
{
    public function downloadFiltered(Request $request)
    {
        $request->validate([
            'class_id' => 'nullable|exists:classes,id',
        ]);

        $user = Auth::user();

        // Base query for parents (users with parent role)
        $query = User::role('parent')
            ->with(['students.class']) // Use the correct relationship name
            ->withCount('students')
            ->when($request->class_id, function ($q) use ($request) {
                // Filter parents who have students in the selected class
                $q->whereHas('students', function ($studentQuery) use ($request) {
                    $studentQuery->where('class_id', $request->class_id);
                });
            });

        // ✅ Restrict to teacher's own classes (if teacher)
        if ($user->hasRole('teacher')) {
            $teacherClassIds = ClassModel::where('teacher_id', $user->id)->pluck('id');
            $query->whereHas('students', function ($studentQuery) use ($teacherClassIds) {
                $studentQuery->whereIn('class_id', $teacherClassIds);
            });
        }

        $parents = $query->get();

        if ($parents->isEmpty()) {
            return back()->with('error', 'No parent accounts found for the selected class.');
        }

        // Get the selected class name for the filename
        $className = 'All_Classes';
        if ($request->class_id) {
            $selectedClass = ClassModel::find($request->class_id);
            $className = $selectedClass ? str_replace(' ', '_', $selectedClass->name) : 'Selected_Class';
        }

        // ✅ Prepare PDF
        $pdf = Pdf::loadView('pdf.filtered-parent-accounts', [
            'parents' => $parents,
            'class_id' => $request->class_id,
            'selected_class' => $request->class_id ? ClassModel::find($request->class_id) : null,
            'classes' => $user->hasRole('teacher') 
                ? ClassModel::where('teacher_id', $user->id)->get()
                : ClassModel::all(),
            'generated_date' => now()->format('F d, Y'),
            'teacher' => $user,
        ])->setPaper('legal', 'portrait');

        $filename = 'Parent_Accounts_' . $className . '_' . now()->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }
}