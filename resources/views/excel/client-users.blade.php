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
        <th><b>Assignments</b></th>
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
            <td>
                @if ($client->require_profile && $user->last_login_at && $user->completed_profile)
                    * Changed *
                @else
                    {{ $user->generate_password_for_user() }}
                @endif
            </td>

            @foreach ($user->assignments as $assignment)

                <td>
                    {{ $assignment->assessment()->name }}
                    @if ($assignment->completed())
                        (Completed)
                    @endif
                </td>

            @endforeach

        </tr>

    @endforeach

    </tbody>
</table>
</html>