@extends('dashboard.dashboard')

@section('content')

	<!-- Title -->
	<div class="page-title">
		<div class="title-env">
			<h1 class="title">{{ $assessment->name }}: General</h1>
			<p class="description">Edit assessment details and questions.</p>
		</div>
	</div>

	<div class="row">

		<!-- Errors -->
		@include('errors.list')

		<!-- Sub Navigation -->
		@include('dashboard.assessments.partials._subnav', ['active' => 'General'])

		{!! Form::model($assessment, ['method' => 'PATCH', 'action' => ['AssessmentsController@update', $assessment->id]]) !!}
			@include('dashboard.assessments.partials._form', [
				'edit' => true,
				'button_name' => 'Save Changes'
			])
		{!! Form::close() !!}

		<!-- Templates -->
		@include('dashboard.assessments.partials._templates')

	</div>
@stop