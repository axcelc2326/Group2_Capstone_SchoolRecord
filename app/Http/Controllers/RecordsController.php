<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\SF5Record;
use App\Models\HonorRoll;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecordsController extends Controller
{
    // Admin index: supports status filter + pagination (no search)
    public function index(Request $request)
    {
        $request->validate([
            'teacher_id' => 'nullable|integer|exists:users,id',
            'type' => 'nullable|in:sf5,honor,all',
            'status' => 'nullable|in:pending,reviewed,all',
        ]);

        $teacherId = $request->teacher_id;
        $type = $request->type ?? 'all';
        $status = $request->status ?? 'all';

        // Query for SF5
        $sf5Query = SF5Record::with(['class', 'teacher'])
            ->when($teacherId, fn($q) => $q->where('teacher_id', $teacherId))
            ->when($status !== 'all', fn($q) => $q->where('status', $status));

        // Query for Honor Rolls
        $honorQuery = HonorRoll::with(['class', 'teacher'])
            ->when($teacherId, fn($q) => $q->where('teacher_id', $teacherId))
            ->when($status !== 'all', fn($q) => $q->where('status', $status));

        // Execute based on type
        if ($type === 'sf5') {
            $sf5Records = $sf5Query->latest()->paginate(5)->withQueryString();
            $honorLists = collect();
        } elseif ($type === 'honor') {
            $sf5Records = collect();
            $honorLists = $honorQuery->latest()->paginate(5)->withQueryString();
        } else {
            $sf5Records = $sf5Query->latest()->paginate(5)->withQueryString();
            $honorLists = $honorQuery->latest()->paginate(5)->withQueryString();
        }

        // Teachers for dropdown filter
        $teachers = User::role('teacher')->select('id', 'name')->get();

        return inertia('Admin/RecordsIndex', [
            'sf5Records' => $sf5Records,
            'honorLists' => $honorLists,
            'teachers' => $teachers,
            'filters' => $request->only(['teacher_id', 'type', 'status']),
        ]);
    }

    // Returns JSON for modal details
    public function show($type, $id)
    {
        if ($type === 'sf5') {
            $record = SF5Record::with(['class.students.grades.subject', 'teacher'])->findOrFail($id);
            return response()->json([
                'type' => 'sf5',
                'record' => $record,
            ]);
        }

        if ($type === 'honor') {
            $record = HonorRoll::with(['class.students', 'teacher'])->findOrFail($id);
            return response()->json([
                'type' => 'honor',
                'record' => $record,
            ]);
        }

        return response()->json(['message'=>'Invalid type'], 404);
    }

    // Re-generate & download the PDF (redownload)
    public function download($type, $id)
    {
        if ($type === 'sf5') {
            $record = SF5Record::with(['class.students.grades.subject', 'teacher'])->findOrFail($id);

            $pdf = Pdf::loadView('pdf.sf5', [
                'class' => $record->class,
                'students' => $record->class->students,
                'teacher' => $record->teacher,
                'meta' => [
                    'region' => $record->region,
                    'division' => $record->division,
                    'school_id' => $record->school_id,
                    'school_name' => $record->school_name,
                    'school_year' => $record->school_year,
                    'school_head_chair' => $record->school_head_chair,
                ],
                'totals' => [
                    'male' => $record->male_count,
                    'female' => $record->female_count,
                    'overall' => $record->overall_count,
                    'class_average' => $record->class_average,
                ],
            ])->setPaper('legal', 'portrait');

            return $pdf->download("SF5_{$record->class->name}_{$record->school_year}.pdf");
        }

        if ($type === 'honor') {
            $record = HonorRoll::with(['class.students', 'teacher'])->findOrFail($id);

            // Record->data might be null or a JSON string. Normalize to array.
            $honorStudents = [];
            if (! empty($record->data)) {
                // If the model doesn't cast 'data' to array, decode safely:
                $honorStudents = is_array($record->data) ? $record->data : json_decode($record->data, true) ?? [];
            }

            $pdf = Pdf::loadView('pdf.honor_roll', [
                'class' => $record->class,
                'teacher' => $record->teacher,
                'school_year' => $record->school_year,
                'quarter' => $record->quarter,
                'principal' => $record->principal_name,
                'honorStudents' => $record->data ?? [], // ✅ directly from DB
            ])->setPaper('legal', 'portrait');

            return $pdf->download("HonorRoll_{$record->class->name}_{$record->school_year}_Q{$record->quarter}.pdf");
        }

        abort(404, 'Invalid record type.');
    }

    // Toggle reviewed/unreviewed status
    public function toggleReviewed(Request $request, $type, $id)
    {
        $admin = Auth::user();

        if ($type === 'sf5') {
            $record = SF5Record::findOrFail($id);
        } else if ($type === 'honor') {
            $record = HonorRoll::findOrFail($id);
        } else {
            return response()->json(['message' => 'Invalid type'], 404);
        }

        if ($record->status === 'reviewed') {
            $record->status = 'pending';
            $record->reviewed_by = null;
            $record->reviewed_at = null;
        } else {
            $record->status = 'reviewed';
            $record->reviewed_by = $admin->id;
            $record->reviewed_at = now();
        }

        $record->save();

        return response()->json(['status' => $record->status, 'reviewed_at' => $record->reviewed_at]);
    }
}
