<tr data-index="{{ $i }}">
    <td>{!! Form::textarea('tasks['.$i.'][task]', $task['task'], ['class' => 'form-control input-lg autosize array-input', 'rows' => 1]) !!}</td>
    <td>{!! Form::checkbox('tasks['.$i.'][relevant]', true, $task['relevant'], ['class' => 'iswitch iswitch-secondary array-input']) !!}</td>
</tr>