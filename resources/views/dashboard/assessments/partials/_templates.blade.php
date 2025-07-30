<!-- Javascript Templates -->
<div style="display:none;">

    <!-- Question Template -->
    <div id="question-template">
        @include('dashboard.assessments.partials._question', [
            'id' => 0,
            'content' => "This is a sample question",
            'number' => 1,
            'type' => 1,
            'dimension_id' => 0,
            'anchors' => [],
            'practice' => 0,
        ])
    </div>

    <!-- Scoring Method Tags & Weighting Template -->
    <div id="weight-option-template">
        @include('dashboard.assessments.partials._weight_option', ['value' => 0, 'tag' => ''])
    </div>

    <!-- Custom Field Template -->
    <div id="custom-field-template">
        <div class="row custom-field">
            <div class="col-sm-3">
                {!! Form::label('', 'Tag') !!}
                {!! Form::text('custom_fields[tag][]', null, ['class' => 'form-control input-lg']) !!}
            </div>
            <div class="col-sm-3">
                {!! Form::label('', 'Default Value') !!}
                {!! Form::text('custom_fields[default][]', null, ['class' => 'form-control input-lg']) !!}
            </div>
            <div class="col-sm-1">
                <a id="remove-custom-field"><i class="fa-times"></i></a>
            </div>
        </div>
    </div>

</div>