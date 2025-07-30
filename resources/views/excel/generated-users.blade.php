<html>
<table>
    <thead>
    <tr>
        <th><b>Username</b></th>
        <th><b>Name</b></th>
        <th><b>Email</b></th>
        <th><b>Job Title</b></th>
        <th><b>Job Family</b></th>
        <th><b>Password</b></th>
    </tr>
    </thead>
    <tbody>

        @foreach ($users as $user)

        <tr>
            <td>{{ $user->username }}</td>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td>{{ $user->job_title }}</td>
            <td>{{ $user->job_family }}</td>
            <td>{{ $user->generate_password_for_user() }}</td>
        </tr>

        @endforeach

    </tbody>
</table>
</html>