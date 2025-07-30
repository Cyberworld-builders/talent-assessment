@extends('dashboard.dashboard')

@section('content')

    {{-- Title --}}
    <div class="page-title">
        <div class="title-env">
            <h1 class="title">{{ $client->name }}: {{ $analysis->name }} Details</h1>
            <p class="description">View or export the questionnaires completed for this job analysis.</p>
        </div>
    </div>

    {{-- Sub Navigation --}}
    @include('dashboard.clients.partials._subnav')

    <div class="panel panel-headerless">
        <div class="panel-body">

            {{-- Buttons --}}
            <div class="pull-right">
                @if (count($analysis->users))
                    @if ($analysis->sent_at && $analysis->sent_at != 0)
                        <a href="{{ url('dashboard/clients/'.$client->id.'/analysis/'.$analysis->id.'/send') }}" class="btn btn-black"><i class="linecons-paper-plane"></i> Re-Send Questionnaires</a>
                    @else
                        <a href="{{ url('dashboard/clients/'.$client->id.'/analysis/'.$analysis->id.'/send') }}" class="btn btn-success"><i class="linecons-paper-plane"></i> Send Questionnaires</a>
                    @endif
                    <a href="{{ url('dashboard/clients/'.$client->id.'/analysis/'.$analysis->id.'/edit') }}" class="btn btn-black"><i class="fa-plus"></i> Add Users To Analysis</a>
                @endif
            </div>

            <!-- Users -->
            <div class="tab-content" style="background:#fff;">
                <div class="tab-pane active">
                    <table class="table table-hover members-table middle-align">
                        <thead>
                        <tr>
                            {{--<th></th>--}}
                            {{--<th class="hidden-xs hidden-sm"></th>--}}
                            <th>User</th>
                            <th>Sent</th>
                            <th>Status</th>
                            <th>Settings</th>
                        </tr>
                        </thead>
                        <tbody>

                        @if ($analysis->users)
                            @foreach ($analysis->users as $userId)
                                <?php
                                    $user = \App\User::find($userId);
                                    if (!$user)
                                        continue;
                                    $jaq = $user->getJaqForAnalysis($analysis->id);
                                ?>
                                <tr>
                                    <td>
                                        <a class="name" href="/dashboard/users/{{ $user->id }}">{{ $user->name }}</a>
                                    </td>
                                    @if ($jaq)
                                        <td>
                                            @if ($jaq->sent)
                                                <span class="text-black"><i class="linecons-paper-plane"></i> Sent</span>
                                            @else
                                                <span class="email">Not Sent</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($jaq->completed)
                                                <span class="text-success"><i class="fa-check"></i> Completed</span>
                                            @else
												<?php
												    $percent = 0;
												    if ($jaq->name) $percent += 5;
												    if ($jaq->department_name) $percent += 5;
												    if ($jaq->location) $percent += 5;
												    if ($jaq->supervisor_name) $percent += 5;
												    if ($jaq->supervisor_title) $percent += 5;
												    if ($jaq->position_desc) $percent += 5;
												    if ($jaq->ksa_linkages) $percent += 45;
												    if ($jaq->min_education) $percent += 5;
												    if ($jaq->preferred_education) $percent += 5;
												    if ($jaq->min_experience) $percent += 5;
												    if ($jaq->preferred_experience) $percent += 5;
												    if ($jaq->additional_requirements) $percent += 5;
												?>
                                                <div class="progress" style="width: 65%;">
                                                    <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $percent }}%"></div>
                                                </div>
                                                <span class="text-muted text-small">{{ $percent }}% Complete</span>
                                            @endif
                                        </td>
                                        <td>
                                            @role('admin')
                                                {!! Form::open(['method' => 'delete', 'action' => ['JaqsController@destroy', $client->id, $analysis->id, $jaq->id]]) !!}
                                                    <a href="{{ url('dashboard/clients/'.$client->id.'/analysis/'.$analysis->id.'/jaqs/'.$jaq->id) }}" class="edit"><i class="linecons-eye"></i> View</a> &nbsp;&nbsp;&nbsp;&nbsp;
                                                    <a href="{{ url('dashboard/clients/'.$client->id.'/analysis/'.$analysis->id.'/jaqs/'.$jaq->id.'/reset') }}" class="edit reset"><i class="fa-eraser"></i> Reset</a> &nbsp;&nbsp;&nbsp;&nbsp;
                                                    <a href="#null" class="edit delete" data-name="{{ $user->name }}"><i class="linecons-trash"></i> Remove from Analysis</a>
                                                {!! Form::close() !!}
                                            @endrole
                                        </td>
                                    @else
                                        <td colspan="3">
                                            <p class="text-muted">
                                                <i class="fa-exclamation-circle"></i>
                                                There is no Questionnaire associated with this user.
                                            </p>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        @else
                            <tr><td colspan="4">There are no users assigned to this analysis.</td></tr>
                        @endif

                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <script type="text/javascript">
        jQuery(document).ready(function($) {

            // Set headers for AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')
                }
            });

            // Delete the specified resource
            $('.delete').on('click', function() {
                var name = $(this).attr('data-name');
                var form = $(this).closest('form');

                if (confirm('Are you sure you want to remove '+name+' from this analysis? \n\nNOTE: This will also delete this user\'s JAQ.'))
                    form.submit();
            });

            // Reset JAQ
            $('.reset').on('click', function (e) {
                e.preventDefault();
                var href = $(this).attr('href');

                if (confirm('Are you sure you want to reset this questionnaire?')) {
                    window.location = href;
                }
            });
        });
    </script>

@stop
