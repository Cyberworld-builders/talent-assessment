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
            <h1 class="title">Edit User</h1>
            <p class="description">Edit all info for this user.</p>
        </div>
    </div>

    <div class="row">

        {{-- Errors --}}
        @include('errors.list')

        @if (isset($reseller))
            {!! Form::model($user, ['method' => 'PATCH', 'action' => ['ResellersController@updateUser', $reseller->id, $user->id]]) !!}
        @else
            {!! Form::model($user, ['method' => 'PATCH', 'action' => ['UsersController@update', $user->id]]) !!}
        @endif
            @include('dashboard.users.partials._form', ['button_name' => 'Save Changes', 'edit' => true])
        {!! Form::close() !!}

    </div>

@stop

@section('scripts')
    <script src="{{ asset('assets/js/select2/select2.min.js') }}"></script>
@stop

