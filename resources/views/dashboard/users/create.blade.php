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
            <h1 class="title">Create New User</h1>
            <p class="description">Create a new user.</p>
        </div>
    </div>

    <div class="row">

        {{-- Errors --}}
        @include('errors.list')

        @if (isset($reseller))
            {!! Form::open(['url' => 'dashboard/resellers/'.$reseller->id.'/users']) !!}
        @else
            {!! Form::open(['url' => 'dashboard/users']) !!}
        @endif
            @include('dashboard.users.partials._form', ['button_name' => 'Create User', 'edit' => false])
        {!! Form::close() !!}

    </div>

@stop

@section('scripts')
    <script src="{{ asset('assets/js/select2/select2.min.js') }}"></script>
@stop

