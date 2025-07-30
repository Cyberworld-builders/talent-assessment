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

    {{--Title--}}
    <div class="page-title">
        <div class="title-env">
            @if (isset($reseller))
                <h1 class="title">Create New Job</h1>
                <p class="description">Create a new job for this reseller.</p>
            @else
                <h1 class="title">{{ $client->name }}: Create New Job</h1>
                <p class="description">Create a new job for this client.</p>
            @endif
        </div>
    </div>

    <div class="row">

        {{-- Errors --}}
        @include('errors.list')

        {{-- Sub Navigation --}}
        @if (isset($reseller))
            @include('dashboard.resellers.partials._subnav')
        @else
            @include('dashboard.clients.partials._subnav')
        @endif

        {{-- AOE Admin Editing Reseller --}}
        @if (isset($reseller))
            {!! Form::open(['url' => 'dashboard/resellers/'.$reseller->id.'/jobs']) !!}
                @include('dashboard.jobs.partials._form', [
                    'button_name' => 'Create Job'
                ])
            {!! Form::close() !!}
        @else
            {{-- Reseller Creating From Job Template --}}
            @if (isset($jobTemplate))
                {!! Form::open(['url' => 'dashboard/clients/'.$client->id.'/jobs/'.$jobTemplate->id]) !!}
                    @include('dashboard.jobs.partials._form', [
                        'button_name' => 'Create Job'
                    ])
                {!! Form::close() !!}

            {{-- AOE Admin Creating New --}}
            @else
                {!! Form::open(['url' => 'dashboard/clients/'.$client->id.'/jobs']) !!}
                    @include('dashboard.jobs.partials._form', [
                        'button_name' => 'Create Job'
                    ])
                {!! Form::close() !!}
            @endif
        @endif

    </div>

@stop