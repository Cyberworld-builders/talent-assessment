<div class="panel panel-headerless" style="border: 1px solid #eee;background: #f8f8f8;">
    <a class="remove-row-button" href="#null" style="z-index:10;"><i class="fa-times"></i></a>
    <div class="panel-body">

        <!-- User Add Field -->
        <div class="user-add-form">
            <div class="row">
                @role('admin|reseller')
                    <div class="col-sm-2">
                        {!! Form::label('username[]', 'Username', ['class' => 'control-label']) !!}
                        {!! Form::text('username[]', null, ['class' => 'form-control input-lg']) !!}
                    </div>
                    <div class="col-sm-2">
                        {!! Form::label('name[]', 'Name', ['class' => 'control-label']) !!}
                        {!! Form::text('name[]', null, ['class' => 'form-control input-lg']) !!}
                    </div>
                    <div class="col-sm-2">
                        {!! Form::label('email[]', 'Email', ['class' => 'control-label']) !!}
                        {!! Form::text('email[]', null, ['class' => 'form-control input-lg']) !!}
                    </div>
                    <div class="col-sm-2">
                        {!! Form::label('job_title[]', 'Job Title', ['class' => 'control-label']) !!}
                        {!! Form::text('job_title[]', null, ['class' => 'form-control input-lg']) !!}
                    </div>
                    <div class="col-sm-2">
                        {!! Form::label('job_family[]', 'Job Family', ['class' => 'control-label']) !!}
                        {!! Form::text('job_family[]', null, ['class' => 'form-control input-lg']) !!}
                    </div>
                    <div class="col-sm-2">
                        {!! Form::label('job_id[]', 'Add To Job?', ['class' => 'control-label']) !!}
                        <a href="#null" id="apply-job-to-all" class="text-muted text-small">Apply To All</a>
                        {!! Form::select('job_id[]', $jobsArray, (Input::get("job") ? Input::get("job") : 0), ['class' => 'form-control input-lg', 'id' => 'job_id']) !!}
                        <script type="text/javascript">
                            jQuery(document).ready(function($)
                            {
                                $("#job_id").select2({
                                    allowClear: true
                                }).on('select2-open', function()
                                {
                                    // Adding Custom Scrollbar
                                    $(this).data('select2').results.addClass('overflow-hidden').perfectScrollbar();
                                });
                            });
                        </script>
                    </div>
                @endrole
                @role('client')
                    <div class="col-sm-3">
                        {!! Form::label('username[]', 'Username', ['class' => 'control-label']) !!}
                        {!! Form::text('username[]', null, ['class' => 'form-control input-lg']) !!}
                    </div>
                    <div class="col-sm-3">
                        {!! Form::label('name[]', 'Name', ['class' => 'control-label']) !!}
                        {!! Form::text('name[]', null, ['class' => 'form-control input-lg']) !!}
                    </div>
                    <div class="col-sm-3">
                        {!! Form::label('email[]', 'Email', ['class' => 'control-label']) !!}
                        {!! Form::text('email[]', null, ['class' => 'form-control input-lg']) !!}
                    </div>
                    <div class="col-sm-3">
                        {!! Form::label('job_id[]', 'Add To Job?', ['class' => 'control-label']) !!}
                        <a href="#null" id="apply-job-to-all" class="text-muted text-small">Apply To All</a>
                        {!! Form::select('job_id[]', $jobsArray, (Input::get("job") ? Input::get("job") : 0), ['class' => 'form-control input-lg', 'id' => 'job_id']) !!}
                        {{--<script type="text/javascript">--}}
                            {{--jQuery(document).ready(function($)--}}
                            {{--{--}}
                                {{--$("#job_id").select2({--}}
                                    {{--allowClear: true--}}
                                {{--}).on('select2-open', function()--}}
                                {{--{--}}
                                    {{--// Adding Custom Scrollbar--}}
                                    {{--$(this).data('select2').results.addClass('overflow-hidden').perfectScrollbar();--}}
                                {{--});--}}
                            {{--});--}}
                        {{--</script>--}}
                    </div>
                @endrole
            </div>
        </div>

    </div>
</div>