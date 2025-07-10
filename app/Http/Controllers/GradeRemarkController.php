<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GradeRemarkController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required',
            'class_id' => 'required',
            'final_average' => 'required|numeric|min:60|max:100',
            'remarks' => 'required|string'
        ]);

        GradeRemark::updateOrCreate([
            'student_id' => $request->student_id,
            'class_id' => $request->class_id
        ], [
            'final_average' => $request->final_average,
            'remarks' => $request->remarks
        ]);

        return back()->with('message', 'Remark updated.');
    }

}
