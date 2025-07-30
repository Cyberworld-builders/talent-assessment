<html>
{{--<table>--}}
    {{--<thead>--}}
        {{--<tr>--}}
            {{--<th><b>#</b></th>--}}
            {{--<th><b>Dimension</b></th>--}}
            {{--<th><b>Question</b></th>--}}
            {{--<th><b>Answer</b></th>--}}
            {{--<th><b>Score</b></th>--}}
        {{--</tr>--}}
    {{--</thead>--}}
    {{--<tbody>--}}
        {{--@foreach ($questions as $question)--}}
            {{--@if ($question->type != 2)--}}
                {{--<tr>--}}
                    {{--<td>{{ $question->number }}</td>--}}
                    {{--<td>--}}
                        {{--@if ($question->dimension())--}}
                            {{--@if ($question->dimension()->isChild())--}}
                                {{--{{ $question->dimension()->getParent()->name }} ---}}
                            {{--@endif--}}
                            {{--{{ $question->dimension()->name }}--}}
                        {{--@endif--}}
                    {{--</td>--}}
                    {{--<td>{!! strip_tags($question->content) !!}</td>--}}
                    {{--<td>--}}
                        {{--@if ($question->answer_exists($assignment->id, $user->id))--}}
                            {{--{{ $question->answerFromAssignment($assignment->id)->questionText() }}--}}
                        {{--@endif--}}
                    {{--</td>--}}
                    {{--<td>--}}
                        {{--@if ($question->answer_exists($assignment->id, $user->id))--}}
                            {{--{{ $question->answerFromAssignment($assignment->id)->questionScore() }}--}}
                        {{--@endif--}}
                    {{--</td>--}}
                {{--</tr>--}}
            {{--@endif--}}
        {{--@endforeach--}}
    {{--</tbody>--}}
{{--</table>--}}

<table>
    <thead>
        <tr>
            <th colspan="3"><b>Identification</b></th>
            <th><b>{{ $assessment->name }}</b></th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><b>UserID</b></td>
            <td><b>Name</b></td>
            <td><b>Email</b></td>

            <!-- Dimension Codes -->
            @foreach ($questions as $question)
                <td><b>{{ $question->dimension_code() }}</b></td>
            @endforeach
        </tr>
        <tr>
            <td colspan="3"></td>

            <!-- Question -->
            @foreach ($questions as $question)
                <td>{{ $question->number }}. {{ $question->content }}</td>
            @endforeach
        </tr>
        <tr>
            <td>{{ $user->username }}</td>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>

            <!-- Score -->
            @foreach ($questions as $question)
                <td align="left">
                    @if ($question->answer_exists($assignment->id, $user->id))
                        {{ $question->answerFromAssignment($assignment->id)->questionScore() }}
                    @endif
                </td>
            @endforeach
        </tr>
    </tbody>
</table>

</html>