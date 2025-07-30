@extends('dashboard.dashboard')

@section('body-class')
    page-assessments
@stop

@section('content')

	<!-- Title -->
	<div class="page-title">
		<div class="title-env">
			<h1 class="title">Create New Assessment</h1>
			<p class="description">Create a new assessment.</p>
		</div>
	</div>

	<div class="row">

		<!-- Errors -->
		@include('errors.list')

		{!! Form::open(['url' => 'dashboard/assessments', 'enctype' => 'multipart/form-data']) !!}
		@include('dashboard.assessments.partials._form', [
            'edit' => false,
            'button_name' => 'Create Assessment'
        ])
		{!! Form::close() !!}

		<!-- Templates -->
		@include('dashboard.assessments.partials._templates')

	</div>
@stop