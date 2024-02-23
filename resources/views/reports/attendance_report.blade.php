<!-- resources/views/attendance.blade.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance</title>
</head>
<body>
    <h1>Attendance</h1>

    <table border="1">
        <thead>
            <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Employee Number</th>
                <th>Arrival Time</th>
                <th>Departure Time</th>
            </tr>
        </thead>
        <tbody>
            @foreach($attendances as $record)
            <tr>
                <td>{{ $record->first_name }}</td>
                <td>{{$record->last_name}}</td>
                <td>{{ $record->emp_id }}</td>
                <td>{{ $record->start_time }}</td>
                <td>{{ $record->end_time }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
