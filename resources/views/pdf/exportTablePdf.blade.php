<!doctype html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="utf-8">
    <title>گزارش جدول</title>
    <style>
        body {
            font-family: "DejaVu Sans", sans-serif;
            font-size: 12px;
            direction: rtl;
            margin: 20px;
        }

        h3 {
            text-align: center;
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #999;
        }

        th,
        td {
            border: 1px solid #666;
            padding: 8px;
            text-align: center;
            vertical-align: middle;
        }

        th {
            background-color: #f0f0f0;
        }

        tr:nth-child(even) {
            background-color: #fafafa;
        }
    </style>
</head>

<body>

    <h3>گزارش</h3>

    <table>
        <thead>
            <tr>
                @foreach($columns as $key => $label)
                    <th>{{ $label }}</th>
                @endforeach
            </tr>
        </thead>

        <tbody>
            @foreach($rows as $row)
                <tr>
                    @foreach($columns as $key => $label)
                        <td>{{ $row[$key] ?? '---' }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>