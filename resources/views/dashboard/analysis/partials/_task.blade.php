<div class="task" data-index="{{ $index }}">
    <div class="row">
        <div class="remove-task"><i class="fa-times"></i></div>
        @if ($position === '')
            <div class="col-sm-12">{!! Form::textarea(null, null, ['class' => 'form-control input-lg autosize', 'rows' => 1]) !!}</div>
        @else
            <div class="col-sm-12">{!! Form::textarea('tasks['.$position.'][]', $task, ['class' => 'form-control input-lg autosize', 'rows' => 1]) !!}</div>
        @endif
    </div>
</div>