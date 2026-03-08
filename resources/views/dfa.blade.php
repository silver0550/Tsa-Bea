<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>DFA validátor</title>

    <style>
        body{
            font-family: Arial, sans-serif;
            background:#f5f6fa;
            padding:40px;
        }

        .container{
            max-width:800px;
            margin:auto;
            background:white;
            padding:30px;
            border-radius:10px;
            box-shadow:0 5px 20px rgba(0,0,0,0.1);
        }

        h1{
            margin-bottom:10px;
        }

        .regex{
            background:#eef4ff;
            padding:10px 15px;
            border-radius:6px;
            font-family: monospace;
            margin-bottom:25px;
        }

        table{
            width:100%;
            border-collapse:collapse;
        }

        th, td{
            padding:10px;
            border-bottom:1px solid #ddd;
            text-align:left;
        }

        th{
            background:#f0f2f7;
        }

        .ok{
            color:#1a7f37;
            font-weight:bold;
        }

        .error{
            color:#c62828;
            font-weight:bold;
        }
    </style>

</head>
<body>

<div class="container">

    <h1>DFA alapú szám validátor</h1>

    <p>Az alábbi reguláris kifejezéshez készített DFA eredménye:</p>

    <div class="regex">
        (-|e)(0|([1-9][0-9]*))((.[0-9][0-9]*)|e)
    </div>

    <table>

        <thead>
        <tr>
            <th>Bemenet</th>
            <th>Eredmény</th>
        </tr>
        </thead>

        <tbody>

        @foreach($response as $input => $result)

            <tr>
                <td>{{ $input }}</td>

                <td class="{{ $result ? 'ok' : 'error' }}">
                    {{ $result ? 'Helyes' : 'Hibás' }}
                </td>
            </tr>

        @endforeach

        </tbody>

    </table>

</div>

</body>
</html>
