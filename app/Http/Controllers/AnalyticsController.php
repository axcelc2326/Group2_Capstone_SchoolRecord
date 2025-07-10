<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function index()
    {
        // Example: average grade per class
        $data = Grade::selectRaw('class_id, AVG(grade) as avg_grade')
            ->groupBy('class_id')
            ->with('class')
            ->get();

        return inertia('Admin/Analytics', [
            'analytics' => $data,
        ]);
    }

}
