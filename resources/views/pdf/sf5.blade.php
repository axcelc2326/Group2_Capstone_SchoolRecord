<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>School Form 5 (SF5)</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
        .header { text-align: center; margin-bottom: 10px; }
        .meta { width: 100%; margin-bottom: 15px; font-size: 11px; }
        .meta td { padding: 2px 5px; }

        table { border-collapse: collapse; width: 100%; font-size: 10px; }
        th, td { border: 1px solid #000; padding: 3px; text-align: center; }
        th { background: #f0f0f0; font-weight: bold; }

        .left { text-align: left; padding-left: 5px; }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h3>School Form 5 (SF5) - Report on Promotion and Level of Proficiency</h3>
    </div>

    <!-- Metadata -->
    <table class="meta">
        <tr>
            <td><strong>Region:</strong> {{ $meta['region'] }}</td>
            <td><strong>Division:</strong> {{ $meta['division'] }}</td>
            <td><strong>School ID:</strong> {{ $meta['school_id'] }}</td>
        </tr>
        <tr>
            <td><strong>School Name:</strong> {{ $meta['school_name'] }}</td>
            <td><strong>School Year:</strong> {{ $meta['school_year'] }}</td>
            <td><strong>Grade & Section:</strong> {{ $class->grade_level }} - {{ $class->name }}</td>
        </tr>
        <tr>
            <td colspan="2"><strong>Class Adviser:</strong> {{ $teacher->name }}</td>
            <td><strong>SCC Chair:</strong> {{ $meta['scc_chair'] }}</td>
        </tr>
        <tr>
            <td colspan="3"><strong>School Head:</strong> {{ $meta['school_head'] }}</td>
        </tr>
    </table>

    <!-- Main Table -->
    <table>
        <thead>
            <tr>
                <th rowspan="2">LRN</th>
                <th rowspan="2">Name</th>
                <th rowspan="2">Sex</th>
                @foreach ($subjects as $subject)
                    <th colspan="4">{{ $subject->name }}</th>
                @endforeach
                <th rowspan="2">General Average</th>
                <th rowspan="2">Remarks</th>
            </tr>
            <tr>
                @foreach ($subjects as $subject)
                    <th>Q1</th>
                    <th>Q2</th>
                    <th>Q3</th>
                    <th>Q4</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($students as $student)
                <tr>
                    <td>{{ $student->lrn }}</td>
                    <td class="left">{{ $student->last_name }}, {{ $student->first_name }}</td>
                    <td>{{ $student->gender }}</td>

                    @foreach ($subjects as $subject)
                        @php
                            $q1 = $student->grades->where('subject_id', $subject->id)->where('quarter', 1)->first()->grade ?? '';
                            $q2 = $student->grades->where('subject_id', $subject->id)->where('quarter', 2)->first()->grade ?? '';
                            $q3 = $student->grades->where('subject_id', $subject->id)->where('quarter', 3)->first()->grade ?? '';
                            $q4 = $student->grades->where('subject_id', $subject->id)->where('quarter', 4)->first()->grade ?? '';
                        @endphp
                        <td>{{ $q1 }}</td>
                        <td>{{ $q2 }}</td>
                        <td>{{ $q3 }}</td>
                        <td>{{ $q4 }}</td>
                    @endforeach

                    @php
                        $finals = $student->grades->groupBy('subject_id')->map(function ($grades) {
                            return $grades->avg('grade');
                        });
                        $generalAverage = $finals->avg() ? number_format($finals->avg(), 2) : '';
                        $remarks = $generalAverage >= 75 ? 'Promoted' : 'Retained';
                    @endphp

                    <td>{{ $generalAverage }}</td>
                    <td>{{ $remarks }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
