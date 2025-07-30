@extends('dashboard.dashboard')

@section('styles')
    <style>
        .config {
            background: white;
            border: 1px solid #eee;
        }
        .row {
            padding: 10px 20px;
        }
    </style>
@stop

@section('content')
    {{--{!! Form::open(['url' => 'dashboard/config']) !!}--}}
    <h3>Databases</h3>
    <div class="config">
        <div class="header">
            <div class="row">
                <div class="col-xs-2"><strong>Database</strong></div>
                <div class="col-xs-2"><strong>Type</strong></div>
                <div class="col-xs-6"><strong>Status</strong></div>
                <div class="col-xs-2"><strong>Settings</strong></div>
            </div>
        </div>
        <div class="options">
            @foreach ($resellers as $reseller)
                <div class="row">
                    <div class="col-xs-2">
                        {{ $reseller->db_name }}
                    </div>
                    <div class="col-xs-2">
                        @if ($reseller->db_host)
                            Amazon
                        @else
                            Local
                        @endif
                    </div>
                    <div class="col-xs-6">
                        @if ($reseller->db_updated === true)
                            <span class="text-success">Updated</span>
                        @else
                            @foreach ($reseller->db_updated as $missing)
                                <span class="text-danger">{{ $missing }}</span><br/>
                            @endforeach
                        @endif
                    </div>
                    <div class="col-xs-2">
                        @if ($reseller->db_updated !== true)
                            <a class="btn btn-orange" href="/dashboard/config/databases/{{ $reseller->id }}/update">Update Database</a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    <br/>
{{--    {!! Form::submit('Update Config') !!}--}}
{{--    {!! Form::close() !!}--}}
@stop
