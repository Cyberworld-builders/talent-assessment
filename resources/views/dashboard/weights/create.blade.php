@extends('dashboard.dashboard')

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
            @if (isset($reseller))
                <h1 class="title">Weights for {{ $job->name }}</h1>
                <h4>Configuring Assessment: <span class="label label-white" style="font-size: 13px; background: white; font-weight: 400; position: relative; top: -1px; margin-left: 5px;">{{ $assessment->name }}</span></h4>
            @else
                <h1 class="title">{{ $client->name }}: Weights for {{ $job->name }}</h1>
                <h4>Configuring Assessment: <span class="label label-white" style="font-size: 13px; background: white; font-weight: 400; position: relative; top: -1px; margin-left: 5px;">{{ $assessment->name }}</span></h4>
            @endif
        </div>
    </div>

    <div class="row">

        {{-- Errors --}}
        @include('errors.list')

        {{-- Sub Navigation --}}
        @if (isset($reseller))
            @include('dashboard.resellers.partials._subnav', ['active' => 'Weighting'])
        @else
            @include('dashboard.clients.partials._subnav', ['active' => 'Weighting'])
        @endif

        @if (isset($reseller))
            {!! Form::open(['url' => 'dashboard/resellers/'.$reseller->id.'/weights/'.$job->id.'/'.$assessment->id]) !!}
                @include('dashboard.weights.partials._form', [
                    'edit' => false,
                    'button_name' => 'Set Custom Weighting'
                ])
            {!! Form::close() !!}
        @else
            {!! Form::open(['url' => 'dashboard/clients/'.$client->id.'/weights/'.$job->id.'/'.$assessment->id]) !!}
                @include('dashboard.weights.partials._form', [
                    'edit' => false,
                    'button_name' => 'Set Custom Weighting'
                ])
            {!! Form::close() !!}
        @endif

    </div>
@stop