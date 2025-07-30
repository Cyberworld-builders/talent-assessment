<div class="panel panel-headerless">
    <div class="panel-body">
        <div class="member-form-inputs">

            <!-- Expiration Field -->
            <div class="form-group">
                <div class="row">
                    <div class="col-sm-4">
                        {!! Form::label('selectassignments', 'Select Assignments', ['class' => 'control-label']) !!}
                        <p class="small text-muted">Select Multiple assignments for bulk editing.</p>
                    </div>
                    <div class="col-sm-8" style="height: 250px;
    overflow-y: scroll;">
                        <div class="input-group" style="width:100%;">
                            <table width="100%">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Assignment</th>
                                        <th>User</th>
                                        <th>Expires On</th>
                                    </tr>
                                    <tr>
                                        <td colspan="4"><a href="#null" class="select-all-items text-small"><i class="fa-arrow-down"></i> <span>Select All</span></a></td>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($assignments as $assignment)
                                        <tr>
                                            <td>
                                                <span>{!! Form::checkbox('assignments[]', $assignment['assignment_id'], null, ['class' => 'form-control input-lg','style'=>'width: 20px;height: 20px;']) !!}</span>
                                            </td>
                                            <td><span style="line-height:1.7;"> {{ $assignment['assessment'] }}</span></td>
                                            <td>{{ $assignment['user']->name }}</td>
                                            <td>{{ $assignment['expires']->format('M d Y - h:i:s') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                             </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Expiration Field -->
            <div class="form-group">
                <div class="row">
                    <div class="col-sm-4">
                        {!! Form::label('expiration', 'Expiration Date', ['class' => 'control-label']) !!}
                        <p class="small text-muted">Users will not be able to start or finish unfinished assignments after they have expired.</p>
                    </div>
                    <div class="col-sm-8">
                        <div class="input-group">
                            {!! Form::text('expiration', Carbon\Carbon::tomorrow()->format('D, d M Y'), [
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
            @role('admin')
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
            @endrole

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
                        ], 0, ['class' => 'form-control input-lg']) !!}
                    </div>
                </div>
            </div>
            <div class="form-group field-reminder" style="display:none;">
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
                        ], '+2 weeks', ['class' => 'form-control input-lg']) !!}
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

        $('.select-all-items').on('click', function() {
            if (! $(this).hasClass('active'))
            {
                $('td input[type="checkbox"]').each(function() {
                    $(this).prop('checked', true);
                });
                $(this).addClass('active');
                $('span', this).html('Un-select All');
            }
            else
            {
                $('td input[type="checkbox"]').each(function() {
                    $(this).prop('checked', false);
                });
                $(this).removeClass('active');
                $('span', this).html('Select All');
            }
        });
    });
</script>