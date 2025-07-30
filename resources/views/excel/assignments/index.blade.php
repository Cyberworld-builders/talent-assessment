<html>

<style type="text/css">
    table {
        border-spacing: 0px;
        font-family: Sans-Serif;
        font-weight: 300;
        font-size: 14px;
    }
    table td {
        border: 1px solid #eee;
        border-spacing: 0px;
    }
    table td b {
        font-weight: bold;
    }
</style>

    <table>
        <tbody>
            <tr>
                <td colspan="3"><b>Identification</b></td>
                @foreach ($assessments as $assessment)
                    <td><b>{{ $assessment->name }}</b></td>
                    @for ($i = 0; $i < count($assessment->questions) - 1; $i++)
                        <td></td>
                    @endfor
                @endforeach
            </tr>
            <tr>
                <td><b>UserID</b></td>
                <td><b>Name</b></td>
                <td><b>Email</b></td>

                <!-- Dimension Codes -->
                @foreach ($assessments as $assessment)
                    {{-- !! Bad, should probably use scopes on this one --}}
                    @foreach ($assessment->filteredQuestions() as $question)
                        <td><b>{{ $question->dimension_code() }}</b></td>
                    @endforeach
                @endforeach
            </tr>
            <tr>
                <td colspan="3"></td>

                <!-- Questions -->
                @foreach ($assessments as $assessment)
                    {{-- !! Bad, should probably use scopes on this one --}}
                    @foreach ($assessment->filteredQuestions() as $question)
                        <td>{{ $question->number }}. {{ $question->content }}</td>
                    @endforeach
                @endforeach
            </tr>
            @foreach ($users as $user)
                <tr>
                    <td>{{ $user->username }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>

                    <!-- Score -->
                    @foreach ($assessments as $assessment)
                        {{--@if (in_array($assessment->id, $user->assigned_assessments))--}}
                        @if (false !== $key = array_search($assessment->id, $user->assigned_assessments['assessment_ids']))
                            {{-- !! Bad, should probably use scopes on this one --}}
                            @foreach ($assessment->filteredQuestions() as $question)
                                <td align="left">
                                    @if ($question->answer_exists($user->assigned_assessments['assignment_ids'][$key], $user->id))
                                        {{ $question->answerFromAssignment($user->assigned_assessments['assignment_ids'][$key])->questionScore() }}
                                    @endif
                                </td>
                            @endforeach
                        @else
                            {{-- !! Bad, should probably use a single scope on this one, just for filtering non-answerables, not for sorting them since they're blank anyway --}}
                            @foreach ($assessment->filteredQuestions() as $question)
                                <td></td>
                            @endforeach
                        @endif
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

</html>