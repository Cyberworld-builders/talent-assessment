@extends('dashboard.clientdashboard')

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/js/select2/select2.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/js/select2/select2-bootstrap.css') }}">
@stop

@section('content')
    <div class="wrapper">

        {{-- Header --}}
        <div class="intro-box">
            <div class="row">
                <div class="col-sm-7">
                    <h1>Hello, {{ $user->name }}!</h1>
                    <h3>What would you like to do?</h3>
                </div>
                <div class="col-sm-5">
                    <div class="searchbox">
                        <i class="linecons-search"></i>
                        {!! Form::select('user_search_form', $usersArray, null, ['class' => 'form-control input-lg user-search-form', 'id' => 'user_search_form', 'multiple']) !!}
                        <script type="text/javascript">
                            jQuery(document).ready(function($)
                            {
                                $("#user_search_form").select2({
                                    placeholder: 'Search for a user',
                                    //allowClear: true
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

        {{-- Touts --}}
        <div class="touts">
            <div class="row">
                <div class="col-sm-3">
                    <a href="{{ url('dashboard/all-users') }}" class="tout">
                        <i class="linecons-user"></i><br/>
                        <p>View Applicants</p>
                        <p class="text-small">
                            View and manage all of your applicants across all of your jobs.
                        </p>
                    </a>
                </div>
                <div class="col-sm-3">
                    <a href="{{ url('dashboard/selection') }}" class="tout">
                        <i class="fa-line-chart" style="display: inline-block; margin-top: 15px;"></i><br/>
                        <p>Employee Selection</p>
                        <p class="text-small">
                            Review viable applicants & their performance for specific jobs.
                        </p>
                    </a>
                </div>
                <div class="col-sm-3">
                    <a href="{{ url('dashboard/assignments') }}" class="tout">
                        <i class="fa-list-ol" style="margin-top: 16px;"></i><br/>
                        <p>Assignments</p>
                        <p class="text-small">
                            View and manage all assigned assessments.
                        </p>
                    </a>
                </div>
                <div class="col-sm-3">
                    <a href="{{ url('account') }}" class="tout">
                        <i class="linecons-cog" style="margin-top: -1px;"></i><br/>
                        <p>My Account</p>
                        <p class="text-small">
                            Manage you account settings and login credentials.
                        </p>
                    </a>
                </div>
            </div>
        </div>

        <a href="https://s3-us-west-2.amazonaws.com/aoe-uploads/docs/How_to_Navigate_AOEs_Client_Platform.pdf" target="_blank">
            <i class="fa-question-circle" style="margin-top: 16px; color: #fff; float: right; font-size: 4em;"></i
        </a>
    </div>

    <script type="text/javascript">
        jQuery(document).ready(function($){

            // Remove cached values from user add form
            function clear_user_search_form()
            {
                $('.user-search-form').val('');
                $('.user-search-form .select2-search-choice').remove();
                $('.user-search-form .select2-input').attr('placeholder', 'Search for a user').width(380);
            }
            clear_user_search_form();

            // User add form
            $('.user-search-form').on('change', function(){
                var userid = $(this).val();
                window.location = '/dashboard/all-users/'+userid[0];
                clear_user_search_form();
            });
        });
    </script>
@stop

@section('scripts')
    <script src="{{ asset('assets/js/select2/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/selectboxit/jquery.selectBoxIt.min.js') }}"></script>
@stop