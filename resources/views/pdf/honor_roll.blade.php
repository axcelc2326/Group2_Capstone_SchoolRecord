<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Honor Roll - {{ $class->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
            color: #000;
            font-size: 12px;
        }

        /* ===== HEADER ===== */
        .header {
            display: flex;
            align-items: center;
            justify-content: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header img {
            width: 100px; /* ✅ Adjust logo size here */
            height: auto;
            margin-right: 20px;
        }

        .school-info {
            text-align: center;
        }

        .school-name {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 3px;
        }

        .address {
            font-size: 11px;
            line-height: 1.3;
        }

        /* ===== TITLE ===== */
        .title-section {
            text-align: center;
            margin-top: 10px;
            margin-bottom: 20px;
        }

        .title-section h3 {
            margin: 4px 0;
        }

        /* ===== TABLE ===== */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 12px;
        }

        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }

        td.text-left {
            text-align: left;
        }

        .honor {
            color: green;
            font-weight: bold;
        }

        /* ===== FOOTER ===== */
        .footer {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
            width: 100%;
        }

        .signature-block {
            text-align: left;
            width: 45%;
        }

        .signature-block .label {
            font-weight: bold;
            text-transform: uppercase;
        }

        .signature-block .name {
            margin-top: 30px;
            text-decoration: underline;
            font-weight: bold;
        }

        .signature-block .title {
            font-size: 11px;
        }

        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>

    <!-- ===== HEADER SECTION ===== -->
    <div class="header">
        <img src="{{ public_path('images/Logo.jpg') }}" alt="School Logo">
        <div class="school-info">
            <div class="school-name">MATER DEI COLLEGE</div>
            <div class="address">Cabulijan, Tubigon, Bohol<br>(038) 508-8106</div>
        </div>
    </div>

    <!-- ===== TITLE SECTION ===== -->
    <div class="title-section">
        <h3>{{ strtoupper($class->name) }}</h3>
        <h4>SY {{ $school_year }} | {{ $quarter }}</h4>
        <h3><strong>HONOR LIST</strong></h3>
    </div>

    <!-- ===== HONOR TABLE ===== -->
    <table>
        <thead>
            <tr>
                <th>Student’s Name</th>
                <th>General Average</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            @forelse($honorStudents as $student)
                <tr>
                    <td class="text-left">{{ strtoupper($student['name']) }}</td>
                    <td>{{ $student['average'] }}</td>
                    <td class="honor">{{ $student['rank'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3">No qualified students for honors.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- ===== SIGNATURE SECTION ===== -->
    <div class="footer">
        <div class="signature-block">
            <div class="label">Prepared By:</div>
            <div class="name">{{ strtoupper($teacher->name) }}</div>
            <div class="title">Class Adviser</div>
        </div>

        <div class="signature-block">
            <div class="label">Noted By:</div>
            <div class="name">{{ strtoupper($principal) }}</div>
            <div class="title">School Principal</div>
        </div>
    </div>

</body>
</html>
