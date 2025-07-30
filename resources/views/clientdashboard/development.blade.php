@extends('dashboard.dashboard')

@section('content')

    <!-- Title -->
    <div class="page-title">
        <div class="title-env">
            <h1 class="title">Employee Development</h1>
            <p class="description"></p>
        </div>
    </div>

    <div class="row">


        <div class="panel panel-headerless">
            <div class="panel-body">

<!-- Add Job Button -->
                <div class="pull-right">
                    {!! Form::open(['method' => 'post', 'action' => ['ClientDashboardController@development']]) !!}
                    {!! Form::text('name', null, ['class' => 'form-control','placeholder'=>'Search by name','style'=>'float: left;width: 72%;'])!!}
                    {!! Form::submit('Search', ['class' => 'btn btn-primary','style'=>'float:right;']) !!}
                    {!! Form::close() !!}
                </div>

                <!-- Surveys -->
                <div class="tab-content">
                    <div class="tab-pane active">
                        <table class="table table-hover members-table middle-align">
                            <thead>
                            <tr>
                                
                                <th>Survey Name</th>
                                <th>Survey Date</th>
                                <th>Distribute To Leaders?</th>
                                
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($surveys as $survey)

                                <tr>
                                    
                                    <td class="user-name">
                                        <a href="" class="name">{{ $survey['name'] }}</a>
                                    </td>
                                    <td>
                                        <span class="email">{{ $survey['date'] }}</span>
                                    </td>
                                    <td>
                                        
                                            <a href="#null" class="edit"><i class="linecons-mail"></i> Email Leaders</a> &nbsp;&nbsp;&nbsp;&nbsp;
                                        
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

@stop