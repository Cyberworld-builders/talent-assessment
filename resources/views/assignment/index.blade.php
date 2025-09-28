@extends('app')

@section('title')
    Assignments
@stop

@section('styles')
    {{-- Custom Fonts --}}
    @if ($user->client->whitelabel)
        @if ($user->client->id == 29)
            <link rel="preconnect" href="https://fonts.gstatic.com">
            <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;700&display=swap" rel="stylesheet">
        @endif
    @endif

    {{-- Main Styles --}}
    <style type="text/css">
        body {
            background: url({{ asset('images/report-background.jpg') }}) no-repeat fixed 0 0 transparent;
            background-size: cover;
        }
        .assignments .main-content {
            display: block;
            background-color: transparent;
            max-width: 1170px;
            margin: 0 auto;
            font-family: 'Avant Garde';
        }
        .assignments .main-content .title-env {
            text-align: center;
        }
        .assignments .main-content .title-env h1 {
            font-family: 'Avant Garde Demi';
        }
        .assignments .main-content .input-field {
            margin-bottom: 20px;
        }

        .assignments th {
            font-family: 'Avant Garde Demi';
            font-size: 18px;
        }

        .assignments .tab-content .tab-pane {
            border: 1px solid #ccc;
        }

        .assignments .tab-content table {
            margin-bottom: 0;
        }

        .assignments td.user-name .name {
            font-weight: 400;
        }

        footer {
            clear: both;
            text-align: center;
            margin-top: 60px;
        }
        /*.select2-container.select2-allowclear .select2-choice abbr {*/
            /*margin-top: 3px;*/
        /*}*/

        /* ------------------- */
        /* White Label Styling */
        /* ------------------- */
        /* 
            For auto white-labeling, need the following:
            - Logo image
            - Background image
            - Header color
        */
        @if ($user->client->whitelabel)
            @if ($user->client->id == 29)
                body {
                    background: url({{ asset('images/angela-background.jpg') }}) no-repeat fixed 0 0 transparent;
                }
                /* Navbar */
                .navbar.horizontal-menu {
                    box-shadow: none;
                }
                .navbar-brand .logo a p {
                    font-size: 13px;
                    color: #5C5958;
                }
                .navbar-inner {
                    background: #ecdedb;
                    text-transform: uppercase;
                    font-family: 'Nunito Sans', sans-serif;
                    font-weight: bold;
                    letter-spacing: 0.5px;
                    font-size: 13px;
                }
                .navbar.horizontal-menu .navbar-inner > .nav > li > a:hover {
                    background: #e5d1c6;
                }
                /* Content */
                .assignments .tab-content .tab-pane {
                    border: 1px solid #e3cec2;
                }
                .tab-content .tab-pane .table {
                    font-family: 'Nunito Sans', sans-serif;
                    letter-spacing: 0.5px;
                    border: 2px solid #e3cec2;
                    color: #5C5958;
                }
                .members-table thead tr th {
                    background: #e3cec2;
                    font-family: 'Nunito Sans', sans-serif;
                    font-weight: bold;
                    font-size: 13px;
                    letter-spacing: 0.5px;
                    border-bottom: none;
                }
                .members-table tbody tr {
                    background: #f6ebe8 !important;
                }
                .members-table tbody tr td {
                    border-top: 0;
                    border-bottom: 2px solid #e3cec2;
                    padding: 16px;
                }
                .members-table tbody tr td .name + span {
                    color: #CCA59A;
                }
                .members-table tbody tr td .email,
                .members-table tbody tr td .email:hover {
                    color: #CCA59A;
                }
            @endif
        @endif
    </style>
@stop

@section('body')

    {{-- Nav --}}
    @include('assignment.partials._nav')

    <div class="page-container assignments">
        <div class="main-content">

            {{-- Title --}}
            <div class="title-env">
                <br/><br/>
                @if ($user->client->whitelabel)
                    <img src="{{ $user->client->logo }}" />
                @else
                    <img src="{{ asset('assets/images/logo-small.png') }}" />
                @endif
                <br/>
                {{-- <h2>{{ translate('Your Assignments') }}</h2> --}}
                <br/><br/><br/>
            </div>

            {{-- Tab Content --}}
            <div class="tab-content">
                <div class="tab-pane active">

                    @if (! $assignments->isEmpty())
                        <table class="table table-hover members-table middle-align">
                            <thead>
                                <tr>
                                    <th>{{ translate('Assignment') }}</th>
                                    <th>{{ translate('Completed') }}</th>
                                    <th>{{ translate('Expiration') }}</th>
                                    <th>{{ translate('Settings') }}</th>
                                </tr>
                            </thead>
                            <tbody>


                                @foreach ($assignments as $i => $assignment)

                                    <?php if (! $assignment->assessment()) continue; ?>

                                    <tr style="background-color:white;margin-top:2px;">
                                    <td class="user-name">

                                        @if ($assignment->translation())
                                            <a href="#" class="name">{{ $assignment->translation()->name }}</a>
                                        @else
                                            <a href="#" class="name">{{ $assignment->assessment()->name }}</a>
                                        @endif
                                        <span>Assigned on {{ $assignment->created_at->format('M d Y - h:i:s') }}</span>
                                    </td>
                                    <td class="hidden-xs hidden-sm action-links">
                                        <span class="action-links">
                                            @if ($assignment->completed)
                                                <a class="edit"><i class="fa-check"></i> {{ translate('Completed') }} {{ $assignment->completed_at->format('M d Y - h:i:s') }}</a>
                                            @else
                                                @if (! $assignment->started_at)
                                                    <a class="delete"><i class="fa-times"></i> {{ translate('Not Completed') }}</a>
                                                    {{--<span style="color:#aaa;font-size:11px;">--}}
                                                        {{--{{ $assignment->answers->count() }} / {{ $assignment->assessment()->questions->count() }}--}}
                                                    {{--</span>--}}
                                                @else
                                                    <?php
                                                        $answers = $assignment->answers->count();
                                                        $questions = $assignment->assessment()->questions->count();
                                                        $percentage = ($answers / $questions) * 100 . '%';
                                                    ?>
                                                    <div class="progress" style="width: 30%;">
                                                        <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="{{ $answers }}" aria-valuemin="0" aria-valuemax="{{ $questions }}" style="width: {{ $percentage }}"></div>
                                                    </div>
                                                @endif
                                            @endif
                                        </span>
                                    </td>
                                    <td class="action-links">
                                        <span class="action-links">
                                            @if (! $assignment->completed)
                                                @if ($assignment->expires > Carbon\Carbon::now())
                                                    <span class="email">{{ translate('Expires in') }} {{ displayElapsedTime($assignment->expires) }}</span>
                                                @else
                                                    <a class="delete">{{ translate('Expired') }} {{ $assignment->expires->diffForHumans(null, true) }} ago</a>
                                                @endif
                                            @else
                                                <span style="color: #bbb;">&nbsp;---</span>
                                            @endif
                                        </span>
                                    </td>
                                    <td>
                                        @if (! $assignment->completed && $assignment->expires > Carbon\Carbon::now())
                                            {{--<a href="{{ $assignment->url }}"><i class="fa-pencil-square-o"></i> Take Assessment</a>--}}
                                            @if (! $assignment->started_at)
                                                <a href="{{ url('/assignments/stage/'.$assignment->id) }}"><i class="fa-pencil-square-o"></i> {{ translate('Take Assessment') }}</a>
                                            @else
                                                <a href="{{ $assignment->url }}"><i class="fa-pencil-square-o"></i> {{ translate('Continue Assessment') }}</a>
                                            @endif
                                        @endif
                                    </td>
                                </tr>

                                @endforeach

                            </tbody>
                        </table>
                    @else
                        <p style="padding: 30px 30px 20px 30px;">{{ translate('You have no assessments assigned to you.') }}</p>
                    @endif

                </div>
            </div>

            @if ($jaqs && !$jaqs->isEmpty())
                <br><br>
                <div class="title-env">
                    <h1>{{ translate('Job Analysis Questionnaires') }}</h1>
                </div>

                <div class="tab-content">
                    <div class="tab-pane active">

                            <table class="table table-hover members-table middle-align">
                                <thead>
                                <tr>
                                    <th>{{ translate('Job Analysis For') }}</th>
                                    <th>{{ translate('Completed') }}</th>
                                    <th>{{ translate('Settings') }}</th>
                                </tr>
                                </thead>
                                <tbody>

                                @foreach ($jaqs as $i => $jaq)

                                    <tr style="background-color:white;margin-top:2px;">
                                        <td class="user-name">
                                            <a href="#" class="name">{{ $jaq->analysis->name }}</a>
                                            <span>Sent on {{ $jaq->sent_at->format('M d Y - h:i:s') }}</span>
                                        </td>
                                        <td class="hidden-xs hidden-sm action-links">
                                        <span class="action-links">
                                            @if ($jaq->completed)
                                                <a class="edit"><i class="fa-check"></i> Completed {{ $jaq->completed_at->format('M d Y - h:i:s') }}</a>
                                            @else
                                                <a class="delete"><i class="fa-times"></i> Not Completed</a>
                                            @endif
                                        </span>
                                        </td>
                                        <td>
                                            @if (! $jaq->completed)
                                                <a href="{{ url('/jaq/'.$jaq->id) }}"><i class="fa-pencil-square-o"></i> {{ translate('Take Questionnaire') }}</a>
                                            @endif
                                        </td>
                                    </tr>

                                @endforeach

                                </tbody>
                            </table>

                    </div>
                </div>
            @endif

        </div>

        <footer>
            <img src="{{ asset('assets/images/logo-small.png') }}" />
        </footer>

    </div>

@stop