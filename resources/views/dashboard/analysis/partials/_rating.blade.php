<div class="rating" data-index="{{ $index }}">
    <div class="row">
        <div class="remove-rating"><i class="fa-times"></i></div>
        @if ($position === '')
            <div class="select-col col-sm-3 type">
                {!! Form::label('ratings', 'Type', ['class' => 'control-label']) !!}
                {!! Form::select(null, [
                    'assessment' => 'Assessment',
                    'dimension' => 'Dimension',
                ], null, ['class' => 'form-control input-lg']) !!}
            </div>
            <div class="select-col col-sm-3 element">
                {!! Form::label('ratings', 'Assessment', ['class' => 'control-label']) !!}
                {!! Form::select(null, $assessmentsArray, null, ['class' => 'form-control input-lg']) !!}
            </div>
            <div class="col-sm-6 description">
                {!! Form::label('ratings', 'Description', ['class' => 'control-label']) !!}
                {!! Form::textarea(null, null, ['class' => 'form-control input-lg autosize', 'rows' => 1]) !!}
            </div>
        @else
            <div class="select-col col-sm-3 type">
                {!! Form::label('ratings', 'Type', ['class' => 'control-label']) !!}
                {!! Form::select('ratings['.$position.'][0][type]', [
                    'assessment' => 'Assessment',
                    'dimension' => 'Dimension',
                ], $type, ['class' => 'form-control input-lg']) !!}
            </div>
            <div class="select-col col-sm-3 element">
                {!! Form::label('ratings', 'Assessment', ['class' => 'control-label']) !!}
                {!! Form::select('ratings['.$position.'][0][id]', ($type == 'dimension' ? $dimensionsArray : $assessmentsArray), $id, ['class' => 'form-control input-lg']) !!}
            </div>
            <div class="col-sm-6 description">
                {!! Form::label('ratings', 'Description', ['class' => 'control-label']) !!}
                {!! Form::textarea('ratings['.$position.'][0][description]', $description, ['class' => 'form-control input-lg autosize', 'rows' => 1]) !!}
            </div>
        @endif
    </div>
</div>