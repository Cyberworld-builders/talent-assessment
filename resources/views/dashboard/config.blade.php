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
    {!! Form::open(['url' => 'dashboard/config']) !!}
    <h3>Global Configuration</h3>
    <div class="config">
        <div class="header">
            <div class="row">
                <div class="col-xs-2"><strong>Id</strong></div>
                <div class="col-xs-8"><strong>Name</strong></div>
                <div class="col-xs-2"><strong>Value</strong></div>
            </div>
        </div>
        <div class="options" style="position: relative; overflow: hidden;">
            @foreach ($options as $option)
                <div class="row" style="border-bottom: 1px solid #eee; postition: relative;">
                    <div class="col-xs-2">
                        {!! Form::text('options[id][]', $option->id, ['readonly']) !!}
                    </div>
                    <div class="col-xs-8">
                        {!! Form::text('options[name][]', $option->name) !!}
                    </div>
                    <div class="col-xs-2">
                        {!! Form::text('options[value][]', $option->value) !!}
                    </div>
                </div>
            @endforeach
        </div>
        <div class="new-option">
            <div class="row">
                <div class="col-xs-2"></div>
                <div class="col-xs-8">
                    {!! Form::text('new_option[name]') !!}
                </div>
                <div class="col-xs-2">
                    {!! Form::text('new_option[value]') !!}
                </div>
            </div>
        </div>
    </div>
    <br/>
    {!! Form::submit('Update Config') !!}
    {!! Form::close() !!}
@stop
