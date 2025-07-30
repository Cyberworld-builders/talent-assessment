<div class="ksa" data-index="{{ $index }}">
    <div class="row">
        <div class="remove-ksa"><i class="fa-times"></i></div>
        @if ($position === '')
            <div class="col-sm-4">
                {!! Form::label('ksas', 'Name', ['class' => 'control-label']) !!}
                {!! Form::text(null, null, ['class' => 'form-control input-lg']) !!}
            </div>
            <div class="col-sm-8">
                {!! Form::label('ksas', 'Description', ['class' => 'control-label']) !!}
                {!! Form::textarea(null, null, ['class' => 'form-control input-lg autosize', 'rows' => 1]) !!}
            </div>
        @else
            <div class="col-sm-4">
                {!! Form::label('ksas', 'Name', ['class' => 'control-label']) !!}
                {!! Form::text('ksas['.$position.']['.$index.'][name]', $ksa, ['class' => 'form-control input-lg']) !!}
            </div>
            <div class="col-sm-8">
                {!! Form::label('ksas', 'Description', ['class' => 'control-label']) !!}
                {!! Form::textarea('ksas['.$position.']['.$index.'][description]', $description, ['class' => 'form-control input-lg autosize', 'rows' => 1]) !!}
            </div>
        @endif
    </div>
</div>