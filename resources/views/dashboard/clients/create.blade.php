@extends('dashboard.dashboard')

@section('body-class')
    page-clients
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
            <h1 class="title">Create Client</h1>
            <p class="description">Create a new client.</p>
        </div>
    </div>

    <div class="row">

        {{-- Errors --}}
        @include('errors.list')

        @if (isset($reseller))
            {!! Form::open(['url' => 'dashboard/resellers/'.$reseller->id.'/clients', 'enctype' => 'multipart/form-data']) !!}
        @else
            {!! Form::open(['url' => 'dashboard/clients', 'enctype' => 'multipart/form-data']) !!}
        @endif
            @include('dashboard.clients.partials._form', [
                'edit' => false,
                'button_name' => 'Add Client',
            ])
        {!! Form::close() !!}

    </div>

@stop