@extends('dashboard.dashboard')

@section('content')

    <!-- Title -->
    <div class="page-title">
        <div class="title-env">
            <h1 class="title">{{ $assessment->name }}: Dimensions</h1>
            <p class="description">Edit the dimensions that apply to this assessment.</p>
        </div>
    </div>

    <div class="row">

        <!-- Sub Navigation -->
        @include('dashboard.assessments.partials._subnav', ['active' => 'Dimensions'])

        <div class="panel panel-headerless">
            <div class="panel-body">

                <!-- Add Dimension Button -->
                <div class="pull-right">
                    <a href="{{ url('dashboard/assessments/'.$assessment->id.'/dimensions/create') }}" class="btn btn-black"><i class="fa-plus"></i> Add Dimension</a>
                </div>

                <!-- Dimensions -->
                <div class="tab-content">
                    <div class="tab-pane active">
                        <table class="table table-hover members-table middle-align">
                            <thead>
                            <tr>
                                <th>Dimension</th>
                                <th>Code</th>
                                {{--<th>Parent</th>--}}
                                <th>Settings</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($dimensions as $i => $dimension)

                                <tr style="background-color:white;margin-top:2px;">
                                    <td class="user-name">
                                        <a href="#" class="name">{{ $dimension->name }}</a>
                                    </td>
                                    <td>
                                        <div class="label label-white">
                                            {{ $dimension->code }}
                                        </div>
                                    </td>
                                    {{--<td>---</td>--}}
                                    <td>
                                        {!! Form::open(['method' => 'delete', 'action' => ['DimensionsController@destroy', $assessment->id, $dimension->id]]) !!}
                                            <a href="{{ url('dashboard/assessments/'.$assessment->id.'/dimensions/'.$dimension->id.'/edit') }}"><i class="linecons-pencil"></i> Edit</a>&nbsp;&nbsp;&nbsp;
                                            <a href="#null" class="delete" data-name="{{ $dimension->name }}"><i class="linecons-trash"></i> Remove</a>
                                        {!! Form::close() !!}
                                    </td>
                                </tr>

                                <?php $subdimensions = $dimension->getChildren(); ?>
                                @if (! $subdimensions->isEmpty())
                                    @foreach($subdimensions as $j => $subdimension)

                                        <tr style="background-color:white;margin-top:2px;">
                                            <td class="user-name">
                                                <a href="#" class="name">
                                                    <i class="fa-level-up" style="position:relative; transform: rotate(90deg); margin-right:10px; margin-left:10px; color: #aaa;"></i>
                                                    {{ $subdimension->name }}
                                                </a>
                                            </td>
                                            <td>
                                                <div class="label label-white">
                                                    {{ $subdimension->code }}
                                                </div>
                                            </td>
                                            {{--<td>--}}
                                                {{--{{ $subdimension->getParent()->name }}--}}
                                            {{--</td>--}}
                                            <td>
                                                {!! Form::open(['method' => 'delete', 'action' => ['DimensionsController@destroy', $assessment->id, $subdimension->id]]) !!}
                                                    <a href="{{ url('dashboard/assessments/'.$assessment->id.'/dimensions/'.$subdimension->id.'/edit') }}"><i class="linecons-pencil"></i> Edit</a>&nbsp;&nbsp;&nbsp;
                                                    <a href="#null" class="delete" data-name="{{ $subdimension->name }}"><i class="linecons-trash"></i> Remove</a>
                                                {!! Form::close() !!}
                                            </td>
                                        </tr>

                                    @endforeach
                                @endif
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

                if (confirm('Are you sure you want to delete '+name+' and all of its Sub-dimensions?'))
                    form.submit();
            });
        });
    </script>

@stop