@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
    <style>
        html .select2-container.select2-container-multi .select2-choices .select2-search-choice {
            padding: 6px 9px 6px 21px;
        }
        html .select2-container.select2-container-multi .select2-choices {
            padding: 4px;
        }
    </style>
@stop

<div class="panel panel-headerless">
    <div class="panel-body">
        <div class="member-form-inputs">

            {{-- Name --}}
            <div class="row">
                <div class="col-sm-3">
                    {!! Form::label('name', 'Model Name', ['class' => 'control-label']) !!}
                    <p class="small text-muted">Set a name for this predictive model.</p>
                </div>
                <div class="col-sm-9">
                    {!! Form::text('name', null, ['class' => 'form-control input-lg']) !!}
                </div>
            </div>

            {{-- Job --}}
            <div class="row">
                <div class="col-sm-3">
                    {!! Form::label('job', 'Job', ['class' => 'control-label']) !!}
                    <p class="small text-muted">The job that this model will apply to.</p>
                </div>
                <div class="col-sm-9">
                    {!! Form::select('job', $jobsArray, 0, ['class' => 'form-control input-lg', 'id' => 'job']) !!}
                </div>
            </div>

            {{-- Assessments --}}
            <div class="row">
                <div class="col-sm-3">
                    {!! Form::label('assessments[]', 'Assessments', ['class' => 'control-label']) !!}
                    <p class="small text-muted">The assessments that this model will apply to.</p>
                </div>
                <div class="col-sm-9">
                    @if ($edit)
                        {!! Form::select('assessments[]', $jobAssessments, $model->assessments, ['class' => 'form-control input-lg', 'id' => 'assessments', 'multiple']) !!}
                    @else
                        {!! Form::select('assessments[]', [], null, ['class' => 'form-control input-lg', 'id' => 'assessments', 'disabled', 'multiple']) !!}
                    @endif
                    <script type="text/javascript">
                        jQuery(document).ready(function($)
                        {
                            $("#assessments").select2({
                                placeholder: 'Select Assessments',
                                allowClear: true
                            }).on('select2-open', function()
                            {
                                // Adding Custom Scrollbar
                                $(this).data('select2').results.addClass('overflow-hidden').perfectScrollbar();
                            });
                        });
                    </script>
                </div>
            </div>

            {{-- Model --}}
            <div class="row">
                <div class="col-sm-3">
                    {!! Form::label('file', 'Decision Tree', ['class' => 'control-label']) !!}
                    <p class="small text-muted">This should be an XML or PMML file exported from SPSS Modeler.</p>
                </div>
                <div class="col-sm-9">
                    @if ($edit)
                        <div class="file" style="max-width: 230px;">
                            <p class="pull-left">
                                <i class="fa-file"></i> <strong>Filename:</strong> {{ $model->filename }}<br/>
                                <i class="fa-share-alt"></i> <strong>Data Points:</strong> {{ $model->model->TreeModel->Node->{'@attributes'}->recordCount }}
                            </p>
                            <a class="btn btn-black" id="new-file" style="margin-top: 14px" href="#null">Upload New File</a>
                        </div>
                        {!! Form::file('file', ['id' => 'file', 'disabled', 'style' => 'display:none;']) !!}
                    @else
                        {!! Form::file('file', ['id' => 'file']) !!}
                    @endif
                </div>
            </div>

            {{-- Configuration --}}
            @if ($edit)
                <div class="row factors">
                    <div class="col-sm-3">
                        {!! Form::label('factors', 'Configuration', ['class' => 'control-label']) !!}
                        <p class="small text-muted">
                            These are the scoring factors that have been detected as part of the uploaded Decision Tree.
                            These factors must match their appropriate entities in this platform for the Decision Tree
                            to be properly configured.
                        </p>
                    </div>
                    <div class="col-sm-9">
                        @foreach ($model->factors as $i => $factor)
                            <div class="row factor">
                                <div class="col-xs-4">
                                    <h4>{{ $factor['name'] }}</h4>
                                </div>
                                <div class="col-xs-4">
                                    {!! Form::select('factors[type][]', [
                                        null => 'Select a type',
                                        'assessment' => 'Assessment',
                                        'dimension' => 'Dimension',
                                    ], $model->factors[$i]['type'], ['class' => 'form-control input-lg factor-type']) !!}
                                </div>
                                <div class="col-xs-4">
                                    @if ($model->factors[$i]['type'] == 'assessment')
                                        {!! Form::select('factors[id][]', $assessmentsArray, $model->factors[$i]['id'], ['class' => 'form-control input-lg factor-id']) !!}
                                    @elseif ($model->factors[$i]['type'] == 'dimension')
                                        {!! Form::select('factors[id][]', $dimensionsArray, $model->factors[$i]['id'], ['class' => 'form-control input-lg factor-id']) !!}
                                    @else
                                        {!! Form::select('factors[id][]', [], null, ['class' => 'form-control input-lg factor-id', 'disabled']) !!}
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>

    </div>
</div>

<div class="form-group">
    <br/>
    <div class="pull-right">
        {!! Form::submit($button_name , ['class' => 'btn btn-primary btn-lg']) !!}
    </div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function($){

        // Set headers for AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
            }
        });

        @if (! $edit)

            var jobAssessments = {!! json_encode($jobAssessments) !!};

            $('#job').on('change', function()
            {
                update_job_assessments();
            });

            update_job_assessments();

            function update_job_assessments()
            {
                var jobId = $('#job').val();
                $('#assessments option').remove();

                if (jobId)
                {
                    for (var i = 0; i < jobAssessments[jobId].length; i++)
                        $('#assessments').append('<option value="'+jobAssessments[jobId][i]["id"]+'">'+jobAssessments[jobId][i]["name"]+'</option>');
                    $('#assessments').removeAttr('disabled');
                }
                else
                    $('#assessments').attr('disabled', '');
            }

        @else

            var assessments = {!! json_encode($assessments) !!};
            var dimensions = {!! json_encode($dimensions) !!};

            $('#new-file').on('click', function(){
                $('.file').hide();
                $('.factors').hide();
                $('#file').fadeIn().removeAttr('disabled');
            });

            $('.factor-type').on('change', function()
            {
                update_factor_choices($(this));
            });

//            $('.factor-type').each(function() {
//                update_factor_choices($(this));
//            });

            function update_factor_choices($select)
            {
                var val = $select.val();

                $idSelect = $select.closest('.factor').find('.factor-id');
                $('option', $idSelect).remove();

                if (val == 'assessment')
                {
                    for (var i = 0; i < assessments.length; i++)
                        $idSelect.append('<option value="'+assessments[i]["id"]+'">'+assessments[i]["name"]+'</option>');
                    $idSelect.removeAttr('disabled');
                }

                else if (val == 'dimension')
                {
                    for (var i = 0; i < dimensions.length; i++)
                        $idSelect.append('<option value="'+dimensions[i]["id"]+'">'+dimensions[i]["name"]+'</option>');
                    $idSelect.removeAttr('disabled');
                }

                else
                {
                    $idSelect.attr('disabled', '');
                }
            }

        @endif
    });
</script>

@section('scripts')
    <script src="{{ asset('assets/js/select2/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/selectboxit/jquery.selectBoxIt.min.js') }}"></script>
@stop

