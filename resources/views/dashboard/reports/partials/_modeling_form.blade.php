@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/js/icheck/skins/all.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
@stop

<style>
    .row.no-border:before {
        border:none;
        margin: 0;
        padding: 0;
        background: none;
        height: 0;
    }
    .report-page-wrapper {
        background: url("https://s3-us-west-2.amazonaws.com/aoe-uploads/images/aoe-background.jpg");
        padding: 20px;
        position: relative;
        text-align: center;
    }
    .report-page-wrapper .cover-logo {
        max-width: 30%;
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
        top: 40px;
    }
    .report-page {
        background: white;
        padding: 20px;
        padding-top: 90px;
        list-style: none;
    }
    .report-page li {
        border: 1px solid #ddd;
        padding: 5px;
        margin-bottom: 2px;
        font-family: "Bebas Neue";
        font-size: 20px;
        cursor: move;
        background: white;
    }
    .reserve-box {
        border: 2px solid #ddd;
        padding: 10px;
        margin-top: 10px;
        list-style: none;
    }
    .reserve-box li {
        cursor: move;
    }
    h3 {
        font-family: "Bebas Neue";
        font-size: 20px;
    }
    .tocify .tocify-item.active > a {
        color: #333;
        font-size: 16px;
        cursor: pointer;
    }
    .tocify .tocify-item.active > a:hover {
        background: #f0f0f0;
    }
    .tocify .tocify-item > a {
        color: #333;
        font-size: 16px;
        cursor: pointer;
    }
    .tocify .tocify-item > a:hover {
        background: #f0f0f0;
        color: #777;
    }
    .tocify .tocify-item a i {
        margin-right: 5px;
    }
</style>

<div class="panel panel-headerless">
    <div class="panel-body">
        <div class="row">
            <div class="member-form-inputs">

                {{-- Sidebar --}}
                <div class="col-sm-3">
                    @include('dashboard.reports.partials._sidenav', ['active' => 'modeling'])
                </div>

                {{-- Form --}}
                <div class="col-sm-9">

                    {{-- Model --}}
                    <div class="row">
                        <div class="col-sm-3">
                            {!! Form::label('file', 'Decision Tree', ['class' => 'control-label']) !!}
                            <p class="small text-muted">This should be an XML or PMML file exported from SPSS Modeler.</p>
                        </div>
                        <div class="col-sm-6">
                            @if ($report->model)
                                <div class="file" style="max-width: 230px;">
                                    <p class="pull-left">
                                        <i class="fa-file"></i> <strong>Filename:</strong> {{ $report->model_filename }}<br/>
                                        <i class="fa-share-alt"></i> <strong>Data Points:</strong> {{ $report->model->TreeModel->Node->{'@attributes'}->recordCount }}
                                    </p>
                                    <a class="btn btn-black" id="new-file" style="margin-top: 14px" href="#null">Upload New File</a>
                                </div>
                                {!! Form::file('file', ['id' => 'file', 'disabled', 'style' => 'display:none;']) !!}
                            @else
                                {!! Form::file('file', ['id' => 'file']) !!}
                            @endif
                        </div>
                        <div class="col-sm-3">
                            @if ($report->model)
                                @if ($report->model_configured)
                                    <div class="text text-success"><i class="fa-check"></i> Configured</div>
                                @else
                                    <div class="text text-danger"><i class="fa-times"></i> Not Configured</div>
                                @endif
                            @endif
                        </div>
                    </div>

                    {{-- Configuration --}}
                    @if ($report->model)
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
                                @foreach ($report->model_factors as $i => $factor)
                                    <div class="row factor">
                                        <div class="col-xs-4">
                                            <h4>{{ $factor->name }}</h4>
                                        </div>
                                        <div class="col-xs-4">
                                            {!! Form::select('factors[type][]', [
                                                null => 'Select a type',
                                                'assessment' => 'Assessment',
                                                'dimension' => 'Dimension',
                                            ], $report->model_factors[$i]->type, ['class' => 'form-control input-lg factor-type']) !!}
                                        </div>
                                        <div class="col-xs-4">
                                            @if ($report->model_factors[$i]->type == 'assessment')
                                                {!! Form::select('factors[id][]', $assessmentsArray, $report->model_factors[$i]->id, ['class' => 'form-control input-lg factor-id']) !!}
                                            @elseif ($report->model_factors[$i]->type == 'dimension')
                                                {!! Form::select('factors[id][]', $dimensionsArray, $report->model_factors[$i]->id, ['class' => 'form-control input-lg factor-id']) !!}
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
    </div>
</div>

<!-- Submit Field -->
<div class="form-group">
    <br/>
    <div class="pull-right">
        {!! Form::submit($button_name, ['class' => 'btn btn-primary btn-lg']) !!}
    </div>
    <div class="clearfix"></div>
</div>

<!-- Scripts -->
<script>
    (function($){
        $(document).ready(function(){

            // Set headers for AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
                }
            });

            @if ($report->model)
                var assessments = {!! json_encode($assessments) !!};
                var dimensions = {!! json_encode($dimensions) !!};

                $('#new-file').on('click', function(){
                    $('.file').hide();
                    $('.factors').hide();
                    $('#file').fadeIn().removeAttr('disabled');
                });

                $('.factor-type').on('change', function() {
                    update_factor_choices($(this));
                });

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
                        $idSelect.attr('disabled', '');
                }
            @endif
        });
    })(jQuery);
</script>

@section('scripts')
    <script src="{{ asset('assets/js/select2/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery-ui/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('assets/js/selectboxit/jquery.selectBoxIt.min.js') }}"></script>
@stop