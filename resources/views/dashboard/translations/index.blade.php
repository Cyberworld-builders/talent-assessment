@extends('dashboard.dashboard')

@section('content')

    <!-- Title -->
    <div class="page-title">
        <div class="title-env">
            <h1 class="title">{{ $assessment->name }}: Translations</h1>
            <p class="description">Manage the translations for this assessment.</p>
        </div>
    </div>

    <div class="row">

        <!-- Sub Navigation -->
        @include('dashboard.assessments.partials._subnav', ['active' => 'Translations'])

        <div class="panel panel-headerless">
            <div class="panel-body">

                <!-- Add Translation Button -->
                <div class="pull-right">
                    <a href="{{ url('dashboard/assessments/'.$assessment->id.'/translations/create') }}" class="btn btn-black"><i class="fa-plus"></i> Add Translation</a>
                </div>

                <!-- Translations -->
                <div class="tab-content">
                    <div class="tab-pane active">
                        <table class="table table-hover members-table middle-align">
                            <thead>
                            <tr>
                                {{--<th></th>--}}
                                <th>Name</th>
                                <th>Language</th>
                                <th>Created By</th>
                                <th>Settings</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($translations as $translation)

                                <tr>
                                    {{--<td class="user-cb">--}}
                                    {{--<input type="checkbox" class="cbr" name="members-list[]" value="1" checked />--}}
                                    {{--</td>--}}
                                    <td class="user-name">
                                        <a href="{{ url('dashboard/assessments/'.$assessment->id.'/translations/'.$translation->id.'/edit') }}" class="name">{{ $translation->name }}</a>
                                    </td>
                                    <td>
                                        <span class="email">{{ $translation->language()->name }}</span>
                                    </td>
                                    <td>
                                        {{ $translation->user->name }}
                                    </td>
                                    <td>
                                        {!! Form::open(['method' => 'delete', 'action' => ['TranslationsController@destroy', $assessment->id, $translation->id]]) !!}
                                            <a href="{{ url('dashboard/assessments/'.$assessment->id.'/translations/'.$translation->id.'/edit') }}" class="edit"><i class="linecons-pencil"></i> Edit</a> &nbsp;&nbsp;&nbsp;&nbsp;
                                            <a href="#null" class="edit delete" data-name="{{ $translation->name }}"><i class="linecons-trash"></i> Delete</a>
                                        {!! Form::close() !!}
                                    </td>
                                </tr>

                            @endforeach
                            </tbody>
                        </table>

                    </div>
                </div>

            </div>
        </div>

    </div>

    <script type="text/javascript">
        jQuery(document).ready(function($){

            // Delete the specified resource
            $('.delete').on('click', function() {
                var name = $(this).attr('data-name');
                var form = $(this).closest('form');

                if (confirm('Are you sure you want to delete '+name+'?'))
                    form.submit();
            });
        });
    </script>

@stop