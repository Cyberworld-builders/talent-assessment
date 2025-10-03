@extends('dashboard.dashboard')

@section('content')

	<!-- Title -->
	<div class="page-title">
		<div class="title-env">
			<h1 class="title">{{ $assessment->name }}: New Editor</h1>
			<p class="description">Modern, flexible assessment editor with drag-and-drop reordering.</p>
		</div>
	</div>

	<div class="row">

		<!-- Errors -->
		@include('errors.list')

		<!-- Sub Navigation -->
		@include('dashboard.assessments.partials._subnav', ['active' => 'Edit Assessment (New)'])

		{!! Form::model($assessment, ['method' => 'PATCH', 'action' => ['AssessmentsController@updateNew', $assessment->id]]) !!}
			@include('dashboard.assessments.partials._form-new', [
				'edit' => true,
				'button_name' => 'Save Changes'
			])
		{!! Form::close() !!}

		<!-- Templates -->
		@include('dashboard.assessments.partials._templates-new')

	</div>
@stop
