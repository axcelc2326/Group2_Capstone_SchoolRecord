<!DOCTYPE html>
<html>
<head>
    <title>{{ $student->first_name }} {{ $student->last_name }} - Academic Grades</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 13px;
            color: #333;
            margin: 0;
            padding: 1.5rem;
            background-color: #fff;
        }

        h2 {
            text-align: center;
            font-size: 18px;
            margin-bottom: 0.2rem;
            color: #2c3e50;
        }

        p {
            text-align: center;
            font-size: 14px;
            margin: 0.2rem 0;
            color: #555;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px 6px;
            text-align: center;
        }

        th {
            background-color: #3498db;
            color: #fff;
            font-weight: 600;
        }

        tr:nth-child(even) td {
            background-color: #f9f9f9;
        }

        td {
            font-size: 13px;
        }

        td:first-child {
            text-align: left;
            font-weight: 500;
        }

        .average {
            font-weight: bold;
            color: #27ae60;;
        }
    </style>
</head>
<body>
    {{-- Student Info --}}
    <h2>
        {{ $student->first_name }} 
        {{ $student->middle_name ? $student->middle_name . ' ' : '' }} 
        {{ $student->last_name }}
    </h2>

    <p>
        Grade: {{ $student->class?->grade_level ?? 'N/A' }}, 
        Section: {{ $student->class?->name ?? 'N/A' }}
    </p>

    {{-- Grades Table --}}
    <table>
        <thead>
            <tr>
                <th>Subject</th>
                <th>Q1</th>
                <th>Q2</th>
                <th>Q3</th>
                <th>Q4</th>
                <th>Average</th>
            </tr>
        </thead>
        <tbody>
            @foreach($student->grades->groupBy(fn($g) => $g->subject->name ?? $g->subject) as $subjectName => $grades)
            <tr>
                <td>{{ $subjectName }}</td>
                @foreach(['Q1','Q2','Q3','Q4'] as $q)
                    @php
                        $grade = $grades->firstWhere('quarter', $q);
                    @endphp
                    <td>{{ $grade->grade ?? '—' }}</td>
                @endforeach
                <td class="average">{{ round($grades->avg(fn($g) => $g->grade ?: 0)) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
