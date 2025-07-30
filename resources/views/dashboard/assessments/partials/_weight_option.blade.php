<div class="row weight-input">
    <div class="col-sm-2">
        <div class="input-group">
            {!! Form::input('number', '', $value, [
                'class' => 'form-control input-lg weight-value',
                'min' => -10,
                'max' => 10,
            ]) !!}
        </div>
    </div>
    <div class="col-sm-8">
        {!! Form::text('', $tag, [
            'class' => 'form-control input-lg weight-tag',
            'data-role' => 'tagsinput'
        ]) !!}
    </div>
    <div class="col-sm-2">
        <div id="remove-weight-option" class="btn btn-gray"><i class="fa-minus"></i> Remove</div>
    </div>
</div>