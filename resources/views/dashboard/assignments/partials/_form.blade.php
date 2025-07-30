<div class="panel panel-headerless">
    <div class="panel-body">
        <div class="member-form-inputs">

            <!-- Expiration Field -->
            <div class="form-group">
                <div class="row">
                    <div class="col-sm-4">
                        {!! Form::label('expiration', 'Expiration Date', ['class' => 'control-label']) !!}
                        <p class="small text-muted">Users will not be able to start or finish unfinished assignments after they have expired.</p>
                    </div>
                    <div class="col-sm-8">
                        <div class="input-group">
                            {!! Form::text('expiration', null, [
                                'class' => 'form-control input-lg datepicker',
                                'data-format' => 'D, dd M yyyy',
                            ]) !!}
                            <div class="input-group-addon">
                                <i class="linecons-calendar"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- White Label Field -->
            <div class="form-group">
                <div class="row">
                    <div class="col-sm-4">
                        {!! Form::label('whitelabel', 'White-Label', ['class' => 'control-label']) !!}
                        <p class="small text-muted">White-labeled assessments will display the logo and background of the client to which the user belongs to.</p>
                    </div>
                    <div class="col-sm-8">
                    
                        {!! Form::select('whitelabel', [
                            0 => 'No',
                            1 => 'Yes',
                        ], null, ['class' => 'form-control input-lg']) !!}
                    </div>
                </div>
            </div>

  <!-- Email Reminder -->
            <div class="form-group">
                <div class="row">
                    <div class="col-sm-4">
                        {!! Form::label('email-reminder', 'Email Reminder', ['class' => 'control-label']) !!}
                        
                    </div>
                    <div class="col-sm-8">
                    {!! Form::select('reminder', [
                            0 => 'No',
                            1 => 'Yes',
                        ], $assignment->reminder, ['class' => 'form-control input-lg']) !!}
                    </div>
                </div>
            </div>
<div class="form-group field-reminder" style="display:{{$switch_reminder}};">
                <div class="row">
                    <div class="col-sm-4">
                        {!! Form::label('field-reminder', 'Reminder Frequency', ['class' => 'control-label']) !!}
                        <p class="small text-muted">Users will be notifiy pending tasks on selected frequency.</p>
                    </div>
                    <div class="col-sm-8">
                    {!! Form::select('reminder-frequency', [
                            '+1 week' => '1 Week',
                            '+2 weeks' => '2 Weeks',
                            '+3 weeks' =>'3 Weeks',
                            '+1 month'=>'Monthly'
                        ], $assignment->reminder_frequency, ['class' => 'form-control input-lg']) !!}
                    </div>
                </div>
            </div>

            <!-- Submit Field -->
            <div class="form-group">
                <br/>
                <div class="pull-right">
                    {!! Form::submit('Update', ['class' => 'btn btn-primary btn-lg']) !!}
                </div>
                <div class="clearfix"></div>
            </div>

        </div>
    </div>
</div>
<!-- Scripts -->
<script type="text/javascript">
    jQuery(document).ready(function ($)
    {
        $('select[name="reminder"]').on('change', function()
        {
            var val = $(this).val();
            if (val == 1)
                $('.field-reminder').slideDown();
            else
                $('.field-reminder').slideUp();
        });
    });
        </script>