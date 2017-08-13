<!doctype html>
<html>
<head>
    <title>
        Process
    </title>
</head>
<body>
<table border>
    <thead>
    <tr>
        <td> Слово или фраза</td>
        <td> Количество повторений</td>
    </tr>
    </thead>
    <tbody>
    @foreach ($sorted2 as $item)
        <tr>
            <td>
                {{$item['words']}}

            </td>
            <td>
                {{$item['reps']}}

            </td>

        </tr>

    @endforeach

    </tbody>
</table>

</body>
</html>
