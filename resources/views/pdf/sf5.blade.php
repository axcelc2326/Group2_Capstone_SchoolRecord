<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Form 5 (SF5) Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
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
            font-size: 14px;
            margin: 0;
            padding: 0;
        }
        
        .header p {
            font-size: 11px;
            margin: 2px 0;
            font-style: italic;
        }
        
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        .info-table td {
            padding: 3px 5px;
            border: 1px solid #000;
        }
        
        .info-table .label {
            font-weight: bold;
            width: 15%;
        }
        
        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        .summary-table th, .summary-table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
        }
        
        .summary-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        
        .students-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 11px;
        }
        
        .students-table th, .students-table td {
            border: 1px solid #000;
            padding: 4px;
        }
        
        .students-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }
        
        .name-col {
            width: 40%;
            text-align: left;
        }
        
        .action-col {
            width: 25%;
        }
        
        .gender-header {
            text-align: center;
            font-weight: bold;
            padding: 5px;
            background-color: #e6e6e6;
            border: 1px solid #000;
            margin-top: 10px;
        }
        
        .instructions {
            margin-top: 20px;
            font-size: 11px;
        }
        
        .instructions ol {
            padding-left: 20px;
            margin: 5px 0;
        }
        
        .instructions li {
            margin-bottom: 5px;
        }
        
        .signature-area {
            margin-top: 30px;
        }
        
        .signature-line {
            width: 100%;
            margin-bottom: 25px;
        }
        
        .signature-line .label {
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .signature-line .name {
            text-decoration: underline;
            margin-bottom: 5px;
        }
        
        .signature-line .title {
            font-style: italic;
        }
        
        .combined-total {
            text-align: center;
            margin-top: 20px;
            font-weight: bold;
        }
        
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>School Form 5 (SF 5) Report on Promotion and Level of Proficiency & Achievement</h1>
            <p>(This replaces Forms 18-81, 18-82, 18A and List of Graduates)</p>
        </div>
        
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
                    <td>{{ $totals['promoted'] ?? 0 }}</td>
                </tr>
                <tr>
                    <td>*CONDITIONALLY PROMOTED*</td>
                    <td>{{ $totals['conditional_male'] ?? 0 }}</td>
                    <td>{{ $totals['conditional_female'] ?? 0 }}</td>
                    <td>{{ $totals['conditional'] ?? 0 }}</td>
                </tr>
                <tr>
                    <td>RETAINED</td>
                    <td>{{ $totals['retained_male'] ?? 0 }}</td>
                    <td>{{ $totals['retained_female'] ?? 0 }}</td>
                    <td>{{ $totals['retained'] ?? 0 }}</td>
                </tr>
            </tbody>
        </table>
        
        <!-- Male Students -->
        <div class="gender-header">MALE</div>
        <table class="students-table">
            <thead>
                <tr>
                    <th>LRN</th>
                    <th class="name-col">LEARNER'S NAME (Last Name, First Name, Middle Name)</th>
                    <th>GENERAL AVERAGE</th>
                    <th class="action-col">ACTION TAKEN: PROMOTED, CONDITIONAL or RETAINED</th>
                    <th>Did Not Meet Expectations in the Learning Areas as of end of current School Year</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $maleStudents = $students->filter(function($student) {
                        return strtolower($student->gender) === 'male';
                    });
                @endphp
                
                @foreach($maleStudents as $student)
                    @php
                        $finals = $student->grades->groupBy('subject_id')->map(fn ($grades) => $grades->avg('grade'));
                        $generalAverage = $finals->avg();
                        $actionTaken = '';
                        $honorsText = '';

                        if ($generalAverage >= 75) {
                            if ($generalAverage >= 98) {
                                $actionTaken = 'PROMOTED WITH HIGHEST HONORS';
                                $honorsText = 'PROMOTED WITH HIGHEST HONORS';
                            } elseif ($generalAverage >= 95) {
                                $actionTaken = 'PROMOTED WITH HIGH HONORS';
                                $honorsText = 'PROMOTED WITH HIGH HONORS';
                            } elseif ($generalAverage >= 90) {
                                $actionTaken = 'PROMOTED WITH HONORS';
                                $honorsText = 'PROMOTED WITH HONORS';
                            } else {
                                $actionTaken = 'PROMOTED';
                                $honorsText = 'PROMOTED';
                            }
                        } elseif ($generalAverage >= 70) {
                            $actionTaken = 'CONDITIONAL';
                            $honorsText = 'CONDITIONAL';
                        } else {
                            $actionTaken = 'RETAINED';
                            $honorsText = 'RETAINED';
                        }
                    @endphp
                    <tr>
                        <td>{{ $student->lrn }}</td>
                        <td>{{ strtoupper($student->last_name) }}, {{ strtoupper($student->first_name) }} {{ strtoupper($student->middle_name) }}</td>
                        <td>{{ $generalAverage ? number_format($generalAverage, 0) : '' }}</td>
                        <td>{{ $honorsText }}</td>
                        <td>
                            @if($generalAverage < 75)
                                All Subjects
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        <!-- Female Students -->
        <div class="gender-header">FEMALE</div>
        <table class="students-table">
            <thead>
                <tr>
                    <th>LRN</th>
                    <th class="name-col">LEARNER'S NAME (Last Name, First Name, Middle Name)</th>
                    <th>GENERAL AVERAGE</th>
                    <th class="action-col">ACTION TAKEN: PROMOTED, CONDITIONAL or RETAINED</th>
                    <th>Did Not Meet Expectations in the Learning Areas as of end of current School Year</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $femaleStudents = $students->filter(function($student) {
                        return strtolower($student->gender) === 'female';
                    });
                @endphp
                
                @foreach($femaleStudents as $student)
                    @php
                        $finals = $student->grades->groupBy('subject_id')->map(fn ($grades) => $grades->avg('grade'));
                        $generalAverage = $finals->avg();
                        $actionTaken = '';
                        $honorsText = '';

                        if ($generalAverage >= 75) {
                            if ($generalAverage >= 98) {
                                $actionTaken = 'PROMOTED WITH HIGHEST HONORS';
                                $honorsText = 'PROMOTED WITH HIGHEST HONORS';
                            } elseif ($generalAverage >= 95) {
                                $actionTaken = 'PROMOTED WITH HIGH HONORS';
                                $honorsText = 'PROMOTED WITH HIGH HONORS';
                            } elseif ($generalAverage >= 90) {
                                $actionTaken = 'PROMOTED WITH HONORS';
                                $honorsText = 'PROMOTED WITH HONORS';
                            } else {
                                $actionTaken = 'PROMOTED';
                                $honorsText = 'PROMOTED';
                            }
                        } elseif ($generalAverage >= 70) {
                            $actionTaken = 'CONDITIONAL';
                            $honorsText = 'CONDITIONAL';
                        } else {
                            $actionTaken = 'RETAINED';
                            $honorsText = 'RETAINED';
                        }
                    @endphp
                    <tr>
                        <td>{{ $student->lrn }}</td>
                        <td>{{ strtoupper($student->last_name) }}, {{ strtoupper($student->first_name) }} {{ strtoupper($student->middle_name) }}</td>
                        <td>{{ $generalAverage ? number_format($generalAverage, 0) : '' }}</td>
                        <td>{{ $honorsText }}</td>
                        <td>
                            @if($generalAverage < 75)
                                All Subjects
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        <div class="instructions">
            <p><strong>Instructions:</strong></p>
            <ol>
                <li>The SCC shall conduct checking in their own school, no swapping of SCC nor one school to another is permitted.</li>
                <li>The name of SCC members shall be printed and put their signature on top of their printed name.</li>
                <li>The school head is accountable and liable for any wrongful entry on the forms (DepEd Order 4, 2018 par 5) therefore, the SCC is not required to sign in SF 5.</li>
                <li>Only LIS generated SF5 shall be recognized (DO 11, 2018, page 7).</li>
                <li>The form shall be submitted to the Division Office together with the accompanied SFOR1: SCC (DepEd Order 11, 2018, page 11).</li>
            </ol>
        </div>
        
        <div class="signature-area">
            <div class="signature-line">
                <div class="label">PREPARED BY:</div>
                <div class="name">{{ $teacher->name }}</div>
                <div class="title">Class Adviser</div>
            </div>
            
            <div class="signature-line">
                <div class="label">CERTIFIED CORRECT & SUBMITTED BY:</div>
                <div class="name">{{ $meta['school_head_chair'] }}</div>
                <div class="title">School Head & SCC Chair</div>
            </div>
            
            <div class="signature-line">
                <div class="label">REVIEWED BY: SCC Members</div>
                <br>
                <div>_______________________________</div>
                <div class="title">Signature Over Printed Name</div>
                <br>
                <div>_______________________________</div>
                <div class="title">Signature Over Printed Name</div>
                <br>
                <div>_______________________________</div>
                <div class="title">Signature Over Printed Name</div>
                <br>
                <div>_______________________________</div>
                <div class="title">Signature Over Printed Name</div>
            </div>
        </div>
        
        <div class="combined-total">
            {{ $totals['overall'] }} &lt;==&gt; COMBINED
        </div>
    </div>
</body>
</html>