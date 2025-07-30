@extends('dashboard.dashboard')

@section('styles')
    <style>
        #cancel-download {
            text-decoration: underline;
            cursor: pointer;
            float: right;
            position: relative;
            top: -30px;
        }
        .user {
            padding: 20px;
            background: #F6F6F6;
            font-size: 14px;
            margin-bottom: 2px;
        }
        .user span {
            display: block;
            font-size: 12px;
            color: #9d9d9d;
        }
        .user .info {
            color: #0b97c4;
        }
        .members-table thead tr th {
            font-size: 10px;
        }
        .arrow {
            margin: 10px 23px 10px 65px;
            color: #cfcfcf;
        }
        .status {
            display: inline-block;
            vertical-align: middle;
            background: #b7b7b7;
            margin-right: 5px;
            position: relative;
            top: -1px;
            width: 8px;
            height: 8px;
            -webkit-border-radius: 8px;
            -webkit-background-clip: padding-box;
            -moz-border-radius: 8px;
            -moz-background-clip: padding;
            border-radius: 8px;
            background-clip: padding-box;
            -webkit-transition: all 220ms ease-in-out;
            -moz-transition: all 220ms ease-in-out;
            -o-transition: all 220ms ease-in-out;
            transition: all 220ms ease-in-out;
        }
        .status.green {
            background-color: #8dc63f;
        }
        .status.lime {
            background-color: #b9c945;
        }
        .status.yellow {
            background-color: #ffba00;
        }
        .status.orange {
            background-color: #d36e30;
        }
        .status.red {
            background-color: #cc3f44;
        }
        .fit {
            background: white none repeat scroll 0% 0%;
            padding: 9px 16px 9px 13px;
            display: inline-block;
            border: 1px solid rgb(239, 239, 239);
            text-transform: uppercase;
            font-size: 10px;
            letter-spacing: 1px;
            color: #9e9e9e;
        }
    </style>
@stop

@section('content')

    {{-- Reseller Title --}}
    @if (isset($reseller))
        <div class="page-title orange">
            <div class="title-env">
                <h1 class="title">{{ $reseller->name }}</h1>
            </div>
        </div>
    @endif

    {{-- Title --}}
    <div class="page-title">
        <div class="title-env">
            <h1 class="title">Overview</h1>
            <p class="description">View all the info at a glance for this reseller.</p>
        </div>
    </div>

    <div class="row">

        {{-- Errors --}}
        @include('errors.list')

        {{-- Sub Navigation --}}
        @include('dashboard.resellers.partials._subnav', ['active' => 'General'])

        <div class="panel panel-headerless">
            <div class="panel-body">
                <section class="profile-env">
                    <div class="row">
                        <div class="col-sm-3" style="padding: 18px;">

                            {{-- Sidebar --}}
                            <div class="user-info-sidebar">

                                {{-- User Info --}}
                                <a class="user-img">
                                    @if ($reseller->logo)
                                        <img src="{{ show_image($reseller->logo) }}" alt="user-img" class="img-responsive" style="max-width: 190px;margin-bottom: 18px;" />
                                    @else
                                        <img src="{{ url('assets/images/user-4.png') }}" alt="user-img" class="img-cirlce img-responsive img-thumbnail" />
                                    @endif
                                </a>
                                <a class="user-name">{{ $reseller->name }}</a>
                                <span class="user-title">Reseller</span>
                                <hr />

                                {{-- Assignments Count --}}
                                <ul class="list-unstyled user-friends-count">
                                    <li>
                                        <span>{{ $reseller->clientsCount() }}</span>
                                        Clients
                                    </li>
                                    <li>
                                        <span>{{ $reseller->usersCount() }}</span>
                                        Users
                                    </li>
                                </ul>
                                <hr><br>

                                {{-- Controls --}}
                                <div style="text-align: center;">
                                    <a class="btn btn-black" href="/dashboard/resellers/{{ $reseller->id }}/edit" style="padding: 8px 46px"><i class="fa-pencil"></i> Edit Reseller</a><br>
                                    @role('admin')
                                        @if ($reseller->db_status == 'available')
                                            <a id="download-all-data" class="btn btn-info" style="padding: 8px 29px"><i class="fa-download"></i> Download All Data</a><br>
                                        @endif
                                        <a class="btn btn-success" href="/dashboard/resellers/{{ $reseller->id }}/logout" style="padding: 8px 53px"><i class="fa-download"></i> Login Link</a>
                                    @endrole
                                </div>

                                {{-- Progress Bar --}}
                                <div class="progress-status col-sm-12">
                                    <div class="progress progress-striped active" style="background-color:white;">
                                        <div id="progress-bar" class="progress-bar progress-bar-success"></div>
                                    </div>
                                    <div id="progress-text"></div>
                                </div>
                                <div style="clear:both;"></div>
                            </div>

                        </div>
                        <div class="col-sm-9">

                            {{-- Notifications --}}
                            <section class="user-timeline-stories" style="padding-top: 0;">
                                <article class="timeline-story">
                                    <i class="fa-paper-plane-empty block-icon"></i>
                                    <header style="margin-bottom: 30px;">
                                        <h3>Notifications</h3>
                                    </header>
                                    <p>No notifications available at this time.</p>
                                </article>
                            </section>

                        </div>
                    </div>
                </section>
            </div>
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

            // Server-sent Events
            var es;

            $('#download-all-data').on('click', function()
            {
                var url = '/dashboard/assignments/download/{{ $reseller->id }}';
                es = new EventSource(url);

                // Add a cancel option
                $cancel = $('<a id="cancel-download"><i class="fa-times"></i> Cancel</a>');
                $('#progress-text').after($cancel);

                // Listen for messages
                es.addEventListener('message', function(e) {
                    var result = JSON.parse(e.data);

                    // Completed
                    if (result.i == -1)
                    {
                        es.close();
                        $('#progress-text').text('');
                        $('#progress-bar').css('width', '0%');
                        $('#cancel-download').remove();
                        window.location = '/download/' + result.message.file;
                    }

                    // Update progress
                    else
                    {
                        $('#progress-text').text('Preparing data... ' + result.message.toFixed(2) + '%');
                        $('#progress-bar').css('width', result.message.toFixed(2) + '%');
                    }
                });

                // Error
                es.addEventListener('error', function(e) {
                    console.log(e);
                    alert('Error occurred');
                    es.close();
                });

            });

            // Cancel download
            $('.progress-status').on('click', '#cancel-download', function(){
                es.close();
                $('#progress-text').text('');
                $('#progress-bar').css('width', '0%');
                $(this).remove();
            });
        });
    </script>

@stop