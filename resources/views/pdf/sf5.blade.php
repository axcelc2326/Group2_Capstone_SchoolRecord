<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Form 5 (SF5) Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 0;
            padding: 20px;
            color: #000;
        }
        .container {
            width: 100%;
            max-width: 8.5in;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            font-weight: bold;
        }
        .header h1 {
            font-size: 13px;
            margin: 0;
            padding: 0;
        }
        .header p {
            font-size: 10px;
            margin: 2px 0;
            font-style: italic;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        td, th {
            border: 1px solid #000;
            padding: 4px;
            vertical-align: top;
        }
        .info-table td {
            font-size: 11px;
        }
        .info-table .label {
            font-weight: bold;
            width: 20%;
            background: #f8f8f8;
        }
        .summary-table th {
            background-color: #eaeaea;
            font-weight: bold;
            text-align: center;
        }
        .students-table th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
        }
        .gender-header {
            text-align: center;
            font-weight: bold;
            background-color: #d9d9d9;
            border: 1px solid #000;
            margin-top: 10px;
            padding: 4px;
        }
        .signature-area {
            margin-top: 25px;
            font-size: 11px;
        }
        .signature-line {
            margin-top: 15px;
        }
        .signature-line .label {
            font-weight: bold;
        }
        .signature-line .name {
            text-decoration: underline;
            margin-top: 20px;
        }
        .signature-line .title {
            font-style: italic;
            font-size: 10px;
        }
        .combined-total {
            text-align: center;
            margin-top: 20px;
            font-weight: bold;
        }
        .instructions {
            margin-top: 20px;
            font-size: 10px;
        }
        .instructions ol {
            padding-left: 20px;
        }
        .page-break {
            page-break-after: always;
        }
        .section-title {
            text-align: center;
            font-weight: bold;
            margin: 15px 0 10px 0;
            font-size: 12px;
        }
    </style>
</head>
<body>
<div class="container">

    <!-- Header -->
    <div class="header">
        <h1>School Form 5 (SF 5)</h1>
        <p>Report on Promotion and Level of Proficiency & Achievement</p>
        <p><em>(This replaces Forms 18-81, 18-82, 18A and List of Graduates)</em></p>
    </div>

    <!-- School Info -->
    <table class="info-table">
        <tr>
            <td class="label">Region</td>
            <td>{{ $meta['region'] }}</td>
            <td class="label">Division</td>
            <td>{{ $meta['division'] }}</td>
        </tr>
        <tr>
            <td class="label">School ID</td>
            <td>{{ $meta['school_id'] }}</td>
            <td class="label">School Year</td>
            <td>{{ $meta['school_year'] }}</td>
        </tr>
        <tr>
            <td class="label">School Name</td>
            <td>{{ $meta['school_name'] }}</td>
            <td class="label">Grade Level | Section</td>
            <td>{{ $class->grade_level }} - {{ $class->name }}</td>
        </tr>
    </table>

    <!-- Action Taken Summary -->
    <div class="section-title">ACTION TAKEN: PROMOTED, CONDITIONAL or RETAINED</div>
    
    <table class="summary-table">
        <thead>
        <tr>
            <th>STATUS</th>
            <th>MALE</th>
            <th>FEMALE</th>
            <th>TOTAL</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>PROMOTED</td>
            <td>{{ $totals['promoted_male'] ?? 0 }}</td>
            <td>{{ $totals['promoted_female'] ?? 0 }}</td>
            <td>{{ ($totals['promoted_male'] ?? 0) + ($totals['promoted_female'] ?? 0) }}</td>
        </tr>
        <tr>
            <td>CONDITIONALLY PROMOTED</td>
            <td>{{ $totals['conditional_male'] ?? 0 }}</td>
            <td>{{ $totals['conditional_female'] ?? 0 }}</td>
            <td>{{ ($totals['conditional_male'] ?? 0) + ($totals['conditional_female'] ?? 0) }}</td>
        </tr>
        <tr>
            <td>RETAINED</td>
            <td>{{ $totals['retained_male'] ?? 0 }}</td>
            <td>{{ $totals['retained_female'] ?? 0 }}</td>
            <td>{{ ($totals['retained_male'] ?? 0) + ($totals['retained_female'] ?? 0) }}</td>
        </tr>
        </tbody>
    </table>

    <!-- MALE Students -->
    <div class="gender-header">MALE</div>
    <table class="students-table">
        <thead>
        <tr>
            <th>LRN</th>
            <th>LEARNER'S NAME<br>(Last Name, First Name, Middle Name)</th>
            <th>GENERAL<br>AVERAGE</th>
            <th>ACTION TAKEN</th>
            <th>Did Not Meet Expectations<br>of the Learning Areas as of end of current School Year</th>
        </tr>
        </thead>
        <tbody>
        @foreach($students->where('gender', 'male') as $student)
            @php
                $finals = $student->grades->groupBy('subject_id')->map(fn($g) => $g->avg('grade'));
                $genAve = $finals->avg();
                if ($genAve >= 98) $action = 'PROMOTED WITH HIGHEST HONORS';
                elseif ($genAve >= 95) $action = 'PROMOTED WITH HIGH HONORS';
                elseif ($genAve >= 90) $action = 'PROMOTED WITH HONORS';
                elseif ($genAve >= 75) $action = 'PROMOTED';
                elseif ($genAve >= 70) $action = 'CONDITIONAL';
                else $action = 'RETAINED';
            @endphp
            <tr>
                <td>{{ $student->lrn }}</td>
                <td>{{ strtoupper("{$student->last_name}, {$student->first_name} {$student->middle_name}") }}</td>
                <td>{{ $genAve ? number_format($genAve, 0) : '' }}</td>
                <td>{{ $action }}</td>
                <td>@if($genAve < 75) All Subjects @endif</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <!-- FEMALE Students -->
    <div class="gender-header">FEMALE</div>
    <table class="students-table">
        <thead>
        <tr>
            <th>LRN</th>
            <th>LEARNER'S NAME<br>(Last Name, First Name, Middle Name)</th>
            <th>GENERAL<br>AVERAGE</th>
            <th>ACTION TAKEN</th>
            <th>Did Not Meet Expectations<br>of the Learning Areas as of end of current School Year</th>
        </tr>
        </thead>
        <tbody>
        @foreach($students->where('gender', 'female') as $student)
            @php
                $finals = $student->grades->groupBy('subject_id')->map(fn($g) => $g->avg('grade'));
                $genAve = $finals->avg();
                if ($genAve >= 98) $action = 'PROMOTED WITH HIGHEST HONORS';
                elseif ($genAve >= 95) $action = 'PROMOTED WITH HIGH HONORS';
                elseif ($genAve >= 90) $action = 'PROMOTED WITH HONORS';
                elseif ($genAve >= 75) $action = 'PROMOTED';
                elseif ($genAve >= 70) $action = 'CONDITIONAL';
                else $action = 'RETAINED';
            @endphp
            <tr>
                <td>{{ $student->lrn }}</td>
                <td>{{ strtoupper("{$student->last_name}, {$student->first_name} {$student->middle_name}") }}</td>
                <td>{{ $genAve ? number_format($genAve, 0) : '' }}</td>
                <td>{{ $action }}</td>
                <td>@if($genAve < 75) All Subjects @endif</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <!-- Level of Proficiency & Achievement -->
    <div class="section-title">LEVEL OF PROFICIENCY & ACHIEVEMENT</div>
    
    <table class="summary-table">
        <thead>
        <tr>
            <th>Descriptor & Grading</th>
            <th>MALE</th>
            <th>FEMALE</th>
            <th>TOTAL</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>Did Not Meet Expectations (74 & below)</td>
            <td>{{ $totals['below_75_male'] ?? 0 }}</td>
            <td>{{ $totals['below_75_female'] ?? 0 }}</td>
            <td>{{ ($totals['below_75_male'] ?? 0) + ($totals['below_75_female'] ?? 0) }}</td>
        </tr>
        <tr>
            <td>Fairly Satisfactory (75-79)</td>
            <td>{{ $totals['fair_75_79_male'] ?? 0 }}</td>
            <td>{{ $totals['fair_75_79_female'] ?? 0 }}</td>
            <td>{{ ($totals['fair_75_79_male'] ?? 0) + ($totals['fair_75_79_female'] ?? 0) }}</td>
        </tr>
        <tr>
            <td>Satisfactory (80-84)</td>
            <td>{{ $totals['satisfactory_80_84_male'] ?? 0 }}</td>
            <td>{{ $totals['satisfactory_80_84_female'] ?? 0 }}</td>
            <td>{{ ($totals['satisfactory_80_84_male'] ?? 0) + ($totals['satisfactory_80_84_female'] ?? 0) }}</td>
        </tr>
        <tr>
            <td>Very Satisfactory (85-89)</td>
            <td>{{ $totals['very_satisfactory_85_89_male'] ?? 0 }}</td>
            <td>{{ $totals['very_satisfactory_85_89_female'] ?? 0 }}</td>
            <td>{{ ($totals['very_satisfactory_85_89_male'] ?? 0) + ($totals['very_satisfactory_85_89_female'] ?? 0) }}</td>
        </tr>
        <tr>
            <td>Outstanding (90-100)</td>
            <td>{{ $totals['outstanding_90_100_male'] ?? 0 }}</td>
            <td>{{ $totals['outstanding_90_100_female'] ?? 0 }}</td>
            <td>{{ ($totals['outstanding_90_100_male'] ?? 0) + ($totals['outstanding_90_100_female'] ?? 0) }}</td>
        </tr>
        </tbody>
    </table>

    <!-- Signatures -->
    <div class="signature-area">
        <div class="signature-line">
            <div class="label">PREPARED BY:</div>
            <div class="name">{{ $teacher->name ?? 'MARCHIS APARICIO GIUDITO' }}</div>
            <div class="title">Class Adviser</div>
        </div>

        <div class="signature-line">
            <div class="label">CERTIFIED CORRECT & SUBMITTED BY:</div>
            <div class="name">{{ $meta['school_head_chair'] ?? 'NOISEN LOFRANCO GASCO' }}</div>
            <div class="title">School Head & SCC Chair</div>
        </div>

        <div class="signature-line">
            <div class="label">REVIEWED BY: SCC Members</div>
            <p>______________________________</p>
            <p>______________________________</p>
            <p>______________________________</p>
            <p>______________________________</p>
        </div>
    </div>

    <div class="combined-total">
        {{ $students->count() }} &lt;==&gt; COMBINED
    </div>

    <div class="instructions">
        <p><strong>Instructions:</strong></p>
        <ol>
            <li>The SCC shall conduct checking in their own school. No swapping between schools is allowed.</li>
            <li>The SCC members' names shall be printed with their signatures above.</li>
            <li>The school head is accountable for all entries (DepEd Order 4, 2018 par 5).</li>
            <li>Only LIS-generated SF5 shall be recognized (DO 11, 2018, page 7).</li>
            <li>Submit this form to the Division Office with SFOR1: SCC (DepEd Order 11, 2018, page 11).</li>
        </ol>
    </div>

</div>
</body>
</html>