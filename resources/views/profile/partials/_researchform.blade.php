<div class="panel panel-default panel-border">


    {{--<div class="member-form-add-header">--}}
    {{--<div class="row">--}}

    {{--<div class="col-md-2 col-sm-4 pull-right-sm">--}}
    {{--<a href="" class="btn btn-white"><i class="fa-gear"></i> Change Image</a>--}}
    {{--</div>--}}

    {{--<div class="col-md-10 col-sm-8">--}}
    {{--<div class="user-img">--}}
    {{--<img src="{{ url('assets/images/user-4.png') }}" class="img-circle" alt="user-pic">--}}
    {{--</div>--}}
    {{--<div class="user-name">--}}
    {{--<a href="#">{{ $user->name }}</a>--}}
    {{--<span>{{ $user->roles->first()->name }}</span>--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--</div>--}}


    <div>
        <div class="row">
            <div class="col-sm-6">
                <div class="input-field">
                    {!! Form::label('age', translate('Age'), ['class' => 'control-label']) !!}
                    {!! Form::input('number', 'age', null, ['class' => 'form-control input-lg']) !!}
                </div>
                <div class="input-field">
                    {!! Form::label('gender', translate('Gender'), ['class' => 'control-label']) !!}
                    {!! Form::select('gender', [
                        '' => '',
                        'Male' => translate('Male'),
                        'Female' => translate('Female')
                    ], null, ['class' => 'form-control input-lg']) !!}
                    <script type="text/javascript">
                        jQuery(document).ready(function($)
                        {
                            $("#gender").select2({
                                placeholder: '{{ translate('Select your gender...') }}',
                                allowClear: true
                            }).on('select2-open', function()
                            {
                                // Adding Custom Scrollbar
                                $(this).data('select2').results.addClass('overflow-hidden').perfectScrollbar();
                            });
                        });
                    </script>
                </div>
                <div class="input-field">
                    {!! Form::label('ethnicity', translate('Ethnicity'), ['class' => 'control-label']) !!}
                    {!! Form::select('ethnicity', [
                        '' => '',
                        'Asian' => translate('Asian'),
                        'White' => translate('White'),
                        'Black or African American' => translate('Black or African American'),
                        'Hispanic or Latino' => translate('Hispanic or Latino'),
                        'Native American' => translate('Native American'),
                        'Pacific Islander' => translate('Pacific Islander'),
                        'Decline to Answer' => translate('Decline to Answer'),
                        'Other' => translate('Other'),
                    ], null, ['class' => 'form-control input-lg']) !!}
                    <script type="text/javascript">
                        jQuery(document).ready(function($)
                        {
                            $("#ethnicity").select2({
                                placeholder: '{{ translate('Select your ethnicity...') }}',
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
            <div class="col-sm-6">
                <div class="input-field">
                    {!! Form::label('industry', translate('Industry'), ['class' => 'control-label']) !!}
                    {!! Form::select('industry', [
                        '' => '',
                        'Advertising and Marketing' => translate('Advertising and Marketing'),
                        'Banking and Financial Services' => translate('Banking and Financial Services'),
                        'Business Support Services' => translate('Business Support Services'),
                        'Construction' => translate('Construction'),
                        'Education' => translate('Education'),
                        'Energy, Utilities, and Telecommunications' => translate('Energy, Utilities, and Telecommunications'),
                        'Entertainment and Media' => translate('Entertainment and Media'),
                        'Food and Beverage' => translate('Food and Beverage'),
                        'Government' => translate('Government'),
                        'Health Care' => translate('Health Care'),
                        'Industrial Metals and Mining' => translate('Industrial Metals and Mining'),
                        'Information Technology' => translate('Information Technology'),
                        'Law Enforcement' => translate('Law Enforcement'),
                        'Leisure and Hospitality' => translate('Leisure and Hospitality'),
                        'Manufacturing' => translate('Manufacturing'),
                        'Pharmaceuticals' => translate('Pharmaceuticals'),
                        'Retail Sales' => translate('Retail Sales'),
                        'Sports and Recreation' => translate('Sports and Recreation'),
                        'Transportation' => translate('Transportation'),
                        'Other' => translate('Other'),
                    ], null, [
                        'class' => 'form-control input-lg',
                        'id' => 'industry',
                    ]) !!}
                    <script type="text/javascript">
                        jQuery(document).ready(function($)
                        {
                            $("#industry").select2({
                                placeholder: '{{ translate('Select your industry...') }}',
                                allowClear: true
                            }).on('select2-open', function()
                            {
                                // Adding Custom Scrollbar
                                //console.log($(this).data('select2'));
                                //$(this).data('select2').results.addClass('overflow-hidden').perfectScrollbar();
                            });
                        });
                    </script>
                </div>
                <div class="input-field">
                    {!! Form::label('purpose', translate('Assessment Purpose'), ['class' => 'control-label']) !!}
                    {!! Form::select('purpose', [
                        '' => '',
                        'Applying for a job' => translate('Applying for a job'),
                        'For my current employer' => translate('For my current employer'),
                        'Other' => translate('Other'),
                    ], null, ['class' => 'form-control input-lg']) !!}
                    <script type="text/javascript">
                        jQuery(document).ready(function($)
                        {
                            $("#purpose").select2({
                                placeholder: '{{ translate('Select your purpose for taking this assessment...') }}',
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
        </div>
    </div>

</div>

<div class="form-group">
    <br/>
    <div class="pull-right">
        {!! Form::submit(translate('Update Profile'), ['class' => 'btn btn-primary btn-lg']) !!}
    </div>
</div>